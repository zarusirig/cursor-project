<?php

namespace CNP\SEO\Admin;



if (!defined('ABSPATH')) exit;



// AJAX handler for Google News preflight
add_action('wp_ajax_gn_preflight_test', function() {
  if (!current_user_can('manage_options')) {
    wp_die('Unauthorized');
  }

  if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'gn_preflight_test')) {
    wp_die('Security check failed');
  }

  $url = esc_url_raw($_POST['url'] ?? '');
  if (empty($url)) {
    wp_send_json(['ok' => false, 'errors' => ['No URL provided']]);
  }

  // Call the preflight validation function (will be defined in feeds.php or separate module)
  if (function_exists('CNP\\SEO\\GN\\run_preflight_check')) {
    $result = \CNP\SEO\GN\run_preflight_check($url);
    wp_send_json($result);
  } else {
    wp_send_json(['ok' => false, 'errors' => ['Preflight functionality not available']]);
  }
});

add_action('admin_menu', function(){

  // Top-level CNP menu (created once; reused by both plugins)

  if (!isset($GLOBALS['menu_cnp_registered'])){

    add_menu_page('CNP', 'CNP', 'manage_options', 'cnp', '__return_null', 'dashicons-admin-site', 58);

    $GLOBALS['menu_cnp_registered'] = true;

  }

  add_submenu_page('cnp', 'CNP SEO', 'SEO', 'manage_options', 'cnp-seo', __NAMESPACE__.'\\render_settings');
  add_submenu_page('cnp', 'Google News', 'Google News', 'manage_options', 'cnp-seo-gn', __NAMESPACE__.'\\render_gn_preflight');

});



