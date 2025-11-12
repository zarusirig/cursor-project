<?php

namespace CNP\SEO\GN;

if (!defined('ABSPATH')) exit;

// Register Google News feed endpoints
add_action('init', function() {
  $opts = get_option('cnp_seo_settings', []);
  if (empty($opts['gn_enabled'])) {
    return;
  }

  // Sitewide editorial feed
  add_feed('site', __NAMESPACE__ . '\\output_site_feed');

  // Category feeds (all categories automatically)
  add_feed('category', __NAMESPACE__ . '\\output_category_feed');

  // Pillar feeds (mapped categories)
  add_feed('pillar', __NAMESPACE__ . '\\output_pillar_feed');

  // News feed for Publisher Center
  add_feed('news', __NAMESPACE__ . '\\output_news_feed');
});

// Sitewide editorial feed
function output_site_feed() {
  $opts = get_option('cnp_seo_settings', []);
  $item_count = $opts['gn_feed_item_count'] ?? 100;

  $args = [
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => $item_count,
    'orderby' => 'date',
    'order' => 'DESC',
    'meta_query' => [
      [
        'key' => '_cnp_seo_noindex',
        'compare' => 'NOT EXISTS',
      ],
    ],
  ];

  output_feed($args, 'Sitewide Editorial Feed', 'Latest articles from ' . get_bloginfo('name'));
}

// Category feed
function output_category_feed() {
  $category_slug = get_query_var('category_name');
  if (empty($category_slug)) {
    status_header(404);
    exit;
  }

  $category = get_category_by_slug($category_slug);
  if (!$category) {
    status_header(404);
    exit;
  }

  $opts = get_option('cnp_seo_settings', []);
  $item_count = $opts['gn_feed_item_count'] ?? 100;

  $args = [
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => $item_count,
    'orderby' => 'date',
    'order' => 'DESC',
    'cat' => $category->term_id,
    'meta_query' => [
      [
        'key' => '_cnp_seo_noindex',
        'compare' => 'NOT EXISTS',
      ],
    ],
  ];

  output_feed($args, $category->name, 'Articles in ' . $category->name . ' from ' . get_bloginfo('name'));
}

// Pillar feed
function output_pillar_feed() {
  $pillar_slug = get_query_var('category_name'); // Reusing category_name for pillar
  if (empty($pillar_slug)) {
    status_header(404);
    exit;
  }

  $opts = get_option('cnp_seo_settings', []);
  $section_map = json_decode($opts['gn_section_map'] ?? '{}', true);

  if (!isset($section_map[$pillar_slug])) {
    status_header(404);
    exit;
  }

  $item_count = $opts['gn_feed_item_count'] ?? 100;

  // Get all category IDs that map to this pillar
  $category_ids = [];
  foreach ($section_map as $cat_slug => $pillar_name) {
    if ($pillar_name === $section_map[$pillar_slug]) {
      $cat = get_category_by_slug($cat_slug);
      if ($cat) {
        $category_ids[] = $cat->term_id;
      }
    }
  }

  if (empty($category_ids)) {
    status_header(404);
    exit;
  }

  $args = [
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => $item_count,
    'orderby' => 'date',
    'order' => 'DESC',
    'category__in' => $category_ids,
    'meta_query' => [
      [
        'key' => '_cnp_seo_noindex',
        'compare' => 'NOT EXISTS',
      ],
    ],
  ];

  output_feed($args, $section_map[$pillar_slug], 'Articles in ' . $section_map[$pillar_slug] . ' from ' . get_bloginfo('name'));
}

