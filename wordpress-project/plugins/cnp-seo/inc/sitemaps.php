<?php

namespace CNP\SEO\Sitemaps;



if (!defined('ABSPATH')) exit;



// Disable WordPress core sitemaps to avoid conflicts
add_filter('wp_sitemaps_enabled', '__return_false');



// Register sitemap endpoints and redirects
add_action('init', function(){
  // Add rewrite rules with highest priority
  add_rewrite_rule('^sitemap\.xml/?$', 'index.php?cnp_sitemap=index', 'top');
  add_rewrite_rule('^sitemap-([a-z]+)-([0-9]+)\.xml/?$', 'index.php?cnp_sitemap=$matches[1]&cnp_sitemap_page=$matches[2]', 'top');
  add_rewrite_rule('^news-sitemap\.xml/?$', 'index.php?cnp_sitemap=news', 'top');
}, 1);

// Direct URL redirects using wp_loaded
add_action('wp_loaded', function(){
  if (!is_admin() && isset($_SERVER['REQUEST_URI'])) {
    $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    if ($path === 'sitemap.xml') {
      wp_redirect(rest_url('cnp-seo/v1/sitemap/index'), 301);
      exit;
    } elseif (preg_match('/^sitemap-([a-z]+)-([0-9]+)\.xml$/', $path, $matches)) {
      $type = $matches[1];
      $page = intval($matches[2]);
      wp_redirect(rest_url("cnp-seo/v1/sitemap/{$type}/{$page}"), 301);
      exit;
    } elseif ($path === 'news-sitemap.xml') {
      wp_redirect(rest_url('cnp-seo/v1/sitemap/news'), 301);
      exit;
    }
  }
}, 1);

// Register query vars
add_filter('query_vars', function($vars){
  $vars[] = 'cnp_sitemap';
  $vars[] = 'cnp_sitemap_page';
  return $vars;
});


// Flush rewrite rules on activation
register_activation_hook(CNP_SEO_PATH . '../cnp-seo.php', 'flush_rewrite_rules');
register_deactivation_hook(CNP_SEO_PATH . '../cnp-seo.php', 'flush_rewrite_rules');

// Cache management
function get_sitemap_cache_key($type, $page = 1) {
  return "cnp_sitemap_{$type}_{$page}";
}

function get_cached_sitemap($type, $page = 1) {
  $key = get_sitemap_cache_key($type, $page);
  return get_transient($key);
}

function set_cached_sitemap($type, $page = 1, $content) {
  $opts = get_option('cnp_seo_settings', []);
  $ttl = $opts['sitemap_cache_ttl'] ?? 600;

  $key = get_sitemap_cache_key($type, $page);
  set_transient($key, $content, $ttl);
}

function clear_sitemap_cache($type = null) {
  global $wpdb;

  $pattern = 'cnp_sitemap';
  if ($type) {
    $pattern .= "_{$type}";
  }

  $wpdb->query($wpdb->prepare(
    "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
    '_transient_' . $pattern . '%'
  ));

  $wpdb->query($wpdb->prepare(
    "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
    '_transient_timeout_' . $pattern . '%'
  ));
}

// URL exclusion helpers
function is_url_excluded($url) {
  $opts = get_option('cnp_seo_settings', []);

  // Check excluded paths
  if (!empty($opts['sitemap_excluded_paths'])) {
    $excluded_paths = array_map('trim', explode("\n", $opts['sitemap_excluded_paths']));
    $parsed_url = parse_url($url);

    if (isset($parsed_url['path'])) {
      foreach ($excluded_paths as $path) {
        $path = trim($path, '/');
        if ($path && strpos($parsed_url['path'], '/' . $path) === 0) {
          return true;
        }
      }
    }
  }

  return false;
}

function is_post_excluded($post_id) {
  // Check noindex meta
  if (get_post_meta($post_id, '_cnp_seo_noindex', true)) {
    return true;
  }

  $opts = get_option('cnp_seo_settings', []);
  $post = get_post($post_id);

  // Check excluded post types
  if (in_array($post->post_type, $opts['sitemap_excluded_post_types'] ?? [])) {
    return true;
  }

  return false;
}

