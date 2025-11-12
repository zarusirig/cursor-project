<?php
/**
 * CNP News Theme Functions
 * 
 * This file contains the main theme setup, enqueuing of assets,
 * and custom functionality for the CNP News WordPress theme.
 *
 * @package CNP_News
 * @version 1.0.0
 * @author CNP News Development Team
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Define theme constants
 */
define('CNP_THEME_VERSION', '1.0.0');
define('CNP_THEME_DIR', get_template_directory());
define('CNP_THEME_URI', get_template_directory_uri());
define('CNP_THEME_ASSETS', CNP_THEME_URI . '/assets');

/**
 * Theme Setup
 * 
 * Sets up theme defaults and registers support for various WordPress features.
 */
function cnp_news_setup() {
    // Make theme available for translation
    load_theme_textdomain('cnp-news', CNP_THEME_DIR . '/languages');
    
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');
    
    // Let WordPress manage the document title
    add_theme_support('title-tag');
    
    // Enable support for Post Thumbnails on posts and pages
    add_theme_support('post-thumbnails');
    
    // Set default thumbnail size
    set_post_thumbnail_size(1200, 630, true);
    
    // Add additional image sizes for CNP News
    add_image_size('cnp-featured-large', 1200, 675, true);   // 16:9 for hero images
    add_image_size('cnp-featured-medium', 768, 576, true);   // 4:3 for standard cards
    add_image_size('cnp-featured-small', 300, 300, true);    // 1:1 for list items
    add_image_size('cnp-og-image', 1200, 630, true);         // Open Graph/Twitter
    add_image_size('cnp-discover', 1200, 675, true);         // Google Discover
    
    // Add theme support for selective refresh for widgets
    add_theme_support('customize-selective-refresh-widgets');
    
    // Add support for core custom logo
    add_theme_support('custom-logo', array(
        'height'      => 64,
        'width'       => 200,
        'flex-width'  => true,
        'flex-height' => true,
        'header-text' => array('site-title', 'site-description'),
    ));
    
    // Add support for HTML5 markup
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));
    
    // Add theme support for custom background
    add_theme_support('custom-background', array(
        'default-color' => 'ffffff',
    ));
    
    // Add support for responsive embedded content
    add_theme_support('responsive-embeds');
    
    // Add support for experimental link color control
    add_theme_support('experimental-link-color');
    
    // Add support for post formats (if needed)
    add_theme_support('post-formats', array(
        'video',
        'audio',
        'gallery',
        'quote',
    ));
    
    // Add support for editor styles
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor-style.css');
    
    // Add support for wide and full alignments
    add_theme_support('align-wide');
    
    // Add support for block editor color palette
    add_theme_support('editor-color-palette', array(
        array(
            'name'  => __('Background', 'cnp-news'),
            'slug'  => 'background',
            'color' => '#ffffff',
        ),
        array(
            'name'  => __('Surface', 'cnp-news'),
            'slug'  => 'surface',
            'color' => '#f8fafc',
        ),
        array(
            'name'  => __('Foreground', 'cnp-news'),
            'slug'  => 'foreground',
            'color' => '#0b1220',
        ),
        array(
            'name'  => __('Primary', 'cnp-news'),
            'slug'  => 'primary',
            'color' => '#1d4ed8',
        ),
        array(
            'name'  => __('Accent', 'cnp-news'),
            'slug'  => 'accent',
            'color' => '#10b981',
        ),
    ));
    
    // Disable custom colors in favor of our palette
    add_theme_support('disable-custom-colors');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary'   => __('Primary Navigation', 'cnp-news'),
        'footer'    => __('Footer Navigation', 'cnp-news'),
        'social'    => __('Social Links', 'cnp-news'),
    ));
}
add_action('after_setup_theme', 'cnp_news_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 */
function cnp_news_content_width() {
    $GLOBALS['content_width'] = apply_filters('cnp_news_content_width', 768);
}
add_action('after_setup_theme', 'cnp_news_content_width', 0);

/**
 * Enqueue scripts and styles
 */
function cnp_news_scripts() {
    // Enqueue Google Fonts - Newsreader (headlines) + Inter (body)
    wp_enqueue_style(
        'cnp-google-fonts',
        'https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,400;6..72,600;6..72,700&family=Inter:wght@400;500;600;700&display=swap',
        array(),
        null
    );
    
    // Enqueue theme stylesheet
    wp_enqueue_style(
        'cnp-news-style',
        get_stylesheet_uri(),
        array('cnp-google-fonts'),
        CNP_THEME_VERSION
    );
    
    // Enqueue custom CSS for additional styling
    wp_enqueue_style(
        'cnp-news-custom',
        CNP_THEME_ASSETS . '/css/custom.css',
        array('cnp-news-style'),
        CNP_THEME_VERSION
    );
    
    // Enqueue Google Fonts (preload for performance)
    wp_enqueue_style(
        'cnp-news-fonts',
        cnp_news_get_google_fonts_url(),
        array(),
        CNP_THEME_VERSION
    );
    
    // Enqueue main JavaScript file
    wp_enqueue_script(
        'cnp-news-script',
        CNP_THEME_ASSETS . '/js/main.js',
        array(),
        CNP_THEME_VERSION,
        true
    );
    
    // Localize script for AJAX calls
    wp_localize_script('cnp-news-script', 'cnp_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('cnp_nonce'),
    ));
    
    // Enqueue comment reply script on single posts with comments
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'cnp_news_scripts');

