<?php

/**

 * Plugin Name: CNP Automation

 * Description: Content automation for cnpnews — generator queue, entities, internal linking, QA gates.

 * Author: CNP News

 * Version: 0.1.0

 * Requires at least: 6.6

 * Requires PHP: 8.0

 * Text Domain: cnp-automation

 */



if (!defined('ABSPATH')) exit;

define('CNP_AUT_PATH', plugin_dir_path(__FILE__));

define('CNP_AUT_URL', plugin_dir_url(__FILE__));

define('CNP_AUT_VER', '0.1.0');



require_once CNP_AUT_PATH . 'inc/bootstrap.php';

// Recirculation click tracking redirect endpoint
add_action('init', function() {
  add_rewrite_rule('^cnp/rc/?$', 'index.php?cnp_rc_redirect=1', 'top');
});

add_filter('query_vars', function($vars) {
  $vars[] = 'cnp_rc_redirect';
  return $vars;
});

add_action('template_redirect', function() {
  if (get_query_var('cnp_rc_redirect')) {
    // Get parameters
    $from_post_id = intval(isset($_GET['from']) ? $_GET['from'] : 0);
    $to_post_id = intval(isset($_GET['to']) ? $_GET['to'] : 0);
    $block = sanitize_text_field(isset($_GET['b']) ? $_GET['b'] : 'related_in_hub');

    // Basic validation
    if (!$from_post_id || !$to_post_id || $from_post_id === $to_post_id) {
      wp_die('Invalid request', 400);
    }

    // Check if posts exist and are published
    $from_post = get_post($from_post_id);
    $to_post = get_post($to_post_id);

    if (!$from_post || !$to_post || $from_post->post_status !== 'publish' || $to_post->post_status !== 'publish') {
      wp_die('Invalid posts', 400);
    }

    // Record the click
    \CNP\AUT\Metrics\emit_recirculation_click($from_post_id, $to_post_id, $block);

    // Redirect to destination
    wp_redirect(get_permalink($to_post_id), 302);
    exit;
  }
});

register_activation_hook(__FILE__, function(){
  global $wpdb;

  // Create jobs table
  $table_name = $wpdb->prefix . 'cnp_jobs';
  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $table_name (
    id int(11) NOT NULL AUTO_INCREMENT,
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status enum('queued','generating','draft_created','needs_review','published','failed','cancelled') NOT NULL DEFAULT 'queued',
    brief longtext NOT NULL,
    post_id int(11) NULL,
    error text NULL,
    locks varchar(255) NULL,
    started_at datetime NULL,
    finished_at datetime NULL,
    priority int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY status_idx (status),
    KEY created_at_idx (created_at),
    KEY priority_idx (priority, created_at)
  ) $charset_collate;";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);

  // Create entities table
  $entities_table = $wpdb->prefix . 'cnp_entities';
  $entities_sql = "CREATE TABLE $entities_table (
    id int(11) NOT NULL AUTO_INCREMENT,
    post_id int(11) NOT NULL,
    entity_key varchar(255) NOT NULL,
    entity_label varchar(255) NOT NULL,
    weight float NOT NULL DEFAULT 0,
    first_seen datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_seen datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY post_entity (post_id, entity_key),
    KEY entity_key_idx (entity_key),
    KEY post_id_idx (post_id),
    KEY weight_idx (weight)
  ) $charset_collate;";
  dbDelta($entities_sql);

  // Create entity index table
  $entity_index_table = $wpdb->prefix . 'cnp_entity_index';
  $entity_index_sql = "CREATE TABLE $entity_index_table (
    entity_key varchar(255) NOT NULL,
    post_id int(11) NOT NULL,
    score float NOT NULL DEFAULT 0,
    updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (entity_key, post_id),
    KEY post_id_idx (post_id),
    KEY score_idx (score)
  ) $charset_collate;";
    dbDelta($entity_index_sql);

    // Create metrics daily table
    $metrics_table = $wpdb->prefix . 'cnp_metrics_daily';
    $metrics_sql = "CREATE TABLE $metrics_table (
      id bigint(20) NOT NULL AUTO_INCREMENT,
      date date NOT NULL,
      metric varchar(100) NOT NULL,
      dim1 varchar(255) NULL,
      dim2 varchar(255) NULL,
      value bigint(20) NOT NULL DEFAULT 0,
      updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (id),
      UNIQUE KEY unique_metric_date (date, metric, dim1, dim2),
      KEY date_metric_idx (date, metric),
      KEY metric_idx (metric)
    ) $charset_collate;";
    dbDelta($metrics_sql);

  // Initialize default settings
  add_option('cnp_automation_settings', [
    'api_enabled' => 1,
    'default_status' => 'draft',
    'default_author' => 0,
    'default_category' => 0,
    'default_tags' => '',
    'default_language' => 'en',
    'auto_insert_ai_disclosure' => 1,
    'require_sources_if_claims' => 1,
    'max_generate_per_hour' => 20,
    'webhook_job_status_url' => '',
    'notify_email' => '',
    'linker_enabled' => 1,
    'linker_min_post_age_days' => 2,
    'linker_max_suggestions' => 10,
    'linker_per_section_cap' => 2,
    'linker_anchor_min_chars' => 18,
    'linker_blacklist_terms' => "click here\nread more\nlearn more\nfind out more\nsee more\ncheck it out",
    'pillar_pages_map' => '{}',

    // Analytics settings
    'ga4_enable_admin_events' => 1,
    'ga4_measurement_id' => '',
    'ga4_debug_mode' => 0,
    'metrics_retention_days' => 180,
    'dashboard_sample_pct' => 100,
  ]);

  // Schedule cron job for processing queue
  if (!wp_next_scheduled('cnp_automation_process_queue')) {
    wp_schedule_event(time(), 'cnp_automation_frequent', 'cnp_automation_process_queue');
  }

  // Schedule daily maintenance cron
  if (!wp_next_scheduled('cnp_automation_daily_maintenance')) {
    wp_schedule_event(time(), 'daily', 'cnp_automation_daily_maintenance');
  }
});



register_deactivation_hook(__FILE__, function(){});
