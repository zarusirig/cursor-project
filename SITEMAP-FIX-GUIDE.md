# Sitemap Fix Guide - CNP News

**Date:** November 13, 2025
**Issue:** sitemap.xml and news-sitemap.xml not working
**Status:** ✅ FIXED

---

## 🐛 Issues Identified

### Problem 1: Missing Bootstrap Include
The `sitemaps.php` file was not being loaded in `bootstrap.php`, so all sitemap functionality was unavailable.

**Location:** `/wordpress-project/plugins/cnp-seo/inc/bootstrap.php`

### Problem 2: Missing Default Settings
The plugin activation hook did not set default values for sitemap settings, causing the REST API to reject requests with "sitemaps disabled" error.

**Location:** `/wordpress-project/plugins/cnp-seo/cnp-seo.php`

### Problem 3: No Rewrite Rules Flush
Rewrite rules were not being flushed on plugin activation, so WordPress didn't know how to route `/sitemap.xml` requests.

---

## ✅ Fixes Applied

### Fix 1: Added Sitemaps to Bootstrap ✓

**File:** `wordpress-project/plugins/cnp-seo/inc/bootstrap.php`

**Change:**
```php
// Before
require_once CNP_SEO_PATH . 'inc/labels.php';

// REST
require_once CNP_SEO_PATH . 'rest/routes.php';

// After
require_once CNP_SEO_PATH . 'inc/labels.php';
require_once CNP_SEO_PATH . 'inc/sitemaps.php';  // ← Added

// REST
require_once CNP_SEO_PATH . 'rest/routes.php';
```

### Fix 2: Added Default Sitemap Settings ✓

**File:** `wordpress-project/plugins/cnp-seo/cnp-seo.php`

**Change:**
```php
register_activation_hook(__FILE__, function(){
  add_option('cnp_seo_settings', [
    'site_title_format' => '%title% — %sitename%',
    'site_desc_default' => '',

    // ← Added all sitemap settings
    'sitemaps_enabled' => 1,
    'sitemap_posts_enabled' => 1,
    'sitemap_pages_enabled' => 1,
    'sitemap_categories_enabled' => 1,
    'sitemap_tags_enabled' => 1,
    'sitemap_authors_enabled' => 1,
    'sitemap_images_enabled' => 0,
    'sitemap_max_urls' => 2000,
    'sitemap_cache_ttl' => 600,
    'sitemap_excluded_paths' => '',
    'sitemap_excluded_post_types' => [],
    'sitemap_excluded_taxonomies' => [],

    // News sitemap settings
    'news_sitemap_enabled' => 1,
    'sitemap_news_window_hours' => 48,
    'news_publication_name' => get_bloginfo('name'),
    'news_publication_lang' => 'en',

    // ... rest of settings
  ]);

  // ← Added rewrite rules flush
  flush_rewrite_rules();
});
```

### Fix 3: Created Migration Script ✓

**File:** `wordpress-project/scripts/fix-sitemaps.php`

This script:
- Updates existing installations with missing settings
- Flushes rewrite rules
- Tests all sitemap URLs
- Provides diagnostic information
- Shows content counts and recommendations

---

## 🚀 How to Apply the Fix

### Option A: Fresh Installation (New Sites)

If you haven't installed the plugin yet:

1. The fixes are already in the code
2. Install/activate the plugin normally:
   ```bash
   # In WordPress admin
   Plugins → Installed Plugins → CNP SEO → Activate
   ```
3. Sitemaps will work automatically
4. Test by visiting: `https://your-site.com/sitemap.xml`

### Option B: Existing Installation (Already Activated)

If the plugin is already activated:

1. **Run the fix script:**

   **Method 1: Via Browser (Recommended)**
   ```bash
   # Copy script to wp-content
   cp wordpress-project/scripts/fix-sitemaps.php wp-content/

   # Visit in browser (must be logged in as admin)
   https://your-site.com/wp-content/fix-sitemaps.php

   # Delete script after running
   rm wp-content/fix-sitemaps.php
   ```

   **Method 2: Via WP-CLI**
   ```bash
   wp eval-file wordpress-project/scripts/fix-sitemaps.php
   ```

   **Method 3: Manual (via WordPress admin)**
   - Go to: Plugins → Deactivate CNP SEO
   - Go to: Plugins → Activate CNP SEO
   - This will run the activation hook with new settings