function render_settings(){
  // Handle form submission
  if (isset($_POST['submit']) && check_admin_referer('cnp_seo_settings')) {
    $opts = get_option('cnp_seo_settings', []);

    // Meta & Schema settings
    $opts['site_title_format'] = sanitize_text_field($_POST['site_title_format'] ?? '%title% — %sitename%');
    $opts['default_description'] = sanitize_textarea_field($_POST['default_description'] ?? '');
    $opts['twitter_site'] = sanitize_text_field($_POST['twitter_site'] ?? '');
    $opts['default_og_image'] = esc_url_raw($_POST['default_og_image'] ?? '');
    $opts['noindex_search'] = isset($_POST['noindex_search']) ? 1 : 0;

    // Google News settings
    $opts['gn_enabled'] = isset($_POST['gn_enabled']) ? 1 : 0;
    $opts['gn_publication_name'] = sanitize_text_field($_POST['gn_publication_name'] ?? get_bloginfo('name'));
    $opts['gn_publication_lang'] = sanitize_text_field($_POST['gn_publication_lang'] ?? 'en');
    $opts['gn_primary_country'] = sanitize_text_field($_POST['gn_primary_country'] ?? '');
    $opts['gn_square_logo_url'] = esc_url_raw($_POST['gn_square_logo_url'] ?? '');
    $opts['gn_rect_logo_url'] = esc_url_raw($_POST['gn_rect_logo_url'] ?? '');
    $opts['gn_include_full_content_in_feeds'] = isset($_POST['gn_include_full_content_in_feeds']) ? 1 : 0;
    $opts['gn_feed_item_count'] = absint($_POST['gn_feed_item_count'] ?? 100);
    $opts['gn_exclude_sponsored_from_news'] = isset($_POST['gn_exclude_sponsored_from_news']) ? 1 : 0;
    $opts['gn_section_map'] = sanitize_textarea_field($_POST['gn_section_map'] ?? '{}');
    $opts['gn_corrections_url'] = esc_url_raw($_POST['gn_corrections_url'] ?? '');
    $opts['gn_support_email'] = sanitize_email($_POST['gn_support_email'] ?? '');

    // Sitemap settings
    $opts['sitemaps_enabled'] = isset($_POST['sitemaps_enabled']) ? 1 : 0;
    $opts['sitemap_posts_enabled'] = isset($_POST['sitemap_posts_enabled']) ? 1 : 0;
    $opts['sitemap_pages_enabled'] = isset($_POST['sitemap_pages_enabled']) ? 1 : 0;
    $opts['sitemap_categories_enabled'] = isset($_POST['sitemap_categories_enabled']) ? 1 : 0;
    $opts['sitemap_tags_enabled'] = isset($_POST['sitemap_tags_enabled']) ? 1 : 0;
    $opts['sitemap_authors_enabled'] = isset($_POST['sitemap_authors_enabled']) ? 1 : 0;
    $opts['sitemap_images_enabled'] = isset($_POST['sitemap_images_enabled']) ? 1 : 0;
    $opts['news_sitemap_enabled'] = isset($_POST['news_sitemap_enabled']) ? 1 : 0;
    $opts['news_publication_name'] = sanitize_text_field($_POST['news_publication_name'] ?? get_bloginfo('name'));
    $opts['news_publication_lang'] = sanitize_text_field($_POST['news_publication_lang'] ?? 'en');
    $opts['sitemap_max_urls'] = absint($_POST['sitemap_max_urls'] ?? 2000);
    $opts['sitemap_cache_ttl'] = absint($_POST['sitemap_cache_ttl'] ?? 600);
    $opts['sitemap_excluded_post_types'] = array_map('sanitize_text_field', explode(',', $_POST['sitemap_excluded_post_types'] ?? ''));
    $opts['sitemap_excluded_post_types'] = array_filter($opts['sitemap_excluded_post_types']);
    $opts['sitemap_excluded_taxonomies'] = array_map('sanitize_text_field', explode(',', $_POST['sitemap_excluded_taxonomies'] ?? ''));
    $opts['sitemap_excluded_taxonomies'] = array_filter($opts['sitemap_excluded_taxonomies']);
    $opts['sitemap_excluded_paths'] = sanitize_textarea_field($_POST['sitemap_excluded_paths'] ?? '');
    $opts['sitemap_news_window_hours'] = absint($_POST['sitemap_news_window_hours'] ?? 48);

    // Links & Redirects settings
    $opts['link_policy_enabled'] = isset($_POST['link_policy_enabled']) ? 1 : 0;
    $opts['affiliate_detection_mode'] = sanitize_text_field($_POST['affiliate_detection_mode'] ?? 'auto');
    $opts['affiliate_param_keys'] = sanitize_textarea_field($_POST['affiliate_param_keys'] ?? "aff\nref\nutm_source=affiliate");
    $opts['force_external_nofollow'] = isset($_POST['force_external_nofollow']) ? 1 : 0;
    $opts['respect_existing_rel'] = isset($_POST['respect_existing_rel']) ? 1 : 0;
    $opts['blocked_domains'] = sanitize_textarea_field($_POST['blocked_domains'] ?? '');
    $opts['broken_scan_enabled'] = isset($_POST['broken_scan_enabled']) ? 1 : 0;
    $opts['broken_scan_schedule'] = sanitize_text_field($_POST['broken_scan_schedule'] ?? 'weekly');
    $opts['broken_scan_timeout'] = absint($_POST['broken_scan_timeout'] ?? 8);
    $opts['broken_scan_user_agent'] = sanitize_text_field($_POST['broken_scan_user_agent'] ?? 'CNPLinkBot/1.0 (+' . home_url() . ')');
    $opts['broken_scan_max_urls'] = absint($_POST['broken_scan_max_urls'] ?? 500);
    $opts['broken_report_email'] = sanitize_email($_POST['broken_report_email'] ?? '');

    update_option('cnp_seo_settings', $opts);

    // Process redirects
    $redirects = [];
    if (isset($_POST['redirects']) && is_array($_POST['redirects'])) {
      foreach ($_POST['redirects'] as $redirect) {
        if (!empty($redirect['from'])) {
          $redirects[] = [
            'from' => sanitize_text_field($redirect['from']),
            'to' => sanitize_text_field($redirect['to']),
            'type' => sanitize_text_field($redirect['type']),
            'wildcard' => isset($redirect['wildcard']) ? 1 : 0,
            'active' => isset($redirect['active']) ? 1 : 0,
          ];
        }
      }
    }
    update_option('cnp_seo_redirects', $redirects);

    // Flush rewrite rules for redirects
    flush_rewrite_rules(false);
    echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
  }

  // Handle flush rules separately
  if (isset($_POST['flush_rules']) && check_admin_referer('cnp_seo_settings')) {
    flush_rewrite_rules(false);
    echo '<div class="notice notice-success"><p>Rewrite rules flushed successfully.</p></div>';
  }

  $opts = get_option('cnp_seo_settings', []);

  ?>

  <div class="wrap">

    <h1>CNP SEO</h1>

    <p>Core SEO controls (titles, meta, schema, sitemaps).</p>

    <form method="post">
      <?php wp_nonce_field('cnp_seo_settings'); ?>

      <h2>Meta & Schema</h2>
      <table class="form-table">
        <tr>
          <th scope="row">Site Title Format</th>
          <td>
            <input type="text" name="site_title_format" value="<?php echo esc_attr($opts['site_title_format'] ?? '%title% — %sitename%'); ?>" class="regular-text">
            <p class="description">Available tokens: %title%, %sitename%, %sep%</p>
          </td>
        </tr>

        <tr>
          <th scope="row">Default Description</th>
          <td>
            <textarea name="default_description" rows="3" cols="50" class="large-text"><?php echo esc_textarea($opts['default_description'] ?? ''); ?></textarea>
            <p class="description">Used when no custom description is set</p>
          </td>
        </tr>

        <tr>
          <th scope="row">Twitter Site</th>
          <td>
            <input type="text" name="twitter_site" value="<?php echo esc_attr($opts['twitter_site'] ?? ''); ?>" class="regular-text" placeholder="@cnpnews">
            <p class="description">Twitter handle for the site (without @)</p>
          </td>
        </tr>

        <tr>
          <th scope="row">Default OG Image</th>
          <td>
            <input type="url" name="default_og_image" value="<?php echo esc_url($opts['default_og_image'] ?? ''); ?>" class="regular-text">
            <p class="description">Absolute URL for default OpenGraph image</p>
          </td>
        </tr>

        <tr>
          <th scope="row">Noindex Search Results</th>
          <td>
            <label>
              <input type="checkbox" name="noindex_search" value="1" <?php checked($opts['noindex_search'] ?? 1, 1); ?>>
              Add noindex to search results pages
            </label>
          </td>
        </tr>
      </table>

      <h2>Sitemaps</h2>
      <table class="form-table">
        <tr>
          <th scope="row">Enable Sitemaps</th>
          <td>
            <label>
              <input type="checkbox" name="sitemaps_enabled" value="1" <?php checked($opts['sitemaps_enabled'] ?? 1, 1); ?>>
              Enable XML sitemaps
            </label>
          </td>
        </tr>

        <tr>
          <th scope="row">Content Types</th>
          <td>
            <label>
              <input type="checkbox" name="sitemap_posts_enabled" value="1" <?php checked($opts['sitemap_posts_enabled'] ?? 1, 1); ?>>
              Posts
            </label>
            <br>
            <label>
              <input type="checkbox" name="sitemap_pages_enabled" value="1" <?php checked($opts['sitemap_pages_enabled'] ?? 1, 1); ?>>
              Pages
            </label>
            <br>
            <label>
              <input type="checkbox" name="sitemap_categories_enabled" value="1" <?php checked($opts['sitemap_categories_enabled'] ?? 1, 1); ?>>
              Categories
            </label>
            <br>
            <label>
              <input type="checkbox" name="sitemap_tags_enabled" value="1" <?php checked($opts['sitemap_tags_enabled'] ?? 1, 1); ?>>
              Tags
            </label>
            <br>
            <label>
              <input type="checkbox" name="sitemap_authors_enabled" value="1" <?php checked($opts['sitemap_authors_enabled'] ?? 1, 1); ?>>
              Authors
            </label>
            <br>
            <label>
              <input type="checkbox" name="sitemap_images_enabled" value="1" <?php checked($opts['sitemap_images_enabled'] ?? 1, 1); ?>>
              Images
            </label>
          </td>
        </tr>

        <tr>
          <th scope="row">News Sitemap</th>
          <td>
            <label>
              <input type="checkbox" name="news_sitemap_enabled" value="1" <?php checked($opts['news_sitemap_enabled'] ?? 1, 1); ?>>
              Enable Google News sitemap
            </label>
          </td>
        </tr>

        <tr>
          <th scope="row">News Publication Name</th>
          <td>
            <input type="text" name="news_publication_name" value="<?php echo esc_attr($opts['news_publication_name'] ?? get_bloginfo('name')); ?>" class="regular-text">
            <p class="description">Name for Google News sitemap</p>
          </td>
        </tr>

        <tr>
          <th scope="row">News Publication Language</th>
          <td>
            <input type="text" name="news_publication_lang" value="<?php echo esc_attr($opts['news_publication_lang'] ?? 'en'); ?>" class="regular-text" placeholder="en">
            <p class="description">ISO language code (e.g., en, en-US)</p>
          </td>
        </tr>

        <tr>
          <th scope="row">Max URLs per File</th>
          <td>
            <input type="number" name="sitemap_max_urls" value="<?php echo esc_attr($opts['sitemap_max_urls'] ?? 2000); ?>" class="small-text" min="1" max="50000">
            <p class="description">Maximum URLs per sitemap file before splitting</p>
          </td>
        </tr>

        <tr>
          <th scope="row">Cache TTL</th>
          <td>
            <input type="number" name="sitemap_cache_ttl" value="<?php echo esc_attr($opts['sitemap_cache_ttl'] ?? 600); ?>" class="small-text" min="60" max="86400">
            <p class="description">Cache time in seconds (default: 10 minutes)</p>
          </td>
        </tr>

        <tr>
          <th scope="row">Excluded Post Types</th>
          <td>
            <input type="text" name="sitemap_excluded_post_types" value="<?php echo esc_attr(implode(',', $opts['sitemap_excluded_post_types'] ?? [])); ?>" class="regular-text" placeholder="attachment,revision">
            <p class="description">Comma-separated post type slugs to exclude</p>
          </td>
        </tr>

        <tr>
          <th scope="row">Excluded Taxonomies</th>
          <td>
            <input type="text" name="sitemap_excluded_taxonomies" value="<?php echo esc_attr(implode(',', $opts['sitemap_excluded_taxonomies'] ?? [])); ?>" class="regular-text" placeholder="post_format">
            <p class="description">Comma-separated taxonomy slugs to exclude</p>
          </td>
        </tr>

        <tr>
          <th scope="row">Excluded Paths</th>
          <td>
            <textarea name="sitemap_excluded_paths" rows="3" class="large-text"><?php echo esc_textarea($opts['sitemap_excluded_paths'] ?? ''); ?></textarea>
            <p class="description">One path per line (e.g., /privacy, /terms)</p>
          </td>
        </tr>

        <tr>
          <th scope="row">News Window Hours</th>
          <td>
            <input type="number" name="sitemap_news_window_hours" value="<?php echo esc_attr($opts['sitemap_news_window_hours'] ?? 48); ?>" class="small-text" min="1" max="168">
            <p class="description">Include articles from last N hours in news sitemap</p>
          </td>
        </tr>
      </table>

      <h2>Google News</h2>
      <table class="form-table">
        <tr>
          <th scope="row">Enable Google News</th>
          <td>
            <label>
              <input type="checkbox" name="gn_enabled" value="1" <?php checked($opts['gn_enabled'] ?? 1, 1); ?>>
              Enable Google News integration and RSS feeds
            </label>
          </td>
        </tr>

        <tr>
          <th scope="row">Publication Name</th>
          <td>
            <input type="text" name="gn_publication_name" value="<?php echo esc_attr($opts['gn_publication_name'] ?? get_bloginfo('name')); ?>" class="regular-text">
            <p class="description">Name for Google News sitemap and Publisher Center</p>
          </td>
        </tr>

        <tr>
          <th scope="row">Publication Language</th>
          <td>
            <input type="text" name="gn_publication_lang" value="<?php echo esc_attr($opts['gn_publication_lang'] ?? 'en'); ?>" class="regular-text" placeholder="en">
            <p class="description">ISO language code (e.g., en, en-US)</p>
          </td>
        </tr>

        <tr>
          <th scope="row">Primary Country</th>
          <td>
            <input type="text" name="gn_primary_country" value="<?php echo esc_attr($opts['gn_primary_country'] ?? ''); ?>" class="regular-text" placeholder="US" maxlength="2">
            <p class="description">Optional ISO-3166-1 alpha-2 country code (e.g., US, GB)</p>
          </td>
        </tr>

        <tr>
          <th scope="row">Square Logo URL</th>
          <td>
            <input type="url" name="gn_square_logo_url" value="<?php echo esc_url($opts['gn_square_logo_url'] ?? ''); ?>" class="regular-text">
            <p class="description">Square logo (≥512×512 PNG or SVG) for Organization schema</p>
          </td>
        </tr>

        <tr>
          <th scope="row">Rectangular Logo URL</th>
          <td>
            <input type="url" name="gn_rect_logo_url" value="<?php echo esc_url($opts['gn_rect_logo_url'] ?? ''); ?>" class="regular-text">
            <p class="description">Rectangular logo (1000×250 PNG or SVG) for Organization schema</p>
          </td>
        </tr>

        <tr>
          <th scope="row">Include Full Content in Feeds</th>
          <td>
            <label>
              <input type="checkbox" name="gn_include_full_content_in_feeds" value="1" <?php checked($opts['gn_include_full_content_in_feeds'] ?? 1, 1); ?>>
              Include full post content in RSS feeds (<content:encoded>)
            </label>
          </td>
        </tr>

        <tr>
          <th scope="row">Feed Item Count</th>
          <td>
            <input type="number" name="gn_feed_item_count" value="<?php echo esc_attr($opts['gn_feed_item_count'] ?? 100); ?>" class="small-text" min="10" max="1000">
            <p class="description">Maximum items per RSS feed (Publisher Center accepts large feeds)</p>
          </td>
        </tr>

        <tr>
          <th scope="row">Exclude Sponsored from News</th>
          <td>
            <label>
              <input type="checkbox" name="gn_exclude_sponsored_from_news" value="1" <?php checked($opts['gn_exclude_sponsored_from_news'] ?? 1, 1); ?>>
              Exclude sponsored/paid posts from News sitemap and News feed
            </label>
          </td>
        </tr>

        <tr>
          <th scope="row">Section Map</th>
          <td>
            <textarea name="gn_section_map" rows="6" cols="50" class="large-text" placeholder='{"artificial-intelligence": "Artificial Intelligence", "fintech-markets": "Fintech & Markets", ...}'><?php echo esc_textarea($opts['gn_section_map'] ?? '{}'); ?></textarea>
            <p class="description">JSON mapping of category slugs to pillar/section names for Publisher Center</p>
          </td>
        </tr>

        <tr>
          <th scope="row">Corrections Policy URL</th>
          <td>
            <input type="url" name="gn_corrections_url" value="<?php echo esc_url($opts['gn_corrections_url'] ?? ''); ?>" class="regular-text">
            <p class="description">Absolute URL to your corrections policy page</p>
          </td>
        </tr>

        <tr>
          <th scope="row">Support Email</th>
          <td>
            <input type="email" name="gn_support_email" value="<?php echo esc_attr($opts['gn_support_email'] ?? ''); ?>" class="regular-text">
            <p class="description">Optional contact email for Publisher Center support</p>
          </td>
        </tr>
      </table>

      <h2>Links & Redirects</h2>
      <table class="form-table">

        <tr>
          <th scope="row">Outbound Link Policy</th>
          <td>
            <label>
              <input type="checkbox" name="link_policy_enabled" value="1" <?php checked($opts['link_policy_enabled'] ?? 1, 1); ?>>
              Enable outbound link policy processing
            </label>
          </td>
        </tr>

        <tr>
          <th scope="row">Affiliate Detection Mode</th>
          <td>
            <label>
              <input type="radio" name="affiliate_detection_mode" value="auto" <?php checked($opts['affiliate_detection_mode'] ?? 'auto', 'auto'); ?>>
              Auto (detect by URL patterns)
            </label>
            <br>
            <label>
              <input type="radio" name="affiliate_detection_mode" value="manual" <?php checked($opts['affiliate_detection_mode'] ?? 'auto', 'manual'); ?>>
              Manual (only when post has affiliate toggle)
            </label>
          </td>
        </tr>

        <tr>
          <th scope="row">Affiliate Parameter Keys</th>
          <td>
            <textarea name="affiliate_param_keys" rows="3" class="large-text"><?php echo esc_textarea($opts['affiliate_param_keys'] ?? "aff\nref\nutm_source=affiliate"); ?></textarea>
            <p class="description">One per line. Bare tokens match query keys, key=value matches contains</p>
          </td>
        </tr>

        <tr>
          <th scope="row">Force External Nofollow</th>
          <td>
            <label>
              <input type="checkbox" name="force_external_nofollow" value="1" <?php checked($opts['force_external_nofollow'] ?? 0, 1); ?>>
              Add nofollow to all external links
            </label>
          </td>
        </tr>

        <tr>
          <th scope="row">Respect Existing Rel</th>
          <td>
            <label>
              <input type="checkbox" name="respect_existing_rel" value="1" <?php checked($opts['respect_existing_rel'] ?? 1, 1); ?>>
              Merge with existing rel attributes
            </label>
          </td>
        </tr>

        <tr>
          <th scope="row">Blocked Domains</th>
          <td>
            <textarea name="blocked_domains" rows="3" class="large-text" placeholder="badexample.com&#10;spam.tld"><?php echo esc_textarea($opts['blocked_domains'] ?? ''); ?></textarea>
            <p class="description">One domain per line. External links to these domains will be removed</p>
          </td>
        </tr>

        <tr>
          <th scope="row">Broken Link Scan</th>
          <td>
            <label>
              <input type="checkbox" name="broken_scan_enabled" value="1" <?php checked($opts['broken_scan_enabled'] ?? 1, 1); ?>>
              Enable broken link scanning
            </label>
          </td>
        </tr>

        <tr>
          <th scope="row">Scan Schedule</th>
          <td>
            <select name="broken_scan_schedule">
              <option value="weekly" <?php selected($opts['broken_scan_schedule'] ?? 'weekly', 'weekly'); ?>>Weekly</option>
              <option value="daily" <?php selected($opts['broken_scan_schedule'] ?? 'weekly', 'daily'); ?>>Daily</option>
            </select>
          </td>
        </tr>

        <tr>
          <th scope="row">Scan Timeout</th>
          <td>
            <input type="number" name="broken_scan_timeout" value="<?php echo esc_attr($opts['broken_scan_timeout'] ?? 8); ?>" class="small-text" min="1" max="30"> seconds
          </td>
        </tr>

        <tr>
          <th scope="row">Scan User Agent</th>
          <td>
            <input type="text" name="broken_scan_user_agent" value="<?php echo esc_attr($opts['broken_scan_user_agent'] ?? 'CNPLinkBot/1.0 (+' . home_url() . ')'); ?>" class="regular-text">
          </td>
        </tr>

        <tr>
          <th scope="row">Max URLs per Scan</th>
          <td>
            <input type="number" name="broken_scan_max_urls" value="<?php echo esc_attr($opts['broken_scan_max_urls'] ?? 500); ?>" class="small-text" min="10" max="2000">
          </td>
        </tr>

        <tr>
          <th scope="row">Report Email</th>
          <td>
            <input type="email" name="broken_report_email" value="<?php echo esc_attr($opts['broken_report_email'] ?? ''); ?>" class="regular-text" placeholder="admin@example.com">
            <p class="description">Send scan reports to this email (optional)</p>
          </td>
        </tr>
      </table>

      <h3>Redirects</h3>
      <?php
      $redirects = get_option('cnp_seo_redirects', []);
      ?>
      <div id="redirects-manager">
        <table class="wp-list-table widefat fixed striped" id="redirects-table">
          <thead>
            <tr>
              <th>From</th>
              <th>To</th>
              <th>Type</th>
              <th>Wildcard</th>
              <th>Active</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="redirects-tbody">
            <?php if (empty($redirects)): ?>
            <tr class="no-items">
              <td colspan="6">No redirects found.</td>
            </tr>
            <?php else: ?>
              <?php foreach ($redirects as $index => $redirect): ?>
              <tr>
                <td><input type="text" name="redirects[<?php echo $index; ?>][from]" value="<?php echo esc_attr($redirect['from']); ?>" class="regular-text"></td>
                <td><input type="text" name="redirects[<?php echo $index; ?>][to]" value="<?php echo esc_attr($redirect['to']); ?>" class="regular-text"></td>
                <td>
                  <select name="redirects[<?php echo $index; ?>][type]">
                    <option value="301" <?php selected($redirect['type'], '301'); ?>>301</option>
                    <option value="410" <?php selected($redirect['type'], '410'); ?>>410</option>
                  </select>
                </td>
                <td><input type="checkbox" name="redirects[<?php echo $index; ?>][wildcard]" value="1" <?php checked($redirect['wildcard'], 1); ?>></td>
                <td><input type="checkbox" name="redirects[<?php echo $index; ?>][active]" value="1" <?php checked($redirect['active'], 1); ?>></td>
                <td><button type="button" class="button delete-redirect">Delete</button></td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>

        <p>
          <button type="button" class="button" id="add-redirect">Add Redirect</button>
          <button type="button" class="button" id="import-redirects">Import CSV</button>
          <button type="button" class="button" id="export-redirects">Export CSV</button>
          <button type="submit" name="flush_rules" class="button" value="1">Flush Rules</button>
          <input type="file" id="import-file" accept=".csv" style="display: none;">
        </p>
      </div>

      <h3>Broken Link Report</h3>
      <?php
      $report = \CNP\SEO\Links\get_broken_link_report();
      if (!empty($report)): ?>
        <div class="broken-link-report">
          <p><strong>Last scan:</strong> <?php echo date('Y-m-d H:i:s', $report['run_time']); ?></p>
          <div class="report-stats">
            <span class="stat">Checked: <?php echo $report['total_checked']; ?></span>
            <span class="stat">OK: <?php echo $report['ok']; ?></span>
            <span class="stat">Broken: <?php echo $report['broken']; ?></span>
            <span class="stat">Warnings: <?php echo $report['warning']; ?></span>
          </div>

          <?php if (!empty($report['broken_links'])): ?>
            <h4>Broken Links (<?php echo count($report['broken_links']); ?>)</h4>
            <table class="wp-list-table widefat fixed striped">
              <thead>
                <tr>
                  <th>URL</th>
                  <th>Status</th>
                  <th>Post</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach (array_slice($report['broken_links'], 0, 20) as $link): ?>
                  <tr>
                    <td><a href="<?php echo esc_url($link['url']); ?>" target="_blank"><?php echo esc_html($link['url']); ?></a></td>
                    <td><?php echo esc_html($link['status']); ?></td>
                    <td><a href="<?php echo get_edit_post_link($link['post_id']); ?>"><?php echo esc_html($link['post_title']); ?></a></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>

          <?php if (!empty($report['warning_links'])): ?>
            <h4>Warning Links (<?php echo count($report['warning_links']); ?>)</h4>
            <table class="wp-list-table widefat fixed striped">
              <thead>
                <tr>
                  <th>URL</th>
                  <th>Status</th>
                  <th>Post</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach (array_slice($report['warning_links'], 0, 20) as $link): ?>
                  <tr>
                    <td><a href="<?php echo esc_url($link['url']); ?>" target="_blank"><?php echo esc_html($link['url']); ?></a></td>
                    <td><?php echo esc_html($link['status']); ?></td>
                    <td><a href="<?php echo get_edit_post_link($link['post_id']); ?>"><?php echo esc_html($link['post_title']); ?></a></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <p>No broken link scan report available. The scanner will run according to the schedule above.</p>
      <?php endif; ?>

      <h2>Status</h2>
      <table class="form-table">
        <tr><th scope="row">Version</th><td><?php echo esc_html(CNP_SEO_VER); ?></td></tr>
        <tr><th scope="row">News sitemap</th><td><?php echo !empty($opts['news_sitemap_enabled']) ? 'Enabled' : 'Disabled'; ?></td></tr>
      </table>

      <p class="submit">
        <input type="submit" name="submit" class="button button-primary" value="Save Changes">
      </p>

    </form>

    <style>
      .report-stats { margin: 10px 0; }
      .report-stats .stat { display: inline-block; margin-right: 15px; padding: 5px 10px; background: #f1f1f1; border-radius: 3px; }
      .broken-link-report h4 { margin-top: 20px; }
    </style>

    <script>
      jQuery(document).ready(function($) {
        // Add redirect row
        $('#add-redirect').on('click', function() {
          var rowCount = $('#redirects-tbody tr').length;
          var newRow = '<tr>' +
            '<td><input type="text" name="redirects[' + rowCount + '][from]" value="" class="regular-text" placeholder="/old-path/"></td>' +
            '<td><input type="text" name="redirects[' + rowCount + '][to]" value="" class="regular-text" placeholder="/new-path/ or https://example.com"></td>' +
            '<td><select name="redirects[' + rowCount + '][type]"><option value="301">301</option><option value="410">410</option></select></td>' +
            '<td><input type="checkbox" name="redirects[' + rowCount + '][wildcard]" value="1"></td>' +
            '<td><input type="checkbox" name="redirects[' + rowCount + '][active]" value="1" checked></td>' +
            '<td><button type="button" class="button delete-redirect">Delete</button></td>' +
            '</tr>';
          $('#redirects-tbody').append(newRow);
        });

        // Delete redirect row
        $(document).on('click', '.delete-redirect', function() {
          $(this).closest('tr').remove();
        });

        // Import redirects
        $('#import-redirects').on('click', function() {
          $('#import-file').click();
        });

        $('#import-file').on('change', function() {
          var file = this.files[0];
          if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
              var csv = e.target.result;
              importRedirectsCSV(csv);
            };
            reader.readAsText(file);
          }
        });

        function importRedirectsCSV(csv) {
          var lines = csv.trim().split('\n');
          var header = lines.shift().split(',');

          if (header.length !== 5 || header[0] !== 'from' || header[1] !== 'to' || header[2] !== 'type' || header[3] !== 'wildcard' || header[4] !== 'active') {
            alert('Invalid CSV format. Expected headers: from,to,type,wildcard,active');
            return;
          }

          var imported = 0;
          var rowCount = $('#redirects-tbody tr').length;

          lines.forEach(function(line) {
            if (!line.trim()) return;

            var data = line.split(',');
            if (data.length === 5) {
              var from = data[0].trim();
              var to = data[1].trim();
              var type = data[2].trim();
              var wildcard = data[3].trim();
              var active = data[4].trim();

              if (from && (type === '301' || type === '410')) {
                var checkedWildcard = wildcard == '1' ? 'checked' : '';
                var checkedActive = active == '1' ? 'checked' : '';

                var newRow = '<tr>' +
                  '<td><input type="text" name="redirects[' + rowCount + '][from]" value="' + from + '" class="regular-text"></td>' +
                  '<td><input type="text" name="redirects[' + rowCount + '][to]" value="' + to + '" class="regular-text"></td>' +
                  '<td><select name="redirects[' + rowCount + '][type]"><option value="301" ' + (type === '301' ? 'selected' : '') + '>301</option><option value="410" ' + (type === '410' ? 'selected' : '') + '>410</option></select></td>' +
                  '<td><input type="checkbox" name="redirects[' + rowCount + '][wildcard]" value="1" ' + checkedWildcard + '></td>' +
                  '<td><input type="checkbox" name="redirects[' + rowCount + '][active]" value="1" ' + checkedActive + '></td>' +
                  '<td><button type="button" class="button delete-redirect">Delete</button></td>' +
                  '</tr>';

                $('#redirects-tbody').append(newRow);
                rowCount++;
                imported++;
              }
            }
          });

          alert('Imported ' + imported + ' redirects from CSV.');
        }

        // Export redirects
        $('#export-redirects').on('click', function() {
          var csv = 'from,to,type,wildcard,active\n';

          $('#redirects-tbody tr').each(function() {
            var from = $(this).find('input[name*="[from]"]').val();
            var to = $(this).find('input[name*="[to]"]').val();
            var type = $(this).find('select[name*="[type]"]').val();
            var wildcard = $(this).find('input[name*="[wildcard]"]:checked').length ? '1' : '0';
            var active = $(this).find('input[name*="[active]"]:checked').length ? '1' : '0';

            csv += from + ',' + to + ',' + type + ',' + wildcard + ',' + active + '\n';
          });

          var blob = new Blob([csv], { type: 'text/csv' });
          var url = window.URL.createObjectURL(blob);
          var a = document.createElement('a');
          a.href = url;
          a.download = 'cnp-redirects-' + new Date().toISOString().split('T')[0] + '.csv';
          document.body.appendChild(a);
          a.click();
          document.body.removeChild(a);
          window.URL.revokeObjectURL(url);
        });
      });
    </script>

  </div>

  <?php

}

