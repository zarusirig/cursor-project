# WordPress Installation Guide - CNP News

## Prerequisites
- ✅ Server configured (see `server-setup.md`)
- ✅ Domain pointing to server with Cloudflare
- ✅ SSL certificate installed
- ✅ Database server running

## 1. Database Setup

### Create Database and User
```sql
-- Connect to MySQL as root
mysql -u root -p

-- Create database
CREATE DATABASE cnpnews_wp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create dedicated user
CREATE USER 'cnpnews_user'@'localhost' IDENTIFIED BY 'SECURE_PASSWORD_HERE';

-- Grant privileges
GRANT ALL PRIVILEGES ON cnpnews_wp.* TO 'cnpnews_user'@'localhost';

-- Create backup user (read-only)
CREATE USER 'cnpnews_backup'@'localhost' IDENTIFIED BY 'BACKUP_PASSWORD_HERE';
GRANT SELECT, LOCK TABLES ON cnpnews_wp.* TO 'cnpnews_backup'@'localhost';

FLUSH PRIVILEGES;
EXIT;
```

### Verify Database Connection
```bash
mysql -u cnpnews_user -p cnpnews_wp -e "SELECT DATABASE();"
```

## 2. WordPress Download & Installation

### Download WordPress
```bash
cd /tmp
wget https://wordpress.org/latest.tar.gz
tar -xzf latest.tar.gz

# Set up directory structure
sudo mkdir -p /var/www/cnpnews.net/public_html
sudo cp -R wordpress/* /var/www/cnpnews.net/public_html/

# Set proper permissions
sudo chown -R www-data:www-data /var/www/cnpnews.net/public_html/
sudo find /var/www/cnpnews.net/public_html/ -type d -exec chmod 755 {} \;
sudo find /var/www/cnpnews.net/public_html/ -type f -exec chmod 644 {} \;

# Special permissions for wp-config.php (will be created)
sudo chmod 600 /var/www/cnpnews.net/public_html/wp-config.php
```

## 3. WordPress Configuration

### Generate Security Keys
```bash
curl -s https://api.wordpress.org/secret-key/1.1/salt/
```

### Create wp-config.php
Use the template from `wp-config-template.php` and update with:
- Database credentials
- Security keys from above
- CNP News specific configurations

### Copy wp-config.php
```bash
sudo cp /path/to/wp-config-template.php /var/www/cnpnews.net/public_html/wp-config.php
sudo chown www-data:www-data /var/www/cnpnews.net/public_html/wp-config.php
sudo chmod 600 /var/www/cnpnews.net/public_html/wp-config.php
```

## 4. WordPress Installation via Browser

### Navigate to Installation
1. Go to `https://cnpnews.net/wp-admin/install.php`
2. Complete the 5-minute installation:

**Site Information:**
- **Site Title:** CNP News
- **Username:** (choose secure admin username, NOT "admin")
- **Password:** (generate strong password)
- **Email:** admin@cnpnews.net
- **Search Engine Visibility:** ✅ Checked (discourage search engines until ready)

### Verify Installation
1. Login to `/wp-admin/`
2. Check **Settings > General:**
   - Site Title: CNP News
   - Tagline: Clarity in Tech. Confidence in Business.
   - WordPress Address (URL): https://cnpnews.net
   - Site Address (URL): https://cnpnews.net
   - Email Address: admin@cnpnews.net
   - Timezone: Asia/Kathmandu
   - Date Format: F j, Y
   - Time Format: g:i a

## 5. Essential WordPress Settings

### Permalinks Configuration
**Settings > Permalinks:**
- Structure: ✅ Post name (`/%postname%/`)
- Category base: `category`
- Tag base: `tag`

### Media Settings
**Settings > Media:**
- Thumbnail size: 300 × 300 (crop)
- Medium size: 768 × 768
- Large size: 1200 × 1200
- ✅ Organize uploads into month/year folders