2. **Verify it worked:**
   ```bash
   curl -I https://your-site.com/sitemap.xml
   # Should return: HTTP/1.1 200 OK or 301 redirect (followed by 200)

   curl -I https://your-site.com/news-sitemap.xml
   # Should return: HTTP/1.1 200 OK or 301 redirect (followed by 200)
   ```

---

## 🧪 Testing & Verification

### Test 1: Check Sitemap URLs Work

Visit these URLs in your browser:

1. **Main Sitemap Index:**
   ```
   https://your-site.com/sitemap.xml
   ```
   Should show XML with links to child sitemaps:
   ```xml
   <?xml version="1.0" encoding="UTF-8"?>
   <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
     <sitemap>
       <loc>https://your-site.com/sitemap-posts-1.xml</loc>
       <lastmod>2025-11-13T...</lastmod>
     </sitemap>
     <sitemap>
       <loc>https://your-site.com/sitemap-pages-1.xml</loc>
       <lastmod>2025-11-13T...</lastmod>
     </sitemap>
     ...
   </sitemapindex>
   ```

2. **News Sitemap:**
   ```
   https://your-site.com/news-sitemap.xml
   ```
   Should show XML with recent posts (last 48 hours):
   ```xml
   <?xml version="1.0" encoding="UTF-8"?>
   <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
           xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
     <url>
       <loc>https://your-site.com/article-url/</loc>
       <news:news>
         <news:publication>
           <news:name>CNP News</news:name>
           <news:language>en</news:language>
         </news:publication>
         <news:publication_date>2025-11-13T...</news:publication_date>
         <news:title>Article Title</news:title>
       </news:news>
     </url>
   </urlset>
   ```

3. **Child Sitemaps:**
   ```
   https://your-site.com/sitemap-posts-1.xml
   https://your-site.com/sitemap-pages-1.xml
   https://your-site.com/sitemap-categories-1.xml
   ```
   Should show individual URL entries

### Test 2: Validate with Google

1. **Rich Results Test:**
   ```
   https://search.google.com/test/rich-results?url=https://your-site.com/sitemap.xml
   ```

2. **Sitemap Validator:**
   ```
   https://www.xml-sitemaps.com/validate-xml-sitemap.html
   ```
   Enter your sitemap URL and click "Validate"

### Test 3: Check REST API Endpoints

These REST endpoints should also work:

```bash
# Main sitemap index
curl https://your-site.com/wp-json/cnp-seo/v1/sitemap/index

# News sitemap
curl https://your-site.com/wp-json/cnp-seo/v1/sitemap/news

# Posts sitemap page 1
curl https://your-site.com/wp-json/cnp-seo/v1/sitemap/posts/1
```

### Test 4: Check robots.txt Integration

Visit:
```
https://your-site.com/robots.txt
```

Should include:
```
# Sitemaps
Sitemap: https://your-site.com/sitemap.xml
Sitemap: https://your-site.com/news-sitemap.xml
```

---

## 📊 Expected Output

### With Content (Posts Published)

**Main Sitemap (`/sitemap.xml`):**
- Status: 200 OK
- Content-Type: application/xml
- Contains: `<sitemapindex>` with child sitemap links

**News Sitemap (`/news-sitemap.xml`):**
- Status: 200 OK
- Content-Type: application/xml
- Contains: Posts from last 48 hours with `<news:news>` tags

**Child Sitemaps (`/sitemap-posts-1.xml`, etc):**
- Status: 200 OK
- Contains: Individual `<url>` entries with `<loc>` and `<lastmod>`

### Without Content (Empty Site)

If you have no published posts yet:

**Main Sitemap:**
- Status: 200 OK (but may be empty or minimal)

