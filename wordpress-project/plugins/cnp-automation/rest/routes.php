<?php

namespace CNP\AUT\REST;

if (!defined('ABSPATH')) exit;

use CNP\AUT\Jobs as Jobs;
use CNP\AUT\Queue as Queue;
use CNP\AUT\Utils as Utils;
use CNP\AUT\Entities as Entities;
use CNP\AUT\Linking as Linking;

function authenticate_api_request($request) {
  // Check if API is enabled
  if (!Utils\setting('api_enabled', 1)) {
    return new \WP_Error('api_disabled', 'API is disabled', ['status' => 403]);
  }

  // Get authorization header
  $auth_header = $request->get_header('authorization');

  if ($auth_header && preg_match('/^Bearer\s+(.*)$/i', $auth_header, $matches)) {
    $api_key = trim($matches[1]);
  } else {
    // Check for api_key in query parameters (server-to-server)
    $api_key = $request->get_param('cnp_key');
  }

  if (empty($api_key)) {
    return new \WP_Error('missing_auth', 'API key required', ['status' => 401]);
  }

  // Validate API key
  $valid_keys = array_filter(array_map('trim', explode("\n", Utils\setting('api_keys', ''))));

  if (!in_array($api_key, $valid_keys)) {
    return new \WP_Error('invalid_auth', 'Invalid API key', ['status' => 401]);
  }

  return true;
}

function permission_callback_api($request) {
  // First try API key authentication
  $api_auth = authenticate_api_request($request);
  if (!is_wp_error($api_auth)) {
    return true;
  }

  // Fall back to logged-in user with edit_posts capability
  return current_user_can('edit_posts');
}

function permission_callback_admin($request) {
  return current_user_can('manage_options');
}

add_action('rest_api_init', function(){
  // Health check endpoint
  register_rest_route('cnp-automation/v1', '/health', [
    'methods' => 'GET',
    'permission_callback' => '__return_true',
    'callback' => function(){ return ['ok' => true, 'ver' => CNP_AUT_VER]; }
  ]);

  // Create job endpoint
  register_rest_route('cnp-automation/v1', '/jobs', [
    'methods' => 'POST',
    'permission_callback' => __NAMESPACE__.'\\permission_callback_api',
    'callback' => __NAMESPACE__.'\\create_job_endpoint',
    'args' => [
      'title' => [
        'required' => true,
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      ],
      'dek' => [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      ],
      'category_slug' => [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      ],
      'category_id' => [
        'type' => 'integer',
        'sanitize_callback' => 'absint',
      ],
      'tags' => [
        'type' => 'array',
        'items' => ['type' => 'string'],
      ],
      'language' => [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      ],
      'author_id' => [
        'type' => 'integer',
        'sanitize_callback' => 'absint',
      ],
      'target_template' => [
        'type' => 'string',
        'enum' => ['article', 'review', 'comparison', 'explainer', 'live'],
      ],
      'entities' => [
        'type' => 'array',
        'items' => ['type' => 'string'],
      ],
      'internal_links_desired' => [
        'type' => 'integer',
        'sanitize_callback' => 'absint',
      ],
      'featured_image_url' => [
        'type' => 'string',
        'format' => 'uri',
      ],
      'sources' => [
        'type' => 'array',
        'items' => [
          'type' => 'object',
          'properties' => [
            'url' => ['type' => 'string', 'format' => 'uri'],
            'label' => ['type' => 'string'],
          ],
          'required' => ['url', 'label'],
        ],
      ],
      'notes' => [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_textarea_field',
      ],
      'priority' => [
        'type' => 'integer',
        'minimum' => -10,
        'maximum' => 10,
        'default' => 0,
      ],
      'external_id' => [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      ],
    ],
  ]);

  // Get job endpoint
  register_rest_route('cnp-automation/v1', '/jobs/(?P<id>\d+)', [
    'methods' => 'GET',
    'permission_callback' => __NAMESPACE__.'\\permission_callback_api',
    'callback' => __NAMESPACE__.'\\get_job_endpoint',
    'args' => [
      'id' => [
        'required' => true,
        'type' => 'integer',
        'sanitize_callback' => 'absint',
      ],
    ],
  ]);

  // Cancel job endpoint
  register_rest_route('cnp-automation/v1', '/jobs/(?P<id>\d+)/cancel', [
    'methods' => 'POST',
    'permission_callback' => __NAMESPACE__.'\\permission_callback_api',
    'callback' => __NAMESPACE__.'\\cancel_job_endpoint',
    'args' => [
      'id' => [
        'required' => true,
        'type' => 'integer',
        'sanitize_callback' => 'absint',
      ],
    ],
  ]);

  // List jobs endpoint (admin only)
  register_rest_route('cnp-automation/v1', '/jobs', [
    'methods' => 'GET',
    'permission_callback' => __NAMESPACE__.'\\permission_callback_admin',
    'callback' => __NAMESPACE__.'\\list_jobs_endpoint',
    'args' => [
      'status' => [
        'type' => 'string',
        'enum' => ['queued', 'generating', 'draft_created', 'needs_review', 'published', 'failed', 'cancelled'],
      ],
      'limit' => [
        'type' => 'integer',
        'minimum' => 1,
        'maximum' => 100,
        'default' => 50,
        'sanitize_callback' => 'absint',
      ],
      'offset' => [
        'type' => 'integer',
        'minimum' => 0,
        'default' => 0,
        'sanitize_callback' => 'absint',
      ],
    ],
  ]);
});

