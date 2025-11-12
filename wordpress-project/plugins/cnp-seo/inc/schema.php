<?php

namespace CNP\SEO\Schema;



if (!defined('ABSPATH')) exit;



// Inject JSON-LD structured data
add_action('wp_head', function(){
  // Skip if in admin, REST, or feeds
  if (is_admin() || wp_is_json_request() || is_feed()) {
    return;
  }

  $post_id = get_the_ID();

  // Always output Organization schema (site-wide)
  output_organization_schema();

  // Person schema for author archives
  if (is_author()) {
    output_person_schema();
  }

  // BreadcrumbList schema for single posts and category pages
  if (is_singular('post') && $post_id) {
    output_breadcrumb_schema($post_id);
  } elseif (is_category()) {
    output_category_breadcrumb_schema();
  }

  // NewsArticle schema for single posts
  if (is_singular('post') && $post_id) {
    output_newsarticle_schema($post_id);

    // Correction comment schema if correction exists
    $correction_note = get_post_meta($post_id, '_cnp_correction_note', true);
    if (!empty($correction_note)) {
      output_correction_schema($post_id, $correction_note);
    }

    // Review schema if review data exists
    if (has_review_data($post_id)) {
      output_review_schema($post_id);
    }
  }

}, 2); // Priority 2 to come after meta tags (priority 1)

function output_organization_schema() {
  $site_name = get_bloginfo('name');
  $site_url = home_url('/');
  $site_description = get_bloginfo('description');
  $opts = get_option('cnp_seo_settings', []);

  $schema = [
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $site_name,
    'url' => $site_url,
  ];

  // Add logo from site icon or Google News settings
  $logos = [];

  // Site icon (square)
  $site_icon_id = get_option('site_icon');
  if ($site_icon_id) {
    $icon_url = wp_get_attachment_image_url($site_icon_id, 'full');
    if ($icon_url) {
      $logos[] = [
        '@type' => 'ImageObject',
        'url' => $icon_url,
      ];
    }
  }

  // Google News logos
  if (!empty($opts['gn_square_logo_url'])) {
    $logos[] = [
      '@type' => 'ImageObject',
      'url' => $opts['gn_square_logo_url'],
    ];
  }

  if (!empty($opts['gn_rect_logo_url'])) {
    $logos[] = [
      '@type' => 'ImageObject',
      'url' => $opts['gn_rect_logo_url'],
    ];
  }

  if (!empty($logos)) {
    $schema['logo'] = count($logos) === 1 ? $logos[0] : $logos;
  }

  if ($site_description) {
    $schema['description'] = $site_description;
  }

  // Add corrections policy
  if (!empty($opts['gn_corrections_url'])) {
    $schema['correctionsPolicy'] = $opts['gn_corrections_url'];
  }

  // Add sameAs if available (can be extended later)
  $schema['sameAs'] = [];

  echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}

function output_person_schema() {
  $author_id = get_query_var('author');
  if (!$author_id) {
    return;
  }

  $author = get_userdata($author_id);
  if (!$author) {
    return;
  }

  $schema = [
    '@context' => 'https://schema.org',
    '@type' => 'Person',
    'name' => $author->display_name,
    'url' => get_author_posts_url($author_id),
  ];

  // Add bio if available
  $bio = get_user_meta($author_id, 'description', true);
  if ($bio) {
    $schema['description'] = $bio;
  }

  // Add avatar if available
  $avatar_url = get_avatar_url($author_id, ['size' => 96]);
  if ($avatar_url && strpos($avatar_url, 'gravatar.com') === false) {
    $schema['image'] = $avatar_url;
  }

  // Add website if available
  $website = $author->user_url;
  if ($website) {
    $schema['sameAs'] = [$website];
  }

  echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}

function output_newsarticle_schema($post_id) {
  $post = get_post($post_id);
  if (!$post) {
    return;
  }

  $headline = wp_strip_all_tags(get_the_title($post_id));
  $permalink = get_permalink($post_id);
  $published = get_post_time('c', false, $post_id);
  $modified = get_post_modified_time('c', false, $post_id);

  // Get author info
  $author_id = $post->post_author;
  $author = get_userdata($author_id);
  $author_name = $author ? $author->display_name : get_bloginfo('name');

  $schema = [
    '@context' => 'https://schema.org',
    '@type' => 'NewsArticle',
    'mainEntityOfPage' => [
      '@type' => 'WebPage',
      '@id' => $permalink,
    ],
    'headline' => $headline,
    'datePublished' => $published,
    'dateModified' => $modified,
    'author' => [
      '@type' => 'Person',
      'name' => $author_name,
    ],
    'publisher' => [
      '@type' => 'Organization',
      'name' => get_bloginfo('name'),
      'url' => home_url('/'),
    ],
    'isAccessibleForFree' => true,
  ];

  // Add logo to publisher if available
  $site_icon_id = get_option('site_icon');
  if ($site_icon_id) {
    $icon_url = wp_get_attachment_image_url($site_icon_id, 'full');
    if ($icon_url) {
      $schema['publisher']['logo'] = [
        '@type' => 'ImageObject',
        'url' => $icon_url,
      ];
    }
  }

  // Add images
  $images = get_post_images($post_id);
  if (!empty($images)) {
    $schema['image'] = $images;
  }

  // Add article section (primary category)
  $primary_category = get_primary_category($post_id);
  if ($primary_category) {
    $schema['articleSection'] = $primary_category;
  }

  // Add Google News genre if label is set
  $gn_label = get_post_meta($post_id, '_cnp_gn_label', true);
  if (!empty($gn_label)) {
    $genre_map = [
      'opinion' => 'Opinion',
      'satire' => 'Satire',
      'press_release' => 'Press Release',
      'user_generated' => 'User Generated',
    ];
    if (isset($genre_map[$gn_label])) {
      $schema['genre'] = $genre_map[$gn_label];
    }
  }

  echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}