**News Sitemap:**
- Status: 200 OK (but empty `<urlset>`)
- This is normal - will populate when you publish content

**Solution:**
- Publish at least 1-2 posts to test properly
- Posts must be recent (within 48 hours) for news sitemap

---

## 🔧 Troubleshooting

### Issue: 404 Not Found

**Symptoms:**
```bash
curl -I https://your-site.com/sitemap.xml
# HTTP/1.1 404 Not Found
```

**Causes & Solutions:**

1. **Rewrite rules not flushed**
   ```bash
   # In WordPress admin
   Settings → Permalinks → Save Changes
   # This flushes rewrite rules
   ```

2. **Plugin not active**
   ```bash
   # Check plugin status
   Plugins → Installed Plugins
   # Ensure CNP SEO is activated
   ```

3. **Permalink structure issue**
   ```bash
   # Change permalink structure
   Settings → Permalinks → Post name → Save
   ```

### Issue: Empty Sitemaps

**Symptoms:**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
</urlset>
```

**Causes & Solutions:**

1. **No published content**
   - Publish at least 1 post or page
   - Check post status is "Publish" not "Draft"

2. **News sitemap empty: Posts too old**
   - News sitemap only shows last 48 hours
   - Publish new content or increase window:
     ```php
     // In wp-admin or via code
     $opts = get_option('cnp_seo_settings');
     $opts['sitemap_news_window_hours'] = 72; // 3 days
     update_option('cnp_seo_settings', $opts);
     ```

3. **Posts excluded by meta**
   - Check if posts have `_cnp_seo_noindex` meta set
   - Edit post → E-E-A-T meta box → Ensure "noindex" is unchecked

### Issue: Redirect Loop

**Symptoms:**
```bash
curl -I https://your-site.com/sitemap.xml
# HTTP/1.1 301 Moved Permanently
# Location: https://your-site.com/sitemap.xml
```

**Solution:**
- Conflicting redirect rules in `.htaccess` or Nginx config
- Check for other SEO plugins (Yoast, All in One SEO)
- Disable other sitemap plugins
- The CNP SEO plugin disables WordPress core sitemaps automatically

### Issue: XML Parse Error

**Symptoms:**
```
XML Parsing Error: not well-formed
```

**Causes & Solutions:**

1. **PHP errors in output**
   - Check: `wp-content/debug.log` for errors
   - Enable debugging:
     ```php
     // wp-config.php
     define('WP_DEBUG', true);
     define('WP_DEBUG_LOG', true);
     ```

2. **Invalid characters in content**
   - Check post titles for special characters
   - Plugin escapes with `esc_html()` and `esc_url()`

3. **Output buffering issue**
   - Other plugins outputting content before XML
   - Deactivate other plugins temporarily to test

---

## 📈 Submit to Google

Once sitemaps are working:

### 1. Google Search Console

1. Go to: https://search.google.com/search-console
2. Select your property (or add cnpnews.net)
3. Navigate to: **Sitemaps** (left sidebar)
4. Add new sitemap:
   ```
   sitemap.xml
   news-sitemap.xml
   ```
5. Click **Submit**
6. Status should change to "Success" within a few minutes
7. Check back in 24 hours to see indexed URLs

### 2. Bing Webmaster Tools

1. Go to: https://www.bing.com/webmasters
2. Select your site
3. Navigate to: **Sitemaps**
4. Add sitemaps:
   ```
   https://cnpnews.net/sitemap.xml
   https://cnpnews.net/news-sitemap.xml
   ```
5. Click **Submit**

---

## 📋 Checklist: Sitemap Readiness

Use this checklist before submitting to Google News:

- [ ] Main sitemap accessible at `/sitemap.xml` (200 OK)
- [ ] News sitemap accessible at `/news-sitemap.xml` (200 OK)
- [ ] Child sitemaps loading properly
- [ ] Sitemaps declared in `/robots.txt`
- [ ] At least 20 posts published (for Google News)
- [ ] At least 1 post in last 48 hours (for news sitemap)
- [ ] All posts have publish dates
- [ ] No XML parse errors (validated)
- [ ] Google Search Console verified
- [ ] Sitemaps submitted to GSC
- [ ] Sitemaps submitted to Bing Webmaster Tools
- [ ] Test on mobile (responsive check)

---

## 🎯 Performance Notes

### Caching

Sitemaps are cached for **10 minutes (600 seconds)** by default.

**Cache is automatically cleared when:**
- New post published
- Post updated
- Post deleted
- Category/tag edited
- Author profile updated
- Plugin settings changed

**Manual cache clear:**
```php
// Via code or WP-CLI
\CNP\SEO\Sitemaps\clear_sitemap_cache();

