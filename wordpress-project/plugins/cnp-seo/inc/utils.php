<?php

namespace CNP\SEO\Utils;



if (!defined('ABSPATH')) exit;



function setting($key, $default = null){

  $opts = get_option('cnp_seo_settings', []);

  return $opts[$key] ?? $default;

}



function is_truthy($val){

  return in_array($val, [1, true, '1'], true);

}



function excerpt_fallback($post_id, $max = 160){

  if (!$post_id) {
    $post_id = get_the_ID();
  }

  $excerpt = get_the_excerpt($post_id);

  if (empty(trim($excerpt))) {
    $content = get_post_field('post_content', $post_id);
    $excerpt = wp_trim_words(strip_tags($content), 30, '...');
  }

  $excerpt = preg_replace('/\s+/', ' ', trim($excerpt));

  if (strlen($excerpt) > $max) {
    $excerpt = substr($excerpt, 0, $max - 3) . '...';
  }

  return $excerpt;

}



function absolute_url($url){

  if (empty($url)) {
    return '';
  }

  if (wp_http_validate_url($url)) {
    return $url;
  }

  // If it's a relative URL, make it absolute
  if (strpos($url, '/') === 0) {
    return home_url($url);
  }

  return '';

}