### Discussion Settings
**Settings > Discussion:**
- ❌ Allow link notifications from other blogs
- ❌ Allow link notifications from other blogs to new posts
- ❌ Allow people to submit comments on new posts
- Comment Moderation: ✅ Comments must be manually approved
- Comment Blocklist: Add common spam terms

### Privacy Settings
**Settings > Privacy:**
- Create Privacy Policy page (will be updated later)

## 6. User Management

### Create Editorial Roles
```php
// Add to functions.php or use plugin
add_role('senior_editor', 'Senior Editor', array(
    'read' => true,
    'edit_posts' => true,
    'edit_others_posts' => true,
    'edit_published_posts' => true,
    'publish_posts' => true,
    'delete_posts' => true,
    'delete_others_posts' => true,
    'delete_published_posts' => true,
    'edit_pages' => true,
    'edit_others_pages' => true,
    'edit_published_pages' => true,
    'publish_pages' => true,
    'delete_pages' => true,
    'delete_others_pages' => true,
    'delete_published_pages' => true,
    'manage_categories' => true,
    'upload_files' => true,
));

add_role('content_strategist', 'Content Strategist', array(
    'read' => true,
    'edit_posts' => true,
    'edit_published_posts' => true,
    'publish_posts' => true,
    'delete_posts' => true,
    'upload_files' => true,
    'manage_categories' => true,
));
```

### Create User Accounts
Create accounts for team members:
- Senior Editor
- Content Strategist  
- WordPress Developer
- Technical SEO Specialist

## 7. Security Hardening

### Remove Default Content
1. Delete "Hello World" post
2. Delete "Sample Page"
3. Delete default comment
4. Remove default themes (keep one backup)
5. Remove "Hello Dolly" plugin

### Disable File Editing
Already configured in wp-config.php:
```php
define('DISALLOW_FILE_EDIT', true);
```

### Hide WordPress Version
Add to theme's functions.php:
```php
// Remove WordPress version from head
remove_action('wp_head', 'wp_generator');

// Remove version from RSS feeds
add_filter('the_generator', '__return_empty_string');
```

### Security Headers (already in Nginx config)
- X-Frame-Options: SAMEORIGIN
- X-Content-Type-Options: nosniff
- X-XSS-Protection: 1; mode=block
- Content-Security-Policy
- Referrer-Policy

## 8. Essential Plugin Installation

### Core Plugins (to be installed)
1. **Rank Math SEO** - SEO optimization
2. **LiteSpeed Cache** - Performance caching
3. **Wordfence Security** - Security monitoring
4. **WP-Optimize** - Database optimization
5. **WebP Express** - Image optimization
6. **CAOS** - Local Google Analytics hosting

### Plugin Installation Commands
```bash
# Using WP-CLI (recommended)
wp plugin install rankmath --activate
wp plugin install litespeed-cache --activate
wp plugin install wordfence --activate
wp plugin install wp-optimize --activate
wp plugin install webp-express --activate
wp plugin install host-analyticsjs-local --activate
```

## 9. Theme Installation

### Install CNP News Custom Theme
```bash
# Copy custom theme
sudo cp -R /path/to/cnp-news-theme /var/www/cnpnews.net/public_html/wp-content/themes/

# Set permissions
sudo chown -R www-data:www-data /var/www/cnpnews.net/public_html/wp-content/themes/cnp-news-theme/
sudo find /var/www/cnpnews.net/public_html/wp-content/themes/cnp-news-theme/ -type f -exec chmod 644 {} \;
sudo find /var/www/cnpnews.net/public_html/wp-content/themes/cnp-news-theme/ -type d -exec chmod 755 {} \;

# Activate theme via WP-CLI
wp theme activate cnp-news-theme
```

## 10. Initial Content Setup

### Create Essential Pages
```bash
# Create pages via WP-CLI
wp post create --post_type=page --post_title="About CNP News" --post_status=draft
wp post create --post_type=page --post_title="Contact" --post_status=draft
wp post create --post_type=page --post_title="Privacy Policy" --post_status=draft
wp post create --post_type=page --post_title="Terms of Use" --post_status=draft
wp post create --post_type=page --post_title="Editorial Policy" --post_status=draft
wp post create --post_type=page --post_title="Corrections" --post_status=draft
```

