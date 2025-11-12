<?php

namespace CNP\AUT\Editor;

use CNP\AUT\Linking as Linking;
use CNP\AUT\Utils as Utils;

if (!defined('ABSPATH')) exit;

/**
 * Register editor assets and sidebar panel
 */
add_action('enqueue_block_editor_assets', function() {
  // Only load for posts
  $screen = get_current_screen();
  if (!$screen || $screen->post_type !== 'post') {
    return;
  }

  // Check if linking is enabled
  if (!Utils\setting('linker_enabled', 1)) {
    return;
  }

  wp_enqueue_script(
    'cnp-automation-linking',
    CNP_AUT_URL . 'assets/js/editor-linking.js',
    ['wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-api-fetch'],
    CNP_AUT_VER,
    true
  );

  wp_enqueue_style(
    'cnp-automation-linking',
    CNP_AUT_URL . 'assets/css/editor-linking.css',
    [],
    CNP_AUT_VER
  );

  // Localize script with settings
  wp_localize_script('cnp-automation-linking', 'cnpLinkingSettings', [
    'nonce' => wp_create_nonce('wp_rest'),
    'restUrl' => rest_url('cnp-automation/v1/'),
    'maxSuggestions' => Utils\setting('linker_max_suggestions', 10),
    'perSectionCap' => Utils\setting('linker_per_section_cap', 2),
    'minAnchorLength' => Utils\setting('linker_anchor_min_chars', 18),
  ]);
});

/**
 * Register the editor sidebar panel
 */
add_action('init', function() {
  if (!Utils\setting('linker_enabled', 1)) {
    return;
  }

  // Register the plugin script - the plugin itself is registered in JavaScript
  wp_register_script(
    'cnp-automation-linking-panel',
    CNP_AUT_URL . 'assets/js/editor-linking-panel.js',
    ['wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data'],
    CNP_AUT_VER,
    true
  );
});

/**
 * Add the sidebar panel using the classic editor approach (for backward compatibility)
 */
add_action('add_meta_boxes', function() {
  if (!Utils\setting('linker_enabled', 1)) {
    return;
  }

  // Only add for posts
  $screen = get_current_screen();
  if (!$screen || $screen->post_type !== 'post') {
    return;
  }

  add_meta_box(
    'cnp-automation-linking',
    'Internal Links',
    __NAMESPACE__ . '\\render_linking_metabox',
    'post',
    'side',
    'default'
  );
});

/**
 * Render the linking metabox
 */
