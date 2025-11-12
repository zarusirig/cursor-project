<?php

namespace CNP\SEO\Meta;



if (!defined('ABSPATH')) exit;



// Register post meta keys
add_action('init', function(){
  $post_types = get_post_types(['public' => true], 'names');

  foreach ($post_types as $post_type) {
    register_post_meta($post_type, '_cnp_seo_title', [
      'show_in_rest' => true,
      'single' => true,
      'type' => 'string',
      'sanitize_callback' => 'sanitize_text_field',
    ]);

    register_post_meta($post_type, '_cnp_seo_description', [
      'show_in_rest' => true,
      'single' => true,
      'type' => 'string',
      'sanitize_callback' => 'sanitize_textarea_field',
    ]);

    register_post_meta($post_type, '_cnp_seo_canonical', [
      'show_in_rest' => true,
      'single' => true,
      'type' => 'string',
      'sanitize_callback' => function($value) {
        return wp_http_validate_url($value) ? esc_url_raw($value) : '';
      },
    ]);

    register_post_meta($post_type, '_cnp_seo_noindex', [
      'show_in_rest' => true,
      'single' => true,
      'type' => 'boolean',
      'sanitize_callback' => function($value) {
        return (bool) $value;
      },
    ]);

    register_post_meta($post_type, '_cnp_seo_nofollow', [
      'show_in_rest' => true,
      'single' => true,
      'type' => 'boolean',
      'sanitize_callback' => function($value) {
        return (bool) $value;
      },
    ]);

    register_post_meta($post_type, '_cnp_seo_og_image', [
      'show_in_rest' => true,
      'single' => true,
      'type' => 'string',
      'sanitize_callback' => function($value) {
        return wp_http_validate_url($value) ? esc_url_raw($value) : '';
      },
    ]);
  }
});

// Add metabox
add_action('add_meta_boxes', function(){
  $post_types = get_post_types(['public' => true], 'names');

  foreach ($post_types as $post_type) {
    add_meta_box(
      'cnp-seo-meta',
      'CNP SEO',
      __NAMESPACE__ . '\\render_metabox',
      $post_type,
      'normal',
      'high'
    );
  }
});

// Save metabox data
add_action('save_post', function($post_id){
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (!current_user_can('edit_post', $post_id)) return;

  $fields = [
    '_cnp_seo_title',
    '_cnp_seo_description',
    '_cnp_seo_canonical',
    '_cnp_seo_noindex',
    '_cnp_seo_nofollow',
    '_cnp_seo_og_image',
  ];

  foreach ($fields as $field) {
    if (isset($_POST[$field])) {
      $value = $_POST[$field];

      if (in_array($field, ['_cnp_seo_noindex', '_cnp_seo_nofollow'])) {
        $value = (bool) $value;
      }

      update_post_meta($post_id, $field, $value);
    }
  }
});

// Render metabox
function render_metabox($post){
  wp_nonce_field('cnp_seo_meta', 'cnp_seo_meta_nonce');

  $title = get_post_meta($post->ID, '_cnp_seo_title', true);
  $description = get_post_meta($post->ID, '_cnp_seo_description', true);
  $canonical = get_post_meta($post->ID, '_cnp_seo_canonical', true);
  $noindex = get_post_meta($post->ID, '_cnp_seo_noindex', true);
  $nofollow = get_post_meta($post->ID, '_cnp_seo_nofollow', true);
  $og_image = get_post_meta($post->ID, '_cnp_seo_og_image', true);

  ?>
  <table class="form-table">
    <tr>
      <th scope="row">Search Title</th>
      <td>
        <input type="text" name="_cnp_seo_title" value="<?php echo esc_attr($title); ?>" class="large-text">
        <p class="description">Custom title for search engines (leave empty to use default)</p>
      </td>
    </tr>

    <tr>
      <th scope="row">Meta Description</th>
      <td>
        <textarea name="_cnp_seo_description" rows="3" class="large-text"><?php echo esc_textarea($description); ?></textarea>
        <div class="description">
          <span id="description-count">0</span>/160 characters
        </div>
        <p class="description">Description for search results (leave empty to use excerpt)</p>
      </td>
    </tr>

    <tr>
      <th scope="row">Canonical URL</th>
      <td>
        <input type="url" name="_cnp_seo_canonical" value="<?php echo esc_url($canonical); ?>" class="large-text" placeholder="https://example.com/canonical-url">
        <p class="description">Custom canonical URL (leave empty to use default)</p>
      </td>
    </tr>

    <tr>
      <th scope="row">Robots</th>
      <td>
        <label>
          <input type="checkbox" name="_cnp_seo_noindex" value="1" <?php checked($noindex, 1); ?>>
          Noindex (prevent search engines from indexing this page)
        </label>
        <br>
        <label>
          <input type="checkbox" name="_cnp_seo_nofollow" value="1" <?php checked($nofollow, 1); ?>>
          Nofollow (prevent search engines from following links on this page)
        </label>
      </td>
    </tr>

    <tr>
      <th scope="row">OG Image URL</th>
      <td>
        <input type="url" name="_cnp_seo_og_image" value="<?php echo esc_url($og_image); ?>" class="large-text">
        <p class="description">Custom image for social sharing (leave empty to use featured image)</p>
      </td>
    </tr>
  </table>

  <script>
  jQuery(document).ready(function($){
    function updateCount(){
      var count = $('textarea[name="_cnp_seo_description"]').val().length;
      $('#description-count').text(count);
      if (count > 160) {
        $('#description-count').css('color', 'red');
      } else {
        $('#description-count').css('color', 'inherit');
      }
    }
    $('textarea[name="_cnp_seo_description"]').on('input', updateCount);
    updateCount();
  });
  </script>
  <?php
}


