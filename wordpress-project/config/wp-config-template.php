<?php
/**
 * WordPress Configuration File - CNP News
 * 
 * This file contains the following configurations:
 * * MySQL settings
 * * Secret Keys
 * * Database Table prefix
 * * ABSPATH
 * * CNP News specific optimizations
 * 
 * @package WordPress
 * @subpackage CNP_News
 */

// ** MySQL Database Settings ** //
/** Database name */
define('DB_NAME', 'cnpnews_wp');

/** MySQL database username */
define('DB_USER', 'cnpnews_user');

/** MySQL database password */
define('DB_PASSWORD', 'SECURE_PASSWORD_HERE');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', 'utf8mb4_unicode_ci');

/**
 * Authentication Unique Keys and Salts
 * 
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * 
 * @since 2.6.0
 */
define('AUTH_KEY',         'REPLACE_WITH_GENERATED_KEY');
define('SECURE_AUTH_KEY',  'REPLACE_WITH_GENERATED_KEY');
define('LOGGED_IN_KEY',    'REPLACE_WITH_GENERATED_KEY');
define('NONCE_KEY',        'REPLACE_WITH_GENERATED_KEY');
define('AUTH_SALT',        'REPLACE_WITH_GENERATED_SALT');
define('SECURE_AUTH_SALT', 'REPLACE_WITH_GENERATED_SALT');
define('LOGGED_IN_SALT',   'REPLACE_WITH_GENERATED_SALT');
define('NONCE_SALT',       'REPLACE_WITH_GENERATED_SALT');

/**
 * WordPress Database Table prefix
 * 
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'cnp_';

/**
 * WordPress Debugging Mode
 * 
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
define('WP_DEBUG_DISPLAY', false);

// For staging/development, use:
// define('WP_DEBUG', true);
// define('WP_DEBUG_LOG', true);
// define('WP_DEBUG_DISPLAY', false);
// define('SCRIPT_DEBUG', true);

/**
 * CNP News Specific Configuration
 * 
 * Custom settings for optimal performance and security
 */

// ** Security Settings ** //
/** Disable file editing from admin */
define('DISALLOW_FILE_EDIT', true);

/** Disable plugin/theme installation from admin */
define('DISALLOW_FILE_MODS', false); // Set to true after initial setup

/** Force SSL for admin and login */
define('FORCE_SSL_ADMIN', true);

/** Security keys timeout */
define('COOKIE_DOMAIN', '.cnpnews.net');

/** Limit login attempts */
define('WP_LOGIN_ATTEMPTS', 5);

// ** Performance Settings ** //
/** Memory limit */
define('WP_MEMORY_LIMIT', '512M');

/** Maximum execution time */
define('WP_MAX_EXECUTION_TIME', 300);

/** Increase maximum upload size */
define('WP_UPLOAD_MAX_SIZE', '64M');

/** Enable WordPress caching */
define('WP_CACHE', true);

/** Enable compression */
define('COMPRESS_CSS', true);
define('COMPRESS_SCRIPTS', true);
define('CONCATENATE_SCRIPTS', false); // Set to true if no conflicts

/** Object caching */
define('WP_CACHE_KEY_SALT', 'cnpnews_' . md5(__DIR__));

// ** Database Optimization ** //
/** Optimize database tables */
define('WP_ALLOW_REPAIR', false); // Only enable when needed

/** Increase database timeout */
define('DB_TIMEOUT', 30);

/** Enable persistent database connections */
define('WP_USE_EXT_MYSQL', false);

// ** Content and Media Settings ** //
/** Default theme */
define('WP_DEFAULT_THEME', 'cnp-news-theme');

/** Media upload path (for better organization) */
define('UPLOADS', 'wp-content/uploads');

/** Allow SVG uploads (with security plugin) */
define('ALLOW_UNFILTERED_UPLOADS', false);

/** Empty trash automatically */
define('EMPTY_TRASH_DAYS', 7);

/** Limit post revisions */
define('WP_POST_REVISIONS', 5);

/** Autosave interval (in seconds) */
define('AUTOSAVE_INTERVAL', 300); // 5 minutes

// ** Multi-environment Configuration ** //
/** Environment detection */
if (defined('WP_CLI') && WP_CLI) {
    // WP-CLI specific settings
    define('WP_DEBUG', false);
}

// Staging environment
if (strpos($_SERVER['HTTP_HOST'], 'staging.') !== false) {
    define('WP_DEBUG', true);
    define('WP_DEBUG_LOG', true);
    define('DISALLOW_INDEXING', true);
    
    // Staging database (if different)
    // define('DB_NAME', 'cnpnews_staging');
}

// Development environment
if (strpos($_SERVER['HTTP_HOST'], 'dev.') !== false || 
    strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    define('WP_DEBUG', true);
    define('WP_DEBUG_LOG', true);
    define('WP_DEBUG_DISPLAY', true);
    define('SCRIPT_DEBUG', true);
    define('SAVEQUERIES', true);
    
    // Development database
    // define('DB_NAME', 'cnpnews_dev');
}

// ** SEO and Content Settings ** //
/** Site URL (set explicitly for performance) */
define('WP_HOME', 'https://cnpnews.net');
define('WP_SITEURL', 'https://cnpnews.net');

