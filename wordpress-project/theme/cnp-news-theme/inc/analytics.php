<?php
/**
 * Analytics Integration - CNP News
 * 
 * GA4 configuration, custom event tracking, and performance monitoring.
 * Handles tracking of user engagement, scroll depth, reading time, and conversions.
 * 
 * @package CNP_News
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register analytics custom post meta
 */
add_action('init', function() {
    register_post_meta('post', 'cnp_analytics_tracking_enabled', [
        'type'         => 'boolean',
        'single'       => true,
        'default'      => true,
        'show_in_rest' => true,
    ]);
});

/**
 * Add analytics configuration to theme options
 */
add_action('wp_enqueue_scripts', function() {
    $ga4_id = defined('CNP_GA4_ID') ? CNP_GA4_ID : '';
    
    if (empty($ga4_id)) {
        return;
    }
    
    // Create analytics object to pass to JavaScript
    $analytics_config = [
        'ga4_id'        => $ga4_id,
        'site_url'      => home_url(),
        'post_id'       => get_the_ID(),
        'post_type'     => get_post_type(),
        'is_single'     => is_single(),
        'is_archive'    => is_archive(),
        'current_user'  => get_current_user_id(),
    ];
    
    wp_localize_script('cnp-news-script', 'cnpAnalytics', $analytics_config);
});

/**
 * Emit GA4 tracking code
 * Uses local hosting via CAOS plugin if available
 */
add_action('wp_head', function() {
    $ga4_id = defined('CNP_GA4_ID') ? CNP_GA4_ID : '';
    
    if (empty($ga4_id)) {
        return;
    }
    
    // Check if CAOS plugin is active
    if (!function_exists('caos_get_option')) {
        // Manual GA4 implementation (fallback)
        // The CAOS plugin (Complete Analytics Optimization Suite) handles this better
        // but we include a fallback for reference
        ?>
        <!-- GA4 Tracking (via gtag.js) - Preferably use CAOS plugin for local hosting -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($ga4_id); ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?php echo esc_attr($ga4_id); ?>', {
                'anonymize_ip': true,
                'allow_google_signals': false,
            });
        </script>
        <?php
    }
}, 1);

/**
 * Track custom events via gtag
 * JavaScript handles actual event firing in main.js
 */
function cnp_track_event($event_name, $event_data = []) {
    if (is_admin() || !defined('CNP_GA4_ID')) {
        return;
    }
    
    // This is called from JS, but we provide a PHP wrapper for server-side events
    // Generally client-side tracking (JS) is preferred for user interactions
    
    $default_data = [
        'event_category' => 'engagement',
        'event_label'    => '',
    ];
    
    $event_data = array_merge($default_data, $event_data);
    
    // Store for potential server-side tracking
    // In practice, use Measurement Protocol if needed
    return $event_data;
}

/**
 * Enqueue analytics tracking JavaScript
 */
add_action('wp_enqueue_scripts', function() {
    if (!defined('CNP_GA4_ID') || empty(CNP_GA4_ID)) {
        return;
    }
    
    $analytics_js = "
    /**
     * CNP News Analytics Tracking
     * Custom events for GA4 integration
     */
    
    (function() {
        'use strict';
        
        // Check if gtag is available
        if (typeof gtag === 'undefined') {
            console.warn('GA4 not loaded');
            return;
        }
        
        const CNPTracking = {
            // Track scroll depth
            trackScrollDepth: function() {
                const milestones = [25, 50, 75, 100];
                const reached = new Set();
                
                const checkScroll = () => {
                    const scrollPercent = Math.round(
                        (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100
                    );
                    
                    milestones.forEach(milestone => {
                        if (scrollPercent >= milestone && !reached.has(milestone)) {
                            reached.add(milestone);
                            gtag('event', 'scroll_' + milestone, {
                                'event_category': 'engagement',
                                'event_label': 'scroll_depth',
                                'value': milestone
                            });
                        }
                    });
                };
                
                window.addEventListener('scroll', this.throttle(checkScroll, 250));
            },
            
            // Track reading time
            trackReadingTime: function() {
                if (!document.body.classList.contains('single-post')) {
                    return;
                }
                
                let startTime = Date.now();
                let milestones = [30, 60, 120, 300]; // seconds
                let reached = new Set();
                
                setInterval(() => {
                    const timeOnPage = (Date.now() - startTime) / 1000;
                    
                    milestones.forEach(milestone => {
                        if (timeOnPage >= milestone && !reached.has(milestone)) {
                            reached.add(milestone);
                            gtag('event', 'engaged_time_' + milestone + 's', {
                                'event_category': 'engagement',
                                'event_label': 'reading_time',
                                'value': milestone
                            });
                        }
                    });
                }, 5000); // Check every 5 seconds
            },
            
            // Track recirculation clicks
            trackRecirculation: function() {
                document.addEventListener('click', (e) => {
                    const link = e.target.closest('a');
                    if (!link) return;
                    
                    const isRecirculation = link.closest('.cnp-related-posts, .cnp-related, .cnp-card');
                    
                    if (isRecirculation) {
                        gtag('event', 'recirculation_click', {
                            'event_category': 'engagement',
                            'event_label': 'related_article',
                            'link_text': link.textContent.trim(),
                            'link_url': link.href
                        });
                    }
                });
            },
            
            // Track newsletter signup
            trackNewsletterSignup: function() {
                const forms = document.querySelectorAll('.cnp-newsletter-form, .cnp-newsletter-signup form');
                
                forms.forEach(form => {
                    form.addEventListener('submit', (e) => {
                        gtag('event', 'newsletter_signup', {
                            'event_category': 'conversion',
                            'event_label': 'newsletter'
                        });
                    });
                });
            },
            
            // Track external link clicks
            trackExternalLinks: function() {
                document.addEventListener('click', (e) => {
                    const link = e.target.closest('a[href]');
                    if (!link) return;
                    
                    const href = link.getAttribute('href');
                    const hostname = window.location.hostname;
                    const linkHostname = new URL(href, window.location.href).hostname;
                    
                    if (linkHostname !== hostname && !href.startsWith('mailto:') && !href.startsWith('tel:')) {
                        gtag('event', 'external_link_click', {
                            'event_category': 'engagement',
                            'event_label': 'external',
                            'link_url': href
                        });
                    }
                });
            },
            
            // Track affiliate link clicks
            trackAffiliateClicks: function() {
                document.addEventListener('click', (e) => {
                    const link = e.target.closest('a[rel~=\"sponsored\"], a[rel~=\"nofollow\"]');
                    if (!link) return;
                    
                    gtag('event', 'affiliate_click', {
                        'event_category': 'conversion',
                        'event_label': 'affiliate',
                        'link_text': link.textContent.trim(),
                        'link_url': link.href
                    });
                });
            },
            
            // Utility: throttle function
            throttle: function(func, delay) {
                let lastCall = 0;
                return function() {
                    const now = Date.now();
                    if (now - lastCall >= delay) {
                        func.apply(this, arguments);
                        lastCall = now;
                    }
                };
            },
            
            // Initialize all tracking
            init: function() {
                this.trackScrollDepth();
                this.trackReadingTime();
                this.trackRecirculation();
                this.trackNewsletterSignup();
                this.trackExternalLinks();
                this.trackAffiliateClicks();
            }
        };
        
        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => CNPTracking.init());
        } else {
            CNPTracking.init();
        }
        
        // Expose globally for debugging
        window.CNPTracking = CNPTracking;
    })();
    ";
    
    wp_add_inline_script('cnp-news-script', $analytics_js, 'after');
});