function is_term_excluded($term_id, $taxonomy) {
  $opts = get_option('cnp_seo_settings', []);

  // Check excluded taxonomies
  if (in_array($taxonomy, $opts['sitemap_excluded_taxonomies'] ?? [])) {
    return true;
  }

  return false;
}

// Main sitemap index
function output_sitemap_index() {
  $opts = get_option('cnp_seo_settings', []);

  // Start XML
  $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
  $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

  $sitemaps = [];

  // Posts
  if (!empty($opts['sitemap_posts_enabled'])) {
    $pages = get_sitemap_pages('posts');
    foreach ($pages as $page) {
      $sitemaps[] = [
        'loc' => home_url("/sitemap-posts-{$page}.xml"),
        'lastmod' => get_sitemap_lastmod('posts', $page),
      ];
    }
  }

  // Pages
  if (!empty($opts['sitemap_pages_enabled'])) {
    $pages = get_sitemap_pages('pages');
    foreach ($pages as $page) {
      $sitemaps[] = [
        'loc' => home_url("/sitemap-pages-{$page}.xml"),
        'lastmod' => get_sitemap_lastmod('pages', $page),
      ];
    }
  }

  // Categories
  if (!empty($opts['sitemap_categories_enabled'])) {
    $pages = get_sitemap_pages('categories');
    foreach ($pages as $page) {
      $sitemaps[] = [
        'loc' => home_url("/sitemap-categories-{$page}.xml"),
        'lastmod' => get_sitemap_lastmod('categories', $page),
      ];
    }
  }

  // Tags
  if (!empty($opts['sitemap_tags_enabled'])) {
    $pages = get_sitemap_pages('tags');
    foreach ($pages as $page) {
      $sitemaps[] = [
        'loc' => home_url("/sitemap-tags-{$page}.xml"),
        'lastmod' => get_sitemap_lastmod('tags', $page),
      ];
    }
  }

  // Authors
  if (!empty($opts['sitemap_authors_enabled'])) {
    $pages = get_sitemap_pages('authors');
    foreach ($pages as $page) {
      $sitemaps[] = [
        'loc' => home_url("/sitemap-authors-{$page}.xml"),
        'lastmod' => get_sitemap_lastmod('authors', $page),
      ];
    }
  }

  // Images
  if (!empty($opts['sitemap_images_enabled'])) {
    $pages = get_sitemap_pages('images');
    foreach ($pages as $page) {
      $sitemaps[] = [
        'loc' => home_url("/sitemap-images-{$page}.xml"),
        'lastmod' => get_sitemap_lastmod('images', $page),
      ];
    }
  }

  // Output sitemaps
  foreach ($sitemaps as $sitemap) {
    $xml .= "\t<sitemap>\n";
    $xml .= "\t\t<loc>" . esc_url($sitemap['loc']) . "</loc>\n";
    $xml .= "\t\t<lastmod>" . $sitemap['lastmod'] . "</lastmod>\n";
    $xml .= "\t</sitemap>\n";
  }

  $xml .= '</sitemapindex>';

  echo $xml;
}

// Child sitemap output
function output_child_sitemap($type, $page = 1) {
  // Check if type is enabled
  $opts = get_option('cnp_seo_settings', []);
  $enabled_key = "sitemap_{$type}_enabled";
  if (empty($opts[$enabled_key])) {
    status_header(404);
    exit;
  }

  // Try cache first
  $cached = get_cached_sitemap($type, $page);
  if ($cached !== false) {
    echo $cached;
    return;
  }

  // Generate sitemap
  $urls = get_sitemap_urls($type, $page);

  if (empty($urls)) {
    status_header(404);
    exit;
  }

  $xml = generate_sitemap_xml($urls);
  set_cached_sitemap($type, $page, $xml);

  echo $xml;
}

// Get sitemap pages for a type
function get_sitemap_pages($type) {
  $opts = get_option('cnp_seo_settings', []);
  $max_urls = $opts['sitemap_max_urls'] ?? 2000;

  $total_urls = get_sitemap_url_count($type);
  $pages = ceil($total_urls / $max_urls);

  return range(1, max(1, $pages));
}

