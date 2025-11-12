<?php

namespace CNP\AUT;



if (!defined('ABSPATH')) exit;



// Admin

require_once CNP_AUT_PATH . 'admin/settings.php';

require_once CNP_AUT_PATH . 'admin/dashboard.php';



// Modules

require_once CNP_AUT_PATH . 'inc/queue.php';

require_once CNP_AUT_PATH . 'inc/jobs.php';

require_once CNP_AUT_PATH . 'inc/entities.php';

require_once CNP_AUT_PATH . 'inc/linking.php';

require_once CNP_AUT_PATH . 'inc/editor.php';

require_once CNP_AUT_PATH . 'inc/utils.php';

require_once CNP_AUT_PATH . 'inc/metrics.php';



// REST + CLI

require_once CNP_AUT_PATH . 'rest/routes.php';

if (defined('WP_CLI') && WP_CLI) {

  require_once CNP_AUT_PATH . 'cli/commands.php';

}

// Add custom cron schedule
add_filter('cron_schedules', function($schedules) {
  $schedules['cnp_automation_frequent'] = [
    'interval' => 60, // Every minute
    'display' => 'CNP Automation Frequent'
  ];
  return $schedules;
});

// Hook for processing queue
add_action('cnp_automation_process_queue', function() {
  \CNP\AUT\Jobs\process_queue(3); // Process up to 3 jobs per run
});

// Hook for job status changes (webhooks and notifications)
add_action('cnp_automation_job_status_changed', function($job_id, $new_status, $job) {
  \CNP\AUT\Utils\log_event('job_status_changed', [
    'job_id' => $job_id,
    'status' => $new_status,
    'post_id' => $job['post_id'],
    'external_id' => isset($job['brief']['external_id']) ? $job['brief']['external_id'] : null,
  ]);

  // Send webhook notification
  $webhook_url = \CNP\AUT\Utils\setting('webhook_job_status_url');
  if (!empty($webhook_url)) {
    $payload = [
      'id' => $job_id,
      'status' => $new_status,
      'post_id' => $job['post_id'],
      'external_id' => isset($job['brief']['external_id']) ? $job['brief']['external_id'] : null,
      'updated_at' => current_time('mysql'),
    ];

    \CNP\AUT\Utils\send_webhook_notification($webhook_url, $payload);
  }

  // Send email notification for failures
  if ($new_status === 'failed') {
    $email = \CNP\AUT\Utils\setting('notify_email');
    if (!empty($email)) {
      $subject = 'CNP Automation: Job Failed';
      $message = "Job #{$job_id} failed to complete.\n\n";
      $message .= "Title: {$job['brief']['title']}\n";
      $message .= "Error: {$job['error']}\n";
      $message .= "View job: " . admin_url("admin.php?page=cnp-automation&job_id={$job_id}\n");

      \CNP\AUT\Utils\send_email_notification($email, $subject, $message);
    }
  }
}, 10, 3);

// Hook for draft creation (GA4 events and metrics)
add_action('cnp_automation_draft_created', function($post_id, $job_id, $category_id, $template) {
  \CNP\AUT\Utils\log_event('ai_draft_created', [
    'post_id' => $post_id,
    'job_id' => $job_id,
    'category' => $category_id,
    'template' => $template,
  ]);

  // Emit metrics and GA4 event
  \CNP\AUT\Metrics\emit_ai_draft_created($post_id, $job_id, $category_id, $template);
}, 10, 4);

// Hook for post publishing (GA4 events and metrics)
add_action('transition_post_status', function($new_status, $old_status, $post) {
  if ($new_status === 'publish' && $old_status !== 'publish') {
    // Check if this post was created by automation
    if (get_post_meta($post->ID, '_cnp_automation_job', true) === 'generated') {
      $needs_review = get_post_meta($post->ID, '_cnp_needs_review', true);

      if (!$needs_review) {
        // Find the job ID
        global $wpdb;
        $table = $wpdb->prefix . 'cnp_jobs';
        $job = $wpdb->get_row($wpdb->prepare(
          "SELECT id, brief FROM $table WHERE post_id = %d LIMIT 1",
          $post->ID
        ));

        if ($job) {
          \CNP\AUT\Utils\log_event('ai_draft_published', [
            'post_id' => $post->ID,
            'job_id' => $job->id,
            'title' => $post->post_title,
          ]);

          // Get category for metrics
          $categories = get_the_category($post->ID);
          $category_slug = !empty($categories) ? $categories[0]->slug : null;

          // Emit metrics and GA4 event
          \CNP\AUT\Metrics\emit_ai_draft_published($post->ID, $category_slug);

          // Transition job to published
          \CNP\AUT\Queue\transition_status($job->id, 'published');
        }
      }
    }
  }
}, 10, 3);

