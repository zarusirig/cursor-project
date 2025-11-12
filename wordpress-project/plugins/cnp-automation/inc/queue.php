<?php

namespace CNP\AUT\Queue;

if (!defined('ABSPATH')) exit;

// Valid status transitions
const VALID_TRANSITIONS = [
  'queued' => ['generating', 'cancelled'],
  'generating' => ['draft_created', 'failed'],
  'draft_created' => ['needs_review', 'failed'],
  'needs_review' => ['published', 'failed'],
  'published' => [],
  'failed' => ['queued'], // Allow retry
  'cancelled' => [],
];

function get_table_name() {
  global $wpdb;
  return $wpdb->prefix . 'cnp_jobs';
}

function create_job($brief, $priority = 0) {
  global $wpdb;
  $table = get_table_name();

  $result = $wpdb->insert($table, [
    'brief' => wp_json_encode($brief),
    'priority' => $priority,
    'created_at' => current_time('mysql'),
    'updated_at' => current_time('mysql'),
  ]);

  if ($result === false) {
    return new \WP_Error('db_error', 'Failed to create job: ' . $wpdb->last_error);
  }

  return $wpdb->insert_id;
}

function get_job($job_id) {
  global $wpdb;
  $table = get_table_name();

  $row = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM $table WHERE id = %d",
    $job_id
  ), ARRAY_A);

  if (!$row) {
    return null;
  }

  $row['brief'] = json_decode($row['brief'], true);
  return $row;
}

function update_job($job_id, $updates) {
  global $wpdb;
  $table = get_table_name();

  $updates['updated_at'] = current_time('mysql');

  $result = $wpdb->update($table, $updates, ['id' => $job_id]);

  if ($result === false) {
    return new \WP_Error('db_error', 'Failed to update job: ' . $wpdb->last_error);
  }

  return true;
}

function transition_status($job_id, $new_status, $additional_updates = []) {
  global $wpdb;
  $table = get_table_name();

  // Get current job
  $job = get_job($job_id);
  if (!$job) {
    return new \WP_Error('not_found', 'Job not found');
  }

  // Validate transition
  if (!isset(VALID_TRANSITIONS[$job['status']]) || !in_array($new_status, VALID_TRANSITIONS[$job['status']])) {
    return new \WP_Error('invalid_transition', "Cannot transition from {$job['status']} to $new_status");
  }

  $updates = array_merge($additional_updates, [
    'status' => $new_status,
    'updated_at' => current_time('mysql'),
  ]);

  // Set timestamps based on status
  if ($new_status === 'generating' && !$job['started_at']) {
    $updates['started_at'] = current_time('mysql');
  } elseif (in_array($new_status, ['draft_created', 'needs_review', 'published', 'failed', 'cancelled'])) {
    $updates['finished_at'] = current_time('mysql');
  }

  $result = $wpdb->update($table, $updates, ['id' => $job_id]);

  if ($result === false) {
    return new \WP_Error('db_error', 'Failed to update job status: ' . $wpdb->last_error);
  }

  // Trigger webhook and notifications
  do_action('cnp_automation_job_status_changed', $job_id, $new_status, $job);

  return true;
}

function lock_job($job_id, $worker_id = null) {
  global $wpdb;
  $table = get_table_name();

  if (!$worker_id) {
    $worker_id = uniqid('worker_', true);
  }

  // Try to lock the job atomically
  $result = $wpdb->query($wpdb->prepare(
    "UPDATE $table SET locks = %s, status = 'generating', started_at = %s, updated_at = %s
     WHERE id = %d AND status = 'queued' AND (locks IS NULL OR locks = '')",
    $worker_id,
    current_time('mysql'),
    current_time('mysql'),
    $job_id
  ));

  return $result > 0;
}

function unlock_job($job_id) {
  global $wpdb;
  $table = get_table_name();

  return $wpdb->update($table, ['locks' => null], ['id' => $job_id]) !== false;
}

function get_queued_jobs($limit = 10, $priority_first = true) {
  global $wpdb;
  $table = get_table_name();

  $order_by = $priority_first ? 'priority DESC, created_at ASC' : 'created_at ASC';

  return $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $table WHERE status = 'queued' AND (locks IS NULL OR locks = '')
     ORDER BY $order_by LIMIT %d",
    $limit
  ), ARRAY_A);
}

function get_jobs_by_status($status, $limit = 50, $offset = 0) {
  global $wpdb;
  $table = get_table_name();

  return $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $table WHERE status = %s ORDER BY updated_at DESC LIMIT %d OFFSET %d",
    $status,
    $limit,
    $offset
  ), ARRAY_A);
}

function count_jobs_by_status($status = null) {
  global $wpdb;
  $table = get_table_name();

  if ($status) {
    return (int) $wpdb->get_var($wpdb->prepare(
      "SELECT COUNT(*) FROM $table WHERE status = %s",
      $status
    ));
  }

  return (int) $wpdb->get_var("SELECT COUNT(*) FROM $table");
}

function get_recent_jobs($limit = 50, $offset = 0) {
  global $wpdb;
  $table = get_table_name();

  $results = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $table ORDER BY updated_at DESC LIMIT %d OFFSET %d",
    $limit,
    $offset
  ), ARRAY_A);

  // Decode brief JSON for each job
  foreach ($results as &$job) {
    $job['brief'] = json_decode($job['brief'], true);
  }

  return $results;
}

function count_jobs_started_last_hour() {
  global $wpdb;
  $table = get_table_name();

  $hour_ago = date('Y-m-d H:i:s', strtotime('-1 hour'));

  return (int) $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM $table WHERE status IN ('generating', 'draft_created', 'needs_review', 'published')
     AND started_at >= %s",
    $hour_ago
  ));
}
