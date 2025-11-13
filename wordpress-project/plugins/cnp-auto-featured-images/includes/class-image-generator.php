<?php
/**
 * Image Generator Class
 *
 * @package CNP_Auto_Featured_Images
 */

if (!defined('ABSPATH')) {
    exit;
}

class CNP_AFI_Image_Generator {

    /**
     * API instance
     */
    private $api;

    /**
     * Settings
     */
    private $settings;

    /**
     * Constructor
     */
    public function __construct() {
        $this->api = new CNP_AFI_Fal_AI_API();
        $this->settings = get_option('cnp_afi_settings', []);
    }

    /**
     * Generate and attach featured image to post
     *
     * @param int $post_id Post ID
     * @return array|WP_Error
     */
    public function generate_and_attach($post_id) {
        // Get post
        $post = get_post($post_id);
        if (!$post) {
            return new WP_Error('invalid_post', __('Invalid post ID', 'cnp-auto-featured-images'));
        }

        // Check if already has featured image
        if (has_post_thumbnail($post_id)) {
            return new WP_Error('has_thumbnail', __('Post already has a featured image', 'cnp-auto-featured-images'));
        }

        // Generate prompt from post
        $prompt = $this->generate_prompt($post);

        // Log start
        $log_id = $this->log_generation_start($post_id, $post->post_type, $prompt);

        try {
            // Generate image
            $result = $this->api->generate_image($prompt);

            if (is_wp_error($result)) {
                $this->log_generation_error($log_id, $result->get_error_message());
                return $result;
            }

            // Download and attach image
            $attachment_id = $this->download_and_attach_image(
                $result['image_url'],
                $post_id,
                $post->post_title
            );

            if (is_wp_error($attachment_id)) {
                $this->log_generation_error($log_id, $attachment_id->get_error_message());
                return $attachment_id;
            }

            // Set as featured image
            set_post_thumbnail($post_id, $attachment_id);

            // Log success
            $this->log_generation_success($log_id, $result['image_url']);

            // Update stats
            $this->update_generation_stats();

            return [
                'success' => true,
                'attachment_id' => $attachment_id,
                'image_url' => wp_get_attachment_url($attachment_id),
                'prompt' => $prompt,
            ];

        } catch (Exception $e) {
            $this->log_generation_error($log_id, $e->getMessage());
            return new WP_Error('generation_failed', $e->getMessage());
        }
    }

    /**
     * Generate prompt from post
     *
     * @param WP_Post $post Post object
     * @return string
     */
    private function generate_prompt($post) {
        $template = $this->settings['prompt_template'] ?? 'A professional editorial image representing: {title}. {style}';
        $style = $this->settings['image_style'] ?? 'professional news photo';

        // Get post title
        $title = $post->post_title;

        // Get post excerpt or first 150 characters
        $excerpt = '';
        if (!empty($post->post_excerpt)) {
            $excerpt = $post->post_excerpt;
        } elseif (!empty($post->post_content)) {
            $excerpt = wp_trim_words(strip_tags($post->post_content), 30, '');
        }

        // Get categories/tags for context
        $categories = [];
        if ($post->post_type === 'post') {
            $terms = get_the_terms($post->ID, 'category');
            if ($terms && !is_wp_error($terms)) {
                $categories = array_map(function($term) {
                    return $term->name;
                }, $terms);
            }
        }
        $category_context = !empty($categories) ? implode(', ', $categories) : '';

        // Replace placeholders
        $prompt = str_replace(
            ['{title}', '{excerpt}', '{categories}', '{style}'],
            [$title, $excerpt, $category_context, $style],
            $template
        );

        // Clean up prompt
        $prompt = preg_replace('/\s+/', ' ', $prompt); // Multiple spaces to single
        $prompt = trim($prompt);

        // Add safety prefix to avoid inappropriate content
        $prompt = "Safe for work, professional editorial: " . $prompt;

        return $prompt;
    }

    /**
     * Download image from URL and attach to post
     *
     * @param string $image_url Image URL
     * @param int $post_id Post ID
     * @param string $title Image title
     * @return int|WP_Error Attachment ID or error
     */
    private function download_and_attach_image($image_url, $post_id, $title) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Download file to temp location
        $temp_file = download_url($image_url);

