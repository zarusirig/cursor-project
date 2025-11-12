<?php

namespace CNP\AUT\Admin;

if (!defined('ABSPATH')) exit;

// Add dashboard submenu
add_action('admin_menu', function(){
  add_submenu_page(
    'cnp-automation',
    'Automation Insights',
    'Insights',
    'manage_options',
    'cnp-automation-insights',
    __NAMESPACE__ . '\\render_insights_dashboard'
  );
});

// Register dashboard assets
add_action('admin_enqueue_scripts', function($hook) {
  if ($hook !== 'cnp-automation_page_cnp-automation-insights') {
    return;
  }

  // Enqueue GA4 if configured
  $opts = get_option('cnp_automation_settings', []);
  if (!empty($opts['ga4_measurement_id'])) {
    wp_enqueue_script(
      'cnp-automation-ga4',
      'https://www.googletagmanager.com/gtag/js?id=' . esc_attr($opts['ga4_measurement_id']),
      [],
      null,
      false
    );

    wp_add_inline_script('cnp-automation-ga4', sprintf(
      "window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '%s', { 'debug_mode': %s });",
      esc_js($opts['ga4_measurement_id']),
      !empty($opts['ga4_debug_mode']) ? 'true' : 'false'
    ));
  }

  wp_enqueue_style(
    'cnp-automation-dashboard',
    CNP_AUT_URL . 'assets/css/dashboard.css',
    [],
    CNP_AUT_VER
  );

  wp_enqueue_script(
    'cnp-automation-dashboard',
    CNP_AUT_URL . 'assets/js/dashboard.js',
    ['jquery', 'underscore'],
    CNP_AUT_VER,
    true
  );

  wp_localize_script('cnp-automation-dashboard', 'cnpDashboard', [
    'ajaxUrl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('cnp_dashboard'),
    'strings' => [
      'loading' => 'Loading...',
      'error' => 'Error loading data',
      'export' => 'Export CSV'
    ]
  ]);
});

// AJAX handler for dashboard data
add_action('wp_ajax_cnp_dashboard_data', function() {
  if (!current_user_can('manage_options')) {
    wp_die('Unauthorized');
  }

  if (!wp_verify_nonce($_POST['nonce'] ?? '', 'cnp_dashboard')) {
    wp_die('Security check failed');
  }

  $range = intval($_POST['range'] ?? 7);
  $category = sanitize_text_field($_POST['category'] ?? 'all');

  $data = get_dashboard_data($range, $category);

  wp_send_json_success($data);
});