// Frontend head injection
add_action('wp_head', function(){
  // Skip if in admin, REST, or feeds
  if (is_admin() || wp_is_json_request() || is_feed()) {
    return;
  }

  $post_id = get_the_ID();

  // Page Title
  $title = get_seo_title($post_id);
  echo '<title>' . esc_html($title) . '</title>' . "\n";

  // Meta Description
  $description = get_seo_description($post_id);
  if ($description) {
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
  }

  // Robots
  $robots = get_seo_robots($post_id);
  if ($robots && $robots !== 'index,follow') {
    echo '<meta name="robots" content="' . esc_attr($robots) . '">' . "\n";
  }

  // Canonical
  $canonical = get_seo_canonical($post_id);
  if ($canonical) {
    echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";
  }

  // OpenGraph & Twitter
  output_opengraph_meta($post_id);

}, 1);

// Helper functions for SEO meta
function get_seo_title($post_id = null) {
  // Custom title
  if ($post_id && ($custom_title = get_post_meta($post_id, '_cnp_seo_title', true))) {
    return $custom_title . ' — ' . get_bloginfo('name');
  }

  // Default title
  return wp_get_document_title();
}

function get_seo_description($post_id = null) {
  // Custom description
  if ($post_id && ($custom_desc = get_post_meta($post_id, '_cnp_seo_description', true))) {
    return trim(preg_replace('/\s+/', ' ', $custom_desc));
  }

  // Excerpt fallback for singular
  if (is_singular() && $post_id) {
    $content = get_post_field('post_content', $post_id);
    $excerpt = wp_trim_words(strip_tags($content), 30, '...');
    $excerpt = preg_replace('/\s+/', ' ', trim($excerpt));
    return strlen($excerpt) > 160 ? substr($excerpt, 0, 157) . '...' : $excerpt;
  }

  // Default description from settings
  $opts = get_option('cnp_seo_settings', []);
  return $opts['default_description'] ?? '';
}

function get_seo_robots($post_id = null) {
  $directives = [];

  // Noindex from post meta
  if ($post_id && get_post_meta($post_id, '_cnp_seo_noindex', true)) {
    $directives[] = 'noindex';
  }

  // Noindex from search setting
  if (is_search()) {
    $opts = get_option('cnp_seo_settings', []);
    if (!empty($opts['noindex_search'])) {
      $directives[] = 'noindex';
    }
  }

  // Nofollow from post meta
  if ($post_id && get_post_meta($post_id, '_cnp_seo_nofollow', true)) {
    $directives[] = 'nofollow';
  }

  // Default to index,follow if no directives
  if (empty($directives)) {
    return 'index,follow';
  }

  return implode(',', $directives);
}

function get_seo_canonical($post_id = null) {
  // Custom canonical
  if ($post_id && ($custom_canonical = get_post_meta($post_id, '_cnp_seo_canonical', true))) {
    if (wp_http_validate_url($custom_canonical)) {
      return $custom_canonical;
    }
  }

  // Default canonical
  if (is_singular() && $post_id) {
    return get_permalink($post_id);
  }

  // Archive pages - ensure current page
  if (is_archive() || is_home()) {
    $url = home_url(add_query_arg([], $GLOBALS['wp']->request));

    // Add pagination if needed
    if (get_query_var('paged') > 1) {
      $url = add_query_arg('paged', get_query_var('paged'), $url);
    }

    return $url;
  }

  // Search results
  if (is_search()) {
    return add_query_arg('s', get_search_query(), home_url('/'));
  }

  // Fallback
  return home_url(add_query_arg([], $GLOBALS['wp']->request));
}

function output_opengraph_meta($post_id = null) {
  $title = get_seo_title($post_id);
  $description = get_seo_description($post_id);
  $canonical = get_seo_canonical($post_id);
  $og_image = get_og_image($post_id);

  // Get settings
  $opts = get_option('cnp_seo_settings', []);
  $twitter_site = $opts['twitter_site'] ?? '';

  // Basic OG tags
  echo '<meta property="og:type" content="' . (is_singular() ? 'article' : 'website') . '">' . "\n";
  echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
  echo '<meta property="og:url" content="' . esc_url($canonical) . '">' . "\n";

  if ($description) {
    echo '<meta property="og:description" content="' . esc_attr(substr($description, 0, 200)) . '">' . "\n";
  }

  if ($og_image) {
    echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
  }

  // Twitter Card
  if ($og_image) {
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
  } else {
    echo '<meta name="twitter:card" content="summary">' . "\n";
  }

  if ($twitter_site) {
    echo '<meta name="twitter:site" content="@' . esc_attr(ltrim($twitter_site, '@')) . '">' . "\n";
  }
}

function get_og_image($post_id = null) {
  // Custom OG image
  if ($post_id && ($custom_og = get_post_meta($post_id, '_cnp_seo_og_image', true))) {
    if (wp_http_validate_url($custom_og)) {
      return $custom_og;
    }
  }

  // Featured image
  if ($post_id && has_post_thumbnail($post_id)) {
    $thumbnail_id = get_post_thumbnail_id($post_id);
    $image = wp_get_attachment_image_src($thumbnail_id, 'full');
    if ($image && isset($image[0])) {
      return $image[0];
    }
  }

  // Default OG image from settings
  $opts = get_option('cnp_seo_settings', []);
  $default_og = $opts['default_og_image'] ?? '';
  if ($default_og && wp_http_validate_url($default_og)) {
    return $default_og;
  }

  return '';
}
