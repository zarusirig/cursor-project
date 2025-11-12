<?php

namespace CNP\AUT\Admin;

if (!defined('ABSPATH')) exit;

// Handle form submissions
add_action('admin_post_cnp_automation_save', function(){
  if (!current_user_can('manage_options') || !wp_verify_nonce(isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : '', 'cnp_automation_settings')) {
    wp_die('Security check failed');
  }

  $opts = get_option('cnp_automation_settings', []);

  // API & Security
  $opts['api_enabled'] = isset($_POST['api_enabled']) ? 1 : 0;
  $opts['api_keys'] = sanitize_textarea_field(isset($_POST['api_keys']) ? $_POST['api_keys'] : '');

  // Defaults for Generated Posts
  $opts['default_status'] = in_array(isset($_POST['default_status']) ? $_POST['default_status'] : 'draft', ['draft', 'pending']) ? $_POST['default_status'] : 'draft';
  $opts['default_author'] = absint(isset($_POST['default_author']) ? $_POST['default_author'] : 0);
  $opts['default_category'] = absint(isset($_POST['default_category']) ? $_POST['default_category'] : 0);
  $opts['default_tags'] = sanitize_text_field(isset($_POST['default_tags']) ? $_POST['default_tags'] : '');
  $opts['default_language'] = sanitize_text_field(isset($_POST['default_language']) ? $_POST['default_language'] : 'en');

  // AI Disclosure & Policy
  $opts['auto_insert_ai_disclosure'] = isset($_POST['auto_insert_ai_disclosure']) ? 1 : 0;
  $opts['ai_disclosure_block'] = wp_kses_post(isset($_POST['ai_disclosure_block']) ? $_POST['ai_disclosure_block'] : '');
  $opts['require_sources_if_claims'] = isset($_POST['require_sources_if_claims']) ? 1 : 0;
  $opts['max_generate_per_hour'] = max(1, absint(isset($_POST['max_generate_per_hour']) ? $_POST['max_generate_per_hour'] : 20));

  // Webhooks & Notifications
  $opts['webhook_job_status_url'] = esc_url_raw(isset($_POST['webhook_job_status_url']) ? $_POST['webhook_job_status_url'] : '');
  $opts['notify_email'] = sanitize_email(isset($_POST['notify_email']) ? $_POST['notify_email'] : '');

  // Internal Linking
  $opts['linker_enabled'] = isset($_POST['linker_enabled']) ? 1 : 0;
  $opts['linker_min_post_age_days'] = max(0, absint(isset($_POST['linker_min_post_age_days']) ? $_POST['linker_min_post_age_days'] : 2));
  $opts['linker_max_suggestions'] = max(1, absint(isset($_POST['linker_max_suggestions']) ? $_POST['linker_max_suggestions'] : 10));
  $opts['linker_per_section_cap'] = max(1, absint(isset($_POST['linker_per_section_cap']) ? $_POST['linker_per_section_cap'] : 2));
  $opts['linker_anchor_min_chars'] = max(1, absint(isset($_POST['linker_anchor_min_chars']) ? $_POST['linker_anchor_min_chars'] : 18));
  $opts['linker_blacklist_terms'] = sanitize_textarea_field(isset($_POST['linker_blacklist_terms']) ? $_POST['linker_blacklist_terms'] : '');
    $opts['pillar_pages_map'] = wp_json_encode(array_filter(explode("\n", sanitize_textarea_field(isset($_POST['pillar_pages_map']) ? $_POST['pillar_pages_map'] : ''))));

    // Analytics settings
    $opts['ga4_enable_admin_events'] = isset($_POST['ga4_enable_admin_events']) ? 1 : 0;
    $opts['ga4_measurement_id'] = sanitize_text_field(isset($_POST['ga4_measurement_id']) ? $_POST['ga4_measurement_id'] : '');
    $opts['ga4_debug_mode'] = isset($_POST['ga4_debug_mode']) ? 1 : 0;
    $opts['metrics_retention_days'] = max(30, absint(isset($_POST['metrics_retention_days']) ? $_POST['metrics_retention_days'] : 180));
    $opts['dashboard_sample_pct'] = max(1, min(100, absint(isset($_POST['dashboard_sample_pct']) ? $_POST['dashboard_sample_pct'] : 100)));

  update_option('cnp_automation_settings', $opts);

  wp_redirect(add_query_arg('updated', '1', wp_get_referer()));
  exit;
});