// Or clear all transients
wp transient delete-all
```

### Large Sites (1000+ posts)

If you have many posts:

1. **Paginated sitemaps:** Automatically splits at 2,000 URLs per file
2. **Limit URL counts:**
   ```php
   $opts = get_option('cnp_seo_settings');
   $opts['sitemap_max_urls'] = 1000; // Reduce to 1,000
   update_option('cnp_seo_settings', $opts);
   ```

3. **Exclude content types:**
   ```php
   $opts['sitemap_images_enabled'] = 0; // Disable image sitemap
   $opts['sitemap_tags_enabled'] = 0;   // Disable tag sitemap
   update_option('cnp_seo_settings', $opts);
   ```

---

## 🔍 Debug Information

### Check Settings via WP-CLI

```bash
wp option get cnp_seo_settings --format=json | jq .sitemaps_enabled
# Should return: 1

wp option get cnp_seo_settings --format=json | jq .news_sitemap_enabled
# Should return: 1
```

### Check Rewrite Rules

```bash
wp rewrite list --format=table | grep sitemap
# Should show:
# sitemap\.xml                     index.php?cnp_sitemap=index
# news-sitemap\.xml                index.php?cnp_sitemap=news
```

### Test REST API Endpoint

```bash
# Health check
curl https://your-site.com/wp-json/cnp-seo/v1/health
# Should return: {"ok":true,"ver":"0.1.0"}

# Sitemap stats (admin only)
curl https://your-site.com/wp-json/cnp-seo/v1/sitemap-stats \
  --cookie "wordpress_logged_in_..."
```

---

## 📚 Technical Details

### How It Works

1. **URL Routing:**
   - `/sitemap.xml` → Rewrite rule → `?cnp_sitemap=index`
   - Query var caught by REST API redirect
   - Redirects to: `/wp-json/cnp-seo/v1/sitemap/index`
   - REST endpoint generates XML

2. **Content Generation:**
   - `output_sitemap_index()` - Main index
   - `output_child_sitemap($type, $page)` - Posts, pages, etc.
   - `output_news_sitemap()` - News (48h window)

3. **Caching:**
   - Transients API with 10-minute TTL
   - Automatic invalidation on content changes
   - Per-type, per-page cache keys

4. **Security:**
   - All URLs escaped with `esc_url()`
   - Titles escaped with `esc_html()`
   - SQL uses prepared statements
   - No user input in XML generation

---

## ✅ Summary

**What was broken:**
- Sitemaps not loading (missing includes)
- Settings not initialized (missing defaults)
- Rewrite rules not active (no flush)

**What was fixed:**
- Added `sitemaps.php` to bootstrap
- Added all sitemap settings to activation hook
- Added `flush_rewrite_rules()` on activation
- Created migration script for existing installs

**How to verify:**
- Visit `/sitemap.xml` → Should return XML
- Visit `/news-sitemap.xml` → Should return XML
- Run fix script → Should show "✓ WORKING"

**Next steps:**
1. Apply fixes (deactivate/reactivate plugin OR run script)
2. Test sitemaps work
3. Submit to Google Search Console
4. Submit to Bing Webmaster Tools
5. Monitor indexing in GSC

---

**Document Version:** 1.0.0
**Last Updated:** November 13, 2025
**Status:** ✅ Complete
**Author:** Development Team
