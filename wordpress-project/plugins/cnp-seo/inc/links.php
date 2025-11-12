<?php

namespace CNP\SEO\Links;



if (!defined('ABSPATH')) exit;



// Outbound Link Processor
add_filter('the_content', __NAMESPACE__ . '\\process_outbound_links', 100);
add_filter('the_excerpt', __NAMESPACE__ . '\\process_outbound_links', 100);

// Redirect Manager
add_action('template_redirect', __NAMESPACE__ . '\\handle_redirects', 1);

// Broken Link Scanner
add_action('cnp_broken_link_scan', __NAMESPACE__ . '\\run_broken_link_scan');

// Register cron events
add_action('init', function(){
  $opts = get_option('cnp_seo_settings', []);
  $schedule = $opts['broken_scan_schedule'] ?? 'weekly';
  $enabled = $opts['broken_scan_enabled'] ?? 1;

  $next_scheduled = wp_next_scheduled('cnp_broken_link_scan');

  if ($enabled) {
    // If not scheduled or scheduled with different recurrence, reschedule
    if (!$next_scheduled) {
      wp_schedule_event(time(), $schedule, 'cnp_broken_link_scan');
    } else {
      // Check if the schedule has changed by looking at the cron array
      $cron = _get_cron_array();
      $found_schedule = null;

      if ($cron) {
        foreach ($cron as $timestamp => $hooks) {
          if (isset($hooks['cnp_broken_link_scan'])) {
            foreach ($hooks['cnp_broken_link_scan'] as $event) {
              $found_schedule = $event['schedule'];
              break 2;
            }
          }
        }
      }

      // If schedule changed, reschedule
      if ($found_schedule && $found_schedule !== $schedule) {
        wp_clear_scheduled_hook('cnp_broken_link_scan');
        wp_schedule_event(time(), $schedule, 'cnp_broken_link_scan');
      }
    }
  } else {
    // If disabled, clear the schedule
    if ($next_scheduled) {
      wp_clear_scheduled_hook('cnp_broken_link_scan');
    }
  }
});

// Plugin activation/deactivation
register_activation_hook(CNP_SEO_PATH . '../cnp-seo.php', function(){
  // Schedule broken link scan
  $opts = get_option('cnp_seo_settings', []);
  if ($opts['broken_scan_enabled'] ?? 1) {
    $schedule = $opts['broken_scan_schedule'] ?? 'weekly';
    wp_schedule_event(time(), $schedule, 'cnp_broken_link_scan');
  }
  flush_rewrite_rules();
});

register_deactivation_hook(CNP_SEO_PATH . '../cnp-seo.php', function(){
  wp_clear_scheduled_hook('cnp_broken_link_scan');
  flush_rewrite_rules();
});

