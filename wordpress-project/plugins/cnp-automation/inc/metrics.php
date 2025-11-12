<?php

namespace CNP\AUT\Metrics;

if (!defined('ABSPATH')) exit;

// Main metrics increment function
function inc($metric, $dim1 = null, $dim2 = null, $n = 1) {
  global $wpdb;

  if (!is_string($metric) || empty($metric)) {
    return false;
  }

  $table = $wpdb->prefix . 'cnp_metrics_daily';
  $date = current_time('Y-m-d');

  // Sanitize dimensions
  $dim1 = $dim1 ? sanitize_text_field($dim1) : null;
  $dim2 = $dim2 ? sanitize_text_field($dim2) : null;
  $n = absint($n);

  if ($n <= 0) {
    return false;
  }

  // Use INSERT ... ON DUPLICATE KEY UPDATE for atomic increments
  $result = $wpdb->query($wpdb->prepare(
    "INSERT INTO $table (date, metric, dim1, dim2, value)
     VALUES (%s, %s, %s, %s, %d)
     ON DUPLICATE KEY UPDATE value = value + %d",
    $date, $metric, $dim1, $dim2, $n, $n
  ));

  return $result !== false;
}

// GA4 event emitter
function emit_ga4_event($event_name, $params = []) {
  $opts = get_option('cnp_automation_settings', []);

  // Check if GA4 is enabled
  if (empty($opts['ga4_enable_admin_events']) || empty($opts['ga4_measurement_id'])) {
    return false;
  }

  $measurement_id = $opts['ga4_measurement_id'];
  $debug_mode = !empty($opts['ga4_debug_mode']);

  // Remove any PII from params (safety check)
  $safe_params = array_filter($params, function($key) {
    return !in_array($key, ['email', 'user_email', 'user_login', 'ip', 'user_agent']);
  }, ARRAY_FILTER_USE_KEY);

  // For admin events, we'll use a simple approach with gtag if available
  // In a production system, you might want to use Measurement Protocol
  if (is_admin() && function_exists('wp_add_inline_script')) {
    $gtag_config = [
      'event_name' => $event_name,
      'measurement_id' => $measurement_id,
      'params' => $safe_params,
      'debug_mode' => $debug_mode
    ];

    wp_add_inline_script('cnp-automation-ga4', sprintf(
      'if (typeof gtag !== "undefined") { gtag("event", "%s", %s); }',
      esc_js($event_name),
      wp_json_encode($safe_params)
    ));

    return true;
  }

  return false;
}

// Automation lifecycle events
function emit_ai_draft_created($post_id, $job_id, $category = null, $template = null, $source = 'api') {
  $category_slug = $category ? get_term_field('slug', $category) : null;

  inc('draft_created', $category_slug, $template);
  emit_ga4_event('ai_draft_created', [
    'post_id' => $post_id,
    'job_id' => $job_id,
    'category' => $category_slug,
    'template' => $template,
    'source' => $source
  ]);
}

function emit_ai_draft_failed($job_id, $error_code = null) {
  inc('draft_failed');
  emit_ga4_event('ai_draft_failed', [
    'job_id' => $job_id,
    'error_code' => $error_code
  ]);
}

function emit_ai_draft_published($post_id, $category = null, $template = null) {
  $category_slug = $category ? get_term_field('slug', $category) : null;

  inc('draft_published', $category_slug, $template);
  emit_ga4_event('ai_draft_published', [
    'post_id' => $post_id,
    'category' => $category_slug,
    'template' => $template
  ]);
}

// QA events
function emit_qa_result($post_id, $result, $failures = 0, $warnings = 0) {
  // result should be 'pass', 'fail', or 'warn'
  $metric = 'qa_' . $result;
  $category_slug = null;

  // Get post category for dimension
  $categories = get_the_category($post_id);
  if (!empty($categories)) {
    $category_slug = $categories[0]->slug;
  }

  inc($metric, $category_slug);

  $event_params = [
    'post_id' => $post_id,
    'category' => $category_slug,
  ];

  if ($result === 'fail') {
    $event_params['failures'] = $failures;
    emit_ga4_event('qa_fail', $event_params);
  } elseif ($result === 'warn') {
    $event_params['warnings'] = $warnings;
    emit_ga4_event('qa_warn', $event_params);
  } elseif ($result === 'pass') {
    emit_ga4_event('qa_pass', $event_params);
  }
}

function emit_qa_override($post_id, $role = 'unknown') {
  $category_slug = null;
  $categories = get_the_category($post_id);
  if (!empty($categories)) {
    $category_slug = $categories[0]->slug;
  }

  inc('qa_override', $category_slug, $role);
  emit_ga4_event('qa_override', [
    'post_id' => $post_id,
    'category' => $category_slug,
    'role' => $role
  ]);
}

// Recirculation events
function emit_recirculation_impression($post_id, $block = 'related_in_hub', $items = 1) {
  inc('rc_impression', $block);
  emit_ga4_event('recirculation_impression', [
    'post_id' => $post_id,
    'block' => $block,
    'items' => $items
  ]);
}

function emit_recirculation_click($from_post_id, $to_post_id, $block = 'related_in_hub') {
  inc('rc_click', $block);
  emit_ga4_event('recirculation_click', [
    'from_post_id' => $from_post_id,
    'to_post_id' => $to_post_id,
    'block' => $block
  ]);

  // Update inbound count for destination post
  $current_count = get_post_meta($to_post_id, '_cnp_inbound_count', true) ?: 0;
  update_post_meta($to_post_id, '_cnp_inbound_count', $current_count + 1);
}

