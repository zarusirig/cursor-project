<?php
/**
 * Review Meta & Scoring System - CNP News
 * 
 * Handles review meta fields, dynamic score blocks, affiliate link tagging,
 * and review schema implementation for the CNP News theme.
 * 
 * @package CNP_News
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register review post meta fields
 * Makes meta fields visible in REST API for editor use
 */
add_action('init', function() {
    $fields = [
        'cnp_review_item_name'   => [
            'type'         => 'string',
            'single'       => true,
            'default'      => '',
            'description'  => 'Name of the item being reviewed'
        ],
        'cnp_review_item_type'   => [
            'type'         => 'string',
            'single'       => true,
            'default'      => 'software',
            'description'  => 'Type: software, hardware, service'
        ],
        'cnp_review_score'       => [
            'type'         => 'number',
            'single'       => true,
            'default'      => 0,
            'description'  => 'Score from 0 to 10'
        ],
        'cnp_review_label'       => [
            'type'         => 'string',
            'single'       => true,
            'default'      => '',
            'description'  => 'Score label (e.g., Excellent, Good)'
        ],
        'cnp_review_price'       => [
            'type'         => 'string',
            'single'       => true,
            'default'      => '',
            'description'  => 'Product/service pricing'
        ],
        'cnp_review_url'         => [
            'type'         => 'string',
            'single'       => true,
            'default'      => '',
            'description'  => 'Primary affiliate or product URL'
        ],
        'cnp_review_affiliate'   => [
            'type'         => 'boolean',
            'single'       => true,
            'default'      => false,
            'description'  => 'Whether post contains affiliate links'
        ],
    ];
    
    foreach ($fields as $key => $args) {
        register_post_meta('post', $key, array_merge([
            'show_in_rest'   => true,
            'auth_callback'  => function() {
                return current_user_can('edit_posts');
            },
        ], $args));
    }
});

/**
 * Register dynamic block: cnp/review-score
 * Renders score ribbon with color coding
 */
add_action('init', function() {
    register_block_type('cnp/review-score', [
        'render_callback' => function($atts, $content, $block) {
            $post_id = get_the_ID();
            if (!$post_id) {
                return '';
            }
            
            $score = floatval(get_post_meta($post_id, 'cnp_review_score', true));
            if ($score <= 0) {
                return '';
            }
            
            $label = sanitize_text_field(get_post_meta($post_id, 'cnp_review_label', true));
            $val   = number_format($score, 1);
            
            // Determine tone class based on score
            if ($score >= 8) {
                $tone = 'good';
                $tone_label = 'Excellent';
            } elseif ($score >= 6) {
                $tone = 'ok';
                $tone_label = 'Good';
            } else {
                $tone = 'bad';
                $tone_label = 'Fair';
            }
            
            // Use provided label if available, otherwise use auto-determined label
            if (empty($label)) {
                $label = $tone_label;
            }
            
            ob_start();
            ?>
            <div class="cnp-score cnp-score--<?php echo esc_attr($tone); ?>" role="presentation">
                <div class="cnp-score__content">
                    <span class="cnp-score__label">Score:</span>
                    <span class="cnp-score__value"><?php echo esc_html($val); ?><span class="cnp-score__max">/10</span></span>
                </div>
                <?php if (!empty($label)): ?>
                    <span class="cnp-score__rating"><?php echo esc_html($label); ?></span>
                <?php endif; ?>
            </div>
            <?php
            return ob_get_clean();
        },
        'attributes' => [
            'align' => [
                'type' => 'string',
            ],
        ],
    ]);
});

/**
 * Auto-apply rel="sponsored nofollow" to affiliate links
 * Detects affiliate parameters and external links
 */
