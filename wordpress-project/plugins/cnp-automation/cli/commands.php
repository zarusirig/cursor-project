<?php

namespace CNP\AUT\CLI;

if (!defined('ABSPATH')) exit;

use CNP\AUT\Jobs as Jobs;
use CNP\AUT\Queue as Queue;
use CNP\AUT\Utils as Utils;
use CNP\AUT\Entities as Entities;
use CNP\AUT\Linking as Linking;

if (defined('WP_CLI') && WP_CLI) {

  \WP_CLI::add_command('cnp:health', function(){
    \WP_CLI::success('CNP Automation OK v'.CNP_AUT_VER);
  });

  \WP_CLI::add_command('cnp:jobs:enqueue', function($args, $assoc_args){
    $file = isset($assoc_args['file']) ? $assoc_args['file'] : null;

    if (!$file || !file_exists($file)) {
      \WP_CLI::error('Please provide a valid file path with --file parameter');
      return;
    }

    $json_content = file_get_contents($file);
    $briefs = json_decode($json_content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
      \WP_CLI::error('Invalid JSON file: ' . json_last_error_msg());
      return;
    }

    if (!is_array($briefs)) {
      \WP_CLI::error('JSON file must contain an array of briefs');
      return;
    }

    $job_ids = [];
    $errors = [];

    foreach ($briefs as $index => $brief) {
      if (!is_array($brief)) {
        $errors[] = "Item at index {$index} is not an object";
        continue;
      }

      $job_id = Jobs\create_job_from_brief($brief);

      if (is_wp_error($job_id)) {
        $errors[] = "Brief {$index}: " . $job_id->get_error_message();
      } else {
        $job_ids[] = $job_id;
      }
    }

    if (!empty($job_ids)) {
      \WP_CLI::success('Enqueued ' . count($job_ids) . ' jobs: ' . implode(', ', $job_ids));
    }

    if (!empty($errors)) {
      foreach ($errors as $error) {
        \WP_CLI::warning($error);
      }
    }
  });

  \WP_CLI::add_command('cnp:jobs:run', function($args, $assoc_args){
    $max = absint(isset($assoc_args['max']) ? $assoc_args['max'] : 3);

    \WP_CLI::log("Processing up to {$max} queued jobs...");

    $processed = Jobs\process_queue($max);

    if ($processed > 0) {
      \WP_CLI::success("Processed {$processed} jobs");
    } else {
      \WP_CLI::log('No jobs to process');
    }
  });

  \WP_CLI::add_command('cnp:jobs:list', function($args, $assoc_args){
    $status = isset($assoc_args['status']) ? $assoc_args['status'] : null;
    $limit = absint(isset($assoc_args['limit']) ? $assoc_args['limit'] : 50);

    if ($status) {
      $jobs = Queue\get_jobs_by_status($status, $limit);
      $total = Queue\count_jobs_by_status($status);
    } else {
      $jobs = Queue\get_recent_jobs($limit);
      $total = Queue\count_jobs_by_status();
    }

    if (empty($jobs)) {
      \WP_CLI::log('No jobs found');
      return;
    }

    $table = [];
    foreach ($jobs as $job) {
      $table[] = [
        'ID' => $job['id'],
        'Status' => $job['status'],
        'Post ID' => $job['post_id'] ?: '-',
        'Title' => isset($job['brief']['title']) ? $job['brief']['title'] : '-',
        'Created' => $job['created_at'],
        'Updated' => $job['updated_at'],
      ];
    }

    \WP_CLI\Utils\format_items('table', $table, ['ID', 'Status', 'Post ID', 'Title', 'Created', 'Updated']);

    if ($total > $limit) {
      \WP_CLI::log("Showing {$limit} of {$total} jobs");
    }
  });

  \WP_CLI::add_command('cnp:jobs:retry', function($args, $assoc_args){
    $job_id = absint(isset($assoc_args['id']) ? $assoc_args['id'] : 0);

    if (!$job_id) {
      \WP_CLI::error('Please provide a job ID with --id parameter');
      return;
    }

    $result = Jobs\retry_job($job_id);

    if (is_wp_error($result)) {
      \WP_CLI::error($result->get_error_message());
      return;
    }

    \WP_CLI::success("Job {$job_id} has been reset to queued status");
  });

  \WP_CLI::add_command('cnp:jobs:cancel', function($args, $assoc_args){
    $job_id = absint(isset($assoc_args['id']) ? $assoc_args['id'] : 0);

    if (!$job_id) {
      \WP_CLI::error('Please provide a job ID with --id parameter');
      return;
    }

    $result = Jobs\cancel_job($job_id);

    if (is_wp_error($result)) {
      \WP_CLI::error($result->get_error_message());
      return;
    }

    \WP_CLI::success("Job {$job_id} has been cancelled");
  });

  \WP_CLI::add_command('cnp:jobs:stats', function(){
    $stats = [
      'queued' => Queue\count_jobs_by_status('queued'),
      'generating' => Queue\count_jobs_by_status('generating'),
      'draft_created' => Queue\count_jobs_by_status('draft_created'),
      'needs_review' => Queue\count_jobs_by_status('needs_review'),
      'published' => Queue\count_jobs_by_status('published'),
      'failed' => Queue\count_jobs_by_status('failed'),
      'cancelled' => Queue\count_jobs_by_status('cancelled'),
    ];

    $total = array_sum($stats);

    \WP_CLI::log("Job Statistics (Total: {$total})");
    \WP_CLI::log(str_repeat('-', 40));

    foreach ($stats as $status => $count) {
      $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
      \WP_CLI::log(sprintf("%-15s %5d (%5.1f%%)", ucfirst($status), $count, $percentage));
    }

    // Rate limiting info
    $jobs_last_hour = Queue\count_jobs_started_last_hour();
    $max_per_hour = Utils\setting('max_generate_per_hour', 20);
    $remaining = max(0, $max_per_hour - $jobs_last_hour);

    \WP_CLI::log('');
    \WP_CLI::log("Rate Limiting: {$jobs_last_hour}/{$max_per_hour} jobs started in last hour ({$remaining} remaining)");
  });

  \WP_CLI::add_command('cnp:entities:rebuild', function(){
    \WP_CLI::log('Rebuilding entities for all published posts...');

    $count = Entities\rebuild_all_entities();

    \WP_CLI::success("Processed {$count} posts and rebuilt entity index");
  });

  \WP_CLI::add_command('cnp:entities:stats', function($args, $assoc_args){
    $entity = isset($assoc_args['entity']) ? $assoc_args['entity'] : null;

    if (!$entity) {
      \WP_CLI::error('Please provide an entity with --entity parameter');
      return;
    }

    global $wpdb;
    $index_table = $wpdb->prefix . 'cnp_entity_index';

    $results = $wpdb->get_results($wpdb->prepare(
      "SELECT post_id, score FROM $index_table WHERE entity_key = %s ORDER BY score DESC LIMIT 20",
      Entities\normalize_entity_key($entity)
    ));

    if (empty($results)) {
      \WP_CLI::log("No posts found for entity: {$entity}");
      return;
    }

    \WP_CLI::log("Top posts for entity '{$entity}':");
    \WP_CLI::log(str_repeat('-', 50));

    foreach ($results as $row) {
      $post = get_post($row->post_id);
      if ($post) {
        \WP_CLI::log(sprintf("%-5d %-40s %.3f", $post->ID, substr($post->post_title, 0, 40), $row->score));
      }
    }
  });

  \WP_CLI::add_command('cnp:link:suggest', function($args, $assoc_args){
    $post_id = absint(isset($assoc_args['post']) ? $assoc_args['post'] : 0);
    $max = absint(isset($assoc_args['max']) ? $assoc_args['max'] : 10);

    if (!$post_id) {
      \WP_CLI::error('Please provide a post ID with --post parameter');
      return;
    }

    $post = get_post($post_id);
    if (!$post) {
      \WP_CLI::error('Post not found');
      return;
    }

    $suggestions = Linking\get_link_suggestions($post_id, $max);

    if (empty($suggestions)) {
      \WP_CLI::log('No suggestions found');
      return;
    }

    \WP_CLI::log("Link suggestions for: {$post->post_title}");
    \WP_CLI::log(str_repeat('=', 60));

    foreach ($suggestions as $suggestion) {
      \WP_CLI::log("Type: {$suggestion['type']}");
      \WP_CLI::log("Title: {$suggestion['title']}");
      \WP_CLI::log("URL: {$suggestion['url']}");
      \WP_CLI::log("Score: " . number_format($suggestion['score'], 3));
      \WP_CLI::log("Entities: " . implode(', ', $suggestion['entities_matched']));
      \WP_CLI::log(str_repeat('-', 40));
    }
  });

  \WP_CLI::add_command('cnp:link:insert', function($args, $assoc_args){
    $post_id = absint(isset($assoc_args['post']) ? $assoc_args['post'] : 0);
    $apply = isset($assoc_args['apply']);

    if (!$post_id) {
      \WP_CLI::error('Please provide a post ID with --post parameter');
      return;
    }

    $post = get_post($post_id);
    if (!$post) {
      \WP_CLI::error('Post not found');
      return;
    }

    if ($apply) {
      // Get suggestions and insert top ones automatically
      $suggestions = Linking\get_link_suggestions($post_id, 5); // Get top 5

      if (empty($suggestions)) {
        \WP_CLI::log('No suggestions available');
        return;
      }

      $inserts = [];
      foreach ($suggestions as $suggestion) {
        $anchors = Linking\generate_anchor_suggestions($post_id, [$suggestion]);
        if (!empty($anchors)) {
          $anchor = $anchors[0];
          $inserts[] = [
            'suggestion_id' => $suggestion['id'],
            'anchor_text' => $anchor['anchor_text'],
            'section' => $anchor['anchor_section'],
          ];
        }
      }

      if (empty($inserts)) {
        \WP_CLI::log('No valid anchors found');
        return;
      }

      $result = Linking\insert_links($post_id, $inserts);

      if (is_wp_error($result)) {
        \WP_CLI::error($result->get_error_message());
        return;
      }

      \WP_CLI::success("Inserted {$result['inserted']} links, skipped {$result['skipped']}");
    } else {
      \WP_CLI::log('Preview mode - use --apply to actually insert links');
      \WP_CLI::log('Run: wp cnp:link:suggest --post=' . $post_id . ' --max=5');
      \WP_CLI::log('Then manually insert with the REST API or editor UI');
    }
  });

  \WP_CLI::add_command('cnp:link:purge', function($args, $assoc_args){
    $url = isset($assoc_args['url']) ? $assoc_args['url'] : null;

    if (!$url) {
      \WP_CLI::error('Please provide a URL with --url parameter');
      return;
    }

    global $wpdb;
    $posts_table = $wpdb->posts;

    // Find all published posts
    $posts = $wpdb->get_results("SELECT ID, post_content FROM $posts_table WHERE post_type = 'post' AND post_status = 'publish'");

    $updated = 0;
    $total_links = 0;

    foreach ($posts as $post) {
      $original_content = $post->post_content;
      $new_content = $original_content;

      // Remove links to the specified URL
      $pattern = '/<a[^>]*href=["\']' . preg_quote($url, '/') . '["\'][^>]*>(.*?)<\/a>/i';
      $new_content = preg_replace($pattern, '$1', $new_content);

      if ($new_content !== $original_content) {
        // Count how many links were removed
        preg_match_all($pattern, $original_content, $matches);
        $links_removed = count($matches[0]);
        $total_links += $links_removed;

        wp_update_post([
          'ID' => $post->ID,
          'post_content' => $new_content,
        ]);

        $updated++;
        \WP_CLI::log("Updated post {$post->ID}: removed {$links_removed} links");
      }
    }

    if ($updated > 0) {
      \WP_CLI::success("Updated {$updated} posts, removed {$total_links} total links to {$url}");
    } else {
      \WP_CLI::log('No posts found with links to the specified URL');
    }
  });

  // Metrics export command
  \WP_CLI::add_command('cnp:metrics:export', function($args, $assoc_args) {
    $metric = isset($assoc_args['metric']) ? $assoc_args['metric'] : null;
    $range = intval(isset($assoc_args['range']) ? $assoc_args['range'] : 90);
    $out = isset($assoc_args['out']) ? $assoc_args['out'] : null;

    if (!$metric) {
      \WP_CLI::error('Please specify a metric with --metric parameter');
      return;
    }

    if ($out) {
      // Redirect output to file
      ob_start();
      \CNP\AUT\Metrics\export_metrics_csv($metric, $range);
      $csv_content = ob_get_clean();

      if (file_put_contents($out, $csv_content) === false) {
        \WP_CLI::error("Failed to write to file: {$out}");
        return;
      }

      \WP_CLI::success("Exported {$metric} metrics to {$out}");
    } else {
      \WP_CLI::log("Exporting {$metric} metrics for last {$range} days...");
      \CNP\AUT\Metrics\export_metrics_csv($metric, $range);
    }
  });

  // Rebuild CTR command
  \WP_CLI::add_command('cnp:metrics:rebuild-ctr', function($args, $assoc_args) {
    $range = intval(isset($assoc_args['range']) ? $assoc_args['range'] : 7);

    \WP_CLI::log("Rebuilding CTR caches for {$range}-day windows...");

    $updated = \CNP\AUT\Metrics\rebuild_all_rc_ctr($range);

    \WP_CLI::success("Updated CTR for {$updated} posts");
  });

  // Purge old metrics command
  \WP_CLI::add_command('cnp:metrics:purge', function($args, $assoc_args) {
    $older_than = intval(isset($assoc_args['older-than']) ? $assoc_args['older-than'] : 365);

    \WP_CLI::log("Purging metrics older than {$older_than} days...");

    $deleted = \CNP\AUT\Metrics\purge_old_metrics($older_than);

    \WP_CLI::success("Deleted {$deleted} old metric records");
  });

  // Show metrics summary command
  \WP_CLI::add_command('cnp:metrics:status', function() {
    global $wpdb;

    $table = $wpdb->prefix . 'cnp_metrics_daily';

    // Get total records
    $total = $wpdb->get_var("SELECT COUNT(*) FROM $table");
    \WP_CLI::log("Total metric records: {$total}");

    // Get date range
    $date_range = $wpdb->get_row("SELECT MIN(date) as oldest, MAX(date) as newest FROM $table");
    if ($date_range) {
      \WP_CLI::log("Date range: {$date_range->oldest} to {$date_range->newest}");
    }

    // Get metrics by type
    $metrics = $wpdb->get_results("SELECT metric, COUNT(*) as count FROM $table GROUP BY metric ORDER BY count DESC");
    if ($metrics) {
      \WP_CLI::log("\nMetrics by type:");
      foreach ($metrics as $metric) {
        \WP_CLI::log("  {$metric->metric}: {$metric->count} records");
      }
    }

    // Get recent activity (last 7 days)
    $recent = $wpdb->get_var($wpdb->prepare(
      "SELECT SUM(value) FROM $table WHERE date >= %s",
      date('Y-m-d', strtotime('-7 days'))
    ));

    \WP_CLI::log("\nRecent activity (last 7 days): {$recent} total events");
  });

}