// Get URLs for a sitemap page
function get_sitemap_urls($type, $page = 1) {
  $opts = get_option('cnp_seo_settings', []);
  $max_urls = $opts['sitemap_max_urls'] ?? 2000;
  $offset = ($page - 1) * $max_urls;

  switch ($type) {
    case 'posts':
      return get_posts_urls($max_urls, $offset);
    case 'pages':
      return get_pages_urls($max_urls, $offset);
    case 'categories':
      return get_categories_urls($max_urls, $offset);
    case 'tags':
      return get_tags_urls($max_urls, $offset);
    case 'authors':
      return get_authors_urls($max_urls, $offset);
    case 'images':
      return get_images_urls($max_urls, $offset);
    default:
      return [];
  }
}

// Get total URL count for a type
function get_sitemap_url_count($type) {
  switch ($type) {
    case 'posts':
      return count_posts_urls();
    case 'pages':
      return count_pages_urls();
    case 'categories':
      return count_categories_urls();
    case 'tags':
      return count_tags_urls();
    case 'authors':
      return count_authors_urls();
    case 'images':
      return count_images_urls();
    default:
      return 0;
  }
}

// Get lastmod for a sitemap page
function get_sitemap_lastmod($type, $page = 1) {
  $urls = get_sitemap_urls($type, $page);
  $lastmods = array_column($urls, 'lastmod');

  if (empty($lastmods)) {
    return gmdate('c');
  }

  return max($lastmods);
}

// Generate XML from URLs array
function generate_sitemap_xml($urls) {
  $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
  $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

  foreach ($urls as $url) {
    $xml .= "\t<url>\n";
    $xml .= "\t\t<loc>" . esc_url($url['loc']) . "</loc>\n";
    $xml .= "\t\t<lastmod>" . $url['lastmod'] . "</lastmod>\n";
    $xml .= "\t</url>\n";
  }

  $xml .= '</urlset>';

  return $xml;
}

// Posts URLs
function get_posts_urls($limit, $offset) {
  $args = [
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => $limit,
    'offset' => $offset,
    'orderby' => 'modified',
    'order' => 'DESC',
  ];

  $posts = get_posts($args);
  $urls = [];

  foreach ($posts as $post) {
    if (is_post_excluded($post->ID)) {
      continue;
    }

    $url = get_permalink($post->ID);
    if (is_url_excluded($url)) {
      continue;
    }

    $urls[] = [
      'loc' => $url,
      'lastmod' => $post->post_modified_gmt . 'Z',
    ];
  }

  return $urls;
}

function count_posts_urls() {
  $count = wp_count_posts('post');
  return $count->publish;
}

// Pages URLs
function get_pages_urls($limit, $offset) {
  $args = [
    'post_type' => 'page',
    'post_status' => 'publish',
    'posts_per_page' => $limit,
    'offset' => $offset,
    'orderby' => 'modified',
    'order' => 'DESC',
  ];

  $pages = get_posts($args);
  $urls = [];

  foreach ($pages as $page) {
    if (is_post_excluded($page->ID)) {
      continue;
    }

    $url = get_permalink($page->ID);
    if (is_url_excluded($url)) {
      continue;
    }

    $urls[] = [
      'loc' => $url,
      'lastmod' => $page->post_modified_gmt . 'Z',
    ];
  }

  return $urls;
}

function count_pages_urls() {
  $count = wp_count_posts('page');
  return $count->publish;
}

// Categories URLs
function get_categories_urls($limit, $offset) {
  $args = [
    'taxonomy' => 'category',
    'hide_empty' => true,
    'number' => $limit,
    'offset' => $offset,
  ];

  $categories = get_terms($args);
  $urls = [];

  foreach ($categories as $category) {
    if (is_term_excluded($category->term_id, 'category')) {
      continue;
    }

    $url = get_term_link($category);
    if (is_wp_error($url) || is_url_excluded($url)) {
      continue;
    }

    $urls[] = [
      'loc' => $url,
      'lastmod' => get_category_lastmod($category->term_id),
    ];
  }

  return $urls;
}

function count_categories_urls() {
  $count = wp_count_terms('category', ['hide_empty' => true]);
  return $count;
}

