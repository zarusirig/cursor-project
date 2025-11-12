<?php

namespace CNP\AUT\Linking;

use CNP\AUT\Entities as Entities;
use CNP\AUT\Utils as Utils;

if (!defined('ABSPATH')) exit;

/**
 * Get link suggestions for a target post
 */
function get_link_suggestions($target_post_id, $max_suggestions = null) {
  if (!Utils\setting('linker_enabled', 1)) {
    return [];
  }

  $max_suggestions = $max_suggestions ?? Utils\setting('linker_max_suggestions', 10);
  $min_age_days = Utils\setting('linker_min_post_age_days', 2);

  // Get target post info
  $target_post = get_post($target_post_id);
  if (!$target_post) {
    return [];
  }

  $target_entities = Entities\get_post_entities($target_post_id);
  if (empty($target_entities)) {
    return [];
  }

  $target_entity_keys = array_keys($target_entities);
  $target_category_id = get_primary_category_id($target_post_id);

  // Get suggestions from different buckets
  $suggestions = [];

  // 1. Sibling suggestions (same category, shared entities)
  $sibling_suggestions = get_sibling_suggestions($target_post_id, $target_entity_keys, $target_category_id, $min_age_days);
  $suggestions = array_merge($suggestions, $sibling_suggestions);

  // 2. Pillar suggestions (mapped pillar page)
  $pillar_suggestions = get_pillar_suggestions($target_category_id, $target_entity_keys);
  $suggestions = array_merge($suggestions, $pillar_suggestions);

  // 3. Cross-type suggestions (different content types)
  $cross_suggestions = get_cross_type_suggestions($target_post_id, $target_entity_keys, $target_category_id, $min_age_days);
  $suggestions = array_merge($suggestions, $cross_suggestions);

  // Rank and limit suggestions
  $suggestions = rank_suggestions($suggestions, $target_post);

  return array_slice($suggestions, 0, $max_suggestions);
}

/**
 * Get sibling suggestions (same category, shared entities)
 */
function get_sibling_suggestions($target_post_id, $target_entity_keys, $target_category_id, $min_age_days) {
  global $wpdb;

  if (!$target_category_id) return [];

  $suggestions = [];
  $min_age_date = date('Y-m-d H:i:s', strtotime("-{$min_age_days} days"));

  // Find posts in same category with shared entities
  $posts_with_entities = Entities\find_posts_by_entities($target_entity_keys, $target_post_id, 50);

  foreach ($posts_with_entities as $post_data) {
    $post_id = $post_data['post_id'];

    // Check if post is in same category
    if (!has_category($target_category_id, $post_id)) {
      continue;
    }

    // Check minimum age
    $post = get_post($post_id);
    if (strtotime($post->post_date) > strtotime($min_age_date)) {
      continue;
    }

    // Check if post is published and not excluded
    if ($post->post_status !== 'publish' || is_excluded_post($post_id)) {
      continue;
    }

    // Calculate entity overlap score
    $shared_entities = array_intersect_key($post_data['entities'], $target_entity_keys);
    $overlap_score = 0;
    foreach ($shared_entities as $entity_key => $score) {
      $target_weight = $target_entity_keys[$entity_key] ?? 0.5;
      $overlap_score += min($score, $target_weight);
    }
    $overlap_score = min($overlap_score, 1.0);

    // Calculate recency decay
    $post_age_days = (time() - strtotime($post->post_date)) / (60 * 60 * 24);
    $recency_decay = exp(-$post_age_days / 180); // 6-month half-life

    // Authority boost (simplified - count of inbound links)
    $authority_boost = get_inbound_link_count($post_id) >= 3 ? 0.1 : 0;

    $score = $overlap_score * $recency_decay + $authority_boost;

    $suggestions[] = [
      'id' => 'sibling_' . $post_id,
      'type' => 'sibling',
      'post_id' => $post_id,
      'url' => get_permalink($post_id),
      'title' => $post->post_title,
      'entities_matched' => array_keys($shared_entities),
      'score' => $score,
      'overlap_score' => $overlap_score,
      'recency_decay' => $recency_decay,
      'authority_boost' => $authority_boost,
    ];
  }

  return $suggestions;
}

/**
 * Get pillar suggestions
 */
