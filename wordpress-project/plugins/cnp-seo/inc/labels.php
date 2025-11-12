<?php

namespace CNP\SEO\Labels;

if (!defined('ABSPATH')) exit;

// Add Google News metabox to post editor
add_action('add_meta_boxes', function() {
  $opts = get_option('cnp_seo_settings', []);
  if (empty($opts['gn_enabled'])) {
    return;
  }

  add_meta_box(
    'cnp-gn-labels',
    'Google News Labels',
    __NAMESPACE__ . '\\render_gn_labels_metabox',
    'post',
    'side',
    'default'
  );
});

// Render the metabox
function render_gn_labels_metabox($post) {
  wp_nonce_field('cnp_gn_labels_save', 'cnp_gn_labels_nonce');

  $gn_label = get_post_meta($post->ID, '_cnp_gn_label', true);
  $sponsored = get_post_meta($post->ID, '_cnp_sponsored', true);

  ?>
  <p>
    <label for="cnp_gn_label"><strong>Google News Label:</strong></label><br>
    <select name="cnp_gn_label" id="cnp_gn_label" style="width: 100%;">
      <option value="">None</option>
      <option value="opinion" <?php selected($gn_label, 'opinion'); ?>>Opinion</option>
      <option value="satire" <?php selected($gn_label, 'satire'); ?>>Satire</option>
      <option value="press_release" <?php selected($gn_label, 'press_release'); ?>>Press Release</option>
      <option value="user_generated" <?php selected($gn_label, 'user_generated'); ?>>User Generated</option>
    </select>
    <p class="description">Select the appropriate label for Google News. This will be included in feeds and schema.</p>
  </p>

  <p>
    <label for="cnp_sponsored">
      <input type="checkbox" name="cnp_sponsored" id="cnp_sponsored" value="1" <?php checked($sponsored, '1'); ?>>
      <strong>Sponsored Content</strong>
    </label>
    <p class="description">Check if this is sponsored or paid content. Will require visible disclosure and may be excluded from News feeds.</p>
  </p>

  <?php if ($sponsored): ?>
  <div id="sponsored-notice" style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 8px; margin-top: 10px; border-radius: 4px;">
    <p style="margin: 0; color: #856404;"><strong>⚠️ Sponsored Content Notice:</strong></p>
    <p style="margin: 4px 0 0 0; font-size: 12px; color: #856404;">
      Ensure visible "Sponsored" or "Paid" disclosure appears near the top of the article.
      All outbound commercial links should have rel="sponsored nofollow".
    </p>
  </div>
  <?php endif; ?>

  <script>
  jQuery(document).ready(function($) {
    $('#cnp_sponsored').on('change', function() {
      if ($(this).is(':checked')) {
        $('#sponsored-notice').show();
      } else {
        $('#sponsored-notice').hide();
      }
    });
  });
  </script>
  <?php
}

// Save metabox data
add_action('save_post', function($post_id) {
  if (!isset($_POST['cnp_gn_labels_nonce']) || !wp_verify_nonce($_POST['cnp_gn_labels_nonce'], 'cnp_gn_labels_save')) {
    return;
  }

  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
    return;
  }

  // Save GN label
  $gn_label = sanitize_text_field($_POST['cnp_gn_label'] ?? '');
  $allowed_labels = ['', 'opinion', 'satire', 'press_release', 'user_generated'];

  if (in_array($gn_label, $allowed_labels)) {
    if (empty($gn_label)) {
      delete_post_meta($post_id, '_cnp_gn_label');
    } else {
      update_post_meta($post_id, '_cnp_gn_label', $gn_label);
    }
  }

  // Save sponsored flag
  $sponsored = isset($_POST['cnp_sponsored']) ? '1' : '';
  if (empty($sponsored)) {
    delete_post_meta($post_id, '_cnp_sponsored');
  } else {
    update_post_meta($post_id, '_cnp_sponsored', $sponsored);
  }
});

// Add sponsored disclosure to content
add_filter('the_content', function($content) {
  if (!is_singular('post')) {
    return $content;
  }

  $post_id = get_the_ID();
  $sponsored = get_post_meta($post_id, '_cnp_sponsored', true);

  if ($sponsored) {
    $disclosure = '<div class="cnp-sponsored-disclosure" style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 12px; margin-bottom: 20px; border-radius: 4px; font-size: 14px;">';
    $disclosure .= '<strong>Sponsored Content:</strong> This article contains sponsored content.';
    $disclosure .= '</div>';

    $content = $disclosure . $content;
  }

  return $content;
}, 10);

// Add corrections callout to content
add_filter('the_content', function($content) {
  if (!is_singular('post')) {
    return $content;
  }

  $post_id = get_the_ID();
  $correction_note = get_post_meta($post_id, '_cnp_correction_note', true);

  if (!empty($correction_note)) {
    $correction_html = '<div class="cnp-correction-notice" style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px; margin-bottom: 20px;">';
    $correction_html .= '<strong>Correction:</strong> ' . esc_html($correction_note);
    $correction_html .= '</div>';

    $content = $correction_html . $content;
  }

  return $content;
}, 10);

// Add correction meta field to post editor (optional, for editors)
add_action('add_meta_boxes', function() {
  add_meta_box(
    'cnp-correction',
    'Correction Notice',
    __NAMESPACE__ . '\\render_correction_metabox',
    'post',
    'normal',
    'default'
  );
});

function render_correction_metabox($post) {
  wp_nonce_field('cnp_correction_save', 'cnp_correction_nonce');

  $correction_note = get_post_meta($post->ID, '_cnp_correction_note', true);

  ?>
  <p>
    <label for="cnp_correction_note"><strong>Correction Notice:</strong></label><br>
    <textarea name="cnp_correction_note" id="cnp_correction_note" rows="3" style="width: 100%;"><?php echo esc_textarea($correction_note); ?></textarea>
    <p class="description">If this article contains corrections, enter the correction text here. It will appear as a visible callout and in structured data.</p>
  </p>
  <?php
}

// Save correction data
add_action('save_post', function($post_id) {
  if (!isset($_POST['cnp_correction_nonce']) || !wp_verify_nonce($_POST['cnp_correction_nonce'], 'cnp_correction_save')) {
    return;
  }

  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
    return;
  }

  $correction_note = sanitize_textarea_field($_POST['cnp_correction_note'] ?? '');
  if (empty($correction_note)) {
    delete_post_meta($post_id, '_cnp_correction_note');
  } else {
    update_post_meta($post_id, '_cnp_correction_note', $correction_note);
  }
});
