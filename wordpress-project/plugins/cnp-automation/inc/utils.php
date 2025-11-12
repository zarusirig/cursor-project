<?php

namespace CNP\AUT\Utils;

if (!defined('ABSPATH')) exit;

function setting($key, $default = null){
  $opts = get_option('cnp_automation_settings', []);
  return $opts[$key] ?? $default;
}

function validate_brief($brief) {
  $errors = [];

  // Required fields
  if (empty($brief['title']) || !is_string($brief['title'])) {
    $errors[] = 'title is required and must be a string';
  } elseif (strlen($brief['title']) < 8 || strlen($brief['title']) > 120) {
    $errors[] = 'title must be between 8-120 characters';
  }

  // Optional fields with validation
  if (isset($brief['dek']) && (!is_string($brief['dek']) || strlen($brief['dek']) > 200)) {
    $errors[] = 'dek must be a string under 200 characters';
  }

  if (isset($brief['category_slug'])) {
    if (!is_string($brief['category_slug'])) {
      $errors[] = 'category_slug must be a string';
    } elseif (!term_exists($brief['category_slug'], 'category')) {
      $errors[] = 'category_slug does not exist';
    }
  }

  if (isset($brief['category_id'])) {
    if (!is_int($brief['category_id']) || $brief['category_id'] <= 0) {
      $errors[] = 'category_id must be a positive integer';
    } elseif (!term_exists($brief['category_id'], 'category')) {
      $errors[] = 'category_id does not exist';
    }
  }

  if (isset($brief['tags'])) {
    if (!is_array($brief['tags'])) {
      $errors[] = 'tags must be an array';
    } else {
      $brief['tags'] = array_map('sanitize_text_field', $brief['tags']);
      $brief['tags'] = array_unique(array_filter($brief['tags']));
    }
  }

  if (isset($brief['language']) && (!is_string($brief['language']) || strlen($brief['language']) > 10)) {
    $errors[] = 'language must be a string under 10 characters';
  }

  if (isset($brief['author_id'])) {
    if (!is_int($brief['author_id']) || $brief['author_id'] <= 0) {
      $errors[] = 'author_id must be a positive integer';
    } elseif (!get_user_by('id', $brief['author_id'])) {
      $errors[] = 'author_id does not exist';
    }
  }

  if (isset($brief['target_template']) && !in_array($brief['target_template'], ['article', 'review', 'comparison', 'explainer', 'live'])) {
    $errors[] = 'target_template must be one of: article, review, comparison, explainer, live';
  }

  if (isset($brief['entities'])) {
    if (!is_array($brief['entities'])) {
      $errors[] = 'entities must be an array';
    } else {
      $brief['entities'] = array_map('sanitize_text_field', $brief['entities']);
      $brief['entities'] = array_unique(array_filter($brief['entities']));
    }
  }

  if (isset($brief['internal_links_desired']) && (!is_int($brief['internal_links_desired']) || $brief['internal_links_desired'] < 0)) {
    $errors[] = 'internal_links_desired must be a non-negative integer';
  }

  if (isset($brief['featured_image_url'])) {
    if (!is_string($brief['featured_image_url']) || !filter_var($brief['featured_image_url'], FILTER_VALIDATE_URL)) {
      $errors[] = 'featured_image_url must be a valid URL';
    }
  }

  if (isset($brief['sources'])) {
    if (!is_array($brief['sources'])) {
      $errors[] = 'sources must be an array';
    } else {
      foreach ($brief['sources'] as $source) {
        if (!is_array($source) || !isset($source['url']) || !isset($source['label'])) {
          $errors[] = 'each source must have url and label';
        }
      }
    }
  }

  if (isset($brief['notes']) && !is_string($brief['notes'])) {
    $errors[] = 'notes must be a string';
  }

  if (isset($brief['priority']) && (!is_int($brief['priority']) || $brief['priority'] < -10 || $brief['priority'] > 10)) {
    $errors[] = 'priority must be an integer between -10 and 10';
  }

  if (isset($brief['external_id']) && !is_string($brief['external_id'])) {
    $errors[] = 'external_id must be a string';
  }

  return empty($errors) ? $brief : new \WP_Error('validation_error', 'Brief validation failed', $errors);
}

function resolve_category_id($brief) {
  // Priority: category_id > category_slug > default from settings
  if (isset($brief['category_id']) && term_exists($brief['category_id'], 'category')) {
    return $brief['category_id'];
  }

  if (isset($brief['category_slug'])) {
    $term = get_term_by('slug', $brief['category_slug'], 'category');
    if ($term) {
      return $term->term_id;
    }
  }

  $default_category = setting('default_category', 0);
  return $default_category > 0 ? $default_category : null;
}

function resolve_author_id($brief) {
  if (isset($brief['author_id']) && get_user_by('id', $brief['author_id'])) {
    return $brief['author_id'];
  }

  $default_author = setting('default_author', 0);
  return $default_author > 0 ? $default_author : get_current_user_id();
}

function resolve_tags($brief) {
  $tags = [];

  if (!empty($brief['tags'])) {
    $tags = array_merge($tags, $brief['tags']);
  }

  $default_tags = setting('default_tags', '');
  if (!empty($default_tags)) {
    $default_tags_array = array_map('trim', explode(',', $default_tags));
    $tags = array_merge($tags, $default_tags_array);
  }

  return array_unique(array_filter($tags));
}

function resolve_language($brief) {
  return $brief['language'] ?? setting('default_language', 'en');
}

function resolve_status($brief) {
  return setting('default_status', 'draft');
}

function generate_qa_checklist() {
  return [
    'hero_present' => false,
    'key_points_present' => false,
    'why_matters_present' => false,
    'sources_present_if_required' => false,
    'tags_minimum' => false,
    'ai_disclosure_present' => false,
    'author_has_bio' => false,
    'internal_links_minimum' => false,
    'reading_time_reasonable' => false,
  ];
}

function log_event($event_name, $data = []) {
  $log_entry = [
    'timestamp' => current_time('mysql'),
    'event' => $event_name,
    'data' => $data,
  ];

  // Log to file
  $log_file = WP_CONTENT_DIR . '/cnp-automation.log';
  $log_line = wp_json_encode($log_entry) . "\n";
  file_put_contents($log_file, $log_line, FILE_APPEND | LOCK_EX);

  // Also log to WordPress debug log if enabled
  if (WP_DEBUG && WP_DEBUG_LOG) {
    error_log("CNP Automation: $event_name - " . wp_json_encode($data));
  }
}

function send_webhook_notification($url, $payload) {
  if (empty($url)) {
    return true; // No webhook configured
  }

  $response = wp_remote_post($url, [
    'headers' => [
      'Content-Type' => 'application/json',
      'User-Agent' => 'CNP-Automation/' . CNP_AUT_VER,
    ],
    'body' => wp_json_encode($payload),
    'timeout' => 10,
  ]);

  if (is_wp_error($response)) {
    log_event('webhook_failed', [
      'url' => $url,
      'error' => $response->get_error_message(),
      'payload' => $payload,
    ]);
    return false;
  }

  $status_code = wp_remote_retrieve_response_code($response);
  if ($status_code < 200 || $status_code >= 300) {
    log_event('webhook_failed', [
      'url' => $url,
      'status_code' => $status_code,
      'response' => wp_remote_retrieve_body($response),
      'payload' => $payload,
    ]);
    return false;
  }

  return true;
}

function send_email_notification($to, $subject, $message) {
  if (empty($to)) {
    return true; // No email configured
  }

  return wp_mail($to, $subject, $message, [
    'Content-Type: text/html; charset=UTF-8',
    'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
  ]);
}