function get_pillar_suggestions($target_category_id, $target_entity_keys) {
  if (!$target_category_id) return [];

  $pillar_map = json_decode(Utils\setting('pillar_pages_map', '{}'), true);
  $category = get_term($target_category_id);

  if (!$category || !isset($pillar_map[$category->slug])) {
    return [];
  }

  $pillar_url = $pillar_map[$category->slug];

  // Try to find the post/page by URL
  $post_id = url_to_postid($pillar_url);
  if (!$post_id) {
    // Try to find by slug
    $slug = trim($pillar_url, '/');
    $post = get_page_by_path($slug) ?: get_page_by_path($slug, OBJECT, 'post');
    if ($post) {
      $post_id = $post->ID;
    }
  }

  if (!$post_id || is_excluded_post($post_id)) {
    return [];
  }

  $post = get_post($post_id);
  if ($post->post_status !== 'publish') {
    return [];
  }

  // Calculate entity overlap (pillar might not have entities, so use a base score)
  $pillar_entities = Entities\get_post_entities($post_id);
  $shared_entities = array_intersect_key($pillar_entities, $target_entity_keys);
  $overlap_score = count($shared_entities) > 0 ? 0.5 : 0.2; // Base score for pillar links

  return [[
    'id' => 'pillar_' . $post_id,
    'type' => 'pillar',
    'post_id' => $post_id,
    'url' => $pillar_url,
    'title' => $post->post_title,
    'entities_matched' => array_keys($shared_entities),
    'score' => $overlap_score,
    'overlap_score' => $overlap_score,
    'recency_decay' => 1.0, // Pillars don't decay
    'authority_boost' => 0,
  ]];
}

/**
 * Get cross-type suggestions
 */
function get_cross_type_suggestions($target_post_id, $target_entity_keys, $target_category_id, $min_age_days) {
  global $wpdb;

  $suggestions = [];
  $min_age_date = date('Y-m-d H:i:s', strtotime("-{$min_age_days} days"));

  // Find posts with shared entities (different categories/types)
  $posts_with_entities = Entities\find_posts_by_entities($target_entity_keys, $target_post_id, 30);

  foreach ($posts_with_entities as $post_data) {
    $post_id = $post_data['post_id'];

    // Check if post is in different category
    if (has_category($target_category_id, $post_id)) {
      continue; // Skip same category (handled by sibling)
    }

    // Check minimum age
    $post = get_post($post_id);
    if (strtotime($post->post_date) > strtotime($min_age_date)) {
      continue;
    }

    // Check if post is published and not excluded
    if ($post->post_status !== 'publish' || is_excluded_post($post_id)) {
      continue;
    }

    // Calculate entity overlap score
    $shared_entities = array_intersect_key($post_data['entities'], $target_entity_keys);
    $overlap_score = 0;
    foreach ($shared_entities as $entity_key => $score) {
      $target_weight = $target_entity_keys[$entity_key] ?? 0.5;
      $overlap_score += min($score, $target_weight);
    }
    $overlap_score = min($overlap_score, 1.0);

    // Only include if overlap is significant
    if ($overlap_score < 0.3) continue;

    // Calculate recency decay
    $post_age_days = (time() - strtotime($post->post_date)) / (60 * 60 * 24);
    $recency_decay = exp(-$post_age_days / 180);

    // Authority boost
    $authority_boost = get_inbound_link_count($post_id) >= 3 ? 0.1 : 0;

    $score = $overlap_score * $recency_decay + $authority_boost;

    $suggestions[] = [
      'id' => 'cross_' . $post_id,
      'type' => 'cross',
      'post_id' => $post_id,
      'url' => get_permalink($post_id),
      'title' => $post->post_title,
      'entities_matched' => array_keys($shared_entities),
      'score' => $score,
      'overlap_score' => $overlap_score,
      'recency_decay' => $recency_decay,
      'authority_boost' => $authority_boost,
    ];
  }

  return $suggestions;
}

/**
 * Rank suggestions by score and apply diversity rules
 */
function rank_suggestions($suggestions, $target_post) {
  // Sort by score descending
  usort($suggestions, function($a, $b) {
    return $b['score'] <=> $a['score'];
  });

  // Apply diversity rule: avoid duplicate titles/domains
  $seen_titles = [];
  $filtered = [];

  foreach ($suggestions as $suggestion) {
    $title_root = strtolower(preg_replace('/[^a-z0-9]/', '', $suggestion['title']));

    if (!isset($seen_titles[$title_root])) {
      $seen_titles[$title_root] = true;
      $filtered[] = $suggestion;
    }
  }

  return $filtered;
}

/**
 * Generate anchor suggestions for a target post
 */
