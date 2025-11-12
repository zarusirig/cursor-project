<?php
/**
 * Template Helpers - CNP News
 * 
 * Helper functions for querying related posts,
 * generating content blocks, and supporting
 * advanced template functionality.
 * 
 * @package CNP_News
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get related articles by category + tags
 * 
 * @param int   $post_id ID of post to find related content for
 * @param int   $limit   Number of related posts to fetch
 * @return array WordPress query arguments
 */
function cnp_related_query_args($post_id = null, $limit = 6) {
    $post_id = $post_id ?: get_the_ID();
    
    if (!$post_id) {
        return [
            'post_type'      => 'post',
            'posts_per_page' => $limit,
        ];
    }
    
    // Get categories and tags for this post
    $categories = wp_get_post_categories($post_id);
    $tags       = wp_get_post_tags($post_id, ['fields' => 'ids']);
    
    // Build taxonomy query
    $tax_query = [];
    
    if (!empty($categories)) {
        $tax_query[] = [
            'taxonomy' => 'category',
            'field'    => 'term_id',
            'terms'    => $categories,
            'operator' => 'IN'
        ];
    }
    
    if (!empty($tags)) {
        $tax_query[] = [
            'taxonomy' => 'post_tag',
            'field'    => 'term_id',
            'terms'    => $tags,
            'operator' => 'IN'
        ];
    }
    
    // If we have taxonomy terms, add relation
    if (!empty($tax_query)) {
        array_unshift($tax_query, ['relation' => 'OR']);
    }
    
    return [
        'post_type'           => 'post',
        'post__not_in'        => [$post_id],
        'posts_per_page'      => $limit,
        'ignore_sticky_posts' => true,
        'orderby'             => 'date',
        'order'               => 'DESC',
        'tax_query'           => $tax_query,
    ];
}

/**
 * Get featured posts for hero section
 * 
 * @param int $limit Number of featured posts
 * @return WP_Query
 */
function cnp_get_featured_posts($limit = 5) {
    return new WP_Query([
        'post_type'      => 'post',
        'posts_per_page' => $limit,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'meta_key'       => '_is_featured',
        'meta_value'     => '1',
    ]);
}

/**
 * Get posts by pillar hub (category)
 * 
 * @param string $hub     Hub slug (strategy-analysis, ai, startups, policy, fintech, reviews, security, career)
 * @param int    $limit   Number of posts
 * @return WP_Query
 */
function cnp_get_hub_posts($hub = '', $limit = 12) {
    $args = [
        'post_type'      => 'post',
        'posts_per_page' => $limit,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];
    
    if ($hub) {
        $args['category_name'] = $hub;
    }
    
    return new WP_Query($args);
}

/**
 * Get related posts by tag
 * 
 * @param string|array $tags Tag name(s) or ID(s)
 * @param int          $limit Number of posts
 * @param int          $exclude Exclude post ID
 * @return WP_Query
 */
function cnp_get_posts_by_tag($tags = [], $limit = 6, $exclude = 0) {
    $args = [
        'post_type'      => 'post',
        'posts_per_page' => $limit,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];
    
    if (!empty($tags)) {
        $args['tag__in'] = (array) $tags;
    }
    
    if ($exclude > 0) {
        $args['post__not_in'] = [$exclude];
    }
    
    return new WP_Query($args);
}

/**
 * Get posts from multiple categories
 * 
 * @param array $categories Category IDs or slugs
 * @param int   $limit      Number of posts
 * @return WP_Query
 */
function cnp_get_posts_by_categories($categories = [], $limit = 12) {
    $args = [
        'post_type'      => 'post',
        'posts_per_page' => $limit,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];
    
    if (!empty($categories)) {
        $args['category__in'] = (array) $categories;
    }
    
    return new WP_Query($args);
}

/**
 * Check if post is a review
 * 
 * @param int $post_id Post ID
 * @return bool
 */
function cnp_is_review($post_id = 0) {
    $post_id = $post_id ?: get_the_ID();
    
    if (!$post_id) {
        return false;
    }
    
    $categories = wp_get_post_categories($post_id);
    
    // Check if "reviews" category is assigned (you may need to adjust based on your category structure)
    $reviews_cat = get_term_by('slug', 'reviews', 'category');
    
    return $reviews_cat && in_array($reviews_cat->term_id, $categories);
}

/**
 * Check if post is an explainer
 * 
 * @param int $post_id Post ID
 * @return bool
 */