function output_review_schema($post_id) {
  $post = get_post($post_id);
  if (!$post) {
    return;
  }

  // Get review data from post meta
  $review_score = get_post_meta($post_id, 'cnp_review_score', true);
  $review_item_type = get_post_meta($post_id, 'cnp_review_item_type', true) ?: 'Product';
  $review_item_name = get_post_meta($post_id, 'cnp_review_item_name', true) ?: get_the_title($post_id);

  if (!$review_score) {
    return;
  }

  // Author info
  $author_id = $post->post_author;
  $author = get_userdata($author_id);
  $author_name = $author ? $author->display_name : get_bloginfo('name');

  $schema = [
    '@context' => 'https://schema.org',
    '@type' => 'Review',
    'itemReviewed' => [
      '@type' => $review_item_type,
      'name' => $review_item_name,
    ],
    'reviewRating' => [
      '@type' => 'Rating',
      'ratingValue' => (float) $review_score,
      'bestRating' => 10,
      'worstRating' => 0,
    ],
    'author' => [
      '@type' => 'Person',
      'name' => $author_name,
    ],
    'datePublished' => get_post_time('c', false, $post_id),
    'name' => wp_strip_all_tags(get_the_title($post_id)),
    'reviewBody' => wp_trim_words(get_post_field('post_content', $post_id), 60, '...'),
  ];

  echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}

function has_review_data($post_id) {
  return (bool) get_post_meta($post_id, 'cnp_review_score', true);
}

function get_post_images($post_id) {
  $images = [];

  // Featured image
  if (has_post_thumbnail($post_id)) {
    $thumbnail_id = get_post_thumbnail_id($post_id);
    $image = wp_get_attachment_image_src($thumbnail_id, 'full');
    if ($image && isset($image[0])) {
      $images[] = $image[0];
    }
  }

  // Additional images from content (can be extended)
  // For now, just return featured image or empty array

  return $images;
}

function get_primary_category($post_id) {
  $categories = get_the_category($post_id);
  if (!empty($categories)) {
    return $categories[0]->name;
  }
  return '';
}

// BreadcrumbList schema for single posts
function output_breadcrumb_schema($post_id) {
  $categories = get_the_category($post_id);
  if (empty($categories)) {
    return;
  }

  $breadcrumbs = [
    [
      '@type' => 'ListItem',
      'position' => 1,
      'name' => 'Home',
      'item' => home_url('/'),
    ],
  ];

  // Add category breadcrumb
  $primary_category = $categories[0];
  $breadcrumbs[] = [
    '@type' => 'ListItem',
    'position' => 2,
    'name' => $primary_category->name,
    'item' => get_category_link($primary_category->term_id),
  ];

  // Add article breadcrumb
  $breadcrumbs[] = [
    '@type' => 'ListItem',
    'position' => 3,
    'name' => wp_strip_all_tags(get_the_title($post_id)),
    'item' => get_permalink($post_id),
  ];

  $schema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => $breadcrumbs,
  ];

  echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}

// BreadcrumbList schema for category pages
function output_category_breadcrumb_schema() {
  $category = get_queried_object();
  if (!$category || !is_a($category, 'WP_Term')) {
    return;
  }

  $breadcrumbs = [
    [
      '@type' => 'ListItem',
      'position' => 1,
      'name' => 'Home',
      'item' => home_url('/'),
    ],
    [
      '@type' => 'ListItem',
      'position' => 2,
      'name' => $category->name,
      'item' => get_category_link($category->term_id),
    ],
  ];

  $schema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => $breadcrumbs,
  ];

  echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}

// CorrectionComment schema
function output_correction_schema($post_id, $correction_note) {
  $schema = [
    '@context' => 'https://schema.org',
    '@type' => 'CorrectionComment',
    'datePublished' => get_post_modified_time('c', false, $post_id),
    'text' => $correction_note,
  ];

  // Add as hasPart to the NewsArticle
  $news_article_schema = [
    '@context' => 'https://schema.org',
    '@type' => 'NewsArticle',
    'mainEntityOfPage' => [
      '@type' => 'WebPage',
      '@id' => get_permalink($post_id),
    ],
    'hasPart' => $schema,
  ];

  echo '<script type="application/ld+json">' . wp_json_encode($news_article_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}