function generate_anchor_suggestions($target_post_id, $suggestions) {
  if (empty($suggestions)) return [];

  $target_post = get_post($target_post_id);
  if (!$target_post) return [];

  $content = $target_post->post_content;
  $blacklist_terms = array_filter(array_map('trim', explode("\n", Utils\setting('linker_blacklist_terms', ''))));
  $min_anchor_length = Utils\setting('linker_anchor_min_chars', 18);

  $anchors = [];

  foreach ($suggestions as $suggestion) {
    $matched_entities = $suggestion['entities_matched'];
    $suggestion_anchors = find_anchor_candidates($content, $matched_entities, $blacklist_terms, $min_anchor_length);

    if (!empty($suggestion_anchors)) {
      // Group anchors by section
      $sectioned_anchors = [];
      foreach ($suggestion_anchors as $anchor) {
        $section = $anchor['section'];
        if (!isset($sectioned_anchors[$section])) {
          $sectioned_anchors[$section] = [];
        }
        $sectioned_anchors[$section][] = [
          'text' => $anchor['text'],
          'section' => $section,
        ];
      }

      // Take best anchor per section (first one found)
      foreach ($sectioned_anchors as $section => $section_anchors) {
        $anchors[] = array_merge($suggestion, [
          'anchor_text' => $section_anchors[0]['text'],
          'anchor_section' => $section,
          'available_anchors' => $section_anchors,
        ]);
      }
    }
  }

  return $anchors;
}

/**
 * Find anchor candidates in content
 */
function find_anchor_candidates($content, $entities, $blacklist_terms, $min_length) {
  $anchors = [];

  // Scan different content sections
  $sections = [
    'intro' => extract_intro_content($content),
    'body' => extract_body_content($content),
  ];

  // Add H2 sections
  $h2_sections = extract_h2_sections($content);
  $sections = array_merge($sections, $h2_sections);

  foreach ($sections as $section_key => $section_content) {
    $section_anchors = find_anchors_in_text($section_content, $entities, $blacklist_terms, $min_length, $section_key);
    $anchors = array_merge($anchors, $section_anchors);
  }

  return $anchors;
}

/**
 * Extract intro content (title + first paragraph)
 */
function extract_intro_content($content) {
  // Get title + first 500 chars of content
  $plain_content = wp_strip_all_tags($content);
  return substr($plain_content, 0, 500);
}

/**
 * Extract body content (everything after intro)
 */
function extract_body_content($content) {
  $plain_content = wp_strip_all_tags($content);
  return substr($plain_content, 500);
}

/**
 * Extract H2 sections
 */
function extract_h2_sections($content) {
  $sections = [];

  if (preg_match_all('/<h2[^>]*>(.*?)<\/h2>(.*?)(?=<h[23]|$)/is', $content, $matches)) {
    foreach ($matches[1] as $i => $heading) {
      $heading_text = wp_strip_all_tags($heading);
      $section_content = wp_strip_all_tags($matches[2][$i]);
      $sections['h2: ' . $heading_text] = substr($section_content, 0, 1000);
    }
  }

  return $sections;
}

/**
 * Find anchors in a text section
 */
function find_anchors_in_text($text, $entities, $blacklist_terms, $min_length, $section) {
  $anchors = [];
  $sentences = preg_split('/[.!?]+/', $text);

  foreach ($sentences as $sentence) {
    $sentence = trim($sentence);
    if (strlen($sentence) < $min_length) continue;

    // Check if sentence contains any of the entities
    $contains_entity = false;
    foreach ($entities as $entity) {
      if (stripos($sentence, $entity) !== false) {
        $contains_entity = true;
        break;
      }
    }

    if (!$contains_entity) continue;

    // Check against blacklist
    $blacklisted = false;
    foreach ($blacklist_terms as $term) {
      if (stripos($sentence, $term) !== false) {
        $blacklisted = true;
        break;
      }
    }

    if ($blacklisted) continue;

    // Check if sentence is inside existing links (rough check)
    if (preg_match('/https?:\/\//', $sentence)) continue;

    $anchors[] = [
      'text' => $sentence,
      'section' => $section,
      'length' => strlen($sentence),
    ];
  }

  // Sort by length (prefer longer anchors)
  usort($anchors, function($a, $b) {
    return $b['length'] <=> $a['length'];
  });

  return array_slice($anchors, 0, 3); // Max 3 per section
}

/**
 * Insert links into post content
 */
