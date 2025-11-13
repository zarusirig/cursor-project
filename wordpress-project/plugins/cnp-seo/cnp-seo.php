<?php

/**

 * Plugin Name: CNP SEO

 * Description: First-party SEO for cnpnews — titles/meta, schema, sitemaps, canonical, link policy.

 * Author: CNP News

 * Version: 0.1.0

 * Requires at least: 6.6

 * Requires PHP: 8.0

 * Text Domain: cnp-seo

 */



if (!defined('ABSPATH')) exit;

define('CNP_SEO_PATH', plugin_dir_path(__FILE__));

define('CNP_SEO_URL', plugin_dir_url(__FILE__));

define('CNP_SEO_VER', '0.1.0');

// Disable WordPress core sitemaps to avoid conflicts
add_filter('wp_sitemaps_enabled', '__return_false');

require_once CNP_SEO_PATH . 'inc/bootstrap.php';



register_activation_hook(__FILE__, function(){

  add_option('cnp_seo_settings', [

    'site_title_format' => '%title% — %sitename%',

    'site_desc_default' => '',

    // Sitemap settings
    'sitemaps_enabled' => 1,
    'sitemap_posts_enabled' => 1,
    'sitemap_pages_enabled' => 1,
    'sitemap_categories_enabled' => 1,
    'sitemap_tags_enabled' => 1,
    'sitemap_authors_enabled' => 1,
    'sitemap_images_enabled' => 0,
    'sitemap_max_urls' => 2000,
    'sitemap_cache_ttl' => 600,
    'sitemap_excluded_paths' => '',
    'sitemap_excluded_post_types' => [],
    'sitemap_excluded_taxonomies' => [],

    // News sitemap settings
    'news_sitemap_enabled' => 1,
    'sitemap_news_window_hours' => 48,
    'news_publication_name' => get_bloginfo('name'),
    'news_publication_lang' => 'en',

    // Google News settings
    'gn_enabled' => 1,
    'gn_publication_name' => get_bloginfo('name'),
    'gn_publication_lang' => 'en',
    'gn_primary_country' => '',
    'gn_square_logo_url' => '',
    'gn_rect_logo_url' => '',
    'gn_include_full_content_in_feeds' => 1,
    'gn_feed_item_count' => 100,
    'gn_exclude_sponsored_from_news' => 1,
    'gn_section_map' => '{}',
    'gn_corrections_url' => '',
    'gn_support_email' => '',

  ]);

  // Flush rewrite rules to activate sitemap URLs
  flush_rewrite_rules();

});



register_deactivation_hook(__FILE__, function(){

  // keep options; nothing to clean now

});