// News feed for Publisher Center
function output_news_feed() {
  $opts = get_option('cnp_seo_settings', []);
  $window_hours = $opts['sitemap_news_window_hours'] ?? 48;
  $item_count = $opts['gn_feed_item_count'] ?? 100;

  $cutoff_time = gmdate('Y-m-d H:i:s', strtotime("-{$window_hours} hours"));

  $args = [
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => $item_count,
    'orderby' => 'date',
    'order' => 'DESC',
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

  // Exclude sponsored if configured
  if (!empty($opts['gn_exclude_sponsored_from_news'])) {
    $args['meta_query'][] = [
      'key' => '_cnp_sponsored',
      'compare' => 'NOT EXISTS',
    ];
  }

  output_feed($args, 'Latest News', 'Recent articles from the last ' . $window_hours . ' hours from ' . get_bloginfo('name'));
}

// Generic feed output function
function output_feed($args, $title, $description) {
  $opts = get_option('cnp_seo_settings', []);
  $include_full_content = !empty($opts['gn_include_full_content_in_feeds']);

  $posts = get_posts($args);

  header('Content-Type: application/rss+xml; charset=utf-8');

  echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
  echo '<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/">' . "\n";
  echo '<channel>' . "\n";
  echo '<title>' . esc_html($title) . '</title>' . "\n";
  echo '<link>' . esc_url(home_url('/')) . '</link>' . "\n";
  echo '<description>' . esc_html($description) . '</description>' . "\n";
  echo '<language>' . esc_html($opts['gn_publication_lang'] ?? 'en') . '</language>' . "\n";
  echo '<lastBuildDate>' . mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false) . '</lastBuildDate>' . "\n";

  // WebSub/PubSubHubbub for real-time updates
  $hub_url = 'https://pubsubhubbub.appspot.com/';
  echo '<atom:link href="' . esc_url($hub_url) . '" rel="hub" xmlns:atom="http://www.w3.org/2005/Atom"/>' . "\n";
  echo '<atom:link href="' . esc_url(get_feed_link()) . '" rel="self" type="application/rss+xml" xmlns:atom="http://www.w3.org/2005/Atom"/>' . "\n";

  foreach ($posts as $post) {
    echo '<item>' . "\n";
    echo '<title>' . esc_html(wp_strip_all_tags(get_the_title($post))) . '</title>' . "\n";
    echo '<link>' . esc_url(get_permalink($post)) . '</link>' . "\n";
    echo '<guid isPermaLink="true">' . esc_url(get_permalink($post)) . '</guid>' . "\n";
    echo '<pubDate>' . mysql2date('D, d M Y H:i:s +0000', $post->post_date_gmt, false) . '</pubDate>' . "\n";

    // Excerpt as description
    $excerpt = wp_trim_words(get_the_excerpt($post), 30, '...');
    echo '<description>' . esc_html($excerpt) . '</description>' . "\n";

    // Full content if enabled
    if ($include_full_content) {
      setup_postdata($post);
      $content = apply_filters('the_content', get_the_content());
      wp_reset_postdata();
      echo '<content:encoded><![CDATA[' . $content . ']]></content:encoded>' . "\n";
    }

    // Category tags
    $categories = get_the_category($post->ID);
    foreach ($categories as $category) {
      echo '<category>' . esc_html($category->name) . '</category>' . "\n";
    }

    // Google News label if set
    $gn_label = get_post_meta($post->ID, '_cnp_gn_label', true);
    if (!empty($gn_label)) {
      $label_names = [
        'opinion' => 'Opinion',
        'satire' => 'Satire',
        'press_release' => 'Press Release',
        'user_generated' => 'User Generated',
      ];
      if (isset($label_names[$gn_label])) {
        echo '<category>' . esc_html($label_names[$gn_label]) . '</category>' . "\n";
      }
    }

    echo '</item>' . "\n";
  }

  echo '</channel>' . "\n";
  echo '</rss>' . "\n";

  exit;
}

