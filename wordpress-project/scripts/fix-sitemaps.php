<?php
/**
 * Sitemap Fix Script
 *
 * This script updates the CNP SEO plugin settings to enable sitemaps
 * and flushes rewrite rules so that sitemap URLs work properly.
 *
 * Usage:
 * 1. Place this file in /wp-content/
 * 2. Visit: https://your-site.com/wp-content/fix-sitemaps.php
 * 3. Delete this file after running
 *
 * OR run via WP-CLI:
 * wp eval-file fix-sitemaps.php
 */

// Load WordPress
if (!defined('ABSPATH')) {
    require_once(dirname(__FILE__) . '/../../../wp-load.php');
}

// Security check
if (!current_user_can('manage_options') && php_sapi_name() !== 'cli') {
    die('Unauthorized access');
}

echo "<h1>CNP SEO Sitemap Fix</h1>\n";
echo "<pre>\n";

// Get current settings
$opts = get_option('cnp_seo_settings', []);
$updated = false;

echo "Current settings status:\n";
echo "- sitemaps_enabled: " . (isset($opts['sitemaps_enabled']) ? ($opts['sitemaps_enabled'] ? 'YES' : 'NO') : 'NOT SET') . "\n";
echo "- news_sitemap_enabled: " . (isset($opts['news_sitemap_enabled']) ? ($opts['news_sitemap_enabled'] ? 'YES' : 'NO') : 'NOT SET') . "\n\n";

// Add missing sitemap settings
if (!isset($opts['sitemaps_enabled'])) {
    echo "Adding missing sitemap settings...\n";

    $opts['sitemaps_enabled'] = 1;
    $opts['sitemap_posts_enabled'] = 1;
    $opts['sitemap_pages_enabled'] = 1;
    $opts['sitemap_categories_enabled'] = 1;
    $opts['sitemap_tags_enabled'] = 1;
    $opts['sitemap_authors_enabled'] = 1;
    $opts['sitemap_images_enabled'] = 0;
    $opts['sitemap_max_urls'] = 2000;
    $opts['sitemap_cache_ttl'] = 600;
    $opts['sitemap_excluded_paths'] = '';
    $opts['sitemap_excluded_post_types'] = [];
    $opts['sitemap_excluded_taxonomies'] = [];

    $updated = true;
    echo "✓ Sitemap settings added\n";
}

// Add missing news sitemap settings
if (!isset($opts['sitemap_news_window_hours'])) {
    echo "Adding missing news sitemap settings...\n";

    $opts['sitemap_news_window_hours'] = 48;
    $opts['news_publication_name'] = get_bloginfo('name');
    $opts['news_publication_lang'] = 'en';

    $updated = true;
    echo "✓ News sitemap settings added\n";
}

// Ensure sitemaps are enabled
if (empty($opts['sitemaps_enabled'])) {
    $opts['sitemaps_enabled'] = 1;
    $updated = true;
    echo "✓ Sitemaps enabled\n";
}

if (empty($opts['news_sitemap_enabled'])) {
    $opts['news_sitemap_enabled'] = 1;
    $updated = true;
    echo "✓ News sitemap enabled\n";
}

// Update options if changed
if ($updated) {
    update_option('cnp_seo_settings', $opts);
    echo "\n✓ Settings updated successfully\n";
} else {
    echo "\n✓ All settings already correct\n";
}

// Flush rewrite rules
echo "\nFlushing rewrite rules...\n";
flush_rewrite_rules();
echo "✓ Rewrite rules flushed\n";

// Test sitemap URLs
echo "\n" . str_repeat('=', 60) . "\n";
echo "TESTING SITEMAP URLS\n";
echo str_repeat('=', 60) . "\n\n";

$test_urls = [
    'Main Sitemap' => home_url('/sitemap.xml'),
    'News Sitemap' => home_url('/news-sitemap.xml'),
    'REST API - Index' => rest_url('cnp-seo/v1/sitemap/index'),
    'REST API - News' => rest_url('cnp-seo/v1/sitemap/news'),
];