function create_job_endpoint($request) {
  $brief = $request->get_params();

  // Remove REST-specific parameters
  unset($brief['id'], $brief['cnp_key']);

  $job_id = Jobs\create_job_from_brief($brief);

  if (is_wp_error($job_id)) {
    $error_code = $job_id->get_error_code();
    $status = 400;

    if ($error_code === 'rate_limited') {
      $status = 429;
      $retry_after = isset($job_id->get_error_data()['retry_after']) ? $job_id->get_error_data()['retry_after'] : 3600;
      return new \WP_Error($error_code, $job_id->get_error_message(), [
        'status' => $status,
        'retry_after' => $retry_after,
      ]);
    }

    return new \WP_Error($error_code, $job_id->get_error_message(), ['status' => $status]);
  }

  return [
    'job_id' => $job_id,
    'status' => 'queued',
    'post_id' => null,
    'received_at' => current_time('mysql'),
  ];
}

function get_job_endpoint($request) {
  $job_id = $request->get_param('id');
  $job = Queue\get_job($job_id);

  if (!$job) {
    return new \WP_Error('not_found', 'Job not found', ['status' => 404]);
  }

  // Remove sensitive data for non-admin users
  if (!current_user_can('manage_options')) {
    unset($job['brief']);
  }

  return $job;
}

function cancel_job_endpoint($request) {
  $job_id = $request->get_param('id');

  $result = Jobs\cancel_job($job_id);

  if (is_wp_error($result)) {
    $status = 400;
    if ($result->get_error_code() === 'not_found') {
      $status = 404;
    }
    return new \WP_Error($result->get_error_code(), $result->get_error_message(), ['status' => $status]);
  }

  return [
    'id' => $job_id,
    'status' => 'cancelled',
  ];
}

function list_jobs_endpoint($request) {
  $status = $request->get_param('status');
  $limit = $request->get_param('limit');
  $offset = $request->get_param('offset');

  if ($status) {
    $jobs = Queue\get_jobs_by_status($status, $limit, $offset);
    $total = Queue\count_jobs_by_status($status);
  } else {
    $jobs = Queue\get_recent_jobs($limit, $offset);
    $total = Queue\count_jobs_by_status();
  }

  return [
    'jobs' => $jobs,
    'total' => $total,
    'limit' => $limit,
    'offset' => $offset,
  ];
}

function link_suggestions_endpoint($request) {
  $post_id = $request->get_param('post_id');
  $html = $request->get_param('html');
  $category_id = $request->get_param('category_id');
  $tags = $request->get_param('tags') ?: [];

  if ($post_id) {
    // Get suggestions for existing post
    $suggestions = Linking\get_link_suggestions($post_id);
  } elseif ($html && $category_id) {
    // Handle draft content - extract entities and get suggestions
    // For now, create a temporary post-like object
    $temp_post_id = handle_draft_suggestions($html, $category_id, $tags);
    if (is_wp_error($temp_post_id)) {
      return $temp_post_id;
    }
    $suggestions = Linking\get_link_suggestions($temp_post_id);
    // Clean up temp data
    Entities\remove_post_entities($temp_post_id);
  } else {
    return new \WP_Error('missing_params', 'Either post_id or html+category_id required', ['status' => 400]);
  }

  // Generate anchors for suggestions
  $suggestions_with_anchors = [];
  foreach ($suggestions as $suggestion) {
    $anchors = Linking\generate_anchor_suggestions($post_id ?: $temp_post_id, [$suggestion]);

    if (!empty($anchors)) {
      $anchor = $anchors[0]; // Take first anchor
      $suggestions_with_anchors[] = [
        'id' => $suggestion['id'],
        'type' => $suggestion['type'],
        'url' => $suggestion['url'],
        'title' => $suggestion['title'],
        'entities_matched' => $suggestion['entities_matched'],
        'score' => $suggestion['score'],
        'anchors' => [
          [
            'text' => $anchor['anchor_text'],
            'section' => $anchor['anchor_section'],
          ]
        ],
      ];
    }
  }

  return [
    'post_id' => $post_id ?: $temp_post_id,
    'suggestions' => $suggestions_with_anchors,
  ];
}

