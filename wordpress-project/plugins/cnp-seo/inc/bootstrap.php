<?php

namespace CNP\SEO;



if (!defined('ABSPATH')) exit;

// Admin
require_once CNP_SEO_PATH . 'admin/settings.php';

// Modules
require_once CNP_SEO_PATH . 'inc/meta.php';
require_once CNP_SEO_PATH . 'inc/schema.php';
require_once CNP_SEO_PATH . 'inc/links.php';
require_once CNP_SEO_PATH . 'inc/feeds.php';
require_once CNP_SEO_PATH . 'inc/labels.php';
require_once CNP_SEO_PATH . 'inc/sitemaps.php';

// REST
require_once CNP_SEO_PATH . 'rest/routes.php';