foreach ($test_urls as $label => $url) {
    echo "$label:\n";
    echo "  URL: $url\n";

    // Try to fetch the URL
    $response = wp_remote_get($url, [
        'timeout' => 10,
        'sslverify' => false,
    ]);

    if (is_wp_error($response)) {
        echo "  Status: ❌ ERROR - " . $response->get_error_message() . "\n";
    } else {
        $code = wp_remote_retrieve_response_code($response);
        $content_type = wp_remote_retrieve_header($response, 'content-type');
        $body = wp_remote_retrieve_body($response);

        echo "  Status: " . ($code === 200 ? '✓' : '❌') . " HTTP $code\n";
        echo "  Content-Type: $content_type\n";

        if ($code === 200 && strpos($body, '<?xml') === 0) {
            // Count URLs in sitemap
            $url_count = substr_count($body, '<loc>');
            echo "  URLs: $url_count\n";
            echo "  Result: ✓ WORKING\n";
        } elseif ($code === 301 || $code === 302) {
            $location = wp_remote_retrieve_header($response, 'location');
            echo "  Redirect: $location\n";
            echo "  Result: ⚠ REDIRECT (may need to follow)\n";
        } else {
            echo "  Result: ❌ NOT WORKING\n";
            if ($code === 404) {
                echo "  Issue: 404 Not Found - rewrite rules may not be active\n";
            }
        }
    }
    echo "\n";
}

// Count content for sitemaps
echo str_repeat('=', 60) . "\n";
echo "CONTENT COUNTS\n";
echo str_repeat('=', 60) . "\n\n";

$post_count = wp_count_posts('post');
$page_count = wp_count_posts('page');
$cat_count = wp_count_terms('category', ['hide_empty' => true]);
$tag_count = wp_count_terms('post_tag', ['hide_empty' => true]);

echo "Posts (published): " . $post_count->publish . "\n";
echo "Pages (published): " . $page_count->publish . "\n";
echo "Categories: " . $cat_count . "\n";
echo "Tags: " . $tag_count . "\n";

// News sitemap window
$window_hours = $opts['sitemap_news_window_hours'] ?? 48;
$cutoff_time = gmdate('Y-m-d H:i:s', strtotime("-{$window_hours} hours"));
$news_posts = get_posts([
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'date_query' => [
        [
            'column' => 'post_date_gmt',
            'after' => $cutoff_time,
            'inclusive' => true,
        ],
    ],
]);

echo "News sitemap posts (last {$window_hours}h): " . count($news_posts) . "\n";

// Recommendations
echo "\n" . str_repeat('=', 60) . "\n";
echo "RECOMMENDATIONS\n";
echo str_repeat('=', 60) . "\n\n";

if ($post_count->publish < 5) {
    echo "⚠ You have less than 5 published posts. Sitemaps will be minimal.\n";
    echo "  Recommendation: Publish at least 20 posts for meaningful sitemaps\n\n";
}

if (count($news_posts) < 1) {
    echo "⚠ No posts in news sitemap window (last {$window_hours} hours)\n";
    echo "  Recommendation: Publish recent content or increase window to 72 hours\n\n";
}

if ($cat_count < 1) {
    echo "⚠ No categories found\n";
    echo "  Recommendation: Create and assign categories to posts\n\n";
}

echo "✓ Sitemap fix script completed!\n\n";

echo "Next steps:\n";
echo "1. Visit " . home_url('/sitemap.xml') . " to verify main sitemap\n";
echo "2. Visit " . home_url('/news-sitemap.xml') . " to verify news sitemap\n";
echo "3. Submit sitemaps to Google Search Console\n";
echo "4. Delete this script file for security\n\n";

echo "</pre>\n";

// If running via CLI, return success
if (php_sapi_name() === 'cli') {
    exit(0);
}