add_filter('the_content', function($html) {
    // Skip in admin area
    if (is_admin()) {
        return $html;
    }
    
    // Only process on singular posts
    if (!is_singular('post')) {
        return $html;
    }
    
    $post_id = get_the_ID();
    if (!$post_id) {
        return $html;
    }
    
    // Check if post is marked as having affiliate links
    $has_affiliate_flag = (bool) get_post_meta($post_id, 'cnp_review_affiliate', true);
    
    // Parse HTML
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();

    // Add XML encoding declaration for proper UTF-8 handling
    // LIBXML_HTML_NOXMLNS is available in PHP 7.4+, fallback to 0 for older versions
    $libxml_options = defined('LIBXML_HTML_NOXMLNS') ? LIBXML_HTML_NOXMLNS : 0;
    $loaded = @$doc->loadHTML('<?xml encoding="UTF-8" ?>' . $html, $libxml_options);
    libxml_clear_errors();
    
    if (!$loaded) {
        return $html;
    }
    
    $host = parse_url(home_url(), PHP_URL_HOST);
    $links = $doc->getElementsByTagName('a');
    
    // Iterate through all links
    foreach ($links as $link) {
        $href = $link->getAttribute('href');
        
        if (empty($href)) {
            continue;
        }
        
        // Parse URL components
        $url_host = parse_url($href, PHP_URL_HOST);
        $url_path = parse_url($href, PHP_URL_PATH);
        $url_query = parse_url($href, PHP_URL_QUERY);
        
        // Skip internal links, mailto, tel
        if (!$url_host || 
            $url_host === $host || 
            stripos($href, 'mailto:') === 0 || 
            stripos($href, 'tel:') === 0) {
            continue;
        }
        
        // Detect affiliate patterns
        $is_affiliate = false;
        
        if ($has_affiliate_flag) {
            // If post is marked as affiliate, all external links get tagged
            $is_affiliate = true;
        } else {
            // Look for affiliate patterns in URL
            $affiliate_patterns = [
                'aff=',
                'ref=',
                'affiliate=',
                'partner=',
                'utm_source=affiliate',
                'click=',
                'tracking_id=',
            ];
            
            foreach ($affiliate_patterns as $pattern) {
                if (stripos($href, $pattern) !== false) {
                    $is_affiliate = true;
                    break;
                }
            }
        }
        
        // Apply sponsored/nofollow to affiliate links
        if ($is_affiliate) {
            $rel = $link->getAttribute('rel');
            $rels = [];
            
            // Parse existing rel values
            if (!empty($rel)) {
                $rels = preg_split('/\s+/', strtolower($rel), -1, PREG_SPLIT_NO_EMPTY);
            }
            
            // Add sponsored and nofollow
            if (!in_array('sponsored', $rels)) {
                $rels[] = 'sponsored';
            }
            if (!in_array('nofollow', $rels)) {
                $rels[] = 'nofollow';
            }
            
            // Set updated rel attribute
            $link->setAttribute('rel', implode(' ', array_unique($rels)));
        }
    }
    
    // Convert back to HTML
    $body = $doc->getElementsByTagName('body')->item(0);
    if (!$body) {
        return $html;
    }
    
    $new_html = '';
    foreach ($body->childNodes as $child) {
        $new_html .= $doc->saveHTML($child);
    }
    
    return !empty($new_html) ? $new_html : $html;
}, 20);

/**
 * Auto-generate score label from numeric score
 * Helpful utility for consistent labeling
 */
function cnp_score_label_from_score($score) {
    $score = floatval($score);
    
    if ($score >= 9) {
        return 'Outstanding';
    }
    if ($score >= 8) {
        return 'Excellent';
    }
    if ($score >= 7) {
        return 'Very Good';
    }
    if ($score >= 6) {
        return 'Good';
    }
    if ($score >= 5) {
        return 'Average';
    }
    if ($score >= 3) {
        return 'Fair';
    }
    
    return 'Needs Improvement';
}

/**
 * Get tone class for score
 */
function cnp_score_tone_class($score) {
    $score = floatval($score);
    
    if ($score >= 8) {
        return 'good';
    }
    if ($score >= 6) {
        return 'ok';
    }
    
    return 'bad';
}

/**
 * Emit Review schema (JSON-LD) on single review posts
 */
add_action('wp_head', function() {
    if (!is_single()) {
        return;
    }
    
    $post_id = get_the_ID();
    $score = floatval(get_post_meta($post_id, 'cnp_review_score', true));
    
    // Only emit if there's a valid score
    if ($score <= 0) {
        return;
    }
    
    $item_name = get_post_meta($post_id, 'cnp_review_item_name', true);
    $item_type = get_post_meta($post_id, 'cnp_review_item_type', true) ?: 'software';
    
    // Map item type to schema.org type
    $type_map = [
        'software' => 'SoftwareApplication',
        'hardware' => 'Product',
        'service'  => 'Service',
    ];
    
    $schema_type = $type_map[$item_type] ?? 'Thing';
    $author_id = get_post_field('post_author', $post_id);
    
    $schema = [
        '@context'       => 'https://schema.org',
        '@type'          => 'Review',
        'itemReviewed'   => [
            '@type' => $schema_type,
            'name'  => $item_name ?: get_the_title($post_id),
        ],
        'author'         => [
            '@type' => 'Person',
            'name'  => get_the_author_meta('display_name', $author_id),
            'url'   => get_author_posts_url($author_id),
        ],
        'reviewRating'   => [
            '@type'        => 'Rating',
            'ratingValue'  => number_format($score, 1),
            'bestRating'   => '10',
            'worstRating'  => '0',
        ],
        'datePublished'  => get_post_time('c', true, $post_id),
        'dateModified'   => get_post_modified_time('c', true, $post_id),
        'name'           => wp_strip_all_tags(get_the_title($post_id)),
        'reviewBody'     => wp_strip_all_tags(
            wp_trim_words(get_post_field('post_content', $post_id), 60)
        ),
    ];
    
    // Add image if available
    if (has_post_thumbnail($post_id)) {
        $image_url = get_the_post_thumbnail_url($post_id, 'large');
        if ($image_url) {
            $schema['image'] = $image_url;
        }
    }
    
    // Add URL if available
    $url = get_post_meta($post_id, 'cnp_review_url', true);
    if (!empty($url)) {
        $schema['url'] = esc_url($url);
    }
    
    echo '<script type="application/ld+json">';
    echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    echo '</script>' . "\n";
}, 22);

