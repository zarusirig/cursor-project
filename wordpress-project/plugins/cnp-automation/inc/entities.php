<?php

namespace CNP\AUT\Entities;

use CNP\AUT\Utils as Utils;

if (!defined('ABSPATH')) exit;

/**
 * Extract entities from a post
 */
function extract_entities($post_id, $custom_entities = []) {
  if (!Utils\setting('linker_enabled', 1)) {
    return;
  }

  $post = get_post($post_id);
  if (!$post || $post->post_status !== 'publish') {
    return;
  }

  // Get post content
  $title = $post->post_title;
  $content = $post->post_content;

  // Extract H2/H3 headings
  $headings = [];
  if (preg_match_all('/<h([23])[^>]*>(.*?)<\/h[23]>/i', $content, $matches)) {
    foreach ($matches[2] as $heading) {
      $headings[] = wp_strip_all_tags($heading);
    }
  }

  // Get first 800-1200 words of body content
  $plain_content = wp_strip_all_tags($content);
  $words = explode(' ', $plain_content);
  $body_content = implode(' ', array_slice($words, 0, 1000)); // ~1000 words should be plenty

  // Combine and normalize text
  $combined_text = $title . ' ' . implode(' ', $headings) . ' ' . $body_content;
  $normalized_text = normalize_text($combined_text);

  // Extract entities
  $entities = [];

  // Extract proper nouns/brands/products (title case words appearing ≥2 times)
  $title_case_entities = extract_title_case_entities($normalized_text);
  $entities = array_merge($entities, $title_case_entities);

  // Add whitelisted domain entities from custom_entities
  if (!empty($custom_entities)) {
    foreach ($custom_entities as $entity) {
      $entity_key = normalize_entity_key($entity);
      if (!isset($entities[$entity_key])) {
        $entities[$entity_key] = [
          'label' => $entity,
          'weight' => 0.1, // Base weight for custom entities
          'sources' => ['custom']
        ];
      }
      $entities[$entity_key]['weight'] += 0.1;
      $entities[$entity_key]['sources'][] = 'custom';
    }
  }

  // Add category & tags
  $categories = wp_get_post_categories($post_id, ['fields' => 'names']);
  $tags = wp_get_post_tags($post_id, ['fields' => 'names']);

  foreach (array_merge($categories, $tags) as $term) {
    $entity_key = normalize_entity_key($term);
    if (!isset($entities[$entity_key])) {
      $entities[$entity_key] = [
        'label' => $term,
        'weight' => 0.0,
        'sources' => []
      ];
    }
    $entities[$entity_key]['sources'][] = 'taxonomy';
  }

  // Calculate final weights and store
  store_entities($post_id, $entities, $normalized_text);

  return $entities;
}

/**
 * Normalize text for entity extraction
 */
function normalize_text($text) {
  // Remove HTML entities
  $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5);

  // Normalize whitespace
  $text = preg_replace('/\s+/', ' ', $text);
  $text = trim($text);

  return $text;
}

/**
 * Extract title case entities (proper nouns appearing ≥2 times)
 */
function extract_title_case_entities($text) {
  $entities = [];

  // Split into words, keeping only title case words (first letter uppercase, rest lowercase or mixed)
  $words = preg_split('/\s+/', $text);
  $word_counts = [];

  foreach ($words as $word) {
    $word = trim($word, '.,!?;:"\'()[]{}');
    if (empty($word) || strlen($word) < 3) continue;

    // Check if it's title case (starts with uppercase)
    if (!preg_match('/^[A-Z]/', $word)) continue;

    // Skip common stopwords and short words
    if (is_stopword($word)) continue;

    $word_counts[$word] = ($word_counts[$word] ?? 0) + 1;
  }

  foreach ($word_counts as $word => $count) {
    if ($count >= 2) {
      $entity_key = normalize_entity_key($word);
      if (!isset($entities[$entity_key])) {
        $entities[$entity_key] = [
          'label' => $word,
          'weight' => 0.0,
          'sources' => []
        ];
      }
      $entities[$entity_key]['sources'][] = 'title_case';
    }
  }

  return $entities;
}

