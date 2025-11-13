<?php
/**
 * FAL AI API Integration
 *
 * @package CNP_Auto_Featured_Images
 */

if (!defined('ABSPATH')) {
    exit;
}

class CNP_AFI_Fal_AI_API {

    /**
     * API base URL
     */
    private const API_BASE_URL = 'https://fal.run/fal-ai';

    /**
     * API key
     */
    private $api_key;

    /**
     * Settings
     */
    private $settings;

    /**
     * Constructor
     */
    public function __construct() {
        $this->settings = get_option('cnp_afi_settings', []);
        $this->api_key = $this->settings['api_key'] ?? '';
    }

    /**
     * Validate API key
     */
    public function validate_api_key($api_key = null) {
        $key = $api_key ?? $this->api_key;

        if (empty($key)) {
            return [
                'valid' => false,
                'message' => __('API key is empty', 'cnp-auto-featured-images'),
            ];
        }

        try {
            // Test API with a simple request
            $response = $this->make_request('/flux/schnell', [
                'prompt' => 'test',
                'image_size' => 'square',
                'num_inference_steps' => 1, // Minimal steps for fast test
            ], $key);

            if (is_wp_error($response)) {
                return [
                    'valid' => false,
                    'message' => $response->get_error_message(),
                ];
            }

            return [
                'valid' => true,
                'message' => __('API key is valid!', 'cnp-auto-featured-images'),
            ];

        } catch (Exception $e) {
            return [
                'valid' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate image from prompt
     *
     * @param string $prompt The prompt for image generation
     * @param array $options Additional options
     * @return array|WP_Error
     */
    public function generate_image($prompt, $options = []) {
        if (empty($this->api_key)) {
            return new WP_Error('no_api_key', __('No API key configured', 'cnp-auto-featured-images'));
        }

        // Default options
        $defaults = [
            'model' => $this->settings['image_model'] ?? 'fal-ai/flux/schnell',
            'image_size' => $this->settings['image_size'] ?? 'landscape_16_9',
            'num_inference_steps' => 4, // Fast generation
            'num_images' => 1,
            'enable_safety_checker' => true,
        ];

        $options = wp_parse_args($options, $defaults);

        // Prepare model endpoint
        $model = str_replace('fal-ai/', '', $options['model']);
        $endpoint = '/' . $model;

        // Prepare request body
        $body = [
            'prompt' => $prompt,
            'image_size' => $options['image_size'],
            'num_inference_steps' => $options['num_inference_steps'],
            'num_images' => $options['num_images'],
            'enable_safety_checker' => $options['enable_safety_checker'],
        ];

        // Make API request
        $response = $this->make_request($endpoint, $body);

        if (is_wp_error($response)) {
            return $response;
        }

        // Extract image URL
        if (isset($response['images']) && is_array($response['images']) && !empty($response['images'])) {
            return [
                'success' => true,
                'image_url' => $response['images'][0]['url'],
                'width' => $response['images'][0]['width'] ?? null,
                'height' => $response['images'][0]['height'] ?? null,
                'content_type' => $response['images'][0]['content_type'] ?? 'image/jpeg',
                'prompt' => $prompt,
            ];
        }

        return new WP_Error('no_image', __('No image generated', 'cnp-auto-featured-images'));
    }

    /**
     * Make API request
     *
     * @param string $endpoint API endpoint
     * @param array $body Request body
     * @param string $api_key Optional API key override
     * @return array|WP_Error
     */
    private function make_request($endpoint, $body, $api_key = null) {
        $key = $api_key ?? $this->api_key;
        $url = self::API_BASE_URL . $endpoint;

        $args = [
            'method' => 'POST',
            'timeout' => $this->settings['timeout'] ?? 60,
            'headers' => [
                'Authorization' => 'Key ' . $key,
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($body),
        ];

        $response = wp_remote_post($url, $args);

        // Check for errors
        if (is_wp_error($response)) {
            return $response;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);

        // Handle API errors
        if ($status_code !== 200) {
            $error_message = $data['detail'] ?? $data['error'] ?? 'Unknown error';

            if ($status_code === 401) {
                $error_message = __('Invalid API key', 'cnp-auto-featured-images');
            } elseif ($status_code === 429) {
                $error_message = __('Rate limit exceeded. Please try again later.', 'cnp-auto-featured-images');
            } elseif ($status_code === 500) {
                $error_message = __('FAL AI server error. Please try again later.', 'cnp-auto-featured-images');
            }

            return new WP_Error(
                'api_error',
                sprintf(
                    __('API Error (%d): %s', 'cnp-auto-featured-images'),
                    $status_code,
                    $error_message
                )
            );
        }

        return $data;
    }

    /**
     * Get available models
     */
    public function get_available_models() {
        return [
            'fal-ai/flux/schnell' => [
                'name' => 'FLUX Schnell (Fast)',
                'description' => 'Fast generation, good quality (4 steps)',
                'speed' => 'fast',
            ],
            'fal-ai/flux/dev' => [
                'name' => 'FLUX Dev (Balanced)',
                'description' => 'Balanced speed and quality (12 steps)',
                'speed' => 'medium',
            ],
            'fal-ai/flux-pro' => [
                'name' => 'FLUX Pro (Best Quality)',
                'description' => 'Best quality, slower generation (28 steps)',
                'speed' => 'slow',
            ],
            'fal-ai/stable-diffusion-v3-medium' => [
                'name' => 'Stable Diffusion 3 Medium',
                'description' => 'Good quality, fast generation',
                'speed' => 'fast',
            ],
        ];
    }

    /**
     * Get available image sizes
     */
    public function get_available_sizes() {
        return [
            'square_hd' => ['label' => 'Square HD (1024×1024)', 'width' => 1024, 'height' => 1024],
            'square' => ['label' => 'Square (512×512)', 'width' => 512, 'height' => 512],
            'portrait_4_3' => ['label' => 'Portrait 4:3 (768×1024)', 'width' => 768, 'height' => 1024],
            'portrait_16_9' => ['label' => 'Portrait 16:9 (576×1024)', 'width' => 576, 'height' => 1024],
            'landscape_4_3' => ['label' => 'Landscape 4:3 (1024×768)', 'width' => 1024, 'height' => 768],
            'landscape_16_9' => ['label' => 'Landscape 16:9 (1024×576)', 'width' => 1024, 'height' => 576],
        ];
    }

    /**
     * Get available image styles
     */
    public function get_available_styles() {
        return [
            'professional news photo' => __('Professional News Photo', 'cnp-auto-featured-images'),
            'modern digital illustration' => __('Modern Digital Illustration', 'cnp-auto-featured-images'),
            'minimalist design' => __('Minimalist Design', 'cnp-auto-featured-images'),
            'photorealistic' => __('Photorealistic', 'cnp-auto-featured-images'),
            'abstract art' => __('Abstract Art', 'cnp-auto-featured-images'),
            'editorial photography' => __('Editorial Photography', 'cnp-auto-featured-images'),
            'tech concept art' => __('Tech Concept Art', 'cnp-auto-featured-images'),
            'business professional' => __('Business Professional', 'cnp-auto-featured-images'),
        ];
    }
}