/** Content URL (if using CDN later) */
// define('WP_CONTENT_URL', 'https://cdn.cnpnews.net/wp-content');

/** Disable WordPress cron (use system cron) */
define('DISABLE_WP_CRON', true);

/** Enable multisite (if needed for multiple languages) */
// define('WP_ALLOW_MULTISITE', true);

// ** Security Headers (handled by Nginx, but backup) ** //
/** Frame options */
if (!defined('X_FRAME_OPTIONS')) {
    define('X_FRAME_OPTIONS', 'SAMEORIGIN');
}

// ** API and External Services ** //
/** Disable XML-RPC (security) */
add_filter('xmlrpc_enabled', '__return_false');

/** Limit heartbeat API */
define('WP_HEARTBEAT_INTERVAL', 60); // 60 seconds

/** Disable file edit from themes */
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

// ** Logging Configuration ** //
/** Custom log file for CNP News */
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/wp-content/logs/cnp-news.log');

/** Create logs directory if it doesn't exist */
if (!file_exists(dirname(__FILE__) . '/wp-content/logs')) {
    wp_mkdir_p(dirname(__FILE__) . '/wp-content/logs');
}

// ** Plugin-Specific Settings ** //
/** Rank Math SEO */
define('RANK_MATH_PRO_FILE', false);

/** LiteSpeed Cache */
define('LITESPEED_ON', true);

/** Wordfence */
define('WFWAF_ENABLED', true);

// ** Custom Constants for CNP News ** //
/** Brand constants */
define('CNP_SITE_NAME', 'CNP News');
define('CNP_TAGLINE', 'Clarity in Tech. Confidence in Business.');
define('CNP_TIMEZONE', 'Asia/Kathmandu');

/** Content constants */
define('CNP_DEFAULT_CATEGORY', 'strategy-analysis');
define('CNP_FEATURED_IMAGE_WIDTH', 1200);
define('CNP_FEATURED_IMAGE_HEIGHT', 630);

/** E-E-A-T compliance */
define('CNP_REQUIRE_AUTHOR_BIO', true);
define('CNP_REQUIRE_SOURCES', true);
define('CNP_AI_DISCLOSURE_REQUIRED', true);

/** Performance constants */
define('CNP_ENABLE_LAZY_LOAD', true);
define('CNP_OPTIMIZE_IMAGES', true);
define('CNP_ENABLE_WEBP', true);

/** Analytics */
define('CNP_GA4_ID', 'G-XXXXXXXXXX'); // Replace with actual GA4 ID
define('CNP_GSC_VERIFICATION', 'google-site-verification=XXXXXXXX');

/** Social media */
define('CNP_TWITTER_HANDLE', '@cnpnews');
define('CNP_LINKEDIN_PAGE', 'https://linkedin.com/company/cnp-news');

/** Contact information */
define('CNP_CONTACT_EMAIL', 'hello@cnpnews.net');
define('CNP_SUPPORT_EMAIL', 'support@cnpnews.net');
define('CNP_EDITORIAL_EMAIL', 'editorial@cnpnews.net');

// ** Load WordPress Settings ** //
/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

/**
 * Custom function to handle CNP News specific setup
 */
function cnp_news_setup() {
    // Set timezone
    date_default_timezone_set(CNP_TIMEZONE);
    
    // Custom upload directory structure
    add_filter('upload_dir', function($upload) {
        $upload['subdir'] = '/cnp-news' . $upload['subdir'];
        $upload['path'] = $upload['basedir'] . $upload['subdir'];
        $upload['url'] = $upload['baseurl'] . $upload['subdir'];
        return $upload;
    });
}

// Initialize CNP News setup on WordPress init
add_action('init', 'cnp_news_setup');

/**
 * Custom error handler for production
 */
if (!WP_DEBUG) {
    function cnp_custom_error_handler($errno, $errstr, $errfile, $errline) {
        $error_message = date('Y-m-d H:i:s') . " - Error: [$errno] $errstr in $errfile on line $errline\n";
        error_log($error_message, 3, dirname(__FILE__) . '/wp-content/logs/cnp-errors.log');
        return true;
    }
    set_error_handler('cnp_custom_error_handler', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
}

/**
 * Security: Hide WordPress version
 */
function cnp_remove_wp_version() {
    return '';
}
add_filter('the_generator', 'cnp_remove_wp_version');

/**
 * Performance: Optimize queries
 */
if (defined('WP_DEBUG') && WP_DEBUG) {
    // Log slow queries in debug mode
    define('SAVEQUERIES', true);
    
    function cnp_log_slow_queries() {
        global $wpdb;
        if (isset($wpdb->queries)) {
            foreach ($wpdb->queries as $query) {
                if ($query[1] > 0.1) { // Log queries taking more than 0.1 seconds
                    error_log("Slow Query ({$query[1]}s): {$query[0]}", 3, 
                             dirname(__FILE__) . '/wp-content/logs/slow-queries.log');
                }
            }
        }
    }
    add_action('shutdown', 'cnp_log_slow_queries');
}

/* That's all, stop editing! Happy publishing. */