/**
 * Normalize entity key for storage
 */
function normalize_entity_key($entity) {
  $key = strtolower(trim($entity));
  $key = preg_replace('/[^a-z0-9\s]/', '', $key); // Remove special chars
  $key = preg_replace('/\s+/', '_', $key); // Replace spaces with underscores
  return $key;
}

/**
 * Check if word is a stopword
 */
function is_stopword($word) {
  $stopwords = [
    'the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'is', 'are', 'was', 'were',
    'this', 'that', 'these', 'those', 'i', 'you', 'he', 'she', 'it', 'we', 'they', 'me', 'him', 'her', 'us', 'them',
    'my', 'your', 'his', 'its', 'our', 'their', 'what', 'which', 'who', 'when', 'where', 'why', 'how', 'all', 'any',
    'both', 'each', 'few', 'more', 'most', 'other', 'some', 'such', 'no', 'nor', 'not', 'only', 'own', 'same', 'so',
    'than', 'too', 'very', 'can', 'will', 'just', 'should', 'now', 'here', 'there', 'then', 'once', 'also', 'new',
    'first', 'last', 'next', 'old', 'good', 'great', 'best', 'better', 'big', 'small', 'large', 'long', 'short',
    'high', 'low', 'right', 'left', 'one', 'two', 'three', 'four', 'five', 'ten', 'many', 'much', 'few', 'little'
  ];

  return in_array(strtolower($word), $stopwords);
}

/**
 * Calculate entity weights based on sources and content analysis
 */
function calculate_entity_weights($entities, $title, $headings, $body_content) {
  $title_lower = strtolower($title);
  $headings_lower = strtolower(implode(' ', $headings));
  $body_lower = strtolower($body_content);

  foreach ($entities as $key => &$entity) {
    $weight = 0.0;
    $entity_lower = strtolower($entity['label']);

    // Weight by source
    foreach ($entity['sources'] as $source) {
      switch ($source) {
        case 'title':
          $weight += 0.4;
          break;
        case 'heading':
          $weight += 0.3;
          break;
        case 'title_case':
          // Count occurrences in body
          $occurrences = substr_count($body_lower, $entity_lower);
          if ($occurrences >= 3) {
            $weight += 0.2;
          } elseif ($occurrences >= 1) {
            $weight += 0.1;
          }
          break;
        case 'custom':
          $weight += 0.1;
          break;
      }
    }

    // Boost if in title
    if (strpos($title_lower, $entity_lower) !== false) {
      $weight += 0.4;
    }

    // Boost if in headings
    if (strpos($headings_lower, $entity_lower) !== false) {
      $weight += 0.3;
    }

    // Boost if appears frequently in body
    $body_occurrences = substr_count($body_lower, $entity_lower);
    if ($body_occurrences >= 3) {
      $weight += 0.2;
    }

    $entity['weight'] = min($weight, 1.0); // Cap at 1.0
  }

  return $entities;
}

/**
 * Store entities in database
 */