        if (is_wp_error($temp_file)) {
            return $temp_file;
        }

        // Prepare file array for wp_handle_sideload
        $file_array = [
            'name' => 'featured-image-' . $post_id . '-' . time() . '.jpg',
            'tmp_name' => $temp_file,
        ];

        // Move to uploads directory
        $file = wp_handle_sideload($file_array, [
            'test_form' => false,
        ]);

        // Clean up temp file
        if (file_exists($temp_file)) {
            @unlink($temp_file);
        }

        if (isset($file['error'])) {
            return new WP_Error('upload_error', $file['error']);
        }

        // Create attachment
        $attachment = [
            'post_mime_type' => $file['type'],
            'post_title' => sanitize_text_field($title),
            'post_content' => '',
            'post_status' => 'inherit',
        ];

        $attachment_id = wp_insert_attachment($attachment, $file['file'], $post_id);

        if (is_wp_error($attachment_id)) {
            return $attachment_id;
        }

        // Generate metadata
        $attachment_data = wp_generate_attachment_metadata($attachment_id, $file['file']);
        wp_update_attachment_metadata($attachment_id, $attachment_data);

        // Set alt text
        update_post_meta($attachment_id, '_wp_attachment_image_alt', sanitize_text_field($title));

        return $attachment_id;
    }

    /**
     * Bulk generate images for posts without featured images
     *
     * @param array $post_ids Array of post IDs
     * @return array Results
     */
    public function bulk_generate($post_ids) {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($post_ids as $post_id) {
            $result = $this->generate_and_attach($post_id);

            if (is_wp_error($result)) {
                $results['failed']++;
                $results['errors'][$post_id] = $result->get_error_message();
            } else {
                $results['success']++;
            }

            // Small delay to avoid rate limiting
            usleep(500000); // 0.5 seconds
        }

        return $results;
    }

    /**
     * Log generation start
     */
    private function log_generation_start($post_id, $post_type, $prompt) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cnp_afi_logs';

        $wpdb->insert($table_name, [
            'post_id' => $post_id,
            'post_type' => $post_type,
            'prompt' => $prompt,
            'status' => 'processing',
            'created_at' => current_time('mysql'),
        ]);

        return $wpdb->insert_id;
    }

    /**
     * Log generation success
     */
    private function log_generation_success($log_id, $image_url) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cnp_afi_logs';

        $wpdb->update(
            $table_name,
            [
                'status' => 'success',
                'image_url' => $image_url,
            ],
            ['id' => $log_id]
        );
    }

    /**
     * Log generation error
     */
    private function log_generation_error($log_id, $error_message) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cnp_afi_logs';

        $wpdb->update(
            $table_name,
            [
                'status' => 'failed',
                'error_message' => $error_message,
            ],
            ['id' => $log_id]
        );
    }

    /**
     * Update generation stats
     */
    private function update_generation_stats() {
        $settings = get_option('cnp_afi_settings', []);
        $settings['last_generated'] = current_time('mysql');
        $settings['total_generated'] = ($settings['total_generated'] ?? 0) + 1;
        update_option('cnp_afi_settings', $settings);
    }

    /**
     * Get posts without featured images
     *
     * @param string $post_type Post type
     * @param int $limit Limit
     * @return array
     */
    public static function get_posts_without_featured_images($post_type = 'post', $limit = 100) {
        $args = [
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'meta_query' => [
                [
                    'key' => '_thumbnail_id',
                    'compare' => 'NOT EXISTS',
                ],
            ],
            'orderby' => 'date',
            'order' => 'DESC',
        ];

        $query = new WP_Query($args);

        $posts = [];
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $posts[] = [
                    'ID' => get_the_ID(),
                    'title' => get_the_title(),
                    'date' => get_the_date('Y-m-d H:i:s'),
                    'edit_link' => get_edit_post_link(get_the_ID()),
                ];
            }
        }
        wp_reset_postdata();

        return $posts;
    }
}