function link_insert_endpoint($request) {
  $post_id = $request->get_param('post_id');
  $inserts = $request->get_param('inserts');

  if (!$post_id || !is_array($inserts)) {
    return new \WP_Error('invalid_params', 'post_id and inserts array required', ['status' => 400]);
  }

  $result = Linking\insert_links($post_id, $inserts);

  if (is_wp_error($result)) {
    return $result;
  }

  return $result;
}

function link_preview_endpoint($request) {
  $post_id = $request->get_param('post_id');
  $inserts = $request->get_param('inserts');

  if (!$post_id || !is_array($inserts)) {
    return new \WP_Error('invalid_params', 'post_id and inserts array required', ['status' => 400]);
  }

  $post = get_post($post_id);
  if (!$post) {
    return new \WP_Error('not_found', 'Post not found', ['status' => 404]);
  }

  $content = $post->post_content;
  $per_section_cap = Utils\setting('linker_per_section_cap', 2);
  $min_anchor_length = Utils\setting('linker_anchor_min_chars', 18);

  $preview_content = $content;
  $section_counts = [];
  $applied_inserts = [];

  foreach ($inserts as $insert) {
    $suggestion_id = $insert['suggestion_id'];
    $anchor_text = $insert['anchor_text'];
    $section = $insert['section'];

    // Validate anchor
    if (strlen($anchor_text) < $min_anchor_length) {
      continue;
    }

    // Check section cap
    if (!isset($section_counts[$section])) {
      $section_counts[$section] = 0;
    }

    if ($section_counts[$section] >= $per_section_cap) {
      continue;
    }

    // Parse suggestion ID to get target URL
    $url = Linking\get_suggestion_url_from_id($suggestion_id);
    if (!$url) {
      continue;
    }

    // Check if URL already exists in content
    if (strpos($preview_content, $url) !== false) {
      continue;
    }

    // Apply the insert for preview
    $new_content = Linking\insert_link_in_section($preview_content, $anchor_text, $url, $section);

    if ($new_content !== $preview_content) {
      $preview_content = $new_content;
      $section_counts[$section]++;
      $applied_inserts[] = $insert;
    }
  }

  return [
    'post_id' => $post_id,
    'original_content' => $content,
    'preview_content' => $preview_content,
    'applied_inserts' => $applied_inserts,
    'skipped_inserts' => count($inserts) - count($applied_inserts),
  ];
}

function handle_draft_suggestions($html, $category_id, $tags) {
  // Create temporary entity extraction for draft content
  // This is a simplified implementation - in production you'd want more robust temp handling

  global $wpdb;

  // Extract entities from HTML content
  $plain_content = wp_strip_all_tags($html);
  $words = explode(' ', $plain_content);
  $body_content = implode(' ', array_slice($words, 0, 1000));

  // Use a negative ID to avoid conflicts
  $temp_post_id = -time();

  // Extract basic entities (simplified)
  $entities = [];

  // Add tags as entities
  foreach ($tags as $tag) {
    $entity_key = Entities\normalize_entity_key($tag);
    $entities[$entity_key] = [
      'label' => $tag,
      'weight' => 0.5,
      'sources' => ['custom']
    ];
  }

  // Add category as entity
  $category = get_term($category_id);
  if ($category) {
    $entity_key = Entities\normalize_entity_key($category->name);
    $entities[$entity_key] = [
      'label' => $category->name,
      'weight' => 0.5,
      'sources' => ['taxonomy']
    ];
  }

  // Store temp entities
  if (!empty($entities)) {
    foreach ($entities as $entity_key => $entity_data) {
      $wpdb->replace(
        $wpdb->prefix . 'cnp_entity_index',
        [
          'entity_key' => $entity_key,
          'post_id' => $temp_post_id,
          'score' => $entity_data['weight'],
        ],
        ['%s', '%d', '%f']
      );
    }
  }

  return $temp_post_id;
}

