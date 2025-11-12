<?php

namespace CNP\SEO\REST;



if (!defined('ABSPATH')) exit;



add_action('rest_api_init', function(){

  // Google News preflight validator
  register_rest_route('cnp-seo/v1', '/gn/preflight', [
    'methods' => 'GET',
    'permission_callback' => function() {
      return current_user_can('edit_posts'); // editors and above
    },
    'callback' => function($request) {
      $url = isset($request->get_params()['url']) ? esc_url_raw($request->get_params()['url']) : null;

      if (!$url) {
        return new \WP_Error('missing_url', 'URL parameter is required', ['status' => 400]);
      }

      // Check if GN is enabled
      $opts = get_option('cnp_seo_settings', []);
      if (empty($opts['gn_enabled'])) {
        return new \WP_Error('gn_disabled', 'Google News integration is disabled', ['status' => 403]);
      }

      if (function_exists('CNP\\SEO\\GN\\run_preflight_check')) {
        $result = \CNP\SEO\GN\run_preflight_check($url);
        return $result;
      } else {
        return new \WP_Error('function_missing', 'Preflight function not available', ['status' => 500]);
      }
    },
  ]);

  // Publisher Center sections helper
  register_rest_route('cnp-seo/v1', '/gn/sections', [
    'methods' => 'GET',
    'permission_callback' => function() {
      return current_user_can('manage_options');
    },
    'callback' => function() {
      $opts = get_option('cnp_seo_settings', []);
      if (empty($opts['gn_enabled'])) {
        return new \WP_Error('gn_disabled', 'Google News integration is disabled', ['status' => 403]);
      }

      $sections = [
        'sitewide' => [
          'name' => 'All Articles',
          'feed' => home_url('/feed/site.xml'),
        ],
        'news' => [
          'name' => 'Latest (48h)',
          'feed' => home_url('/feed/news.xml'),
        ],
        'categories' => [],
        'pillars' => [],
      ];

      // Get all categories
      $categories = get_categories(['hide_empty' => false]);
      foreach ($categories as $category) {
        $sections['categories'][] = [
          'slug' => $category->slug,
          'name' => $category->name,
          'feed' => home_url("/feed/category/{$category->slug}.xml"),
        ];
      }

      // Get pillar mappings
      $section_map = json_decode($opts['gn_section_map'] ?? '{}', true);
      if (!empty($section_map)) {
        $pillar_groups = [];
        foreach ($section_map as $cat_slug => $pillar_name) {
          if (!isset($pillar_groups[$pillar_name])) {
            $pillar_groups[$pillar_name] = [];
          }
          $pillar_groups[$pillar_name][] = $cat_slug;
        }

        foreach ($pillar_groups as $pillar_name => $cat_slugs) {
          // Use first category slug as pillar slug
          $pillar_slug = $cat_slugs[0];
          $sections['pillars'][] = [
            'slug' => $pillar_slug,
            'name' => $pillar_name,
            'feed' => home_url("/feed/pillar/{$pillar_slug}.xml"),
          ];
        }
      }

      return $sections;
    },
  ]);

  register_rest_route('cnp-seo/v1', '/health', [

    'methods' => 'GET',

    'permission_callback' => '__return_true',

    'callback' => function(){ return ['ok' => true, 'ver' => CNP_SEO_VER]; }

  ]);

  register_rest_route('cnp-seo/v1', '/preview-meta', [

    'methods' => 'GET',

    'permission_callback' => function() {
      // Allow if user can edit posts, or if no post_id specified
      $post_id = isset($_GET['post_id']) ? (int) $_GET['post_id'] : null;
      return !$post_id || current_user_can('edit_post', $post_id);
    },

    'callback' => function($request) {
      $post_id = isset($request->get_params()['post_id']) ? (int) $request->get_params()['post_id'] : null;

      // If post_id provided, use that context
      if ($post_id) {
        $post = get_post($post_id);
        if (!$post) {
          return new \WP_Error('post_not_found', 'Post not found', ['status' => 404]);
        }
        setup_postdata($post);
      }

      // Get computed meta values using direct function calls
      $title = function_exists('CNP\\SEO\\Meta\\get_seo_title') ?
        \CNP\SEO\Meta\get_seo_title($post_id) : wp_get_document_title();

      $description = function_exists('CNP\\SEO\\Meta\\get_seo_description') ?
        \CNP\SEO\Meta\get_seo_description($post_id) : '';

      $robots = function_exists('CNP\\SEO\\Meta\\get_seo_robots') ?
        \CNP\SEO\Meta\get_seo_robots($post_id) : 'index,follow';

      $canonical = function_exists('CNP\\SEO\\Meta\\get_seo_canonical') ?
        \CNP\SEO\Meta\get_seo_canonical($post_id) : '';

      $og_image = function_exists('CNP\\SEO\\Meta\\get_og_image') ?
        \CNP\SEO\Meta\get_og_image($post_id) : '';

      // Reset post data if we set it up
      if ($post_id) {
        wp_reset_postdata();
      }

      return [
        'title' => $title,
        'description' => $description,
        'robots' => $robots,
        'canonical' => $canonical,
        'og_image' => $og_image,
        'post_id' => $post_id,
      ];
    },

  ]);

  // Sitemap index debug endpoint
  register_rest_route('cnp-seo/v1', '/sitemap-index', [
    'methods' => 'GET',
    'permission_callback' => function() {
      return current_user_can('manage_options');
    },
    'callback' => function() {
      $opts = get_option('cnp_seo_settings', []);
      $index_data = [];

      if (!empty($opts['sitemaps_enabled'])) {
        $sitemap_types = [
          'posts' => 'sitemap_posts_enabled',
          'pages' => 'sitemap_pages_enabled',
          'categories' => 'sitemap_categories_enabled',
          'tags' => 'sitemap_tags_enabled',
          'authors' => 'sitemap_authors_enabled',
          'images' => 'sitemap_images_enabled',
        ];

        foreach ($sitemap_types as $type => $setting) {
          if (!empty($opts[$setting])) {
            $pages = \CNP\SEO\Sitemaps\get_sitemap_pages($type);
            $index_data[$type] = [
              'pages' => $pages,
              'total_pages' => count($pages),
              'urls_per_page' => $opts['sitemap_max_urls'] ?? 2000,
            ];
          }
        }

        if (!empty($opts['news_sitemap_enabled'])) {
          $index_data['news'] = [
            'pages' => [1],
            'total_pages' => 1,
            'urls_per_page' => null,
          ];
        }
      }

      return [
        'enabled' => !empty($opts['sitemaps_enabled']),
        'sitemaps' => $index_data,
        'base_url' => home_url('/cnp-sitemap.xml'),
      ];
    },
  ]);

  // Sitemap XML endpoints
  register_rest_route('cnp-seo/v1', '/sitemap/(?P<type>[a-z]+)(?:/(?P<page>\d+))?', [
    'methods' => 'GET',
    'permission_callback' => '__return_true',
    'callback' => function($request) {
      $type = $request->get_param('type');
      $page = (int) $request->get_param('page') ?: 1;

      // Check if sitemaps are enabled
      $opts = get_option('cnp_seo_settings', []);
      if (empty($opts['sitemaps_enabled'])) {
        return new \WP_Error('sitemaps_disabled', 'Sitemaps are disabled', ['status' => 404]);
      }

      // Set XML headers
      header('Content-Type: application/xml; charset=utf-8');
      header('Cache-Control: public, max-age=' . ($opts['sitemap_cache_ttl'] ?? 600));

      // Include sitemaps functions if not already loaded
      if (!function_exists('CNP\\SEO\\Sitemaps\\output_sitemap_index')) {
        require_once CNP_SEO_PATH . 'inc/sitemaps.php';
      }

      // Generate and output sitemap
      switch ($type) {
        case 'index':
          \CNP\SEO\Sitemaps\output_sitemap_index();
          break;
        case 'news':
          if (!empty($opts['news_sitemap_enabled'])) {
            \CNP\SEO\Sitemaps\output_news_sitemap();
          }
          break;
        default:
          \CNP\SEO\Sitemaps\output_child_sitemap($type, $page);
          break;
      }

      exit;
    },
  ]);

  // Sitemap stats debug endpoint
  register_rest_route('cnp-seo/v1', '/sitemap-stats', [
    'methods' => 'GET',
    'permission_callback' => function() {
      return current_user_can('manage_options');
    },
    'callback' => function() {
      global $wpdb;

      $stats = [
        'cache_status' => [],
        'url_counts' => [],
        'settings' => get_option('cnp_seo_settings', []),
      ];

      // Check cache status for different sitemap types
      $sitemap_types = ['posts', 'pages', 'categories', 'tags', 'authors', 'images', 'news', 'index'];
      foreach ($sitemap_types as $type) {
        $cache_key = \CNP\SEO\Sitemaps\get_sitemap_cache_key($type, 1);
        $cached = get_transient($cache_key);
        $stats['cache_status'][$type] = $cached !== false;
      }

      // Get URL counts
      $stats['url_counts'] = [
        'posts' => \CNP\SEO\Sitemaps\count_posts_urls(),
        'pages' => \CNP\SEO\Sitemaps\count_pages_urls(),
        'categories' => \CNP\SEO\Sitemaps\count_categories_urls(),
        'tags' => \CNP\SEO\Sitemaps\count_tags_urls(),
        'authors' => \CNP\SEO\Sitemaps\count_authors_urls(),
        'images' => \CNP\SEO\Sitemaps\count_images_urls(),
      ];

      return $stats;
    },
  ]);

  // Link policy preview endpoint
  register_rest_route('cnp-seo/v1', '/link-policy/preview', [
    'methods' => 'GET',
    'permission_callback' => function() {
      return current_user_can('manage_options');
    },
    'callback' => function($request) {
      $post_id = isset($request->get_params()['post_id']) ? (int) $request->get_params()['post_id'] : null;

      if (!$post_id) {
        return new \WP_Error('missing_post_id', 'Post ID is required', ['status' => 400]);
      }

      $post = get_post($post_id);
      if (!$post) {
        return new \WP_Error('post_not_found', 'Post not found', ['status' => 404]);
      }

      $content = $post->post_content;
      $excerpt = $post->post_excerpt;

      // Process content to find links
      $preview_data = \CNP\SEO\Links\preview_link_policy($content . ' ' . $excerpt, $post_id);

      return [
        'post_id' => $post_id,
        'post_title' => $post->post_title,
        'links' => $preview_data,
      ];
    },
  ]);

  // Redirects test endpoint
  register_rest_route('cnp-seo/v1', '/redirects/test', [
    'methods' => 'POST',
    'permission_callback' => function() {
      return current_user_can('manage_options');
    },
    'callback' => function($request) {
      $params = $request->get_json_params();
      $path = isset($params['path']) ? $params['path'] : null;

      if (!$path) {
        return new \WP_Error('missing_path', 'Path is required', ['status' => 400]);
      }

      // Normalize path
      $path = '/' . ltrim($path, '/');

      $result = \CNP\SEO\Links\test_redirect_path($path);

      return $result;
    },
  ]);

});
