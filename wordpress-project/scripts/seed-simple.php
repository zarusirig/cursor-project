<?php
/**
 * Simplified CNP News Content Seeding Script
 */

require_once('wp-load.php');

if (!is_user_logged_in() || !current_user_can('administrator')) {
    if (php_sapi_name() !== 'cli') {
        die('Must be admin');
    }
}

echo "=== CNP News Content Seeding ===\n\n";

// Helper function to safely create content
function safe_content($text) {
    return wp_kses_post($text);
}

// STEP 1: CREATE CATEGORIES
echo "Creating categories...\n";
$cats = [
    ['Strategy & Analysis', 'strategy-analysis'],
    ['Artificial Intelligence', 'artificial-intelligence'],
    ['Startups & Capital', 'startups-capital'],
    ['Policy & Regulation', 'policy-regulation'],
    ['Fintech & Markets', 'fintech-markets'],
    ['Reviews & Tools', 'reviews-tools'],
    ['Cybersecurity', 'cybersecurity'],
    ['Career & Learning', 'career-learning']
];

$cat_ids = [];
foreach ($cats as $cat) {
    $term = term_exists($cat[1], 'category');
    if (!$term) {
        $result = wp_insert_term($cat[0], 'category', ['slug' => $cat[1]]);
        if (!is_wp_error($result)) {
            $cat_ids[$cat[1]] = $result['term_id'];
            echo "  ✓ Created: {$cat[0]}\n";
        }
    } else {
        $cat_ids[$cat[1]] = $term['term_id'];
        echo "  ✓ Exists: {$cat[0]}\n";
    }
}

// STEP 2: CREATE AUTHORS
echo "\nCreating authors...\n";
$authors = [
    ['alex.carter', 'alex@cnpnews.net', 'Alex Carter', 'author'],
    ['priya.nair', 'priya@cnpnews.net', 'Priya Nair', 'author'],
    ['editorial.desk', 'desk@cnpnews.net', 'Editorial Desk', 'editor']
];

$author_ids = [];
foreach ($authors as $a) {
    $user_id = username_exists($a[0]);
    if (!$user_id) {
        $user_id = wp_insert_user([
            'user_login' => $a[0],
            'user_email' => $a[1],
            'user_pass' => 'changeme123!',
            'display_name' => $a[2],
            'role' => $a[3],
            'description' => 'CNP News contributor with expertise in technology and business.'
        ]);
        if (!is_wp_error($user_id)) {
            echo "  ✓ Created: {$a[2]}\n";
        }
    } else {
        echo "  ✓ Exists: {$a[2]}\n";
    }
    $author_ids[$a[0]] = $user_id;
}

// STEP 3: CREATE TAGS
echo "\nCreating tags...\n";
$tags = ['OpenAI', 'NVIDIA', 'Microsoft', 'Visa', 'Stripe', 'LLMs', 'Regulation', 'M&A', 'Cyber Threats', 'Data Privacy'];

foreach ($tags as $tag) {
    $term = term_exists($tag, 'post_tag');
    if (!$term) {
        wp_insert_term($tag, 'post_tag');
        echo "  ✓ Created tag: $tag\n";
    } else {
        echo "  ✓ Tag exists: $tag\n";
    }
}

// STEP 4: CREATE ESSENTIAL PAGES
echo "\nCreating essential pages...\n";
$pages = [
    ['About CNP News', 'about', '<h2>Our Mission</h2><p>CNP News delivers clarity in technology and confidence in business.</p>'],
    ['Contact', 'contact', '<h2>Get in Touch</h2><p>Email: news@cnpnews.net</p>'],
    ['Editorial Policy', 'editorial-policy', '<h2>Our Editorial Standards</h2><p>We maintain the highest levels of trustworthiness and accuracy.</p>'],
    ['AI Disclosure', 'ai-disclosure', '<h2>How We Use AI</h2><p>We use AI to assist research while maintaining human editorial oversight.</p>'],
    ['Corrections Policy', 'corrections', '<h2>Our Commitment to Accuracy</h2><p>Contact us at corrections@cnpnews.net to report errors.</p>'],
    ['Privacy Policy', 'privacy', '<h2>Privacy Policy</h2><p>We value your privacy and protect your personal information.</p>'],
    ['Terms of Use', 'terms', '<h2>Terms of Use</h2><p>By using CNP News, you agree to these terms.</p>']
];

foreach ($pages as $p) {
    $existing = get_page_by_path($p[1]);
    if (!$existing) {
        wp_insert_post([
            'post_title' => $p[0],
            'post_name' => $p[1],
            'post_content' => $p[2],
            'post_status' => 'publish',
            'post_type' => 'page'
        ]);
        echo "  ✓ Created: {$p[0]}\n";
    } else {
        echo "  ✓ Exists: {$p[0]}\n";
    }
}