// Metrics endpoints for dashboard and exports
add_action('rest_api_init', function() {
  // Dashboard summary endpoint
  register_rest_route('cnp-automation/v1', '/metrics/summary', [
    'methods' => 'GET',
    'permission_callback' => function() {
      return current_user_can('manage_options');
    },
    'callback' => function($request) {
      $range = intval($request->get_param('range')) ?: 7;
      $category = $request->get_param('category') ?: 'all';

      // Validate range
      if (!in_array($range, [7, 30, 90])) {
        $range = 7;
      }

      $data = \CNP\AUT\Admin\get_dashboard_data($range, $category);

      // Cache for 60 seconds
      $cache_key = 'cnp_metrics_summary_' . $range . '_' . $category;
      set_transient($cache_key, $data, 60);

      return $data;
    },
  ]);

  // Top destinations endpoint
  register_rest_route('cnp-automation/v1', '/metrics/links/top-destinations', [
    'methods' => 'GET',
    'permission_callback' => function() {
      return current_user_can('manage_options');
    },
    'callback' => function($request) {
      $range = intval($request->get_param('range')) ?: 30;
      $limit = intval($request->get_param('limit')) ?: 10;

      global $wpdb;
      $table = $wpdb->prefix . 'cnp_metrics_daily';
      $start_date = date('Y-m-d', strtotime("-{$range} days"));

      // This is a simplified query - in production you'd want a more sophisticated join
      $results = $wpdb->get_results($wpdb->prepare(
        "SELECT p.ID, p.post_title, pm.meta_value as inbound_count
         FROM {$wpdb->posts} p
         LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_cnp_inbound_count'
         WHERE p.post_type = 'post' AND p.post_status = 'publish'
         ORDER BY CAST(pm.meta_value AS UNSIGNED) DESC
         LIMIT %d",
        $limit
      ));

      $destinations = [];
      foreach ($results as $post) {
        $ctr = get_post_meta($post->ID, '_cnp_rc_ctr_7d', true) ?: 0;
        $destinations[] = [
          'post_id' => $post->ID,
          'url' => get_permalink($post->ID),
          'title' => $post->post_title,
          'inbound_count' => intval($post->inbound_count ?: 0),
          'ctr' => floatval($ctr),
        ];
      }

      return $destinations;
    },
  ]);

  // Top QA failures endpoint
  register_rest_route('cnp-automation/v1', '/metrics/qa/top-failures', [
    'methods' => 'GET',
    'permission_callback' => function() {
      return current_user_can('manage_options');
    },
    'callback' => function($request) {
      $range = intval($request->get_param('range')) ?: 30;
      $limit = intval($request->get_param('limit')) ?: 5;

      // This would require storing QA failure details in metrics
      // For now, return a placeholder structure
      return [
        ['check_key' => 'hero_missing', 'count' => 12],
        ['check_key' => 'sources_required', 'count' => 8],
        ['check_key' => 'ai_disclosure_missing', 'count' => 6],
        ['check_key' => 'internal_links_insufficient', 'count' => 4],
        ['check_key' => 'tags_minimum', 'count' => 3],
      ];
    },
  ]);

  // Metrics export endpoint
  register_rest_route('cnp-automation/v1', '/metrics/export', [
    'methods' => 'GET',
    'permission_callback' => function() {
      return current_user_can('manage_options');
    },
    'callback' => function($request) {
      $metric = $request->get_param('metric');
      $range = intval($request->get_param('range')) ?: 90;

      if (empty($metric)) {
        return new \WP_Error('missing_metric', 'Metric parameter is required', ['status' => 400]);
      }

      // Validate metric name (basic security)
      $allowed_metrics = [
        'draft_created', 'draft_failed', 'draft_published', 'qa_fail', 'qa_warn', 'qa_pass',
        'qa_override', 'links_inserted', 'rc_impression', 'rc_click', 'newsletter_signup', 'deep_scroll_75'
      ];

      if (!in_array($metric, $allowed_metrics)) {
        return new \WP_Error('invalid_metric', 'Invalid metric name', ['status' => 400]);
      }

      \CNP\AUT\Metrics\export_metrics_csv($metric, $range);
    },
  ]);

  // CTR rebuild endpoint
  register_rest_route('cnp-automation/v1', '/metrics/recompute-ctr', [
    'methods' => 'POST',
    'permission_callback' => function() {
      return current_user_can('manage_options');
    },
    'callback' => function($request) {
      $range = intval($request->get_param('range')) ?: 7;

      $updated = \CNP\AUT\Metrics\rebuild_all_rc_ctr($range);

      return [
        'success' => true,
        'updated_posts' => $updated,
        'range' => $range,
      ];
    },
  ]);
});
