<?php
/**
 * AJAX Handlers
 *
 * @package CNP_Auto_Featured_Images
 */

if (!defined('ABSPATH')) {
    exit;
}

class CNP_AFI_Ajax_Handlers {

    /**
     * Validate API key
     */
    public static function validate_api_key() {
        check_ajax_referer('cnp_afi_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'cnp-auto-featured-images')]);
        }

        $api_key = sanitize_text_field($_POST['api_key'] ?? '');

        if (empty($api_key)) {
            wp_send_json_error(['message' => __('API key is required', 'cnp-auto-featured-images')]);
        }

        $api = new CNP_AFI_Fal_AI_API();
        $result = $api->validate_api_key($api_key);

        if ($result['valid']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }

    /**
     * Save settings
     */
    public static function save_settings() {
        check_ajax_referer('cnp_afi_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'cnp-auto-featured-images')]);
        }

        // Get current settings
        $settings = get_option('cnp_afi_settings', []);

        // Update settings
        $settings['api_key'] = sanitize_text_field($_POST['api_key'] ?? '');
        $settings['api_key_valid'] = (bool) ($_POST['api_key_valid'] ?? false);
        $settings['auto_generate_on_publish'] = (bool) ($_POST['auto_generate_on_publish'] ?? false);
        $settings['image_model'] = sanitize_text_field($_POST['image_model'] ?? 'fal-ai/flux/schnell');
        $settings['image_size'] = sanitize_text_field($_POST['image_size'] ?? 'landscape_16_9');
        $settings['image_style'] = sanitize_text_field($_POST['image_style'] ?? 'professional news photo');
        $settings['prompt_template'] = sanitize_textarea_field($_POST['prompt_template'] ?? '');
        $settings['max_retries'] = intval($_POST['max_retries'] ?? 3);
        $settings['timeout'] = intval($_POST['timeout'] ?? 60);

        update_option('cnp_afi_settings', $settings);

        wp_send_json_success([
            'message' => __('Settings saved successfully!', 'cnp-auto-featured-images'),
        ]);
    }

    /**
     * Generate single image
     */
    public static function generate_single_image() {
        check_ajax_referer('cnp_afi_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => __('Permission denied', 'cnp-auto-featured-images')]);
        }

        $post_id = intval($_POST['post_id'] ?? 0);

        if (!$post_id) {
            wp_send_json_error(['message' => __('Invalid post ID', 'cnp-auto-featured-images')]);
        }

        $generator = new CNP_AFI_Image_Generator();
        $result = $generator->generate_and_attach($post_id);

        if (is_wp_error($result)) {
            wp_send_json_error([
                'message' => $result->get_error_message(),
            ]);
        }

        wp_send_json_success($result);
    }

    /**
     * Bulk generate images
     */
    public static function bulk_generate_images() {
        check_ajax_referer('cnp_afi_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'cnp-auto-featured-images')]);
        }

        $post_ids = isset($_POST['post_ids']) ? array_map('intval', $_POST['post_ids']) : [];

        if (empty($post_ids)) {
            wp_send_json_error(['message' => __('No posts selected', 'cnp-auto-featured-images')]);
        }

        // Process in batches to avoid timeout
        $batch_size = 5;
        $offset = intval($_POST['offset'] ?? 0);
        $batch = array_slice($post_ids, $offset, $batch_size);

        $generator = new CNP_AFI_Image_Generator();
        $results = $generator->bulk_generate($batch);

        wp_send_json_success([
            'results' => $results,
            'processed' => $offset + count($batch),
            'total' => count($post_ids),
            'has_more' => ($offset + count($batch)) < count($post_ids),
        ]);
    }

    /**
     * Get posts without images
     */
    public static function get_posts_without_images() {
        check_ajax_referer('cnp_afi_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => __('Permission denied', 'cnp-auto-featured-images')]);
        }

        $post_type = sanitize_text_field($_POST['post_type'] ?? 'post');
        $limit = intval($_POST['limit'] ?? 100);

        $posts = CNP_AFI_Image_Generator::get_posts_without_featured_images($post_type, $limit);

        wp_send_json_success([
            'posts' => $posts,
            'count' => count($posts),
        ]);
    }
}