/**
 * Monitor Core Web Vitals
 * Output data attributes for JavaScript to track
 */
add_action('wp_footer', function() {
    if (defined('CNP_GA4_ID') && CNP_GA4_ID) {
        ?>
        <script>
        // Core Web Vitals tracking
        // Uses web-vitals library if available, or PerformanceObserver
        
        (function() {
            'use strict';
            
            if (typeof gtag === 'undefined') return;
            
            // Track Largest Contentful Paint (LCP)
            if ('PerformanceObserver' in window) {
                try {
                    const lcpObserver = new PerformanceObserver((list) => {
                        for (const entry of list.getEntries()) {
                            gtag('event', 'page_view', {
                                'metric_name': 'LCP',
                                'value': Math.round(entry.renderTime || entry.loadTime),
                                'event_category': 'web_vitals'
                            });
                        }
                    });
                    lcpObserver.observe({entryTypes: ['largest-contentful-paint']});
                } catch (e) {
                    console.debug('LCP observer error:', e);
                }
            }
            
            // Track Interaction to Next Paint (INP)
            if ('PerformanceObserver' in window) {
                try {
                    const inpObserver = new PerformanceObserver((list) => {
                        for (const entry of list.getEntries()) {
                            gtag('event', 'page_view', {
                                'metric_name': 'INP',
                                'value': Math.round(entry.duration),
                                'event_category': 'web_vitals'
                            });
                        }
                    });
                    inpObserver.observe({entryTypes: ['first-input', 'event']});
                } catch (e) {
                    console.debug('INP observer error:', e);
                }
            }
        })();
        </script>
        <?php
    }
});

/**
 * Add performance monitoring data to head
 */
add_action('wp_head', function() {
    ?>
    <!-- Performance monitoring attributes -->
    <meta name="cnp-performance-enabled" content="true">
    <meta name="cnp-analytics-enabled" content="<?php echo defined('CNP_GA4_ID') ? 'true' : 'false'; ?>">
    <?php
}, 1);

/**
 * Google Search Console verification meta tag
 */
add_action('wp_head', function() {
    if (defined('CNP_GSC_VERIFICATION') && CNP_GSC_VERIFICATION) {
        echo '<meta name="google-site-verification" content="' . esc_attr(CNP_GSC_VERIFICATION) . '">' . "\n";
    }
}, 1);

/**
 * Log analytics events for debugging (development only)
 */
if (defined('WP_DEBUG') && WP_DEBUG) {
    add_action('wp_footer', function() {
        ?>
        <script>
        (function() {
            'use strict';
            
            // Log analytics events to console in debug mode
            window.cnpAnalyticsLog = [];
            
            if (typeof gtag === 'function') {
                const originalGtag = window.gtag;
                window.gtag = function() {
                    window.cnpAnalyticsLog.push(arguments);
                    console.log('GA4 Event:', arguments);
                    return originalGtag.apply(window, arguments);
                };
            }
        })();
        </script>
        <?php
    });
}

/**
 * Enqueue analytics CSS for UI elements
 */
add_action('wp_enqueue_scripts', function() {
    $css = '
    /* Analytics-related styles */
    
    /* External link indicator (optional) */
    a[target="_blank"]::after {
        content: "";
        margin-left: 4px;
        font-size: 0.75em;
    }
    
    /* Affiliate link indicator (optional) */
    a[rel~="sponsored"]::after {
        content: "[sponsored]";
        margin-left: 4px;
        font-size: 0.75em;
        opacity: 0.7;
        font-weight: 500;
    }
    
    /* Newsletter form styles */
    .cnp-newsletter-form input[type="email"] {
        transition: border-color 0.2s ease;
    }
    
    .cnp-newsletter-form input[type="email"]:focus {
        outline: none;
        border-color: var(--cnp-primary);
    }
    ';
    
    wp_add_inline_style('cnp-news-style', $css);
});

// End of analytics.php
