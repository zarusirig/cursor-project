<?php

namespace CNP\AUT\Jobs;

if (!defined('ABSPATH')) exit;

use CNP\AUT\Queue as Queue;
use CNP\AUT\Utils as Utils;

function create_job_from_brief($brief) {
  // Validate brief
  $validated_brief = Utils\validate_brief($brief);
  if (is_wp_error($validated_brief)) {
    return $validated_brief;
  }

  // Check rate limit
  $jobs_started = Queue\count_jobs_started_last_hour();
  $max_per_hour = Utils\setting('max_generate_per_hour', 20);

  if ($jobs_started >= $max_per_hour) {
    return new \WP_Error('rate_limited', 'Rate limit exceeded. Maximum ' . $max_per_hour . ' jobs per hour.', [
      'retry_after' => 3600 - (time() % 3600) // Time until next hour
    ]);
  }

  // Create job
  $priority = isset($brief['priority']) ? $brief['priority'] : 0;
  $job_id = Queue\create_job($validated_brief, $priority);

  if (is_wp_error($job_id)) {
    return $job_id;
  }

  Utils\log_event('job_created', ['job_id' => $job_id, 'title' => $brief['title']]);

  return $job_id;
}

function process_queue($max_jobs = 3) {
  $processed = 0;

  while ($processed < $max_jobs) {
    $queued_jobs = Queue\get_queued_jobs(1);

    if (empty($queued_jobs)) {
      break; // No more jobs
    }

    $job = $queued_jobs[0];

    // Try to lock the job
    if (!Queue\lock_job($job['id'])) {
      continue; // Job was already locked by another worker
    }

    // Process the job
    $result = process_job($job);

    if (is_wp_error($result)) {
      Queue\transition_status($job['id'], 'failed', ['error' => $result->get_error_message()]);
      Utils\log_event('job_failed', ['job_id' => $job['id'], 'error' => $result->get_error_message()]);
    } else {
      Utils\log_event('job_completed', ['job_id' => $job['id'], 'post_id' => $result]);
    }

    $processed++;
  }

  return $processed;
}

function process_job($job) {
  try {
    $brief = $job['brief'];

    // Generate content (stub implementation)
    $generated_content = generate_content_stub($brief);

    // Create post
    $post_data = prepare_post_data($brief, $generated_content);
    $post_id = wp_insert_post($post_data, true);

    if (is_wp_error($post_id)) {
      throw new \Exception('Failed to create post: ' . $post_id->get_error_message());
    }

    // Set post metadata
    set_post_metadata($post_id, $brief, $generated_content);

    // Handle featured image
    handle_featured_image($post_id, $brief);

    // Transition job status
    Queue\transition_status($job['id'], 'draft_created', ['post_id' => $post_id]);
    Queue\transition_status($job['id'], 'needs_review', ['post_id' => $post_id]);

    // Fire GA4 event
    do_action('cnp_automation_draft_created', $post_id, $job['id'], isset($brief['category_id']) ? $brief['category_id'] : null, isset($brief['target_template']) ? $brief['target_template'] : 'article');

    return $post_id;

  } catch (\Exception $e) {
    return new \WP_Error('processing_error', $e->getMessage());
  }
}

function generate_content_stub($brief) {
  // This is a stub implementation - replace with actual AI service integration
  $template = isset($brief['target_template']) ? $brief['target_template'] : 'article';

  // Build prompt (for logging/debugging)
  $prompt = build_generation_prompt($brief);

  // Mock content generation - replace with real AI call
  $content = generate_mock_content($brief, $template);

  Utils\log_event('content_generated_stub', [
    'title' => $brief['title'],
    'template' => $template,
    'word_count' => str_word_count(strip_tags($content['body'])),
  ]);

  return $content;
}

function build_generation_prompt($brief) {
  $prompt = "Generate a " . ($brief['target_template'] ?? 'article') . " about: {$brief['title']}\n\n";

  if (!empty($brief['dek'])) {
    $prompt .= "Short description: {$brief['dek']}\n\n";
  }

  if (!empty($brief['entities'])) {
    $prompt .= "Key entities/topics to cover: " . implode(', ', $brief['entities']) . "\n\n";
  }

  if (!empty($brief['notes'])) {
    $prompt .= "Additional notes: {$brief['notes']}\n\n";
  }

  $prompt .= "Requirements:\n";
  $prompt .= "- Include H2 sections\n";
  $prompt .= "- Add a 'Key Points' section\n";
  $prompt .= "- Include a 'Why This Matters' section\n";
  $prompt .= "- Do not include affiliate claims or fake facts\n";
  $prompt .= "- Cite sources where making factual claims\n";
  $prompt .= "- Write in an informative, journalistic tone\n";

  return $prompt;
}