// STEP 5: CREATE PILLAR HUB PAGES
echo "\nCreating Pillar Hub pages...\n";
$hubs = [
    ['Enterprise AI & Automation', 'enterprise-ai-automation'],
    ['Geopolitics of Tech & Commerce', 'geopolitics-tech-commerce'],
    ['Financial Tech & Investment', 'fintech-investment'],
    ['Foundational Tech & Infrastructure', 'foundational-tech-infra']
];

foreach ($hubs as $h) {
    $existing = get_page_by_path($h[1]);
    if (!$existing) {
        wp_insert_post([
            'post_title' => $h[0],
            'post_name' => $h[1],
            'post_content' => "<h1>{$h[0]}</h1><p>Comprehensive coverage of {$h[0]} for business and technology leaders.</p>",
            'post_status' => 'publish',
            'post_type' => 'page'
        ]);
        echo "  ✓ Created hub: {$h[0]}\n";
    } else {
        echo "  ✓ Hub exists: {$h[0]}\n";
    }
}

// STEP 6: CREATE SAMPLE POSTS
echo "\nCreating sample posts...\n";
$posts = [
    ['OpenAI unveils enterprise controls for LLM governance', 'artificial-intelligence', 'alex.carter', ['OpenAI', 'LLMs', 'Regulation']],
    ['How LLMs cut support costs by 40 percent', 'artificial-intelligence', 'alex.carter', ['LLMs', 'Microsoft']],
    ['Visa pilots real-time B2B settlement rails in APAC', 'fintech-markets', 'priya.nair', ['Visa', 'Regulation']],
    ['Stripe expands AI-fraud tooling for SMBs', 'fintech-markets', 'priya.nair', ['Stripe', 'Data Privacy']],
    ['AI in the back office: where ROI shows up first', 'strategy-analysis', 'priya.nair', ['LLMs', 'M&A']],
    ['Build vs buy for internal AI platforms', 'strategy-analysis', 'priya.nair', ['LLMs', 'OpenAI']],
    ['EU AI Act: what changes for enterprise deployments', 'policy-regulation', 'priya.nair', ['Regulation', 'Data Privacy']],
    ['US antitrust heat on big-tech M&A', 'policy-regulation', 'priya.nair', ['M&A', 'Regulation']],
    ['Tool Review: Best AI CRM for startups in 2025', 'reviews-tools', 'alex.carter', ['OpenAI', 'Microsoft']],
    ['Head-to-head: Claude vs GPT-4 for data teams', 'reviews-tools', 'alex.carter', ['OpenAI', 'LLMs']],
    ['Zero-day in popular VPN appliance exploited', 'cybersecurity', 'alex.carter', ['Cyber Threats', 'Data Privacy']],
    ['7 free AI tools for academic research in 2025', 'career-learning', 'alex.carter', ['LLMs', 'OpenAI']],
    ['AI infra startup raises $72M Series B', 'startups-capital', 'alex.carter', ['M&A', 'NVIDIA']]
];

$content_template = '<p>This is a sample article demonstrating the CNP News content structure and editorial standards.</p>

<h2>Key Points</h2>
<ul>
<li>Expert analysis and context for technology developments</li>
<li>Business implications for decision makers</li>
<li>Actionable insights for practitioners</li>
</ul>

<h2>Why This Matters</h2>
<p>Understanding these developments helps business leaders make informed technology decisions and stay ahead of industry trends.</p>

<p><strong>Related:</strong> More articles in this category</p>';

foreach ($posts as $post) {
    $post_id = wp_insert_post([
        'post_title' => $post[0],
        'post_content' => $content_template,
        'post_status' => 'publish',
        'post_type' => 'post',
        'post_author' => $author_ids[$post[2]] ?? 1,
        'post_category' => [$cat_ids[$post[1]] ?? 1]
    ]);
    
    if (!is_wp_error($post_id)) {
        wp_set_post_tags($post_id, $post[3], false);
        echo "  ✓ Created: {$post[0]}\n";
    }
}

echo "\n=== SEEDING COMPLETE ===\n\n";
echo "✅ 8 categories created\n";
echo "✅ 3 authors created\n";
echo "✅ 10 tags created\n";
echo "✅ 7 essential pages created\n";
echo "✅ 4 Pillar Hub pages created\n";
echo "✅ 13 sample posts created\n\n";

echo "Visit: " . get_site_url() . "\n";
echo "Admin: " . admin_url() . "\n\n";

echo "NEXT STEPS:\n";
echo "1. Add featured images via Media Library\n";
echo "2. Configure navigation menus\n";
echo "3. Update Home template in Site Editor\n";
echo "4. Test responsive design\n\n";

echo "Done!\n";