/**
 * Get Google Fonts URL
 */
function cnp_news_get_google_fonts_url() {
    $fonts = array();
    
    // Newsreader for headlines
    $fonts[] = 'Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800';
    
    // Inter for body text
    $fonts[] = 'Inter:wght@100..900';
    
    $fonts_url = add_query_arg(array(
        'family' => implode('&family=', $fonts),
        'display' => 'swap',
    ), 'https://fonts.googleapis.com/css2');
    
    return $fonts_url;
}

/**
 * Prevent FOUC by immediately applying theme preference
 */
function cnp_news_prevent_fouc() {
    ?>
    <script>
        (function() {
            'use strict';

            // Immediately apply theme to prevent FOUC
            var savedTheme = localStorage.getItem('cnp-theme');
            var systemPrefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
            } else if (systemPrefersDark) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
    <?php
}
add_action('wp_head', 'cnp_news_prevent_fouc', 0);

/**
 * Preload critical resources for performance
 */
function cnp_news_preload_resources() {
    // Preload Google Fonts
    $fonts_url = cnp_news_get_google_fonts_url();
    echo '<link rel="preload" href="' . esc_url($fonts_url) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
    echo '<noscript><link rel="stylesheet" href="' . esc_url($fonts_url) . '"></noscript>' . "\n";

    // Preload critical CSS (if using separate critical CSS file)
    echo '<link rel="preload" href="' . CNP_THEME_ASSETS . '/css/critical.css" as="style">' . "\n";
}
add_action('wp_head', 'cnp_news_preload_resources', 1);

/**
 * Add custom body classes and attributes
 */
function cnp_news_body_classes($classes) {
    // Add class for Gutenberg support
    if (function_exists('has_blocks') && has_blocks()) {
        $classes[] = 'has-blocks';
    }

    // Add class for posts with featured images
    if (is_singular() && has_post_thumbnail()) {
        $classes[] = 'has-featured-image';
    }

    // Add class for pages without sidebar
    if (is_page() || is_single()) {
        $classes[] = 'no-sidebar';
    }

    // Add class for single posts to enable subtle header styling
    if (is_single() && get_post_type() === 'post') {
        $classes[] = 'single-post-article';
    }

    // Add performance optimization class
    $classes[] = 'cnp-optimized';

    return $classes;
}
add_filter('body_class', 'cnp_news_body_classes');

/**
 * Add data-post-id to body for single posts
 */
function cnp_news_add_body_data_attributes() {
    if (is_single() && get_post_type() === 'post') {
        echo '<script>document.body.setAttribute("data-post-id", "' . esc_attr(get_the_ID()) . '");</script>' . "\n";
    }
}
add_action('wp_footer', 'cnp_news_add_body_data_attributes');

/**
 * Custom excerpt length
 */
function cnp_news_excerpt_length($length) {
    return 25; // words
}
add_filter('excerpt_length', 'cnp_news_excerpt_length');

/**
 * Custom excerpt more text
 */
function cnp_news_excerpt_more($more) {
    return '&hellip;';
}
add_filter('excerpt_more', 'cnp_news_excerpt_more');

/**
 * Add custom user profile fields for E-E-A-T
 */
function cnp_news_add_user_profile_fields($user) {
    ?>
    <h3><?php _e('Author Information', 'cnp-news'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="job_title"><?php _e('Job Title', 'cnp-news'); ?></label></th>
            <td>
                <input type="text" name="job_title" id="job_title" value="<?php echo esc_attr(get_the_author_meta('job_title', $user->ID)); ?>" class="regular-text" />
                <p class="description"><?php _e('Author\'s job title or role (e.g., Senior Technology Reporter)', 'cnp-news'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="linkedin"><?php _e('LinkedIn Profile', 'cnp-news'); ?></label></th>
            <td>
                <input type="url" name="linkedin" id="linkedin" value="<?php echo esc_attr(get_the_author_meta('linkedin', $user->ID)); ?>" class="regular-text" />
                <p class="description"><?php _e('Full LinkedIn profile URL', 'cnp-news'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'cnp_news_add_user_profile_fields');
add_action('edit_user_profile', 'cnp_news_add_user_profile_fields');

/**
 * Save custom user profile fields
 */
function cnp_news_save_user_profile_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    update_user_meta($user_id, 'job_title', sanitize_text_field($_POST['job_title']));
    update_user_meta($user_id, 'linkedin', esc_url_raw($_POST['linkedin']));
}
add_action('personal_options_update', 'cnp_news_save_user_profile_fields');
add_action('edit_user_profile_update', 'cnp_news_save_user_profile_fields');

/**
 * Add custom post meta
 */
function cnp_news_add_custom_meta() {
    // Add reading time meta
    add_meta_box(
        'cnp-reading-time',
        __('Reading Time', 'cnp-news'),
        'cnp_news_reading_time_callback',
        'post',
        'side',
        'default'
    );
    
    // Add E-E-A-T meta box
    add_meta_box(
        'cnp-eeat-meta',
        __('E-E-A-T Information', 'cnp-news'),
        'cnp_news_eeat_callback',
        'post',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'cnp_news_add_custom_meta');

/**
 * Reading time callback
 */
function cnp_news_reading_time_callback($post) {
    $reading_time = get_post_meta($post->ID, '_cnp_reading_time', true);
    echo '<p>';
    echo '<label for="cnp_reading_time">' . __('Estimated reading time (minutes):', 'cnp-news') . '</label><br>';
    echo '<input type="number" id="cnp_reading_time" name="cnp_reading_time" value="' . esc_attr($reading_time) . '" min="1" max="60">';
    echo '</p>';
    echo '<p class="description">' . __('Auto-calculated based on word count if left empty.', 'cnp-news') . '</p>';
}

/**
 * E-E-A-T callback
 */
function cnp_news_eeat_callback($post) {
    wp_nonce_field('cnp_eeat_nonce', 'cnp_eeat_nonce');
    
    $sources = get_post_meta($post->ID, '_cnp_sources', true);
    $ai_assistance = get_post_meta($post->ID, '_cnp_ai_assistance', true);
    $fact_checked = get_post_meta($post->ID, '_cnp_fact_checked', true);
    
    echo '<table class="form-table">';
    
    // Sources
    echo '<tr>';
    echo '<th scope="row"><label for="cnp_sources">' . __('Sources', 'cnp-news') . '</label></th>';
    echo '<td>';
    echo '<textarea id="cnp_sources" name="cnp_sources" rows="3" cols="50" class="large-text">' . esc_textarea($sources) . '</textarea>';
    echo '<p class="description">' . __('List primary sources (one per line)', 'cnp-news') . '</p>';
    echo '</td>';
    echo '</tr>';
    
    // AI Assistance
    echo '<tr>';
    echo '<th scope="row">' . __('AI Assistance', 'cnp-news') . '</th>';
    echo '<td>';
    echo '<label><input type="checkbox" name="cnp_ai_assistance" value="1"' . checked($ai_assistance, 1, false) . '> ';
    echo __('This article received AI assistance', 'cnp-news') . '</label>';
    echo '</td>';
    echo '</tr>';
    
    // Fact Checked
    echo '<tr>';
    echo '<th scope="row">' . __('Fact Checked', 'cnp-news') . '</th>';
    echo '<td>';
    echo '<label><input type="checkbox" name="cnp_fact_checked" value="1"' . checked($fact_checked, 1, false) . '> ';
    echo __('This article has been fact-checked', 'cnp-news') . '</label>';
    echo '</td>';
    echo '</tr>';
    
    echo '</table>';
}

/**
 * Save custom post meta
 */
function cnp_news_save_post_meta($post_id) {
    // Check if nonce is valid
    if (!isset($_POST['cnp_eeat_nonce']) || !wp_verify_nonce($_POST['cnp_eeat_nonce'], 'cnp_eeat_nonce')) {
        return;
    }
    
    // Check if user has permission to edit post
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save reading time
    if (isset($_POST['cnp_reading_time'])) {
        update_post_meta($post_id, '_cnp_reading_time', intval($_POST['cnp_reading_time']));
    } else {
        // Auto-calculate reading time if not provided
        $content = get_post_field('post_content', $post_id);
        $word_count = str_word_count(strip_tags($content));
        $reading_time = ceil($word_count / 200); // 200 words per minute
        update_post_meta($post_id, '_cnp_reading_time', $reading_time);
    }
    
    // Save sources
    if (isset($_POST['cnp_sources'])) {
        update_post_meta($post_id, '_cnp_sources', sanitize_textarea_field($_POST['cnp_sources']));
    }
    
    // Save AI assistance flag
    update_post_meta($post_id, '_cnp_ai_assistance', isset($_POST['cnp_ai_assistance']) ? 1 : 0);
    
    // Save fact-checked flag
    update_post_meta($post_id, '_cnp_fact_checked', isset($_POST['cnp_fact_checked']) ? 1 : 0);
}
add_action('save_post', 'cnp_news_save_post_meta');

/**
 * Add structured data for articles
 */
function cnp_news_add_structured_data() {
    if (is_single() && 'post' === get_post_type()) {
        global $post;
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => get_the_title(),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author(),
                'url' => get_author_posts_url(get_the_author_meta('ID')),
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_site_icon_url(512),
                ),
            ),
            'mainEntityOfPage' => array(
                '@type' => 'WebPage',
                '@id' => get_permalink(),
            ),
        );
        
        // Add featured image if available
        if (has_post_thumbnail()) {
            $image_id = get_post_thumbnail_id();
            $image_url = wp_get_attachment_image_url($image_id, 'cnp-og-image');
            $schema['image'] = array($image_url);
        }
        
        // Add description
        if (has_excerpt()) {
            $schema['description'] = get_the_excerpt();
        }
        
        // Add word count and reading time
        $content = get_post_field('post_content', $post->ID);
        $word_count = str_word_count(strip_tags($content));
        $reading_time = get_post_meta($post->ID, '_cnp_reading_time', true) ?: ceil($word_count / 200);
        
        $schema['wordCount'] = $word_count;
        $schema['timeRequired'] = 'PT' . $reading_time . 'M';
        
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }
}
add_action('wp_head', 'cnp_news_add_structured_data');

/**
 * Add Open Graph and Twitter Card meta tags for social sharing
 */
function cnp_news_add_social_meta_tags() {
    if (!is_single() || get_post_type() !== 'post') {
        return;
    }

    $post_id = get_the_ID();
    $title = get_the_title();
    $excerpt = get_the_excerpt();
    $permalink = get_permalink();
    $site_name = get_bloginfo('name');

    // Basic Open Graph tags
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($excerpt) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($permalink) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '">' . "\n";
    echo '<meta property="og:type" content="article">' . "\n";

    // Twitter Card tags
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($excerpt) . '">' . "\n";

    // Featured image for social cards
    if (has_post_thumbnail($post_id)) {
        $og_image_url = get_the_post_thumbnail_url($post_id, 'cnp-og-image');
        if ($og_image_url) {
            echo '<meta property="og:image" content="' . esc_url($og_image_url) . '">' . "\n";
            echo '<meta property="og:image:width" content="1200">' . "\n";
            echo '<meta property="og:image:height" content="630">' . "\n";
            echo '<meta name="twitter:image" content="' . esc_url($og_image_url) . '">' . "\n";

            // Get image alt text
            $image_id = get_post_thumbnail_id($post_id);
            $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true);
            if ($alt_text) {
                echo '<meta property="og:image:alt" content="' . esc_attr($alt_text) . '">' . "\n";
            }
        }
    }

    // Article-specific Open Graph tags
    $author_name = get_the_author();
    if ($author_name) {
        echo '<meta property="article:author" content="' . esc_attr($author_name) . '">' . "\n";
    }

    $published_time = get_the_date('c');
    if ($published_time) {
        echo '<meta property="article:published_time" content="' . esc_attr($published_time) . '">' . "\n";
    }

    $modified_time = get_the_modified_date('c');
    if ($modified_time && $modified_time !== $published_time) {
        echo '<meta property="article:modified_time" content="' . esc_attr($modified_time) . '">' . "\n";
        echo '<meta property="og:updated_time" content="' . esc_attr($modified_time) . '">' . "\n";
    }

    // Article tags
    $tags = get_the_tags();
    if ($tags) {
        foreach ($tags as $tag) {
            echo '<meta property="article:tag" content="' . esc_attr($tag->name) . '">' . "\n";
        }
    }
}
add_action('wp_head', 'cnp_news_add_social_meta_tags', 5);

