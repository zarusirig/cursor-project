<?php
/**
 * Plugin Name: CNP Auto Featured Images
 * Plugin URI: https://cnpnews.net
 * Description: Automatically generate featured images for posts and pages using FAL AI API. Bulk generate or select individual posts.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Author: CNP News
 * Author URI: https://cnpnews.net
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cnp-auto-featured-images
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Plugin constants
define('CNP_AFI_VERSION', '1.0.0');
define('CNP_AFI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CNP_AFI_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CNP_AFI_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class CNP_Auto_Featured_Images {

    /**
     * Instance of this class
     */
    private static $instance = null;

    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Load required files
     */
    private function load_dependencies() {
        require_once CNP_AFI_PLUGIN_DIR . 'includes/class-fal-ai-api.php';
        require_once CNP_AFI_PLUGIN_DIR . 'includes/class-image-generator.php';
        require_once CNP_AFI_PLUGIN_DIR . 'includes/class-admin-dashboard.php';
        require_once CNP_AFI_PLUGIN_DIR . 'includes/class-ajax-handlers.php';
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);

        // Admin hooks
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);

        // AJAX handlers
        add_action('wp_ajax_cnp_afi_validate_api_key', ['CNP_AFI_Ajax_Handlers', 'validate_api_key']);
        add_action('wp_ajax_cnp_afi_save_settings', ['CNP_AFI_Ajax_Handlers', 'save_settings']);
        add_action('wp_ajax_cnp_afi_generate_single', ['CNP_AFI_Ajax_Handlers', 'generate_single_image']);
        add_action('wp_ajax_cnp_afi_bulk_generate', ['CNP_AFI_Ajax_Handlers', 'bulk_generate_images']);
        add_action('wp_ajax_cnp_afi_get_posts_without_images', ['CNP_AFI_Ajax_Handlers', 'get_posts_without_images']);

        // Auto-generate on post publish (if enabled)
        add_action('publish_post', [$this, 'auto_generate_on_publish'], 10, 2);
        add_action('publish_page', [$this, 'auto_generate_on_publish'], 10, 2);
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Create default options
        $default_options = [
            'api_key' => '',
            'api_key_valid' => false,
            'auto_generate_on_publish' => false,
            'image_model' => 'fal-ai/flux/schnell',
            'image_size' => 'landscape_16_9', // 1024x576
            'image_style' => 'professional news photo',
            'prompt_template' => 'A professional editorial image representing: {title}. {style}',
            'max_retries' => 3,
            'timeout' => 60,
            'last_generated' => '',
            'total_generated' => 0,
        ];

        add_option('cnp_afi_settings', $default_options);

        // Create custom database table for generation logs (optional)
        $this->create_logs_table();
    }

    /**
     * Create logs table
     */
    private function create_logs_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cnp_afi_logs';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            post_type varchar(20) NOT NULL,
            prompt text NOT NULL,
            status varchar(20) NOT NULL,
            image_url text,
            error_message text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clean up scheduled events if any
        wp_clear_scheduled_hook('cnp_afi_cleanup_old_logs');
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Auto Featured Images', 'cnp-auto-featured-images'),
            __('Featured Images AI', 'cnp-auto-featured-images'),
            'manage_options',
            'cnp-auto-featured-images',
            ['CNP_AFI_Admin_Dashboard', 'render_dashboard'],
            'dashicons-format-image',
            30
        );

        add_submenu_page(
            'cnp-auto-featured-images',
            __('Settings', 'cnp-auto-featured-images'),
            __('Settings', 'cnp-auto-featured-images'),
            'manage_options',
            'cnp-auto-featured-images',
            ['CNP_AFI_Admin_Dashboard', 'render_dashboard']
        );

        add_submenu_page(
            'cnp-auto-featured-images',
            __('Bulk Generate', 'cnp-auto-featured-images'),
            __('Bulk Generate', 'cnp-auto-featured-images'),
            'manage_options',
            'cnp-afi-bulk-generate',
            ['CNP_AFI_Admin_Dashboard', 'render_bulk_generate']
        );

        add_submenu_page(
            'cnp-auto-featured-images',
            __('Generation Logs', 'cnp-auto-featured-images'),
            __('Logs', 'cnp-auto-featured-images'),
            'manage_options',
            'cnp-afi-logs',
            ['CNP_AFI_Admin_Dashboard', 'render_logs']
        );
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'cnp-auto-featured-images') === false &&
            strpos($hook, 'cnp-afi') === false) {
            return;
        }

        // Styles
        wp_enqueue_style(
            'cnp-afi-admin',
            CNP_AFI_PLUGIN_URL . 'assets/css/admin.css',
            [],
            CNP_AFI_VERSION
        );

        // Scripts
        wp_enqueue_script(
            'cnp-afi-admin',
            CNP_AFI_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            CNP_AFI_VERSION,
            true
        );

        // Localize script
        wp_localize_script('cnp-afi-admin', 'cnpAfiData', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cnp_afi_nonce'),
            'strings' => [
                'validating' => __('Validating API key...', 'cnp-auto-featured-images'),
                'valid' => __('API key is valid!', 'cnp-auto-featured-images'),
                'invalid' => __('API key is invalid. Please check and try again.', 'cnp-auto-featured-images'),
                'generating' => __('Generating image...', 'cnp-auto-featured-images'),
                'success' => __('Image generated successfully!', 'cnp-auto-featured-images'),
                'error' => __('Failed to generate image. Please try again.', 'cnp-auto-featured-images'),
                'bulkProcessing' => __('Processing... {current} of {total}', 'cnp-auto-featured-images'),
                'bulkComplete' => __('Bulk generation complete! Generated {success} images, {failed} failed.', 'cnp-auto-featured-images'),
                'confirmBulk' => __('Generate featured images for {count} posts? This may take several minutes.', 'cnp-auto-featured-images'),
            ],
        ]);
    }

    /**
     * Auto-generate featured image on post publish
     */
    public function auto_generate_on_publish($post_id, $post) {
        // Get settings
        $settings = get_option('cnp_afi_settings', []);

        // Check if auto-generate is enabled
        if (empty($settings['auto_generate_on_publish'])) {
            return;
        }

        // Check if API key is valid
        if (empty($settings['api_key']) || empty($settings['api_key_valid'])) {
            return;
        }

        // Check if post already has featured image
        if (has_post_thumbnail($post_id)) {
            return;
        }

        // Generate and set featured image
        try {
            $generator = new CNP_AFI_Image_Generator();
            $generator->generate_and_attach($post_id);
        } catch (Exception $e) {
            // Log error
            error_log('CNP AFI Auto-generate error: ' . $e->getMessage());
        }
    }
}

// Initialize plugin
function cnp_auto_featured_images_init() {
    return CNP_Auto_Featured_Images::get_instance();
}

// Start the plugin
cnp_auto_featured_images_init();