### Create Category Structure
```bash
# Create main categories
wp term create category "Strategy & Analysis" --description="Executive summaries and forward-looking trends"
wp term create category "Artificial Intelligence" --description="AI news and deep dives for all audiences"  
wp term create category "Startups & Capital" --description="Funding rounds, VC, IPOs, and M&A coverage"
wp term create category "Policy & Regulation" --description="Government tech policy and regulatory analysis"
wp term create category "Fintech & Markets" --description="Banking innovation and tech-driven market movements"
wp term create category "Reviews & Tools" --description="Hands-on reviews and software comparisons"
wp term create category "Cybersecurity" --description="Enterprise security and data protection"
wp term create category "Career & Learning" --description="Skills development and workforce trends"
```

### Create Author Profiles
Set up author pages with proper E-E-A-T information:
- Bio with credentials
- External authority links (LinkedIn, publications)
- Expertise areas
- Contact information

## 11. Testing & Verification

### Site Health Check
1. **Tools > Site Health** - Resolve any critical issues
2. Test all URLs redirect to HTTPS
3. Verify Cloudflare is active (check response headers)
4. Test mobile responsiveness
5. Verify all forms work (contact, comments if enabled)

### Performance Testing
```bash
# Test page load speed
curl -o /dev/null -s -w "%{time_total}\n" https://cnpnews.net

# Test from external tools:
# - Google PageSpeed Insights
# - GTmetrix  
# - WebPageTest
# - Pingdom
```

### Security Testing
```bash
# Test SSL certificate
openssl s_client -connect cnpnews.net:443 -servername cnpnews.net

# Check HTTP security headers
curl -I https://cnpnews.net

# Verify WordPress version is hidden
curl -s https://cnpnews.net | grep generator
```

## 12. Backup Configuration

### Initial Backup
```bash
# Create initial backup before going live
/home/backup/mysql-backup.sh
/home/backup/files-backup.sh
```

### Schedule Automated Backups
```bash
# Add to crontab
0 2 * * * /home/backup/mysql-backup.sh
0 3 * * * /home/backup/files-backup.sh
```

## 13. Go Live Checklist

### Pre-Launch
- [ ] All plugins configured and tested
- [ ] Theme installed and customized
- [ ] Essential pages created
- [ ] Category structure implemented
- [ ] User roles and permissions set
- [ ] Security measures active
- [ ] Performance optimized
- [ ] Backups configured
- [ ] Analytics tracking prepared (not yet active)

### Launch
- [ ] Remove "Discourage search engines" setting
- [ ] Submit sitemap to Google Search Console
- [ ] Activate analytics tracking
- [ ] Enable caching
- [ ] Set up monitoring alerts
- [ ] Notify team of go-live

### Post-Launch
- [ ] Monitor error logs
- [ ] Check Core Web Vitals
- [ ] Verify all redirects work
- [ ] Test contact forms
- [ ] Monitor security logs
- [ ] Begin content publication

## Troubleshooting

### Common Issues
1. **White Screen of Death:** Check error logs, increase memory limit
2. **Database Connection Error:** Verify credentials in wp-config.php
3. **Plugin Conflicts:** Deactivate plugins one by one to identify
4. **Permission Issues:** Reset file permissions as shown above
5. **SSL Certificate Issues:** Verify Cloudflare SSL settings

### Support Resources
- WordPress Codex: https://codex.wordpress.org/
- WordPress Support: https://wordpress.org/support/
- Server logs: `/var/log/nginx/error.log`
- WordPress debug: Enable WP_DEBUG in wp-config.php

---

**Estimated Installation Time:** 3-4 hours  
**Next Steps:** Theme customization and plugin configuration  
**Status:** Ready for content creation and SEO optimization