function generate_mock_content($brief, $template) {
  $title = $brief['title'];

  // AI Disclosure (if enabled)
  $ai_disclosure = '';
  if (Utils\setting('auto_insert_ai_disclosure', 1)) {
    $disclosure_block = Utils\setting('ai_disclosure_block', '');
    if (!empty($disclosure_block)) {
      $ai_disclosure = $disclosure_block . "\n\n";
    }
  }

  // Generate mock body content
  $body = "<!-- wp:heading {\"level\":2} -->\n<h2>Introduction</h2>\n<!-- /wp:heading -->\n\n";
  $body .= "<p>This is a generated article about {$title}. In a real implementation, this would be created by an AI service based on the provided brief and requirements.</p>\n\n";

  if (!empty($brief['entities'])) {
    $body .= "<!-- wp:heading {\"level\":2} -->\n<h2>Key Points</h2>\n<!-- /wp:heading -->\n\n";
    $body .= "<ul>\n";
    foreach ($brief['entities'] as $entity) {
      $body .= "<li><strong>{$entity}:</strong> Information about {$entity} would be included here.</li>\n";
    }
    $body .= "</ul>\n\n";
  }

  $body .= "<!-- wp:heading {\"level\":2} -->\n<h2>Why This Matters</h2>\n<!-- /wp:heading -->\n\n";
  $body .= "<p>Understanding {$title} is important for readers who want to make informed decisions. This section would explain the broader context and implications.</p>\n\n";

  if (!empty($brief['sources'])) {
    $body .= "<!-- wp:heading {\"level\":2} -->\n<h2>Sources</h2>\n<!-- /wp:heading -->\n\n";
    $body .= "<ul>\n";
    foreach ($brief['sources'] as $source) {
      $body .= "<li><a href=\"{$source['url']}\" target=\"_blank\" rel=\"noopener\">{$source['label']}</a></li>\n";
    }
    $body .= "</ul>\n\n";
  }

  // Add TODO markers for future implementation
  $body .= "<!-- CNP_AUTOMATION_TODO: Replace this stub content with real AI-generated content -->\n";
  $body .= "<!-- CNP_AUTOMATION_TODO: Add proper internal linking -->\n";
  $body .= "<!-- CNP_AUTOMATION_TODO: Implement schema markup -->\n";

  return [
    'body' => $ai_disclosure . $body,
    'excerpt' => !empty($brief['dek']) ? $brief['dek'] : wp_trim_words(strip_tags($body), 30, '...'),
    'prompt_used' => build_generation_prompt($brief),
  ];
}

function prepare_post_data($brief, $generated_content) {
  return [
    'post_title' => $brief['title'],
    'post_content' => $generated_content['body'],
    'post_excerpt' => $generated_content['excerpt'],
    'post_status' => Utils\resolve_status($brief),
    'post_author' => Utils\resolve_author_id($brief),
    'post_category' => Utils\resolve_category_id($brief) ? [Utils\resolve_category_id($brief)] : [],
    'tags_input' => Utils\resolve_tags($brief),
    'post_date' => current_time('mysql'),
    'post_date_gmt' => current_time('mysql', 1),
  ];
}

function set_post_metadata($post_id, $brief, $generated_content) {
  // Store automation metadata
  update_post_meta($post_id, '_cnp_automation_job', 'generated');
  update_post_meta($post_id, '_cnp_lang', Utils\resolve_language($brief));

  // Store entities for future linking
  if (!empty($brief['entities'])) {
    update_post_meta($post_id, '_cnp_entities', wp_json_encode($brief['entities']));
  }

  // Store sources
  if (!empty($brief['sources'])) {
    update_post_meta($post_id, '_cnp_sources', wp_json_encode($brief['sources']));
  }

  // Store template info
  if (!empty($brief['target_template'])) {
    update_post_meta($post_id, '_cnp_template', $brief['target_template']);
  }

  // Store featured image missing flag
  if (empty($brief['featured_image_url'])) {
    update_post_meta($post_id, '_cnp_featured_missing', 1);
  }

  // QA checklist
  update_post_meta($post_id, '_cnp_needs_review', 1);
  update_post_meta($post_id, '_cnp_qa_checklist', wp_json_encode(Utils\generate_qa_checklist()));

  // Store generation details for debugging
  update_post_meta($post_id, '_cnp_generation_prompt', $generated_content['prompt_used']);
  update_post_meta($post_id, '_cnp_generated_at', current_time('mysql'));

  // Store external ID if provided
  if (!empty($brief['external_id'])) {
    update_post_meta($post_id, '_cnp_external_id', $brief['external_id']);
  }
}

function handle_featured_image($post_id, $brief) {
  if (empty($brief['featured_image_url'])) {
    update_post_meta($post_id, '_cnp_featured_missing', 1);
    return;
  }

  // Download and sideload the image
  $image_id = media_sideload_image($brief['featured_image_url'], $post_id, '', 'id');

  if (!is_wp_error($image_id)) {
    set_post_thumbnail($post_id, $image_id);
    delete_post_meta($post_id, '_cnp_featured_missing');
    Utils\log_event('featured_image_set', ['post_id' => $post_id, 'image_id' => $image_id]);
  } else {
    update_post_meta($post_id, '_cnp_featured_missing', 1);
    Utils\log_event('featured_image_failed', [
      'post_id' => $post_id,
      'url' => $brief['featured_image_url'],
      'error' => $image_id->get_error_message()
    ]);
  }
}

function cancel_job($job_id) {
  $job = Queue\get_job($job_id);
  if (!$job) {
    return new \WP_Error('not_found', 'Job not found');
  }

  if (!in_array($job['status'], ['queued', 'generating'])) {
    return new \WP_Error('invalid_status', 'Can only cancel queued or generating jobs');
  }

  $result = Queue\transition_status($job_id, 'cancelled');

  if ($result) {
    Utils\log_event('job_cancelled', ['job_id' => $job_id]);
  }

  return $result;
}

function retry_job($job_id) {
  $job = Queue\get_job($job_id);
  if (!$job) {
    return new \WP_Error('not_found', 'Job not found');
  }

  if ($job['status'] !== 'failed') {
    return new \WP_Error('invalid_status', 'Can only retry failed jobs');
  }

  // Reset timestamps and status
  $updates = [
    'status' => 'queued',
    'started_at' => null,
    'finished_at' => null,
    'locks' => null,
    'error' => null,
  ];

  $result = Queue\update_job($job_id, $updates);

  if ($result) {
    Utils\log_event('job_retried', ['job_id' => $job_id]);
  }

  return $result;
}