/**
 * Optional: Auto-insert CTA under review score
 * Inserts a button linking to the primary product URL
 */
add_filter('the_content', function($html) {
    if (!is_singular()) {
        return $html;
    }
    
    $post_id = get_the_ID();
    $score = floatval(get_post_meta($post_id, 'cnp_review_score', true));
    $url = esc_url(get_post_meta($post_id, 'cnp_review_url', true));
    
    // Only insert if post has a score and URL
    if ($score <= 0 || empty($url)) {
        return $html;
    }
    
    // Create CTA HTML
    $cta = sprintf(
        '<div class="cnp-review-cta"><a href="%s" rel="sponsored nofollow" class="cnp-button cnp-button--primary">Check Latest Pricing</a></div>',
        $url
    );
    
    // Insert after first heading (h1 or h2)
    $html = preg_replace('/<\/(h[12])>/', '$0' . $cta, $html, 1);
    
    return $html;
}, 25);

/**
 * Enqueue review CSS styles
 */
add_action('wp_enqueue_scripts', function() {
    $css = '
    /* Review Score Styling */
    .cnp-score {
        display: inline-flex;
        align-items: center;
        gap: 16px;
        padding: 12px 16px;
        border-radius: 8px;
        border: 1px solid var(--cnp-border);
        margin: 12px 0;
        font-family: var(--cnp-font-sans);
    }
    
    .cnp-score__content {
        display: flex;
        align-items: baseline;
        gap: 8px;
    }
    
    .cnp-score__label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--cnp-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .cnp-score__value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--cnp-ink);
    }
    
    .cnp-score__max {
        font-size: 0.875rem;
        font-weight: 400;
        margin-left: 2px;
    }
    
    .cnp-score__rating {
        font-size: 0.95rem;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 4px;
    }
    
    /* Score Tone Colors */
    .cnp-score--good {
        background-color: color-mix(in srgb, var(--cnp-accent) 10%, var(--cnp-surface));
        border-color: var(--cnp-accent);
    }
    
    .cnp-score--good .cnp-score__rating {
        color: var(--cnp-accent);
    }
    
    .cnp-score--ok {
        background-color: color-mix(in srgb, var(--cnp-warn) 10%, var(--cnp-surface));
        border-color: var(--cnp-warn);
    }
    
    .cnp-score--ok .cnp-score__rating {
        color: var(--cnp-warn);
    }
    
    .cnp-score--bad {
        background-color: color-mix(in srgb, var(--cnp-danger) 10%, var(--cnp-surface));
        border-color: var(--cnp-danger);
    }
    
    .cnp-score--bad .cnp-score__rating {
        color: var(--cnp-danger);
    }
    
    /* Review CTA Button */
    .cnp-review-cta {
        margin: 24px 0;
        text-align: center;
    }
    
    .cnp-button {
        display: inline-block;
        padding: 12px 32px;
        border-radius: 6px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }
    
    .cnp-button--primary {
        background-color: var(--cnp-primary);
        color: white;
    }
    
    .cnp-button--primary:hover {
        background-color: color-mix(in srgb, var(--cnp-primary) 85%, black);
        text-decoration: none;
    }
    
    .cnp-button--primary:focus {
        outline: 2px solid var(--cnp-primary);
        outline-offset: 2px;
    }
    
    /* Spec Table Styling */
    .cnp-spec-table {
        width: 100%;
        border-collapse: collapse;
        margin: 24px 0;
    }
    
    .cnp-spec-table th,
    .cnp-spec-table td {
        padding: 12px;
        text-align: left;
        border: 1px solid var(--cnp-border);
    }
    
    .cnp-spec-table th {
        background-color: var(--cnp-surface);
        font-weight: 600;
        color: var(--cnp-ink);
    }
    
    .cnp-spec-table tr:nth-child(even) {
        background-color: color-mix(in srgb, var(--cnp-surface) 50%, transparent);
    }
    
    @media (max-width: 768px) {
        .cnp-spec-table {
            font-size: 0.875rem;
        }
        
        .cnp-spec-table th,
        .cnp-spec-table td {
            padding: 8px;
        }
    }
    ';
    
    // Add inline CSS (or use wp_enqueue_style for separate file)
    wp_add_inline_style('cnp-news-style', $css);
});

// End of reviews.php