add_action('admin_menu', function(){
  // Reuse the top-level CNP menu created by SEO plugin if present
  if (!isset($GLOBALS['menu_cnp_registered'])){
    add_menu_page('CNP', 'CNP', 'manage_options', 'cnp', '__return_null', 'dashicons-admin-site', 58);
    $GLOBALS['menu_cnp_registered'] = true;
  }

  add_submenu_page('cnp', 'CNP Automation', 'Automation', 'manage_options', 'cnp-automation', __NAMESPACE__.'\\render_settings');
});

function render_settings(){
  $opts = get_option('cnp_automation_settings', []);
  $categories = get_categories(['hide_empty' => false]);
  $users = get_users(['role__in' => ['administrator', 'editor']]);

  if (isset($_GET['updated'])) {
    echo '<div class="notice notice-success"><p>Settings saved successfully.</p></div>';
  }
  ?>

  <div class="wrap">
    <h1>CNP Automation Settings</h1>
    <p>Configure the content generation pipeline, queue management, and editorial controls.</p>

    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
      <?php wp_nonce_field('cnp_automation_settings'); ?>
      <input type="hidden" name="action" value="cnp_automation_save">

      <!-- API & Security -->
      <h2>API & Security</h2>
      <table class="form-table">
        <tr>
          <th scope="row">Enable API</th>
          <td>
            <label>
              <input type="checkbox" name="api_enabled" value="1" <?php checked(isset($opts['api_enabled']) ? $opts['api_enabled'] : 1); ?>>
              Allow REST API access for job creation and management
            </label>
          </td>
        </tr>
        <tr>
          <th scope="row">API Keys</th>
          <td>
            <textarea name="api_keys" rows="4" cols="50" placeholder="Enter one API key per line"><?php echo esc_textarea(isset($opts['api_keys']) ? $opts['api_keys'] : ''); ?></textarea>
            <p class="description">API keys for Bearer token authentication. One key per line.</p>
          </td>
        </tr>
      </table>

      <!-- Defaults for Generated Posts -->
      <h2>Defaults for Generated Posts</h2>
      <table class="form-table">
        <tr>
          <th scope="row">Default Status</th>
          <td>
            <select name="default_status">
              <option value="draft" <?php selected(isset($opts['default_status']) ? $opts['default_status'] : 'draft', 'draft'); ?>>Draft</option>
              <option value="pending" <?php selected(isset($opts['default_status']) ? $opts['default_status'] : 'draft', 'pending'); ?>>Pending Review</option>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row">Default Author</th>
          <td>
            <select name="default_author">
              <option value="0">Use current user or site admin</option>
              <?php foreach ($users as $user): ?>
                <option value="<?php echo $user->ID; ?>" <?php selected(isset($opts['default_author']) ? $opts['default_author'] : 0, $user->ID); ?>>
                  <?php echo esc_html($user->display_name); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row">Default Category</th>
          <td>
            <select name="default_category">
              <option value="0">None</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat->term_id; ?>" <?php selected(isset($opts['default_category']) ? $opts['default_category'] : 0, $cat->term_id); ?>>
                  <?php echo esc_html($cat->name); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row">Default Tags</th>
          <td>
            <input type="text" name="default_tags" value="<?php echo esc_attr(isset($opts['default_tags']) ? $opts['default_tags'] : ''); ?>" placeholder="tag1, tag2, tag3">
            <p class="description">Comma-separated list of default tags to assign to generated posts.</p>
          </td>
        </tr>
        <tr>
          <th scope="row">Default Language</th>
          <td>
            <input type="text" name="default_language" value="<?php echo esc_attr(isset($opts['default_language']) ? $opts['default_language'] : 'en'); ?>" placeholder="en">
            <p class="description">ISO language code for generated content.</p>
          </td>
        </tr>
      </table>

      <!-- AI Disclosure & Policy -->
      <h2>AI Disclosure & Policy</h2>
      <table class="form-table">
        <tr>
          <th scope="row">Auto-insert AI Disclosure</th>
          <td>
            <label>
              <input type="checkbox" name="auto_insert_ai_disclosure" value="1" <?php checked(isset($opts['auto_insert_ai_disclosure']) ? $opts['auto_insert_ai_disclosure'] : 1); ?>>
              Automatically insert AI disclosure block at the top of generated content
            </label>
          </td>
        </tr>
        <tr>
          <th scope="row">AI Disclosure Block</th>
          <td>
            <textarea name="ai_disclosure_block" rows="6" cols="50"><?php echo esc_textarea(isset($opts['ai_disclosure_block']) ? $opts['ai_disclosure_block'] : ''); ?></textarea>
            <p class="description">HTML/blocks to insert as AI disclosure. Supports basic HTML and WordPress blocks.</p>
          </td>
        </tr>
        <tr>
          <th scope="row">Require Sources for Claims</th>
          <td>
            <label>
              <input type="checkbox" name="require_sources_if_claims" value="1" <?php checked(isset($opts['require_sources_if_claims']) ? $opts['require_sources_if_claims'] : 1); ?>>
              Require sources when content contains factual claims (enforced by QA checklist)
            </label>
          </td>
        </tr>
        <tr>
          <th scope="row">Max Generate per Hour</th>
          <td>
            <input type="number" name="max_generate_per_hour" value="<?php echo esc_attr(isset($opts['max_generate_per_hour']) ? $opts['max_generate_per_hour'] : 20); ?>" min="1" max="100">
            <p class="description">Rate limit: maximum number of jobs that can start generating per hour.</p>
          </td>
        </tr>
      </table>

      <!-- Webhooks & Notifications -->
      <h2>Webhooks & Notifications</h2>
      <table class="form-table">
        <tr>
          <th scope="row">Job Status Webhook URL</th>
          <td>
            <input type="url" name="webhook_job_status_url" value="<?php echo esc_attr(isset($opts['webhook_job_status_url']) ? $opts['webhook_job_status_url'] : ''); ?>" placeholder="https://example.com/webhook">
            <p class="description">POST endpoint to notify when job status changes. Receives JSON payload with job details.</p>
          </td>
        </tr>
        <tr>
          <th scope="row">Notification Email</th>
          <td>
            <input type="email" name="notify_email" value="<?php echo esc_attr(isset($opts['notify_email']) ? $opts['notify_email'] : ''); ?>" placeholder="admin@example.com">
            <p class="description">Email address to receive status summaries and error notifications.</p>
          </td>
        </tr>
      </table>

      <!-- Internal Linking -->
      <h2>Internal Linking</h2>
      <table class="form-table">
        <tr>
          <th scope="row">Enable Internal Linking</th>
          <td>
            <label>
              <input type="checkbox" name="linker_enabled" value="1" <?php checked(isset($opts['linker_enabled']) ? $opts['linker_enabled'] : 1); ?>>
              Enable automatic internal linking suggestions and insertion
            </label>
          </td>
        </tr>
        <tr>
          <th scope="row">Minimum Post Age (days)</th>
          <td>
            <input type="number" name="linker_min_post_age_days" value="<?php echo esc_attr(isset($opts['linker_min_post_age_days']) ? $opts['linker_min_post_age_days'] : 2); ?>" min="0" max="365">
            <p class="description">Avoid suggesting posts published less than this many days ago (stability).</p>
          </td>
        </tr>
        <tr>
          <th scope="row">Max Suggestions</th>
          <td>
            <input type="number" name="linker_max_suggestions" value="<?php echo esc_attr(isset($opts['linker_max_suggestions']) ? $opts['linker_max_suggestions'] : 10); ?>" min="1" max="50">
            <p class="description">Maximum number of link suggestions to return.</p>
          </td>
        </tr>
        <tr>
          <th scope="row">Links per Section Cap</th>
          <td>
            <input type="number" name="linker_per_section_cap" value="<?php echo esc_attr(isset($opts['linker_per_section_cap']) ? $opts['linker_per_section_cap'] : 2); ?>" min="1" max="10">
            <p class="description">Maximum internal links allowed per H2 section.</p>
          </td>
        </tr>
        <tr>
          <th scope="row">Minimum Anchor Length</th>
          <td>
            <input type="number" name="linker_anchor_min_chars" value="<?php echo esc_attr(isset($opts['linker_anchor_min_chars']) ? $opts['linker_anchor_min_chars'] : 18); ?>" min="1" max="100">
            <p class="description">Shortest anchor text length to accept (prevents single-word spam).</p>
          </td>
        </tr>
        <tr>
          <th scope="row">Blacklist Terms</th>
          <td>
            <textarea name="linker_blacklist_terms" rows="4" cols="50"><?php echo esc_textarea(isset($opts['linker_blacklist_terms']) ? $opts['linker_blacklist_terms'] : "click here\nread more\nlearn more\nfind out more\nsee more\ncheck it out"); ?></textarea>
            <p class="description">Terms never to use as anchors (one per line).</p>
          </td>
        </tr>
        <tr>
          <th scope="row">Pillar Pages Map</th>
          <td>
            <textarea name="pillar_pages_map" rows="6" cols="50" placeholder="category-slug: /pillar-url&#10;another-category: /another-pillar"><?php
              $pillar_map = json_decode(isset($opts['pillar_pages_map']) ? $opts['pillar_pages_map'] : '{}', true);
              if (!empty($pillar_map)) {
                foreach ($pillar_map as $category => $url) {
                  echo esc_textarea($category . ': ' . $url) . "\n";
                }
              }
            ?></textarea>
            <p class="description">Category slug to pillar page URL mappings (format: category-slug: /pillar-url). One per line.</p>
          </td>
        </tr>
      </table>

      <!-- Analytics -->
      <h2>Analytics</h2>
      <table class="form-table">
        <tr>
          <th scope="row">Enable GA4 Admin Events</th>
          <td>
            <label>
              <input type="checkbox" name="ga4_enable_admin_events" value="1" <?php checked(isset($opts['ga4_enable_admin_events']) ? $opts['ga4_enable_admin_events'] : 1); ?>>
              Fire GA4 events in admin for automation actions (safe; no front-end user tracking)
            </label>
          </td>
        </tr>
        <tr>
          <th scope="row">GA4 Measurement ID</th>
          <td>
            <input type="text" name="ga4_measurement_id" value="<?php echo esc_attr(isset($opts['ga4_measurement_id']) ? $opts['ga4_measurement_id'] : ''); ?>" placeholder="G-XXXXXXXXXX">
            <p class="description">GA4 measurement ID for admin event tracking. Leave empty to disable GA4 events.</p>
          </td>
        </tr>
        <tr>
          <th scope="row">GA4 Debug Mode</th>
          <td>
            <label>
              <input type="checkbox" name="ga4_debug_mode" value="1" <?php checked(isset($opts['ga4_debug_mode']) ? $opts['ga4_debug_mode'] : 0); ?>>
              Enable GA4 debug mode for event validation
            </label>
          </td>
        </tr>
        <tr>
          <th scope="row">Metrics Retention (days)</th>
          <td>
            <input type="number" name="metrics_retention_days" value="<?php echo esc_attr(isset($opts['metrics_retention_days']) ? $opts['metrics_retention_days'] : 180); ?>" min="30" max="365">
            <p class="description">Keep server-side rollups for this many days before purging.</p>
          </td>
        </tr>
        <tr>
          <th scope="row">Dashboard Sample %</th>
          <td>
            <input type="number" name="dashboard_sample_pct" value="<?php echo esc_attr(isset($opts['dashboard_sample_pct']) ? $opts['dashboard_sample_pct'] : 100); ?>" min="1" max="100">
            <p class="description">For large sites, sample internal-link CTR calculations (100% = no sampling).</p>
          </td>
        </tr>
      </table>

      <?php submit_button('Save Settings'); ?>
    </form>
  </div>

  <?php
}