// Outbound Link Processing Functions
function process_outbound_links($content) {
  // Skip if not enabled or in admin/REST/feeds
  $opts = get_option('cnp_seo_settings', []);
  if (empty($opts['link_policy_enabled']) || is_admin() || wp_is_json_request() || is_feed()) {
    return $content;
  }

  // Process links with DOMDocument for safety
  $dom = new \DOMDocument();
  $dom->loadHTML('<div>' . $content . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

  $links = $dom->getElementsByTagName('a');

  foreach ($links as $link) {
    $href = $link->getAttribute('href');
    $rel = $link->getAttribute('rel');

    // Skip non-HTTP links
    if (empty($href) || !preg_match('#^https?://#', $href)) {
      continue;
    }

    $is_external = is_external_link($href);

    if ($is_external) {
      // Check blocklist
      if (is_blocked_domain($href)) {
        // Replace link with text content
        $text = $link->textContent ?: $href;
        $text_node = $dom->createTextNode($text);
        $link->parentNode->replaceChild($text_node, $link);
        continue;
      }

      // Process rel attributes
      $new_rel = process_link_rel($href, $rel, get_the_ID());
      if ($new_rel !== $rel) {
        $link->setAttribute('rel', $new_rel);
      }
    }
  }

  // Extract body content (skip the wrapper div)
  $body = $dom->getElementsByTagName('div')->item(0);
  $content = '';
  foreach ($body->childNodes as $node) {
    $content .= $dom->saveHTML($node);
  }

  return $content;
}

function is_external_link($url) {
  $home_host = parse_url(home_url(), PHP_URL_HOST);
  $link_host = parse_url($url, PHP_URL_HOST);

  return $link_host && $link_host !== $home_host;
}

function is_blocked_domain($url) {
  $opts = get_option('cnp_seo_settings', []);
  $blocked_domains = array_filter(array_map('trim', explode("\n", $opts['blocked_domains'] ?? '')));

  if (empty($blocked_domains)) {
    return false;
  }

  $host = parse_url($url, PHP_URL_HOST);
  if (!$host) {
    return false;
  }

  $host = strtolower($host);

  // Remove www. prefix for comparison
  $host = preg_replace('#^www\.#', '', $host);

  foreach ($blocked_domains as $blocked) {
    $blocked = strtolower(trim($blocked));
    if ($blocked) {
      // Remove www. prefix from blocked domain too
      $blocked = preg_replace('#^www\.#', '', $blocked);

      // Check if host matches blocked domain or is a subdomain
      if ($host === $blocked || strpos($host, '.' . $blocked) !== false) {
        return true;
      }
    }
  }

  return false;
}

function process_link_rel($url, $existing_rel, $post_id) {
  $opts = get_option('cnp_seo_settings', []);
  $respect_existing = $opts['respect_existing_rel'] ?? 1;

  if ($respect_existing && $existing_rel) {
    $rel_parts = array_filter(explode(' ', $existing_rel));
  } else {
    $rel_parts = [];
  }

  // Affiliate detection
  if (is_affiliate_link($url, $post_id)) {
    $rel_parts = array_unique(array_merge($rel_parts, ['sponsored', 'nofollow']));
  }

  // Force external nofollow
  if (!empty($opts['force_external_nofollow']) && !in_array('nofollow', $rel_parts)) {
    $rel_parts[] = 'nofollow';
  }

  return implode(' ', $rel_parts);
}

function is_affiliate_link($url, $post_id) {
  $opts = get_option('cnp_seo_settings', []);
  $mode = $opts['affiliate_detection_mode'] ?? 'auto';

  if ($mode === 'manual') {
    // Check for affiliate toggle (from Task 05)
    return get_post_meta($post_id, '_cnp_review_affiliate', true) ? true : false;
  }

  // Auto mode: check URL patterns
  $param_keys = array_filter(array_map('trim', explode("\n", $opts['affiliate_param_keys'] ?? "aff\nref\nutm_source=affiliate")));

  $parsed_url = parse_url($url);
  if (!isset($parsed_url['query'])) {
    return false;
  }

  parse_str($parsed_url['query'], $query_params);

  foreach ($param_keys as $key) {
    if (strpos($key, '=') !== false) {
      // key=value pattern
      list($param_key, $param_value) = explode('=', $key, 2);
      if (isset($query_params[$param_key]) && strpos($query_params[$param_key], $param_value) !== false) {
        return true;
      }
    } else {
      // key presence
      if (isset($query_params[$key])) {
        return true;
      }
    }
  }

  return false;
}

// Redirect Manager
function handle_redirects() {
  // Skip in admin/REST
  if (is_admin() || wp_is_json_request()) {
    return;
  }

  $request_uri = $_SERVER['REQUEST_URI'] ?? '';
  $path = parse_url($request_uri, PHP_URL_PATH);

  $redirects = get_option('cnp_seo_redirects', []);

  foreach ($redirects as $redirect) {
    if (empty($redirect['active'])) {
      continue;
    }

    $from = $redirect['from'];
    $to = $redirect['to'];
    $type = $redirect['type'];
    $wildcard = $redirect['wildcard'];

    $matched = false;
    $target_path = '';

    if ($wildcard) {
      // Wildcard matching
      if (strpos($from, '*') !== false) {
        $pattern = str_replace('*', '(.*)', $from);
        if (preg_match('#^' . $pattern . '$#', $path, $matches)) {
          $matched = true;
          $remainder = $matches[1];

          if (strpos($to, '*') !== false) {
            $target_path = str_replace('*', $remainder, $to);
          } else {
            $target_path = $to . $remainder;
          }
        }
      }
    } else {
      // Exact match
      if ($path === $from) {
        $matched = true;
        $target_path = $to;
      }
    }

    if ($matched) {
      if ($type === '410') {
        status_header(410);
        exit;
      } elseif ($type === '301' && $target_path) {
        // Preserve query string if target doesn't have one
        if (strpos($target_path, '?') === false) {
          $query_string = parse_url($request_uri, PHP_URL_QUERY);
          if ($query_string) {
            $target_path .= '?' . $query_string;
          }
        }

        // Convert relative URLs to absolute
        if (strpos($target_path, 'http') !== 0) {
          $target_path = home_url($target_path);
        }

        wp_redirect($target_path, 301);
        exit;
      }
    }
  }
}

// Redirect Import/Export Functions
function import_redirects_csv($csv_content) {
  $lines = explode("\n", trim($csv_content));
  $header = str_getcsv(array_shift($lines));

  $redirects = [];
  $imported = 0;
  $ignored = 0;

  foreach ($lines as $line) {
    if (empty(trim($line))) continue;

    $data = str_getcsv($line);

    if (count($data) !== 5) {
      $ignored++;
      continue;
    }

    $redirect = [
      'from' => sanitize_text_field($data[0]),
      'to' => sanitize_text_field($data[1]),
      'type' => sanitize_text_field($data[2]),
      'wildcard' => (int) $data[3],
      'active' => (int) $data[4],
    ];

    // Validate
    if (empty($redirect['from']) || !in_array($redirect['type'], ['301', '410'])) {
      $ignored++;
      continue;
    }

    $redirects[] = $redirect;
    $imported++;
  }

  if (!empty($redirects)) {
    update_option('cnp_seo_redirects', $redirects);
    flush_rewrite_rules(false);
  }

  return ['imported' => $imported, 'ignored' => $ignored];
}

function export_redirects_csv() {
  $redirects = get_option('cnp_seo_redirects', []);

  $output = "from,to,type,wildcard,active\n";

  foreach ($redirects as $redirect) {
    $output .= sprintf(
      "%s,%s,%s,%d,%d\n",
      $redirect['from'],
      $redirect['to'],
      $redirect['type'],
      $redirect['wildcard'],
      $redirect['active']
    );
  }

  return $output;
}

// Broken Link Scanner
function run_broken_link_scan() {
  $opts = get_option('cnp_seo_settings', []);
  if (empty($opts['broken_scan_enabled'])) {
    return;
  }

  $max_urls = $opts['broken_scan_max_urls'] ?? 500;
  $timeout = $opts['broken_scan_timeout'] ?? 8;
  $user_agent = $opts['broken_scan_user_agent'] ?? 'CNPLinkBot/1.0 (+' . home_url() . ')';

  // Get recent posts
  $posts = get_posts([
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => 50, // Check recent posts
    'orderby' => 'modified',
    'order' => 'DESC',
  ]);

  $urls_to_check = [];

  foreach ($posts as $post) {
    $content = $post->post_content;
    $excerpt = $post->post_excerpt;

    // Extract links from content
    $links = extract_links_from_content($content . ' ' . $excerpt);

    foreach ($links as $link) {
      if (is_external_link($link) && !is_blocked_domain($link)) {
        $urls_to_check[$link] = [
          'url' => $link,
          'post_id' => $post->ID,
          'post_title' => $post->post_title,
        ];
      }
    }

    if (count($urls_to_check) >= $max_urls) {
      break;
    }
  }

  // Limit to max URLs
  $urls_to_check = array_slice($urls_to_check, 0, $max_urls, true);

  // Check links
  $results = [
    'run_time' => current_time('timestamp'),
    'total_checked' => count($urls_to_check),
    'ok' => 0,
    'broken' => 0,
    'warning' => 0,
    'broken_links' => [],
    'warning_links' => [],
  ];

  foreach ($urls_to_check as $link_data) {
    $status = check_link_status($link_data['url'], $timeout, $user_agent);

    if ($status >= 200 && $status < 300) {
      $results['ok']++;
    } elseif ($status >= 400 && $status < 500) {
      $results['broken']++;
      $results['broken_links'][] = [
        'url' => $link_data['url'],
        'status' => $status,
        'post_id' => $link_data['post_id'],
        'post_title' => $link_data['post_title'],
      ];
    } elseif ($status >= 300 && $status < 400) {
      $results['warning']++;
      $results['warning_links'][] = [
        'url' => $link_data['url'],
        'status' => $status,
        'post_id' => $link_data['post_id'],
        'post_title' => $link_data['post_title'],
      ];
    } else {
      $results['broken']++;
      $results['broken_links'][] = [
        'url' => $link_data['url'],
        'status' => $status,
        'post_id' => $link_data['post_id'],
        'post_title' => $link_data['post_title'],
      ];
    }
  }

  // Store results
  update_option('cnp_seo_broken_report', $results);

  // Send email if configured
  if (!empty($opts['broken_report_email'])) {
    send_broken_link_report($results, $opts['broken_report_email']);
  }

  return $results;
}

function extract_links_from_content($content) {
  $links = [];

  if (preg_match_all('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>/i', $content, $matches)) {
    $links = $matches[1];
  }

  return array_unique($links);
}

function check_link_status($url, $timeout, $user_agent) {
  $args = [
    'timeout' => $timeout,
    'user-agent' => $user_agent,
    'redirection' => 0,
    'sslverify' => false,
  ];

  $response = wp_remote_head($url, $args);

  if (is_wp_error($response)) {
    // Try GET if HEAD fails
    $response = wp_remote_get($url, $args);
    if (is_wp_error($response)) {
      return 0; // Connection error
    }
  }

  $status = wp_remote_retrieve_response_code($response);
  return $status ?: 0;
}

function send_broken_link_report($results, $email) {
  $subject = 'CNP SEO Broken Link Report - ' . get_bloginfo('name');

  $message = "Broken Link Scan Report\n\n";
  $message .= "Run Time: " . date('Y-m-d H:i:s', $results['run_time']) . "\n";
  $message .= "Total URLs Checked: " . $results['total_checked'] . "\n";
  $message .= "OK: " . $results['ok'] . "\n";
  $message .= "Broken: " . $results['broken'] . "\n";
  $message .= "Warnings: " . $results['warning'] . "\n\n";

  if (!empty($results['broken_links'])) {
    $message .= "BROKEN LINKS:\n";
    foreach (array_slice($results['broken_links'], 0, 20) as $link) {
      $message .= "- {$link['url']} (Status: {$link['status']}) in \"{$link['post_title']}\"\n";
    }
    if (count($results['broken_links']) > 20) {
      $message .= "... and " . (count($results['broken_links']) - 20) . " more\n";
    }
  }

  wp_mail($email, $subject, $message);
}

// Helper functions for admin
function get_broken_link_report() {
  return get_option('cnp_seo_broken_report', []);
}

function get_redirects_for_admin() {
  return get_option('cnp_seo_redirects', []);
}

// REST API helper functions
function preview_link_policy($content, $post_id) {
  $opts = get_option('cnp_seo_settings', []);
  $links = [];

  if (empty($opts['link_policy_enabled'])) {
    return $links;
  }

  // Extract links from content
  $dom = new \DOMDocument();
  $dom->loadHTML('<div>' . $content . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

  $anchors = $dom->getElementsByTagName('a');

  foreach ($anchors as $anchor) {
    $href = $anchor->getAttribute('href');
    $rel = $anchor->getAttribute('rel');

    if (empty($href) || !preg_match('#^https?://#', $href)) {
      continue;
    }

    $is_external = is_external_link($href);
    $action = 'unchanged';
    $new_rel = $rel;

    if ($is_external) {
      // Check blocklist
      if (is_blocked_domain($href)) {
        $action = 'blocked';
      } else {
        // Process rel attributes
        $processed_rel = process_link_rel($href, $rel, $post_id);
        if ($processed_rel !== $rel) {
          $new_rel = $processed_rel;
          if (strpos($processed_rel, 'nofollow') !== false) {
            $action = 'nofollow';
          } elseif (strpos($processed_rel, 'sponsored') !== false) {
            $action = 'sponsored_nofollow';
          }
        }
      }
    }

    $links[] = [
      'href' => $href,
      'rel_before' => $rel,
      'rel_after' => $new_rel,
      'action' => $action,
    ];
  }

  return $links;
}

function test_redirect_path($path) {
  $redirects = get_option('cnp_seo_redirects', []);

  foreach ($redirects as $redirect) {
    if (empty($redirect['active'])) {
      continue;
    }

    $from = $redirect['from'];
    $to = $redirect['to'];
    $type = $redirect['type'];
    $wildcard = $redirect['wildcard'];

    $matched = false;
    $target_path = '';

    if ($wildcard) {
      // Wildcard matching
      if (strpos($from, '*') !== false) {
        $pattern = str_replace('*', '(.*)', $from);
        if (preg_match('#^' . $pattern . '$#', $path, $matches)) {
          $matched = true;
          $remainder = $matches[1];

          if (strpos($to, '*') !== false) {
            $target_path = str_replace('*', $remainder, $to);
          } else {
            $target_path = $to . $remainder;
          }
        }
      }
    } else {
      // Exact match
      if ($path === $from) {
        $matched = true;
        $target_path = $to;
      }
    }

    if ($matched) {
      if ($type === '410') {
        return [
          'matched' => true,
          'rule' => $redirect,
          'destination' => null,
          'status' => 410,
        ];
      } elseif ($type === '301' && $target_path) {
        // Convert relative URLs to absolute
        if (strpos($target_path, 'http') !== 0) {
          $target_path = home_url($target_path);
        }

        return [
          'matched' => true,
          'rule' => $redirect,
          'destination' => $target_path,
          'status' => 301,
        ];
      }
    }
  }

  return [
    'matched' => false,
    'rule' => null,
    'destination' => null,
    'status' => null,
  ];
}