function store_entities($post_id, $entities, $normalized_text) {
  global $wpdb;

  $entities_table = $wpdb->prefix . 'cnp_entities';
  $index_table = $wpdb->prefix . 'cnp_entity_index';

  // Get title, headings, and body content for weight calculation
  $post = get_post($post_id);
  $title = $post->post_title;
  $content = $post->post_content;

  $headings = [];
  if (preg_match_all('/<h([23])[^>]*>(.*?)<\/h[23]>/i', $content, $matches)) {
    $headings = array_map('wp_strip_all_tags', $matches[2]);
  }

  $plain_content = wp_strip_all_tags($content);
  $words = explode(' ', $plain_content);
  $body_content = implode(' ', array_slice($words, 0, 1000));

  // Calculate final weights
  $entities = calculate_entity_weights($entities, $title, $headings, $body_content);

  // Store entities
  foreach ($entities as $entity_key => $entity_data) {
    if ($entity_data['weight'] <= 0) continue;

    $wpdb->replace(
      $entities_table,
      [
        'post_id' => $post_id,
        'entity_key' => $entity_key,
        'entity_label' => $entity_data['label'],
        'weight' => $entity_data['weight'],
        'last_seen' => current_time('mysql'),
      ],
      ['%d', '%s', '%s', '%f', '%s']
    );

    // Update index table
    $score = $entity_data['weight']; // For now, score equals weight
    $wpdb->replace(
      $index_table,
      [
        'entity_key' => $entity_key,
        'post_id' => $post_id,
        'score' => $score,
      ],
      ['%s', '%d', '%f']
    );
  }

  // Remove old entities no longer present
  $current_entity_keys = array_keys($entities);
  if (!empty($current_entity_keys)) {
    $placeholders = str_repeat('%s,', count($current_entity_keys) - 1) . '%s';
    $wpdb->query($wpdb->prepare(
      "DELETE FROM $entities_table WHERE post_id = %d AND entity_key NOT IN ($placeholders)",
      array_merge([$post_id], $current_entity_keys)
    ));

    $wpdb->query($wpdb->prepare(
      "DELETE FROM $index_table WHERE post_id = %d AND entity_key NOT IN ($placeholders)",
      array_merge([$post_id], $current_entity_keys)
    ));
  } else {
    $wpdb->delete($entities_table, ['post_id' => $post_id]);
    $wpdb->delete($index_table, ['post_id' => $post_id]);
  }
}

/**
 * Remove all entities for a post
 */
function remove_post_entities($post_id) {
  global $wpdb;

  $entities_table = $wpdb->prefix . 'cnp_entities';
  $index_table = $wpdb->prefix . 'cnp_entity_index';

  $wpdb->delete($entities_table, ['post_id' => $post_id]);
  $wpdb->delete($index_table, ['post_id' => $post_id]);
}

/**
 * Get entities for a post
 */
function get_post_entities($post_id) {
  global $wpdb;

  $entities_table = $wpdb->prefix . 'cnp_entities';

  $results = $wpdb->get_results($wpdb->prepare(
    "SELECT entity_key, entity_label, weight FROM $entities_table WHERE post_id = %d ORDER BY weight DESC",
    $post_id
  ));

  $entities = [];
  foreach ($results as $row) {
    $entities[$row->entity_key] = [
      'label' => $row->entity_label,
      'weight' => (float) $row->weight,
    ];
  }

  return $entities;
}

/**
 * Find posts containing specific entities
 */
function find_posts_by_entities($entity_keys, $exclude_post_id = null, $limit = 20) {
  global $wpdb;

  if (empty($entity_keys)) return [];

  $index_table = $wpdb->prefix . 'cnp_entity_index';

  $placeholders = str_repeat('%s,', count($entity_keys) - 1) . '%s';
  $sql = "SELECT post_id, entity_key, score FROM $index_table WHERE entity_key IN ($placeholders)";

  $params = $entity_keys;
  if ($exclude_post_id) {
    $sql .= " AND post_id != %d";
    $params[] = $exclude_post_id;
  }

  $sql .= " ORDER BY score DESC LIMIT %d";
  $params[] = $limit;

  $results = $wpdb->get_results($wpdb->prepare($sql, $params));

  $posts = [];
  foreach ($results as $row) {
    if (!isset($posts[$row->post_id])) {
      $posts[$row->post_id] = [
        'post_id' => (int) $row->post_id,
        'entities' => [],
        'total_score' => 0,
      ];
    }
    $posts[$row->post_id]['entities'][$row->entity_key] = (float) $row->score;
    $posts[$row->post_id]['total_score'] += (float) $row->score;
  }

  // Sort by total score
  uasort($posts, function($a, $b) {
    return $b['total_score'] <=> $a['total_score'];
  });

  return array_values($posts);
}

/**
 * Rebuild entities for all published posts
 */
function rebuild_all_entities() {
  global $wpdb;

  $posts = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish'");

  $count = 0;
  foreach ($posts as $post_id) {
    extract_entities($post_id);
    $count++;
  }

  return $count;
}