// Internal linking events
function emit_internal_links_inserted($post_id, $count = 1) {
  if ($count <= 0) return;

  $category_slug = null;
  $categories = get_the_category($post_id);
  if (!empty($categories)) {
    $category_slug = $categories[0]->slug;
  }

  inc('links_inserted', $category_slug, null, $count);
  emit_ga4_event('internal_links_inserted', [
    'post_id' => $post_id,
    'category' => $category_slug,
    'count' => $count
  ]);
}

// Newsletter signup
function emit_newsletter_signup($source = 'unknown') {
  inc('newsletter_signup', $source);
  emit_ga4_event('newsletter_signup', [
    'source' => $source
  ]);
}

// Deep scroll tracking
function emit_deep_scroll($post_id, $depth = 75) {
  $category_slug = null;
  $categories = get_the_category($post_id);
  if (!empty($categories)) {
    $category_slug = $categories[0]->slug;
  }

  inc('deep_scroll_' . $depth, $category_slug);
  emit_ga4_event('deep_scroll', [
    'post_id' => $post_id,
    'category' => $category_slug,
    'depth' => $depth
  ]);
}

// CTR calculation and caching
function update_post_rc_ctr($post_id, $days = 7) {
  global $wpdb;

  $table = $wpdb->prefix . 'cnp_metrics_daily';
  $start_date = date('Y-m-d', strtotime("-{$days} days"));
  $end_date = current_time('Y-m-d');

  // Get impressions and clicks for this post (as destination)
  $impressions = $wpdb->get_var($wpdb->prepare(
    "SELECT SUM(value) FROM $table
     WHERE metric = 'rc_impression'
     AND date BETWEEN %s AND %s",
    $start_date, $end_date
  ));

  $clicks = $wpdb->get_var($wpdb->prepare(
    "SELECT SUM(value) FROM $table
     WHERE metric = 'rc_click'
     AND date BETWEEN %s AND %s",
    $start_date, $end_date
  ));

  $ctr = 0;
  if ($impressions > 0) {
    $ctr = round(($clicks / $impressions) * 100, 2);
  }

  update_post_meta($post_id, '_cnp_rc_ctr_' . $days . 'd', $ctr);
  return $ctr;
}

// Batch CTR recalculation
function rebuild_all_rc_ctr($days = 7) {
  global $wpdb;

  $posts_table = $wpdb->posts;

  // Get all posts with recirculation tracking
  $posts = $wpdb->get_col(
    "SELECT ID FROM $posts_table
     WHERE post_type = 'post'
     AND post_status = 'publish'"
  );

  $updated = 0;
  foreach ($posts as $post_id) {
    update_post_rc_ctr($post_id, $days);
    $updated++;
  }

  return $updated;
}

// Metrics query helpers
function get_metrics_sum($metric, $days = 30, $dim1 = null, $dim2 = null) {
  global $wpdb;

  $table = $wpdb->prefix . 'cnp_metrics_daily';
  $start_date = date('Y-m-d', strtotime("-{$days} days"));

  $where_parts = ["metric = %s", "date >= %s"];
  $params = [$metric, $start_date];

  if ($dim1 !== null) {
    $where_parts[] = "dim1 = %s";
    $params[] = $dim1;
  }

  if ($dim2 !== null) {
    $where_parts[] = "dim2 = %s";
    $params[] = $dim2;
  }

  $where_clause = implode(' AND ', $where_parts);

  return $wpdb->get_var($wpdb->prepare(
    "SELECT SUM(value) FROM $table WHERE $where_clause",
    $params
  )) ?: 0;
}

function get_metrics_by_dimension($metric, $dimension = 'dim1', $days = 30, $limit = 10) {
  global $wpdb;

  $table = $wpdb->prefix . 'cnp_metrics_daily';
  $start_date = date('Y-m-d', strtotime("-{$days} days"));

  $results = $wpdb->get_results($wpdb->prepare(
    "SELECT $dimension, SUM(value) as total
     FROM $table
     WHERE metric = %s AND date >= %s AND $dimension IS NOT NULL
     GROUP BY $dimension
     ORDER BY total DESC
     LIMIT %d",
    $metric, $start_date, $limit
  ));

  return $results ?: [];
}

// Export metrics to CSV
function export_metrics_csv($metric, $days = 90) {
  global $wpdb;

  $table = $wpdb->prefix . 'cnp_metrics_daily';
  $start_date = date('Y-m-d', strtotime("-{$days} days"));

  $results = $wpdb->get_results($wpdb->prepare(
    "SELECT date, metric, dim1, dim2, value
     FROM $table
     WHERE metric = %s AND date >= %s
     ORDER BY date ASC",
    $metric, $start_date
  ));

  // Output CSV headers
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename="' . $metric . '-metrics-' . date('Y-m-d') . '.csv"');

  $output = fopen('php://output', 'w');

  // CSV header
  fputcsv($output, ['date', 'metric', 'dim1', 'dim2', 'value']);

  // Data rows
  foreach ($results as $row) {
    fputcsv($output, [
      $row->date,
      $row->metric,
      $row->dim1,
      $row->dim2,
      $row->value
    ]);
  }

  fclose($output);
  exit;
}

// Purge old metrics
function purge_old_metrics($older_than_days = 180) {
  global $wpdb;

  $table = $wpdb->prefix . 'cnp_metrics_daily';
  $cutoff_date = date('Y-m-d', strtotime("-{$older_than_days} days"));

  $deleted = $wpdb->query($wpdb->prepare(
    "DELETE FROM $table WHERE date < %s",
    $cutoff_date
  ));

  return $deleted;
}

// Daily maintenance cron
add_action('cnp_automation_daily_maintenance', function() {
  $opts = get_option('cnp_automation_settings', []);

  // Purge old metrics
  $retention_days = $opts['metrics_retention_days'] ?? 180;
  purge_old_metrics($retention_days);

  // Rebuild CTR caches
  rebuild_all_rc_ctr(7);
});