function render_linking_metabox($post) {
  // Check if post is published (suggestions only work for published posts)
  $is_published = $post->post_status === 'publish';

  ?>
  <div id="cnp-linking-metabox" style="font-size: 12px;">
    <p style="margin-top: 0; color: #666;">
      Get smart internal linking suggestions based on content analysis.
    </p>

    <?php if (!$is_published): ?>
      <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 8px; margin-bottom: 12px; border-radius: 4px;">
        <strong style="color: #856404;">Publish first</strong>
        <p style="margin: 4px 0 0 0; color: #856404;">Suggestions require the post to be published.</p>
      </div>
    <?php else: ?>
      <button type="button" id="cnp-get-suggestions" class="button button-primary" style="width: 100%; margin-bottom: 12px;">
        Get Suggestions
      </button>

      <div id="cnp-linking-results" style="display: none;">
        <div id="cnp-linking-loading" style="text-align: center; padding: 20px; display: none;">
          <span class="spinner is-active"></span>
          <p>Analyzing content...</p>
        </div>

        <div id="cnp-linking-suggestions"></div>

        <div id="cnp-linking-actions" style="margin-top: 12px; display: none;">
          <button type="button" id="cnp-insert-selected" class="button button-secondary" style="width: 100%;">
            Insert Selected Links
          </button>
        </div>
      </div>
    <?php endif; ?>

    <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #ddd;">
      <p style="margin: 0; color: #666; font-size: 11px;">
        Links are inserted respecting section caps and anchor length requirements.
      </p>
    </div>
  </div>

  <script>
  jQuery(document).ready(function($) {
    var suggestions = [];
    var sectionCounts = {};

    $('#cnp-get-suggestions').on('click', function() {
      var $button = $(this);
      var $results = $('#cnp-linking-results');
      var $loading = $('#cnp-linking-loading');
      var $suggestions = $('#cnp-linking-suggestions');

      $button.prop('disabled', true).text('Getting suggestions...');
      $loading.show();
      $results.show();

      $.ajax({
        url: '<?php echo rest_url('cnp-automation/v1/link-suggestions'); ?>',
        method: 'POST',
        data: {
          post_id: <?php echo $post->ID; ?>
        },
        headers: {
          'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
        },
        success: function(response) {
          suggestions = response.suggestions || [];
          renderSuggestions(suggestions);
          $('#cnp-linking-actions').show();
        },
        error: function(xhr) {
          $suggestions.html('<div style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 8px; border-radius: 4px;"><strong style="color: #721c24;">Error:</strong> ' + (xhr.responseJSON?.message || 'Failed to get suggestions') + '</div>');
        },
        complete: function() {
          $button.prop('disabled', false).text('Get Suggestions');
          $loading.hide();
        }
      });
    });

    function renderSuggestions(suggestions) {
      var $container = $('#cnp-linking-suggestions');
      sectionCounts = {};

      if (suggestions.length === 0) {
        $container.html('<p style="color: #666; font-style: italic;">No suggestions found for this post.</p>');
        return;
      }

      var html = '<div style="max-height: 400px; overflow-y: auto;">';

      // Group by type
      var grouped = {};
      suggestions.forEach(function(suggestion) {
        if (!grouped[suggestion.type]) {
          grouped[suggestion.type] = [];
        }
        grouped[suggestion.type].push(suggestion);
      });

      Object.keys(grouped).forEach(function(type) {
        html += '<h4 style="margin: 16px 0 8px 0; text-transform: capitalize; color: #23282d;">' + type + ' Suggestions</h4>';

        grouped[type].forEach(function(suggestion) {
          var checked = '';
          var disabled = '';
          var note = '';

          // Check if already linked
          if (suggestionExists(suggestion.url)) {
            checked = 'checked';
            disabled = 'disabled';
            note = ' <em style="color: #666;">(already linked)</em>';
          }

          html += '<div style="margin-bottom: 12px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9;">';
          html += '<label style="display: block; cursor: pointer;">';
          html += '<input type="checkbox" class="cnp-link-checkbox" value="' + suggestion.id + '" ' + checked + ' ' + disabled + ' style="margin-right: 8px;">';
          html += '<strong style="color: #23282d;">' + suggestion.title + '</strong>';
          html += note;
          html += '</label>';

          if (suggestion.entities_matched && suggestion.entities_matched.length > 0) {
            html += '<div style="margin-top: 4px; font-size: 11px; color: #666;">';
            html += 'Entities: ' + suggestion.entities_matched.join(', ');
            html += '</div>';
          }

          if (suggestion.anchors && suggestion.anchors.length > 0) {
            html += '<div style="margin-top: 4px;">';
            html += '<select class="cnp-anchor-select" data-suggestion="' + suggestion.id + '" style="width: 100%; font-size: 11px;">';
            suggestion.anchors.forEach(function(anchor, index) {
              html += '<option value="' + anchor.text + '">' + anchor.text.substring(0, 50) + '...</option>';
            });
            html += '</select>';
            html += '</div>';
          }

          html += '</div>';
        });
      });

      html += '</div>';
      $container.html(html);
    }

    function suggestionExists(url) {
      var content = tinymce.activeEditor ? tinymce.activeEditor.getContent() : $('#content').val();
      return content.indexOf(url) !== -1;
    }

    $('#cnp-insert-selected').on('click', function() {
      var $button = $(this);
      var selectedSuggestions = [];

      $('.cnp-link-checkbox:checked:not(:disabled)').each(function() {
        var suggestionId = $(this).val();
        var $select = $('.cnp-anchor-select[data-suggestion="' + suggestionId + '"]');
        var anchorText = $select.length ? $select.val() : '';

        selectedSuggestions.push({
          suggestion_id: suggestionId,
          anchor_text: anchorText,
          section: 'body' // Default section
        });
      });

      if (selectedSuggestions.length === 0) {
        alert('Please select at least one suggestion to insert.');
        return;
      }

      $button.prop('disabled', true).text('Inserting...');

      $.ajax({
        url: '<?php echo rest_url('cnp-automation/v1/link-insert'); ?>',
        method: 'POST',
        data: {
          post_id: <?php echo $post->ID; ?>,
          inserts: selectedSuggestions
        },
        headers: {
          'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
        },
        success: function(response) {
          alert('Inserted ' + response.inserted + ' links successfully!' + (response.skipped > 0 ? ' (' + response.skipped + ' skipped)' : ''));

          // Refresh suggestions to show updated status
          $('#cnp-get-suggestions').click();
        },
        error: function(xhr) {
          alert('Error inserting links: ' + (xhr.responseJSON?.message || 'Unknown error'));
        },
        complete: function() {
          $button.prop('disabled', false).text('Insert Selected Links');
        }
      });
    });
  });
  </script>
  <?php
}