// Hook for automatic entity extraction on post save
add_action('save_post', function($post_id, $post, $update) {
  // Only process published posts
  if ($post->post_status !== 'publish' || $post->post_type !== 'post') {
    return;
  }

  // Skip autosaves and revisions
  if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
    return;
  }

  // Extract entities
  $custom_entities = get_post_meta($post_id, '_cnp_entities', true);
  \CNP\AUT\Entities\extract_entities($post_id, $custom_entities);
}, 10, 3);

// Hook for cleaning up entities when post is deleted
add_action('before_delete_post', function($post_id) {
  \CNP\AUT\Entities\remove_post_entities($post_id);
});

// Hook for cleaning up entities when post is trashed
add_action('wp_trash_post', function($post_id) {
  \CNP\AUT\Entities\remove_post_entities($post_id);
});

// Editorial review UI for generated posts
add_action('admin_notices', function() {
  global $post;

  if (!$post || !is_admin() || get_current_screen()->base !== 'post') {
    return;
  }

  // Check if this is a generated post that needs review
  if (get_post_meta($post->ID, '_cnp_automation_job', true) !== 'generated') {
    return;
  }

  $needs_review = get_post_meta($post->ID, '_cnp_needs_review', true);

  if ($needs_review) {
    echo '<div class="notice notice-warning is-dismissible">';
    echo '<h3>🤖 Automation: Needs Editorial Review</h3>';
    echo '<p>This post was generated by AI and requires editorial review before publication.</p>';
    echo '<p><strong>Please complete the QA checklist below and mark as ready for review.</strong></p>';
    echo '</div>';
  }
});

// Add meta box for QA checklist
add_action('add_meta_boxes', function() {
  global $post;

  if (!$post || get_post_meta($post->ID, '_cnp_automation_job', true) !== 'generated') {
    return;
  }

  add_meta_box(
    'cnp-automation-qa',
    '🤖 Automation QA Checklist',
    __NAMESPACE__.'\\render_qa_checklist_meta_box',
    'post',
    'side',
    'high'
  );
});

function render_qa_checklist_meta_box($post) {
  $checklist = get_post_meta($post->ID, '_cnp_qa_checklist', true);
  if (!$checklist) {
    $checklist = \CNP\AUT\Utils\generate_qa_checklist();
  }

  $needs_review = get_post_meta($post->ID, '_cnp_needs_review', true);
  $featured_missing = get_post_meta($post->ID, '_cnp_featured_missing', true);

  wp_nonce_field('cnp_qa_checklist', 'cnp_qa_checklist_nonce');

  echo '<div id="cnp-qa-checklist">';

  // Status indicator
  if ($needs_review) {
    echo '<div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 8px; margin-bottom: 12px; border-radius: 4px;">';
    echo '<strong style="color: #856404;">⏳ Needs Review</strong>';
    echo '<p style="margin: 4px 0 0 0; font-size: 12px; color: #856404;">Complete checklist items and mark as ready.</p>';
    echo '</div>';
  } else {
    echo '<div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 8px; margin-bottom: 12px; border-radius: 4px;">';
    echo '<strong style="color: #155724;">✅ Ready for Review</strong>';
    echo '<p style="margin: 4px 0 0 0; font-size: 12px; color: #155724;">All checks passed, ready for publication.</p>';
    echo '</div>';
  }

  // Featured image reminder
  if ($featured_missing) {
    echo '<div style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 8px; margin-bottom: 12px; border-radius: 4px;">';
    echo '<strong style="color: #721c24;">📷 Featured Image Missing</strong>';
    echo '<p style="margin: 4px 0 0 0; font-size: 12px; color: #721c24;">Add a featured image to complete this post.</p>';
    echo '<a href="#" onclick="wp.media.editor.open(); return false;" style="color: #721c24; text-decoration: underline;">Set Featured Image</a>';
    echo '</div>';
  }

  // Checklist items
  echo '<div style="margin-bottom: 12px;">';
  echo '<strong>Checklist Items:</strong>';
  echo '</div>';

  $items = [
    'hero_present' => 'Hero/intro section present',
    'key_points_present' => 'Key points section included',
    'why_matters_present' => 'Why this matters section included',
    'sources_present_if_required' => 'Sources cited (if factual claims made)',
    'tags_minimum' => 'At least 3 relevant tags assigned',
    'ai_disclosure_present' => 'AI disclosure block present',
    'author_has_bio' => 'Author has bio/profile',
    'internal_links_minimum' => 'Minimum internal links added',
    'reading_time_reasonable' => 'Reading time is reasonable (2-8 min)',
  ];

  foreach ($items as $key => $label) {
    $checked = !empty($checklist[$key]) ? 'checked' : '';
    $status_class = !empty($checklist[$key]) ? 'qa-checked' : 'qa-unchecked';

    echo '<div class="qa-item" style="margin-bottom: 8px;">';
    echo '<label style="display: flex; align-items: center; cursor: pointer;">';
    echo '<input type="checkbox" name="cnp_qa_checklist['.$key.']" value="1" '.$checked.' style="margin-right: 8px;">';
    echo '<span class="'.$status_class.'" style="flex: 1;">'.$label.'</span>';
    echo '</label>';
    echo '</div>';
  }

  // Action buttons
  echo '<div style="border-top: 1px solid #ddd; padding-top: 12px; margin-top: 12px;">';

  if ($needs_review) {
    echo '<button type="button" id="cnp-mark-ready" class="button button-primary" style="width: 100%; margin-bottom: 8px;">';
    echo 'Mark Ready for Review';
    echo '</button>';
  }

  // Job info
  global $wpdb;
  $table = $wpdb->prefix . 'cnp_jobs';
  $job = $wpdb->get_row($wpdb->prepare(
    "SELECT id, status, created_at FROM $table WHERE post_id = %d LIMIT 1",
    $post->ID
  ));

  if ($job) {
    echo '<div style="font-size: 11px; color: #666; margin-top: 8px;">';
    echo '<strong>Job Info:</strong><br>';
    echo "ID: {$job->id} | Status: {$job->status}<br>";
    echo "Created: " . date('M j, Y H:i', strtotime($job->created_at));
    echo '</div>';
  }

  echo '</div>';
  echo '</div>';

  // JavaScript for the mark ready button
  ?>
  <script>
  jQuery(document).ready(function($) {
    $('#cnp-mark-ready').on('click', function() {
      if (confirm('Mark this post as ready for review? This will allow it to be published.')) {
        // Mark all unchecked items as checked
        $('#cnp-qa-checklist input[type="checkbox"]:not(:checked)').prop('checked', true);

        // Submit the form
        $('#post').append('<input type="hidden" name="cnp_mark_ready" value="1">').submit();
      }
    });
  });
  </script>
  <?php
}