function render_gn_preflight() {
  $opts = get_option('cnp_seo_settings', []);

  if (!empty($opts['gn_enabled'])): ?>
    <div class="wrap">
      <h1>Google News Preflight</h1>
      <p>Test any URL before submitting feeds to Publisher Center.</p>

      <div id="gn-preflight-container">
        <form id="gn-preflight-form">
          <?php wp_nonce_field('gn_preflight_test'); ?>
          <table class="form-table">
            <tr>
              <th scope="row">Test URL</th>
              <td>
                <input type="url" id="preflight-url" name="url" value="" class="regular-text" placeholder="https://example.com/article/" required>
                <button type="submit" class="button button-primary">Run Preflight</button>
              </td>
            </tr>
          </table>
        </form>

        <div id="preflight-results" style="display: none;">
          <h3>Results</h3>
          <div id="preflight-status"></div>
          <div id="preflight-errors" class="preflight-section" style="display: none;">
            <h4>Errors</h4>
            <ul id="preflight-errors-list"></ul>
          </div>
          <div id="preflight-warnings" class="preflight-section" style="display: none;">
            <h4>Warnings</h4>
            <ul id="preflight-warnings-list"></ul>
          </div>
          <div id="preflight-facts" class="preflight-section" style="display: none;">
            <h4>Parsed Facts</h4>
            <pre id="preflight-facts-content"></pre>
          </div>
        </div>
      </div>

      <style>
        .preflight-section { margin-top: 20px; }
        .preflight-error { color: #d63638; }
        .preflight-warning { color: #dba617; }
        .preflight-ok { color: #00a32a; }
        #preflight-status { font-weight: bold; margin-bottom: 15px; }
      </style>

      <script>
      jQuery(document).ready(function($) {
        $('#gn-preflight-form').on('submit', function(e) {
          e.preventDefault();

          var url = $('#preflight-url').val();
          if (!url) {
            alert('Please enter a URL to test.');
            return;
          }

          var $button = $(this).find('button[type="submit"]');
          var originalText = $button.text();

          $button.text('Testing...').prop('disabled', true);

          $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
              action: 'gn_preflight_test',
              url: url,
              _wpnonce: $(this).find('[name="_wpnonce"]').val()
            },
            success: function(response) {
              displayPreflightResults(response);
            },
            error: function() {
              $('#preflight-status').html('<span class="preflight-error">Error: Could not perform preflight check.</span>');
              $('#preflight-results').show();
            },
            complete: function() {
              $button.text(originalText).prop('disabled', false);
            }
          });
        });

        function displayPreflightResults(data) {
          $('#preflight-results').show();

          if (data.ok) {
            $('#preflight-status').html('<span class="preflight-ok">✓ All checks passed!</span>');
          } else {
            $('#preflight-status').html('<span class="preflight-error">✗ Issues found - review errors below</span>');
          }

          // Errors
          if (data.errors && data.errors.length > 0) {
            var errorsHtml = '';
            data.errors.forEach(function(error) {
              errorsHtml += '<li class="preflight-error">' + error + '</li>';
            });
            $('#preflight-errors-list').html(errorsHtml);
            $('#preflight-errors').show();
          } else {
            $('#preflight-errors').hide();
          }

          // Warnings
          if (data.warnings && data.warnings.length > 0) {
            var warningsHtml = '';
            data.warnings.forEach(function(warning) {
              warningsHtml += '<li class="preflight-warning">' + warning + '</li>';
            });
            $('#preflight-warnings-list').html(warningsHtml);
            $('#preflight-warnings').show();
          } else {
            $('#preflight-warnings').hide();
          }

          // Facts
          if (data.facts) {
            $('#preflight-facts-content').text(JSON.stringify(data.facts, null, 2));
            $('#preflight-facts').show();
          } else {
            $('#preflight-facts').hide();
          }
        }
      });
      </script>
    </div>
  <?php else: ?>
    <div class="wrap">
      <h1>Google News Preflight</h1>
      <div class="notice notice-warning">
        <p>Google News integration is disabled. <a href="<?php echo admin_url('admin.php?page=cnp-seo'); ?>">Enable it in SEO settings</a> to use preflight validation.</p>
      </div>
    </div>
  <?php endif;
}
