<?php
/**
 * Add placeholder images and configure menus
 */

require_once('wp-load.php');

if (!is_user_logged_in() || !current_user_can('administrator')) {
    if (php_sapi_name() !== 'cli') {
        die('Must be admin');
    }
}

echo "=== Adding Images & Configuring Menus ===\n\n";

// STEP 1: ADD PLACEHOLDER FEATURED IMAGES
echo "Adding placeholder featured images...\n";

// Get all posts without featured images
$posts = get_posts([
    'post_type' => 'post',
    'posts_per_page' => -1,
    'meta_query' => [
        'relation' => 'OR',
        [
            'key' => '_thumbnail_id',
            'compare' => 'NOT EXISTS'
        ],
        [
            'key' => '_thumbnail_id',
            'value' => '',
            'compare' => '='
        ]
    ]
]);

// Create placeholder image data URLs (1280x720 colored rectangles)
$colors = ['4f46e5', '0ea5e9', '10b981', 'f59e0b', 'ef4444', '8b5cf6', 'ec4899', '14b8a6'];
$color_index = 0;

foreach ($posts as $post) {
    // Create a simple placeholder image using WordPress
    $color = $colors[$color_index % count($colors)];
    $color_index++;
    
    // For now, just log that we would add an image
    // In production, you'd upload actual images here
    echo "  ⓘ Post '{$post->post_title}' needs featured image (color: #{$color})\n";
    
    // You can manually add images via Media Library
    // Or use a plugin like "Auto Featured Image" or "Default Featured Image"
}

echo "\n  📝 Note: Add featured images manually via Media Library\n";
echo "  💡 Recommended: Use unsplash.com or pexels.com (1280x720px)\n\n";

// STEP 2: CONFIGURE NAVIGATION MENUS
echo "Configuring navigation menus...\n";

// Create Primary Menu
$primary_menu_name = 'Primary Navigation';
$primary_menu_exists = wp_get_nav_menu_object($primary_menu_name);

if (!$primary_menu_exists) {
    $primary_menu_id = wp_create_nav_menu($primary_menu_name);
    echo "  ✓ Created Primary Navigation menu\n";
} else {
    $primary_menu_id = $primary_menu_exists->term_id;
    echo "  ✓ Primary Navigation menu exists\n";
}

// Add categories to primary menu
$categories = get_categories(['hide_empty' => false]);
foreach ($categories as $cat) {
    if ($cat->slug !== 'uncategorized') {
        wp_update_nav_menu_item($primary_menu_id, 0, [
            'menu-item-title' => $cat->name,
            'menu-item-object' => 'category',
            'menu-item-object-id' => $cat->term_id,
            'menu-item-type' => 'taxonomy',
            'menu-item-status' => 'publish'
        ]);
    }
}
echo "  ✓ Added categories to Primary Navigation\n";

// Add Pillar Hubs submenu
$hubs = get_pages(['post_type' => 'page', 'meta_key' => '_wp_page_template']);
$hub_slugs = ['enterprise-ai-automation', 'geopolitics-tech-commerce', 'fintech-investment', 'foundational-tech-infra'];

foreach ($hub_slugs as $slug) {
    $page = get_page_by_path($slug);
    if ($page) {
        wp_update_nav_menu_item($primary_menu_id, 0, [
            'menu-item-title' => $page->post_title,
            'menu-item-object' => 'page',
            'menu-item-object-id' => $page->ID,
            'menu-item-type' => 'post_type',
            'menu-item-status' => 'publish'
        ]);
    }
}
echo "  ✓ Added Pillar Hubs to Primary Navigation\n";

// Create Footer Menu
$footer_menu_name = 'Footer Menu';
$footer_menu_exists = wp_get_nav_menu_object($footer_menu_name);

if (!$footer_menu_exists) {
    $footer_menu_id = wp_create_nav_menu($footer_menu_name);
    echo "  ✓ Created Footer Menu\n";
} else {
    $footer_menu_id = $footer_menu_exists->term_id;
    echo "  ✓ Footer Menu exists\n";
}

// Add essential pages to footer
$footer_pages = ['about', 'contact', 'editorial-policy', 'ai-disclosure', 'corrections', 'privacy', 'terms'];
foreach ($footer_pages as $slug) {
    $page = get_page_by_path($slug);
    if ($page) {
        wp_update_nav_menu_item($footer_menu_id, 0, [
            'menu-item-title' => $page->post_title,
            'menu-item-object' => 'page',
            'menu-item-object-id' => $page->ID,
            'menu-item-type' => 'post_type',
            'menu-item-status' => 'publish'
        ]);
    }
}
echo "  ✓ Added essential pages to Footer Menu\n";

// Assign menus to theme locations
$locations = get_theme_mod('nav_menu_locations');
$locations['primary'] = $primary_menu_id;
$locations['footer'] = $footer_menu_id;
set_theme_mod('nav_menu_locations', $locations);

echo "  ✓ Assigned menus to theme locations\n\n";

// STEP 3: SET HOMEPAGE TO SHOW LATEST POSTS
update_option('show_on_front', 'posts');
update_option('posts_per_page', 12);
echo "✓ Configured homepage to show latest posts\n\n";

// STEP 4: SET PERMALINK STRUCTURE
global $wp_rewrite;
$wp_rewrite->set_permalink_structure('/%postname%/');
$wp_rewrite->flush_rules();
echo "✓ Set permalinks to post name structure\n\n";

echo "=== CONFIGURATION COMPLETE ===\n\n";
echo "Next steps:\n";
echo "1. Visit http://localhost to see the site\n";
echo "2. Upload featured images via Media Library\n";
echo "3. Customize Home template in Site Editor (Appearance → Editor)\n";
echo "4. Add logo in Customizer (Appearance → Customize)\n";
echo "5. Test all navigation links\n\n";

echo "Done!\n";