function insert_links($post_id, $inserts) {
  $post = get_post($post_id);
  if (!$post) {
    return new \WP_Error('not_found', 'Post not found');
  }

  $content = $post->post_content;
  $per_section_cap = Utils\setting('linker_per_section_cap', 2);
  $min_anchor_length = Utils\setting('linker_anchor_min_chars', 18);

  $inserted = 0;
  $skipped = 0;

  // Track links per section
  $section_counts = [];

  foreach ($inserts as $insert) {
    $suggestion_id = $insert['suggestion_id'];
    $anchor_text = $insert['anchor_text'];
    $section = $insert['section'];

    // Validate anchor
    if (strlen($anchor_text) < $min_anchor_length) {
      $skipped++;
      continue;
    }

    // Check section cap
    if (!isset($section_counts[$section])) {
      $section_counts[$section] = 0;
    }

    if ($section_counts[$section] >= $per_section_cap) {
      $skipped++;
      continue;
    }

    // Parse suggestion ID to get target URL
    $url = get_suggestion_url_from_id($suggestion_id);
    if (!$url) {
      $skipped++;
      continue;
    }

    // Check if URL already exists in content
    if (strpos($content, $url) !== false) {
      $skipped++;
      continue;
    }

    // Find the section and insert link
    $new_content = insert_link_in_section($content, $anchor_text, $url, $section);

    if ($new_content !== $content) {
      $content = $new_content;
      $section_counts[$section]++;
      $inserted++;

      // Update inbound link count for target
      increment_inbound_link_count(get_post_id_from_url($url));
    } else {
      $skipped++;
    }
  }

  // Update post content
  if ($inserted > 0) {
    wp_update_post([
      'ID' => $post_id,
      'post_content' => $content,
    ]);

    // Log the insertion
    Utils\log_event('links_inserted', [
      'post_id' => $post_id,
      'inserted' => $inserted,
      'skipped' => $skipped,
      'inserts' => $inserts,
    ]);

    // Fire GA4 event
    if (function_exists('gtag')) {
      gtag('event', 'internal_links_inserted', [
        'post_id' => $post_id,
        'count' => $inserted,
      ]);
    }
  }

  return [
    'post_id' => $post_id,
    'inserted' => $inserted,
    'skipped' => $skipped,
  ];
}

/**
 * Insert link in specific section
 */
function insert_link_in_section($content, $anchor_text, $url, $section) {
  // For simplicity, we'll do a basic text replacement
  // In a real implementation, you'd want more sophisticated HTML parsing

  if ($section === 'intro') {
    // Insert in the first paragraph after any existing content
    $pattern = '/(<p[^>]*>.*?\/p>)/is';
    $replacement = '$1<p><a href="' . esc_url($url) . '">' . esc_html($anchor_text) . '</a></p>';
    $content = preg_replace($pattern, $replacement, $content, 1);
  } elseif (strpos($section, 'h2: ') === 0) {
    // Insert after specific H2
    $heading_text = str_replace('h2: ', '', $section);
    $heading_pattern = '/(<h2[^>]*>' . preg_quote($heading_text, '/') . '<\/h2>)/i';
    $replacement = '$1<p><a href="' . esc_url($url) . '">' . esc_html($anchor_text) . '</a></p>';
    $content = preg_replace($heading_pattern, $replacement, $content, 1);
  } else {
    // Default to end of content
    $content .= '<p><a href="' . esc_url($url) . '">' . esc_html($anchor_text) . '</a></p>';
  }

  return $content;
}

/**
 * Helper functions
 */
function get_primary_category_id($post_id) {
  $categories = wp_get_post_categories($post_id, ['fields' => 'ids']);
  return !empty($categories) ? min($categories) : null; // Lowest ID = primary
}

function is_excluded_post($post_id) {
  $post = get_post($post_id);
  return $post->post_status !== 'publish' ||
         get_post_meta($post_id, '_cnp_noindex', true) ||
         get_post_meta($post_id, 'exclude_from_search', true);
}

function get_inbound_link_count($post_id) {
  return (int) get_post_meta($post_id, '_cnp_inbound_count', true);
}

function increment_inbound_link_count($post_id) {
  if (!$post_id) return;
  $current = get_inbound_link_count($post_id);
  update_post_meta($post_id, '_cnp_inbound_count', $current + 1);
}

function get_suggestion_url_from_id($suggestion_id) {
  // Parse suggestion ID (format: type_postid)
  $parts = explode('_', $suggestion_id, 2);
  if (count($parts) !== 2) return null;

  $post_id = (int) $parts[1];
  return get_permalink($post_id);
}

function get_post_id_from_url($url) {
  return url_to_postid($url);
}