/**
 * Optimize images for performance
 */
function cnp_news_optimize_images($attr, $attachment, $size) {
    // Add loading="lazy" to non-LCP images
    if (!is_admin() && 'cnp-featured-large' !== $size) {
        $attr['loading'] = 'lazy';
    }
    
    // Add fetchpriority="high" to LCP images
    if ('cnp-featured-large' === $size && (is_home() || is_single())) {
        $attr['fetchpriority'] = 'high';
    }
    
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'cnp_news_optimize_images', 10, 3);

/**
 * Add security headers
 */
function cnp_news_security_headers() {
    if (!is_admin()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
add_action('send_headers', 'cnp_news_security_headers');

/**
 * Remove unnecessary WordPress features for performance
 */
function cnp_news_cleanup() {
    // Remove WordPress version from head
    remove_action('wp_head', 'wp_generator');

    // Remove RSD link
    remove_action('wp_head', 'rsd_link');

    // Remove Windows Live Writer link
    remove_action('wp_head', 'wlwmanifest_link');

    // Remove shortlink
    remove_action('wp_head', 'wp_shortlink_wp_head');

    // Remove feed links (if not using feeds)
    // remove_action('wp_head', 'feed_links_extra', 3);
    // remove_action('wp_head', 'feed_links', 2);

    // Disable emoji scripts (performance optimization)
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
}
add_action('init', 'cnp_news_cleanup');

/**
 * Hide WordPress admin notices on front-end for all users
 */
function cnp_news_hide_admin_notices() {
    if (!is_admin()) {
        remove_action('admin_notices', 'update_nag', 3);
        remove_action('admin_notices', 'maintenance_nag', 10);
        remove_action('admin_notices', 'site_admin_notice', 20);

        // Hide "Welcome to WordPress" and other core notices
        global $wp_filter;
        if (isset($wp_filter['admin_notices'])) {
            foreach ($wp_filter['admin_notices']->callbacks as $priority => $callbacks) {
                foreach ($callbacks as $callback) {
                    if (is_array($callback) && isset($callback['function'])) {
                        // Remove core welcome notices and other admin notices
                        if (strpos($callback['function'], 'wp_welcome_panel') !== false ||
                            strpos($callback['function'], 'welcome_panel') !== false ||
                            strpos($callback['function'], 'admin_notice') !== false) {
                            remove_action('admin_notices', $callback['function'], $priority);
                        }
                    }
                }
            }
        }
    }
}
add_action('admin_init', 'cnp_news_hide_admin_notices');

/**
 * Completely hide admin bar and notices for front-end users
 */
function cnp_news_disable_admin_bar() {
    if (!is_admin() && !current_user_can('administrator')) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'cnp_news_disable_admin_bar');

/**
 * Disable WordPress embeds (performance optimization)
 */
function cnp_news_disable_embeds() {
    // Remove embed script
    wp_deregister_script('wp-embed');
    
    // Remove embed rewrite rules
    add_filter('rewrite_rules_array', function($rules) {
        foreach ($rules as $rule => $rewrite) {
            if (false !== strpos($rewrite, 'embed=true')) {
                unset($rules[$rule]);
            }
        }
        return $rules;
    });
    
    // Remove embed query vars
    add_filter('query_vars', function($vars) {
        $vars = array_diff($vars, array('embed'));
        return $vars;
    });
}
add_action('init', 'cnp_news_disable_embeds', 9999);

/**
 * Add custom CSS classes to blocks
 */
function cnp_news_block_wrapper_render($block_content, $block) {
    // Add performance-optimized classes to image blocks
    if ('core/image' === $block['blockName']) {
        $block_content = str_replace('<img', '<img class="cnp-optimized-image"', $block_content);
    }
    
    return $block_content;
}
add_filter('render_block', 'cnp_news_block_wrapper_render', 10, 2);

/**
 * Register custom block patterns
 */
function cnp_news_register_block_patterns() {
    // Only register if block patterns are supported
    if (function_exists('register_block_pattern_category')) {
        // Register CNP News pattern category
        register_block_pattern_category('cnp-news', array(
            'label' => __('CNP News', 'cnp-news'),
        ));
        
        // Key Takeaways pattern
        register_block_pattern('cnp-news/key-takeaways', array(
            'title'       => __('Key Takeaways', 'cnp-news'),
            'description' => __('A callout box for article key takeaways', 'cnp-news'),
            'categories'  => array('cnp-news'),
            'content'     => '<!-- wp:group {"backgroundColor":"surface","className":"cnp-takeaways"} -->
            <div class="wp-block-group cnp-takeaways has-surface-background-color has-background">
                <!-- wp:heading {"level":3,"textColor":"primary"} -->
                <h3 class="has-primary-color has-text-color">Key Takeaways</h3>
                <!-- /wp:heading -->
                <!-- wp:list -->
                <ul><li>First key point about the article</li><li>Second important takeaway</li><li>Third crucial insight</li></ul>
                <!-- /wp:list -->
            </div>
            <!-- /wp:group -->',
        ));
        
        // Review Score pattern
        register_block_pattern('cnp-news/review-score', array(
            'title'       => __('Review Score', 'cnp-news'),
            'description' => __('A review score display', 'cnp-news'),
            'categories'  => array('cnp-news'),
            'content'     => '<!-- wp:group {"backgroundColor":"accent","textColor":"background","className":"cnp-review-score"} -->
            <div class="wp-block-group cnp-review-score has-background-color has-accent-background-color has-text-color has-background">
                <!-- wp:paragraph {"fontSize":"large"} -->
                <p class="has-large-font-size"><strong>Score: 8.5/10</strong> - Excellent</p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:group -->',
        ));
    }
}
add_action('init', 'cnp_news_register_block_patterns');

/**
 * Include additional theme files
 */
require_once CNP_THEME_DIR . '/inc/templates.php';
require_once CNP_THEME_DIR . '/inc/reviews.php';
require_once CNP_THEME_DIR . '/inc/analytics.php';

/**
 * Theme activation hook
 */
function cnp_news_activation() {
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Set default theme options
    set_theme_mod('cnp_performance_mode', true);
    set_theme_mod('cnp_lazy_loading', true);
    set_theme_mod('cnp_webp_support', true);
}
add_action('after_switch_theme', 'cnp_news_activation');

/**
 * AJAX handler for reading time
 */
function cnp_news_get_reading_time_ajax() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'cnp_nonce')) {
        wp_die('Security check failed');
    }

    $post_id = intval($_POST['post_id'] ?? 0);
    if (!$post_id) {
        wp_send_json_error('Invalid post ID');
    }

    $reading_time = cnp_get_reading_time($post_id);

    wp_send_json_success(array(
        'reading_time' => $reading_time
    ));
}
add_action('wp_ajax_get_reading_time', 'cnp_news_get_reading_time_ajax');
add_action('wp_ajax_nopriv_get_reading_time', 'cnp_news_get_reading_time_ajax');