/**
 * Create assets directory and files if they don't exist
 * This would normally be handled by a build process
 */
add_action('admin_init', function() {
  $js_dir = CNP_AUT_PATH . 'assets/js';
  $css_dir = CNP_AUT_PATH . 'assets/css';

  if (!file_exists($js_dir)) {
    wp_mkdir_p($js_dir);
  }

  if (!file_exists($css_dir)) {
    wp_mkdir_p($css_dir);
  }

  // Create basic JS file for block editor (if needed)
  $js_file = $js_dir . '/editor-linking.js';
  if (!file_exists($js_file)) {
    file_put_contents($js_file, '// CNP Automation Linking - Block Editor Integration
(function(wp) {
  const { registerPlugin } = wp.plugins;
  const { PluginSidebar, PluginSidebarMoreMenuItem } = wp.editPost;
  const { PanelBody, Button } = wp.components;
  const { useState, useEffect } = wp.element;
  const { useSelect, useDispatch } = wp.data;
  const { apiFetch } = wp;

  // Register sidebar panel
  registerPlugin("cnp-linking-sidebar", {
    render: function() {
      const [suggestions, setSuggestions] = useState([]);
      const [loading, setLoading] = useState(false);

      const postId = useSelect(function(select) {
        return select("core/editor").getCurrentPostId();
      });

      const getSuggestions = function() {
        setLoading(true);

        apiFetch({
          path: "cnp-automation/v1/link-suggestions",
          method: "POST",
          data: { post_id: postId }
        }).then(function(response) {
          setSuggestions(response.suggestions || []);
          setLoading(false);
        }).catch(function(error) {
          console.error("Failed to get suggestions:", error);
          setLoading(false);
        });
      };

      return wp.element.createElement(
        PluginSidebar,
        {
          name: "cnp-linking-sidebar",
          title: "Internal Links",
          icon: "admin-links"
        },
        wp.element.createElement(
          PanelBody,
          { title: "Link Suggestions", initialOpen: true },
          wp.element.createElement(
            Button,
            {
              isPrimary: true,
              onClick: getSuggestions,
              disabled: loading,
              style: { width: "100%", marginBottom: "12px" }
            },
            loading ? "Getting Suggestions..." : "Get Suggestions"
          ),
          suggestions.length > 0 && wp.element.createElement(
            "div",
            { style: { maxHeight: "400px", overflowY: "auto" } },
            suggestions.map(function(suggestion) {
              return wp.element.createElement(
                "div",
                {
                  key: suggestion.id,
                  style: {
                    marginBottom: "12px",
                    padding: "8px",
                    border: "1px solid #ddd",
                    borderRadius: "4px"
                  }
                },
                wp.element.createElement("strong", null, suggestion.title),
                suggestion.entities_matched && wp.element.createElement(
                  "div",
                  { style: { fontSize: "11px", color: "#666", marginTop: "4px" } },
                  "Entities: " + suggestion.entities_matched.join(", ")
                )
              );
            })
          )
        )
      );
    }
  });
})(window.wp);
');
  }

  // Create basic CSS file
  $css_file = $css_dir . '/editor-linking.css';
  if (!file_exists($css_file)) {
    file_put_contents($css_file, '/* CNP Automation Linking - Editor Styles */
#cnp-linking-metabox .spinner {
  float: none;
  margin: 0 auto;
}

#cnp-linking-metabox .cnp-link-checkbox {
  margin-right: 8px;
}

#cnp-linking-metabox .cnp-anchor-select {
  margin-top: 4px;
  font-size: 11px;
}

#cnp-linking-results {
  margin-top: 12px;
}

/* Block editor styles would go here */
');
  }
});