// Preflight validation function
function run_preflight_check($url) {
  $errors = [];
  $warnings = [];
  $facts = [];

  // Parse URL
  $parsed_url = parse_url($url);
  if (!$parsed_url || !isset($parsed_url['host'])) {
    return ['ok' => false, 'errors' => ['Invalid URL format'], 'warnings' => $warnings, 'facts' => $facts];
  }

  // Check if URL is on same host
  $site_host = parse_url(home_url(), PHP_URL_HOST);
  if ($parsed_url['host'] !== $site_host) {
    $errors[] = 'URL host does not match site host';
  }

  // Fetch the page
  $response = wp_remote_get($url, [
    'timeout' => 10,
    'user-agent' => 'CNPGN-Preflight/1.0 (+' . home_url() . ')',
    'headers' => ['Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'],
  ]);

  if (is_wp_error($response)) {
    $errors[] = 'Could not fetch URL: ' . $response->get_error_message();
    return ['ok' => false, 'errors' => $errors, 'warnings' => $warnings, 'facts' => $facts];
  }

  $status_code = wp_remote_retrieve_response_code($response);
  $facts['http_status'] = $status_code;

  if ($status_code !== 200) {
    $errors[] = "HTTP status is {$status_code}, expected 200";
  }

  $html = wp_remote_retrieve_body($response);
  $facts['content_length'] = strlen($html);

  if (empty($html)) {
    $errors[] = 'Empty response body';
    return ['ok' => false, 'errors' => $errors, 'warnings' => $warnings, 'facts' => $facts];
  }

  // Check for noindex
  if (preg_match('/<meta[^>]*name=["\']robots["\'][^>]*content=["\'][^"\']*noindex[^"\']*["\'][^>]*>/i', $html)) {
    $errors[] = 'Page contains noindex meta tag';
  }

  // Parse HTML
  libxml_use_internal_errors(true);
  $doc = new \DOMDocument();
  $doc->loadHTML($html);
  $xpath = new \DOMXPath($doc);

  // Check title
  $title_nodes = $xpath->query('//title');
  if ($title_nodes->length === 0) {
    $errors[] = 'Missing <title> tag';
  } else {
    $facts['title'] = trim($title_nodes->item(0)->textContent);
  }

  // Check canonical
  $canonical_nodes = $xpath->query('//link[@rel="canonical"]/@href');
  if ($canonical_nodes->length === 0) {
    $errors[] = 'Missing canonical URL';
  } else {
    $facts['canonical'] = $canonical_nodes->item(0)->value;
    if ($canonical_nodes->item(0)->value !== $url) {
      $warnings[] = 'Canonical URL differs from requested URL';
    }
  }

  // Check OG image
  $og_image_nodes = $xpath->query('//meta[@property="og:image"]/@content');
  if ($og_image_nodes->length === 0) {
    $warnings[] = 'Missing OpenGraph image';
  } else {
    $facts['og_image'] = $og_image_nodes->item(0)->value;
  }

  // Check Twitter card
  $twitter_card_nodes = $xpath->query('//meta[@name="twitter:card"]/@content');
  if ($twitter_card_nodes->length === 0) {
    $warnings[] = 'Missing Twitter card meta';
  }

  // Check NewsArticle schema
  $news_article_scripts = $xpath->query('//script[@type="application/ld+json"]');
  $has_news_article = false;
  $has_organization = false;
  $has_breadcrumb = false;

  foreach ($news_article_scripts as $script) {
    $json_data = json_decode($script->textContent, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
      continue;
    }

    if (isset($json_data['@type']) && $json_data['@type'] === 'NewsArticle') {
      $has_news_article = true;
      $facts['news_article'] = $json_data;

      // Check required NewsArticle fields
      if (empty($json_data['headline'])) {
        $errors[] = 'NewsArticle missing headline';
      }
      if (empty($json_data['datePublished'])) {
        $errors[] = 'NewsArticle missing datePublished';
      }
      if (empty($json_data['author'])) {
        $errors[] = 'NewsArticle missing author';
      }
      if (empty($json_data['publisher'])) {
        $errors[] = 'NewsArticle missing publisher';
      }
      if (empty($json_data['mainEntityOfPage'])) {
        $errors[] = 'NewsArticle missing mainEntityOfPage';
      }

      // Check for genre
      if (isset($json_data['genre'])) {
        $facts['genre'] = $json_data['genre'];
      }

      // Check isAccessibleForFree
      if (!isset($json_data['isAccessibleForFree']) || $json_data['isAccessibleForFree'] !== true) {
        $warnings[] = 'NewsArticle should have isAccessibleForFree: true';
      }
    }

    if (isset($json_data['@type']) && $json_data['@type'] === 'Organization') {
      $has_organization = true;
      $facts['organization'] = $json_data;

      // Check for logos
      if (empty($json_data['logo'])) {
        $warnings[] = 'Organization missing logo';
      }
      if (empty($json_data['correctionsPolicy'])) {
        $warnings[] = 'Organization missing correctionsPolicy';
      }
    }

    if (isset($json_data['@type']) && $json_data['@type'] === 'BreadcrumbList') {
      $has_breadcrumb = true;
      $facts['breadcrumb'] = $json_data;
    }
  }

  if (!$has_news_article) {
    $errors[] = 'Missing NewsArticle structured data';
  }
  if (!$has_organization) {
    $warnings[] = 'Missing Organization structured data';
  }
  if (!$has_breadcrumb) {
    $warnings[] = 'Missing BreadcrumbList structured data';
  }

  // Check for visible label text if GN label is set
  $post_id = url_to_postid($url);
  if ($post_id) {
    $gn_label = get_post_meta($post_id, '_cnp_gn_label', true);
    if (!empty($gn_label)) {
      $facts['gn_label'] = $gn_label;
      $label_texts = [
        'opinion' => ['opinion', 'op-ed', 'editorial'],
        'satire' => ['satire', 'satirical'],
        'press_release' => ['press release', 'press-release'],
        'user_generated' => ['user generated', 'user-generated'],
      ];

      $found_label = false;
      if (isset($label_texts[$gn_label])) {
        foreach ($label_texts[$gn_label] as $text) {
          if (stripos($html, $text) !== false) {
            $found_label = true;
            break;
          }
        }
      }

      if (!$found_label) {
        $errors[] = "GN label '{$gn_label}' set but visible label text not found near top of article";
      }
    }

    // Check sponsored content
    $is_sponsored = get_post_meta($post_id, '_cnp_sponsored', true);
    if ($is_sponsored) {
      $facts['sponsored'] = true;

      // Check for visible sponsored disclosure
      $sponsored_texts = ['sponsored', 'paid content', 'advertisement', 'promoted'];
      $found_sponsored = false;
      foreach ($sponsored_texts as $text) {
        if (stripos($html, $text) !== false) {
          $found_sponsored = true;
          break;
        }
      }

      if (!$found_sponsored) {
        $errors[] = 'Sponsored content flag set but visible disclosure not found';
      }

      // Check outbound links have rel="sponsored nofollow"
      // This would require more complex HTML parsing, simplified check for now
      $warnings[] = 'Verify outbound commercial links have rel="sponsored nofollow"';
    }
  }

  // Check AI disclosure
  if (stripos($html, 'AI') === false && stripos($html, 'artificial intelligence') === false) {
    $warnings[] = 'Site-wide AI disclosure not found';
  }

  return [
    'ok' => empty($errors),
    'errors' => $errors,
    'warnings' => $warnings,
    'facts' => $facts,
  ];
}

/**
 * Ping WebSub hub when new post is published
 */
function ping_websub_hub($post_id) {
  // Only ping for published posts
  if (get_post_status($post_id) !== 'publish') {
    return;
  }

  // Only ping for posts, not pages or other post types
  if (get_post_type($post_id) !== 'post') {
    return;
  }

  $hub_url = 'https://pubsubhubbub.appspot.com/';
  $feed_url = get_feed_link();

  // Ping the hub
  $response = wp_remote_post($hub_url, [
    'body' => [
      'hub.mode' => 'publish',
      'hub.url' => $feed_url,
    ],
    'timeout' => 5,
  ]);

  // Log for debugging (optional)
  if (is_wp_error($response)) {
    error_log('WebSub ping failed: ' . $response->get_error_message());
  }
}
add_action('publish_post', __NAMESPACE__ . '\\ping_websub_hub');