// Save QA checklist
add_action('save_post', function($post_id) {
  if (!isset($_POST['cnp_qa_checklist_nonce']) ||
      !wp_verify_nonce($_POST['cnp_qa_checklist_nonce'], 'cnp_qa_checklist')) {
    return;
  }

  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  // Only process for generated posts
  if (get_post_meta($post_id, '_cnp_automation_job', true) !== 'generated') {
    return;
  }

  // Save checklist
  if (isset($_POST['cnp_qa_checklist']) && is_array($_POST['cnp_qa_checklist'])) {
    $checklist = array_map('intval', $_POST['cnp_qa_checklist']);
    update_post_meta($post_id, '_cnp_qa_checklist', $checklist);
  }

  // Mark as ready for review
  if (isset($_POST['cnp_mark_ready'])) {
    delete_post_meta($post_id, '_cnp_needs_review');
    \CNP\AUT\Utils\log_event('post_marked_ready', ['post_id' => $post_id]);

    // Emit QA override metric
    $categories = get_the_category($post_id);
    $category_slug = !empty($categories) ? $categories[0]->slug : null;
    \CNP\AUT\Metrics\emit_qa_override($post_id, isset(wp_get_current_user()->roles[0]) ? wp_get_current_user()->roles[0] : 'unknown');
  }
});

// Hook for QA result tracking
add_action('cnp_automation_qa_result', function($post_id, $result, $failures = 0, $warnings = 0) {
  \CNP\AUT\Metrics\emit_qa_result($post_id, $result, $failures, $warnings);
}, 10, 4);

// Hook for internal linking
add_action('cnp_automation_links_inserted', function($post_id, $count) {
  \CNP\AUT\Metrics\emit_internal_links_inserted($post_id, $count);
}, 10, 2);

// Hook for recirculation impressions
add_action('cnp_automation_rc_impression', function($post_id, $block = 'related_in_hub', $items = 1) {
  \CNP\AUT\Metrics\emit_recirculation_impression($post_id, $block, $items);
}, 10, 3);

// Hook for newsletter signup
add_action('cnp_newsletter_signup', function($source = 'unknown') {
  \CNP\AUT\Metrics\emit_newsletter_signup($source);
});

// Hook for deep scroll tracking
add_action('cnp_deep_scroll', function($post_id, $depth = 75) {
  \CNP\AUT\Metrics\emit_deep_scroll($post_id, $depth);
});

// Daily maintenance cron hook
add_action('cnp_automation_daily_maintenance', function() {
  \CNP\AUT\Metrics\purge_old_metrics();
  \CNP\AUT\Metrics\rebuild_all_rc_ctr(7);
});