// Main dashboard render function
function render_insights_dashboard() {
  $opts = get_option('cnp_automation_settings', []);
  $categories = get_categories(['hide_empty' => false]);

  // Default to 7-day view
  $current_range = intval($_GET['range'] ?? 7);
  $current_category = sanitize_text_field($_GET['category'] ?? 'all');

  ?>
  <div class="wrap">
    <h1>Automation Insights</h1>
    <p>Track automation outcomes from brief → draft → review → publish → recirculation.</p>

    <!-- Filters -->
    <div class="cnp-dashboard-filters">
      <form method="get" class="cnp-dashboard-filter-form">
        <input type="hidden" name="page" value="cnp-automation-insights">

        <label for="range">Time Range:</label>
        <select name="range" id="range">
          <option value="7" <?php selected($current_range, 7); ?>>Last 7 days</option>
          <option value="30" <?php selected($current_range, 30); ?>>Last 30 days</option>
          <option value="90" <?php selected($current_range, 90); ?>>Last 90 days</option>
        </select>

        <label for="category">Category:</label>
        <select name="category" id="category">
          <option value="all" <?php selected($current_category, 'all'); ?>>All Categories</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?php echo esc_attr($cat->slug); ?>" <?php selected($current_category, $cat->slug); ?>>
              <?php echo esc_html($cat->name); ?>
            </option>
          <?php endforeach; ?>
        </select>

        <button type="submit" class="button">Update</button>
      </form>
    </div>

    <!-- Dashboard Grid -->
    <div class="cnp-dashboard-grid" id="cnp-dashboard-grid">
      <!-- Loading state -->
      <div class="cnp-dashboard-loading">
        <p>Loading dashboard data...</p>
      </div>
    </div>

    <!-- Dashboard Template -->
    <script type="text/template" id="cnp-dashboard-template">
      <!-- Automation Output Tile -->
      <div class="cnp-dashboard-tile cnp-tile-automation">
        <div class="cnp-tile-header">
          <h3>Automation Output</h3>
          <a href="#" class="cnp-tile-export" data-metric="draft_created">Export CSV</a>
        </div>
        <div class="cnp-tile-content">
          <div class="cnp-metric-large">
            <span class="cnp-metric-value"><%= automation.drafts_created %></span>
            <span class="cnp-metric-label">Drafts Created</span>
          </div>
          <div class="cnp-metric-row">
            <div class="cnp-metric">
              <span class="cnp-metric-value cnp-metric-success"><%= automation.published %></span>
              <span class="cnp-metric-label">Published</span>
            </div>
            <div class="cnp-metric">
              <span class="cnp-metric-value cnp-metric-error"><%= automation.failed %></span>
              <span class="cnp-metric-label">Failed</span>
            </div>
            <div class="cnp-metric">
              <span class="cnp-metric-value cnp-metric-warning"><%= automation.qa_overrides %></span>
              <span class="cnp-metric-label">QA Overrides</span>
            </div>
          </div>
          <div class="cnp-metric-row">
            <div class="cnp-metric">
              <span class="cnp-metric-value"><%= automation.publish_rate %>%</span>
              <span class="cnp-metric-label">Publish Rate</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Internal Linking Tile -->
      <div class="cnp-dashboard-tile cnp-tile-linking">
        <div class="cnp-tile-header">
          <h3>Internal Linking</h3>
          <a href="#" class="cnp-tile-export" data-metric="links_inserted">Export CSV</a>
        </div>
        <div class="cnp-tile-content">
          <div class="cnp-metric-large">
            <span class="cnp-metric-value"><%= linking.links_inserted %></span>
            <span class="cnp-metric-label">Links Inserted</span>
          </div>
          <div class="cnp-metric-row">
            <div class="cnp-metric">
              <span class="cnp-metric-value"><%= linking.avg_per_post %></span>
              <span class="cnp-metric-label">Avg per Post</span>
            </div>
            <div class="cnp-metric">
              <span class="cnp-metric-value"><%= linking.ctr %>%</span>
              <span class="cnp-metric-label">Related CTR</span>
            </div>
          </div>
          <div class="cnp-top-list">
            <h4>Top Destination Posts</h4>
            <ol>
              <% _.each(linking.top_destinations, function(dest) { %>
                <li>
                  <a href="<%= dest.url %>" target="_blank"><%= dest.title %></a>
                  (<%= dest.inbound_count %> links, <%= dest.ctr %>% CTR)
                </li>
              <% }); %>
            </ol>
          </div>
        </div>
      </div>

      <!-- QA Friction Tile -->
      <div class="cnp-dashboard-tile cnp-tile-qa">
        <div class="cnp-tile-header">
          <h3>QA Friction</h3>
          <a href="#" class="cnp-tile-export" data-metric="qa_fail">Export CSV</a>
        </div>
        <div class="cnp-tile-content">
          <div class="cnp-metric-large">
            <span class="cnp-metric-value cnp-metric-warning"><%= qa.avg_iterations %></span>
            <span class="cnp-metric-label">Avg Iterations</span>
          </div>
          <div class="cnp-metric-row">
            <div class="cnp-metric">
              <span class="cnp-metric-value cnp-metric-error"><%= qa.failures %></span>
              <span class="cnp-metric-label">Total Failures</span>
            </div>
            <div class="cnp-metric">
              <span class="cnp-metric-value cnp-metric-warning"><%= qa.warnings %></span>
              <span class="cnp-metric-label">Total Warnings</span>
            </div>
          </div>
          <div class="cnp-top-list">
            <h4>Top Failing Checks</h4>
            <ol>
              <% _.each(qa.top_failures, function(failure) { %>
                <li><%= failure.check_key %> (<%= failure.count %>)</li>
              <% }); %>
            </ol>
          </div>
        </div>
      </div>

      <!-- Section Performance Tile -->
      <div class="cnp-dashboard-tile cnp-tile-performance">
        <div class="cnp-tile-header">
          <h3>Section Performance</h3>
          <a href="#" class="cnp-tile-export" data-metric="draft_published">Export CSV</a>
        </div>
        <div class="cnp-tile-content">
          <div class="cnp-metric-large">
            <span class="cnp-metric-value"><%= performance.total_published %></span>
            <span class="cnp-metric-label">Posts Published</span>
          </div>
          <div class="cnp-metric-row">
            <div class="cnp-metric">
              <span class="cnp-metric-value"><%= performance.avg_reading_time %></span>
              <span class="cnp-metric-label">Avg Read Time</span>
            </div>
            <div class="cnp-metric">
              <span class="cnp-metric-value"><%= performance.avg_word_count %></span>
              <span class="cnp-metric-label">Avg Word Count</span>
            </div>
          </div>
          <div class="cnp-section-breakdown">
            <h4>By Category</h4>
            <table class="cnp-category-table">
              <thead>
                <tr>
                  <th>Category</th>
                  <th>Published</th>
                  <th>% with Sources</th>
                </tr>
              </thead>
              <tbody>
                <% _.each(performance.by_category, function(cat) { %>
                  <tr>
                    <td><%= cat.name %></td>
                    <td><%= cat.published %></td>
                    <td><%= cat.sources_pct %>%</td>
                  </tr>
                <% }); %>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </script>

    <style>
      .cnp-dashboard-filters { margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 4px; }
      .cnp-dashboard-filter-form { display: flex; gap: 15px; align-items: center; }
      .cnp-dashboard-filter-form label { font-weight: 600; }
      .cnp-dashboard-filter-form select { min-width: 120px; }

      .cnp-dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-top: 20px; }
      .cnp-dashboard-loading { grid-column: 1 / -1; text-align: center; padding: 40px; }

      .cnp-dashboard-tile { background: white; border: 1px solid #ddd; border-radius: 8px; padding: 20px; }
      .cnp-tile-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
      .cnp-tile-header h3 { margin: 0; color: #23282d; }
      .cnp-tile-export { color: #007cba; text-decoration: none; font-size: 12px; }

      .cnp-metric-large { text-align: center; margin-bottom: 20px; }
      .cnp-metric-value { display: block; font-size: 36px; font-weight: bold; line-height: 1; }
      .cnp-metric-label { font-size: 14px; color: #666; margin-top: 5px; }

      .cnp-metric-row { display: flex; justify-content: space-around; margin-bottom: 20px; }
      .cnp-metric { text-align: center; }
      .cnp-metric .cnp-metric-value { font-size: 24px; }

      .cnp-metric-success { color: #00a32a; }
      .cnp-metric-error { color: #d63638; }
      .cnp-metric-warning { color: #dba617; }

      .cnp-top-list h4 { margin: 15px 0 10px 0; font-size: 14px; }
      .cnp-top-list ol { margin: 0; padding-left: 20px; }
      .cnp-top-list li { font-size: 13px; margin-bottom: 5px; }

      .cnp-category-table { width: 100%; font-size: 12px; }
      .cnp-category-table th { text-align: left; padding: 5px; border-bottom: 1px solid #ddd; }
      .cnp-category-table td { padding: 5px; border-bottom: 1px solid #eee; }
    </style>
  </div>
  <?php
}

// Get dashboard data
function get_dashboard_data($range = 7, $category_filter = 'all') {
  // Automation Output
  $automation = [
    'drafts_created' => \CNP\AUT\Metrics\get_metrics_sum('draft_created', $range, $category_filter === 'all' ? null : $category_filter),
    'published' => \CNP\AUT\Metrics\get_metrics_sum('draft_published', $range, $category_filter === 'all' ? null : $category_filter),
    'failed' => \CNP\AUT\Metrics\get_metrics_sum('draft_failed', $range),
    'qa_overrides' => \CNP\AUT\Metrics\get_metrics_sum('qa_override', $range, $category_filter === 'all' ? null : $category_filter),
  ];

  $total_generated = $automation['drafts_created'] + $automation['failed'];
  $automation['publish_rate'] = $total_generated > 0 ? round(($automation['published'] / $total_generated) * 100, 1) : 0;

  // Internal Linking
  $linking = [
    'links_inserted' => \CNP\AUT\Metrics\get_metrics_sum('links_inserted', $range, $category_filter === 'all' ? null : $category_filter),
    'avg_per_post' => 0, // Would need post count calculation
    'ctr' => 0, // Calculate from impressions/clicks
  ];

  // Calculate CTR
  $impressions = \CNP\AUT\Metrics\get_metrics_sum('rc_impression', $range);
  $clicks = \CNP\AUT\Metrics\get_metrics_sum('rc_click', $range);
  $linking['ctr'] = $impressions > 0 ? round(($clicks / $impressions) * 100, 1) : 0;

  // Top destinations (simplified - would need more complex query)
  $linking['top_destinations'] = [];

  // QA Friction
  $qa = [
    'failures' => \CNP\AUT\Metrics\get_metrics_sum('qa_fail', $range, $category_filter === 'all' ? null : $category_filter),
    'warnings' => \CNP\AUT\Metrics\get_metrics_sum('qa_warn', $range, $category_filter === 'all' ? null : $category_filter),
    'passes' => \CNP\AUT\Metrics\get_metrics_sum('qa_pass', $range, $category_filter === 'all' ? null : $category_filter),
    'avg_iterations' => 0, // Would need calculation based on failure patterns
    'top_failures' => [], // Would need to query failure types
  ];

  // Section Performance
  $performance = [
    'total_published' => $automation['published'],
    'avg_reading_time' => 0, // Would need calculation from post meta
    'avg_word_count' => 0, // Would need calculation from posts
    'by_category' => [], // Would need category breakdown
  ];

  return [
    'automation' => $automation,
    'linking' => $linking,
    'qa' => $qa,
    'performance' => $performance,
  ];
}

// AJAX handler for metric export
add_action('wp_ajax_cnp_export_metric', function() {
  if (!current_user_can('manage_options')) {
    wp_die('Unauthorized');
  }

  if (!wp_verify_nonce($_GET['_wpnonce'] ?? '', 'cnp_dashboard')) {
    wp_die('Security check failed');
  }

  $metric = sanitize_text_field($_GET['metric'] ?? '');
  $range = intval($_GET['range'] ?? 90);

  if (empty($metric)) {
    wp_die('Metric parameter required');
  }

  // Validate metric name
  $allowed_metrics = [
    'draft_created', 'draft_failed', 'draft_published', 'qa_fail', 'qa_warn', 'qa_pass',
    'qa_override', 'links_inserted', 'rc_impression', 'rc_click', 'newsletter_signup', 'deep_scroll_75'
  ];

  if (!in_array($metric, $allowed_metrics)) {
    wp_die('Invalid metric name');
  }

  \CNP\AUT\Metrics\export_metrics_csv($metric, $range);
});