function get_category_lastmod($term_id) {
  global $wpdb;

  $latest_post = $wpdb->get_var($wpdb->prepare(
    "SELECT p.post_modified_gmt
     FROM {$wpdb->posts} p
     INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
     WHERE tr.term_taxonomy_id = %d
     AND p.post_status = 'publish'
     AND p.post_type = 'post'
     ORDER BY p.post_modified_gmt DESC
     LIMIT 1",
    $term_id
  ));

  return $latest_post ? $latest_post . 'Z' : gmdate('c');
}

// Tags URLs
function get_tags_urls($limit, $offset) {
  $args = [
    'taxonomy' => 'post_tag',
    'hide_empty' => true,
    'number' => $limit,
    'offset' => $offset,
  ];

  $tags = get_terms($args);
  $urls = [];

  foreach ($tags as $tag) {
    if (is_term_excluded($tag->term_id, 'post_tag')) {
      continue;
    }

    $url = get_term_link($tag);
    if (is_wp_error($url) || is_url_excluded($url)) {
      continue;
    }

    $urls[] = [
      'loc' => $url,
      'lastmod' => get_tag_lastmod($tag->term_id),
    ];
  }

  return $urls;
}

function count_tags_urls() {
  $count = wp_count_terms('post_tag', ['hide_empty' => true]);
  return $count;
}

function get_tag_lastmod($term_id) {
  return get_category_lastmod($term_id); // Same logic
}

// Authors URLs
function get_authors_urls($limit, $offset) {
  $args = [
    'has_published_posts' => true,
    'number' => $limit,
    'offset' => $offset,
  ];

  $authors = get_users($args);
  $urls = [];

  foreach ($authors as $author) {
    $url = get_author_posts_url($author->ID);
    if (is_url_excluded($url)) {
      continue;
    }

    $urls[] = [
      'loc' => $url,
      'lastmod' => get_author_lastmod($author->ID),
    ];
  }

  return $urls;
}

function count_authors_urls() {
  $count = count_users();
  return $count['avail_roles']['administrator'] + $count['avail_roles']['editor'] + $count['avail_roles']['author'];
}

function get_author_lastmod($author_id) {
  global $wpdb;

  $latest_post = $wpdb->get_var($wpdb->prepare(
    "SELECT post_modified_gmt
     FROM {$wpdb->posts}
     WHERE post_author = %d
     AND post_status = 'publish'
     AND post_type = 'post'
     ORDER BY post_modified_gmt DESC
     LIMIT 1",
    $author_id
  ));

  return $latest_post ? $latest_post . 'Z' : gmdate('c');
}

// Images URLs
function get_images_urls($limit, $offset) {
  global $wpdb;

  $images = $wpdb->get_results($wpdb->prepare(
    "SELECT p.ID, p.post_modified_gmt, pm.meta_value as image_url
     FROM {$wpdb->posts} p
     INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
     WHERE p.post_type = 'attachment'
     AND p.post_status = 'inherit'
     AND p.post_mime_type LIKE 'image/%%'
     AND pm.meta_key = '_wp_attached_file'
     ORDER BY p.post_modified_gmt DESC
     LIMIT %d OFFSET %d",
    $limit, $offset
  ));

  $urls = [];
  $seen_urls = [];

  foreach ($images as $image) {
    $image_url = wp_get_attachment_url($image->ID);
    if (!$image_url || in_array($image_url, $seen_urls)) {
      continue;
    }

    $seen_urls[] = $image_url;

    $urls[] = [
      'loc' => $image_url,
      'lastmod' => $image->post_modified_gmt . 'Z',
    ];
  }

  return $urls;
}

function count_images_urls() {
  global $wpdb;

  $count = $wpdb->get_var(
    "SELECT COUNT(*)
     FROM {$wpdb->posts}
     WHERE post_type = 'attachment'
     AND post_status = 'inherit'
     AND post_mime_type LIKE 'image/%'"
  );

  return (int) $count;
}