/**
 * AJAX handler for dynamic content (tags, sources, author bio)
 */
function cnp_news_get_dynamic_content_ajax() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'cnp_nonce')) {
        wp_die('Security check failed');
    }

    $post_id = intval($_POST['post_id'] ?? 0);
    if (!$post_id) {
        wp_send_json_error('Invalid post ID');
    }

    $response = array(
        'success' => true,
        'tags_html' => '',
        'sources_html' => '',
        'author_bio' => '',
        'linkedin_html' => ''
    );

    // Generate tags HTML
    $tags = get_the_tags($post_id);
    if (!empty($tags)) {
        $response['tags_html'] = '<div class="wp-block-group cnp-article-tags">
            <h2 class="wp-block-heading cnp-section-heading has-foreground-color has-text-color has-sans-serif-font-family" style="font-size:0.875rem;font-weight:600;letter-spacing:0.05em;text-transform:uppercase">Tags</h2>
            <div class="wp-block-post-terms cnp-tag-list">' . get_the_tag_list('', ', ', '', $post_id) . '</div>
        </div>';
    }

    // Generate sources HTML
    $sources = get_post_meta($post_id, '_cnp_sources', true);
    if (!empty($sources)) {
        $source_lines = explode("\n", trim($sources));
        $sources_list = '<ul style="margin: 0; padding-left: 1.5rem;">';
        foreach ($source_lines as $source) {
            $source = trim($source);
            if (!empty($source)) {
                if (filter_var($source, FILTER_VALIDATE_URL)) {
                    $sources_list .= '<li><a href="' . esc_url($source) . '" target="_blank" rel="noopener noreferrer" style="color: var(--wp--preset--color--primary); text-decoration: none;">' . esc_html($source) . '</a></li>';
                } else {
                    $sources_list .= '<li>' . esc_html($source) . '</li>';
                }
            }
        }
        $sources_list .= '</ul>';

        $response['sources_html'] = '<div class="wp-block-group cnp-article-sources" style="border-top-color:var(--wp--preset--color--border);border-top-width:1px;margin-top:var(--wp--preset--spacing--4xl);padding-top:var(--wp--preset--spacing--2xl)">
            <div class="wp-block-group cnp-sources-section">
                <h2 class="wp-block-heading cnp-section-heading has-foreground-color has-text-color has-sans-serif-font-family" style="font-size:0.875rem;font-weight:600;letter-spacing:0.05em;text-transform:uppercase">Sources</h2>
                <div class="cnp-sources-list" style="font-size: 0.875rem; line-height: 1.5;">' . $sources_list . '</div>
            </div>
        </div>';
    }

    // Generate author bio
    $author_id = get_post_field('post_author', $post_id);
    $author_role = get_the_author_meta('job_title', $author_id) ?: 'Contributor';
    $author_bio = get_the_author_meta('description', $author_id);
    if (empty($author_bio)) {
        $author_bio = $author_role . ' covering technology and business news.';
    }
    $response['author_bio'] = '<p style="font-size: 0.875rem; line-height: 1.5; color: var(--wp--preset--color--muted); margin: 0.25rem 0;">' . esc_html($author_bio) . '</p>';

    // Generate LinkedIn link
    $linkedin = get_the_author_meta('linkedin', $author_id);
    if ($linkedin) {
        $response['linkedin_html'] = '<a href="' . esc_url($linkedin) . '" target="_blank" rel="noopener noreferrer" style="color: var(--wp--preset--color--primary); font-size: 0.875rem; text-decoration: none;">LinkedIn</a>';
    }

    wp_send_json_success($response);
}
add_action('wp_ajax_get_dynamic_content', 'cnp_news_get_dynamic_content_ajax');
add_action('wp_ajax_nopriv_get_dynamic_content', 'cnp_news_get_dynamic_content_ajax');

/**
 * Remove inline "Related:" text from post content
 */
function cnp_news_remove_inline_related($content) {
    if (is_single() && get_post_type() === 'post') {
        // Remove the inline related text
        $content = preg_replace('/<p[^>]*>\s*<strong[^>]*>Related:\s*<\/strong>\s*More articles in this category\.?\s*<\/p>/i', '', $content);
        $content = preg_replace('/<p[^>]*>\s*Related:\s*More articles in this category\.?\s*<\/p>/i', '', $content);
    }
    return $content;
}
add_filter('the_content', 'cnp_news_remove_inline_related', 1);

/**
 * Theme deactivation hook
 */
function cnp_news_deactivation() {
    // Flush rewrite rules
    flush_rewrite_rules();
}
add_action('switch_theme', 'cnp_news_deactivation');

// End of functions.php