function cnp_is_explainer($post_id = 0) {
    $post_id = $post_id ?: get_the_ID();
    
    if (!$post_id) {
        return false;
    }
    
    // Check for explainer tag
    $tags = wp_get_post_tags($post_id, ['fields' => 'ids']);
    
    $explainer_tag = get_term_by('slug', 'explainer', 'post_tag');
    
    return $explainer_tag && in_array($explainer_tag->term_id, $tags);
}

/**
 * Get reading time estimate in minutes
 * 
 * @param int $post_id Post ID
 * @return int Minutes
 */
function cnp_get_reading_time($post_id = 0) {
    $post_id = $post_id ?: get_the_ID();
    
    if (!$post_id) {
        return 0;
    }
    
    $cached = get_post_meta($post_id, '_cnp_reading_time', true);
    if ($cached) {
        return (int) $cached;
    }

    $content     = get_post_field('post_content', $post_id);
    $words       = str_word_count(strip_tags($content));
    $reading_min = ceil($words / 225); // 225 words per minute average (realistic for news content)

    update_post_meta($post_id, '_cnp_reading_time', $reading_min);

    return $reading_min;
}

/**
 * Get average reading time for a group of posts
 * 
 * @param array $post_ids Array of post IDs
 * @return int Average reading time
 */
function cnp_get_average_reading_time($post_ids = []) {
    if (empty($post_ids)) {
        return 0;
    }
    
    $times = array_map('cnp_get_reading_time', $post_ids);
    return (int) array_sum($times) / count($times);
}

/**
 * Get breadcrumb trail
 * 
 * @return array Array of breadcrumb items
 */
function cnp_get_breadcrumbs() {
    $breadcrumbs = [];
    
    // Home
    $breadcrumbs[] = [
        'name' => 'Home',
        'url'  => home_url(),
    ];
    
    if (is_single()) {
        $categories = get_the_category();
        if (!empty($categories)) {
            $breadcrumbs[] = [
                'name' => $categories[0]->name,
                'url'  => get_category_link($categories[0]->term_id),
            ];
        }
        
        $breadcrumbs[] = [
            'name' => get_the_title(),
            'url'  => '',
        ];
    } elseif (is_category()) {
        $breadcrumbs[] = [
            'name' => single_cat_title('', false),
            'url'  => '',
        ];
    } elseif (is_search()) {
        $breadcrumbs[] = [
            'name' => sprintf('Search: %s', get_search_query()),
            'url'  => '',
        ];
    } elseif (is_author()) {
        $breadcrumbs[] = [
            'name' => sprintf('Author: %s', get_the_author()),
            'url'  => '',
        ];
    }
    
    return $breadcrumbs;
}

/**
 * Render breadcrumbs
 */
function cnp_render_breadcrumbs() {
    $breadcrumbs = cnp_get_breadcrumbs();
    
    if (empty($breadcrumbs)) {
        return;
    }
    
    echo '<nav class="cnp-breadcrumbs" aria-label="Breadcrumb">';
    echo '<ol itemscope itemtype="https://schema.org/BreadcrumbList">';
    
    foreach ($breadcrumbs as $index => $item) {
        $position = $index + 1;
        echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        
        if ($item['url']) {
            echo '<a itemprop="item" href="' . esc_url($item['url']) . '">';
            echo '<span itemprop="name">' . esc_html($item['name']) . '</span>';
            echo '</a>';
        } else {
            echo '<span itemprop="name">' . esc_html($item['name']) . '</span>';
        }
        
        echo '<meta itemprop="position" content="' . esc_attr($position) . '">';
        echo '</li>';
    }
    
    echo '</ol>';
    echo '</nav>';
}

/**
 * Get posts for infinite scroll / AJAX
 * 
 * @param int $page   Page number
 * @param int $per_page Posts per page
 * @return array Array of posts
 */
function cnp_get_posts_for_ajax($page = 1, $per_page = 12) {
    $args = [
        'post_type'      => 'post',
        'posts_per_page' => $per_page,
        'paged'          => $page,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];
    
    $query = new WP_Query($args);
    
    return [
        'posts'      => $query->posts,
        'total'      => $query->found_posts,
        'max_pages'  => $query->max_num_pages,
        'has_more'   => $page < $query->max_num_pages,
    ];
}

/**
 * Cache reading time metadata for all posts
 * Used during cron job or manual maintenance
 */
function cnp_cache_reading_times() {
    $posts = get_posts([
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_key'       => '_cnp_reading_time',
        'meta_compare'   => 'NOT EXISTS',
    ]);
    
    foreach ($posts as $post_id) {
        cnp_get_reading_time($post_id);
    }
    
    return count($posts);
}

// End of templates.php