// Google News sitemap
function output_news_sitemap() {
  $opts = get_option('cnp_seo_settings', []);
  $window_hours = $opts['sitemap_news_window_hours'] ?? 48;
  $publication_name = $opts['news_publication_name'] ?? get_bloginfo('name');
  $publication_lang = $opts['news_publication_lang'] ?? 'en';
  $exclude_sponsored = !empty($opts['gn_exclude_sponsored_from_news']);

  // Calculate cutoff time
  $cutoff_time = gmdate('Y-m-d H:i:s', strtotime("-{$window_hours} hours"));

  $args = [
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
    'meta_query' => [
      [
        'key' => '_cnp_seo_noindex',
        'compare' => 'NOT EXISTS',
      ],
    ],
  ];

  // Exclude sponsored posts if configured
  if ($exclude_sponsored) {
    $args['meta_query'][] = [
      'key' => '_cnp_sponsored',
      'compare' => 'NOT EXISTS',
    ];
  }

  $posts = get_posts($args);

  // Start XML
  $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
  $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";

  foreach ($posts as $post) {
    // Skip if noindex or excluded
    if (is_post_excluded($post->ID)) {
      continue;
    }

    $url = get_permalink($post->ID);
    if (is_url_excluded($url)) {
      continue;
    }

    $xml .= "\t<url>\n";
    $xml .= "\t\t<loc>" . esc_url($url) . "</loc>\n";
    $xml .= "\t\t<news:news>\n";
    $xml .= "\t\t\t<news:publication>\n";
    $xml .= "\t\t\t\t<news:name>" . esc_html($publication_name) . "</news:name>\n";
    $xml .= "\t\t\t\t<news:language>" . esc_html($publication_lang) . "</news:language>\n";
    $xml .= "\t\t\t</news:publication>\n";
    $xml .= "\t\t\t<news:publication_date>" . mysql2date('c', $post->post_date_gmt) . "</news:publication_date>\n";
    $xml .= "\t\t\t<news:title>" . esc_html(wp_strip_all_tags(get_the_title($post->ID))) . "</news:title>\n";

    // Add genres if GN label is set
    $gn_label = get_post_meta($post->ID, '_cnp_gn_label', true);
    if (!empty($gn_label)) {
      $genre_map = [
        'opinion' => 'Opinion',
        'satire' => 'Satire',
        'press_release' => 'Press Release',
        'user_generated' => 'User Generated',
      ];
      if (isset($genre_map[$gn_label])) {
        $xml .= "\t\t\t<news:genres>" . esc_html($genre_map[$gn_label]) . "</news:genres>\n";
      }
    }

    $xml .= "\t\t</news:news>\n";
    $xml .= "\t</url>\n";
  }

  $xml .= '</urlset>';

  // Cache the result
  set_cached_sitemap('news', 1, $xml);

  echo $xml;
}

// Robots.txt integration
add_action('do_robotstxt', function(){
  $opts = get_option('cnp_seo_settings', []);

  if (!empty($opts['sitemaps_enabled'])) {
    echo "\n# CNP SEO Sitemaps\n";
    echo "Sitemap: " . home_url('/sitemap.xml') . "\n";

    if (!empty($opts['news_sitemap_enabled'])) {
      echo "Sitemap: " . home_url('/news-sitemap.xml') . "\n";
    }
  }
});

// Cache invalidation hooks
add_action('save_post', function($post_id){
  if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
    return;
  }

  $post = get_post($post_id);
  if (!$post || $post->post_status !== 'publish') {
    return;
  }

  // Clear relevant caches
  if ($post->post_type === 'post') {
    clear_sitemap_cache('posts');
    clear_sitemap_cache('news'); // News sitemap may be affected

    // Clear author cache
    clear_sitemap_cache('authors');

    // Clear category/tag caches for this post
    $terms = wp_get_post_terms($post_id, ['category', 'post_tag'], ['fields' => 'ids']);
    if (!empty($terms)) {
      clear_sitemap_cache('categories');
      clear_sitemap_cache('tags');
    }
  } elseif ($post->post_type === 'page') {
    clear_sitemap_cache('pages');
  }

  // Clear index cache
  clear_sitemap_cache('index');
});

add_action('edit_term', function($term_id, $taxonomy){
  if (in_array($taxonomy, ['category', 'post_tag'])) {
    clear_sitemap_cache($taxonomy === 'category' ? 'categories' : 'tags');
    clear_sitemap_cache('index');
  }
});

add_action('profile_update', function(){
  clear_sitemap_cache('authors');
  clear_sitemap_cache('index');
});

add_action('update_option_cnp_seo_settings', function(){
  // Clear all sitemap caches when settings change
  clear_sitemap_cache();
});
