# Essential Plugins for CNP News

This document outlines the core plugins required for CNP News WordPress installation, their configurations, and installation priorities.

## Plugin Installation Priority

### Phase 1: Core Functionality (Install First)
These plugins are essential for basic site operation and security.

### Phase 2: SEO & Performance (Install Second) 
These plugins optimize the site for search engines and performance.

### Phase 3: Content & Analytics (Install Third)
These plugins enhance content creation and provide analytics.

---

## Phase 1: Core Functionality

### 1. Wordfence Security
**Purpose:** Complete security solution with firewall and malware scanning  
**Priority:** 🔴 Critical  
**Free/Premium:** Free version sufficient initially

**Installation:**
```bash
wp plugin install wordfence --activate
```

**Configuration:**
- Enable firewall (Learning Mode initially)
- Set up email alerts for admin
- Configure login protection (5 attempts, 20-minute lockout)
- Enable real-time IP blocklist
- Schedule daily scans at 2 AM
- Whitelist Cloudflare IPs

**Settings File:** `wordfence-config.json`

### 2. WP-Optimize
**Purpose:** Database optimization and cleanup  
**Priority:** 🔴 Critical  
**Free/Premium:** Free version sufficient

**Installation:**
```bash
wp plugin install wp-optimize --activate
```

**Configuration:**
- Schedule weekly database optimization
- Enable automatic cleanup of spam comments, drafts, revisions
- Compress images on upload
- Set revision limit to 5
- Clean expired transients

**Settings File:** `wp-optimize-config.json`

### 3. UpdraftPlus Backup
**Purpose:** Automated backups to cloud storage  
**Priority:** 🔴 Critical  
**Free/Premium:** Free for basic backups

**Installation:**
```bash
wp plugin install updraftplus --activate
```

**Configuration:**
- Daily database backups
- Weekly full site backups
- Store in Google Drive or AWS S3
- Retain 30 daily, 4 weekly backups
- Email notifications on backup completion/failure

---

## Phase 2: SEO & Performance

### 4. Rank Math SEO
**Purpose:** Comprehensive SEO optimization  
**Priority:** 🔴 Critical  
**Free/Premium:** Free version has all needed features

**Installation:**
```bash
wp plugin install seo-by-rankmath --activate
```

**Configuration:**
- Enable News sitemap
- Configure schema markup (NewsArticle, Person, Organization)
- Set up Google Search Console integration
- Enable social media meta tags
- Configure internal linking suggestions
- Set up 404 monitoring
- Enable XML sitemaps with custom post types

**Settings File:** `rankmath-config.json`

### 5. LiteSpeed Cache
**Purpose:** Advanced caching and performance optimization  
**Priority:** 🟡 High  
**Free/Premium:** Free version sufficient  
**Note:** Use only if on LiteSpeed server, otherwise use alternative

**Installation:**
```bash
wp plugin install litespeed-cache --activate
```

**Alternative (for non-LiteSpeed servers):**
```bash
wp plugin install wp-fastest-cache --activate
```

**Configuration:**
- Enable page caching
- Enable object caching (Redis/Memcached)
- Configure CSS/JS minification and combination
- Enable image optimization
- Set up critical CSS generation
- Configure CDN integration (Cloudflare)

### 6. WebP Express
**Purpose:** Convert images to WebP format  
**Priority:** 🟡 High  
**Free/Premium:** Free

**Installation:**
```bash
wp plugin install webp-express --activate
```

**Configuration:**
- Enable WebP conversion for uploads
- Serve WebP to supported browsers
- Fallback to original format for unsupported browsers
- Enable AVIF support if server supports it
- Set quality to 85% for optimal balance

---

## Phase 3: Content & Analytics

### 7. CAOS (Complete Analytics Optimization Suite)
**Purpose:** Host Google Analytics locally for performance  
**Priority:** 🟡 High  
**Free/Premium:** Free

**Installation:**
```bash
wp plugin install host-analyticsjs-local --activate
```

**Configuration:**
- Set GA4 tracking ID
- Enable local hosting of analytics script
- Configure enhanced ecommerce (for affiliate tracking)
- Set up custom events for scroll depth, engagement
- Enable IP anonymization for privacy compliance

### 8. MonsterInsights (Alternative to CAOS)
**Purpose:** Google Analytics integration with WordPress dashboard  
**Priority:** 🟡 High (if not using CAOS)  
**Free/Premium:** Free version for basic analytics

**Installation:**
```bash
wp plugin install google-analytics-for-wordpress --activate
```

### 9. Editorial Calendar
**Purpose:** Content planning and editorial workflow  
**Priority:** 🔵 Medium  
**Free/Premium:** Free

**Installation:**
```bash
wp plugin install editorial-calendar --activate
```

**Configuration:**
- Set up editorial roles and permissions
- Configure content approval workflow
- Enable editorial comments and notes
- Set up publication schedule view

### 10. User Role Editor
**Purpose:** Customize user roles and capabilities  
**Priority:** 🔵 Medium  
**Free/Premium:** Free version sufficient

**Installation:**
```bash
wp plugin install user-role-editor --activate
```

**Configuration:**
- Create custom roles: Senior Editor, Content Strategist, SEO Specialist
- Configure granular permissions for each role
- Restrict admin access to essential users only

---

## Phase 4: Content Enhancement

### 11. Advanced Custom Fields (ACF)
**Purpose:** Custom fields for structured content  
**Priority:** 🔵 Medium  
**Free/Premium:** Free for basic fields

**Installation:**
```bash
wp plugin install advanced-custom-fields --activate
```

**Configuration:**
- Create fields for E-E-A-T information (author expertise, sources)
- Set up review rating fields
- Create fields for pillar hub relationships
- Configure fields for AI disclosure and affiliate disclaimers

### 12. TablePress
**Purpose:** Create and manage tables  
**Priority:** 🔵 Medium  
**Free/Premium:** Free

**Installation:**
```bash
wp plugin install tablepress --activate
```

**Configuration:**
- Enable responsive tables
- Configure table styling to match theme
- Set up CSV import/export
- Enable table search and pagination

### 13. WP Recipe Maker (for How-To Content)
**Purpose:** Structured data for how-to articles  
**Priority:** 🔵 Low  
**Free/Premium:** Free for basic how-to schema

**Installation:**
```bash
wp plugin install wp-recipe-maker --activate
```

**Configuration:**
- Configure for how-to articles instead of recipes
- Set up structured data for instructions
- Enable FAQ schema integration

---

## Plugin Configuration Files

### Installation Script
Create `install-plugins.sh`:
```bash
#!/bin/bash
# CNP News Plugin Installation Script

echo "Installing Phase 1: Core Functionality..."
wp plugin install wordfence --activate
wp plugin install wp-optimize --activate
wp plugin install updraftplus --activate

echo "Installing Phase 2: SEO & Performance..."
wp plugin install seo-by-rankmath --activate
wp plugin install litespeed-cache --activate  # or wp-fastest-cache
wp plugin install webp-express --activate

echo "Installing Phase 3: Content & Analytics..."
wp plugin install host-analyticsjs-local --activate
wp plugin install editorial-calendar --activate
wp plugin install user-role-editor --activate

echo "Installing Phase 4: Content Enhancement..."
wp plugin install advanced-custom-fields --activate
wp plugin install tablepress --activate

echo "Plugin installation complete!"
echo "Please configure each plugin according to the documentation."
```

### Plugin Health Check Script
Create `check-plugins.sh`:
```bash
#!/bin/bash
# Check plugin status and health

echo "Checking plugin status..."
wp plugin list --status=active
echo ""

echo "Checking for plugin updates..."
wp plugin list --update=available
echo ""

echo "Checking plugin health..."
wp plugin verify-checksums --all
```

## Plugin Alternatives

### If LiteSpeed Cache Not Available
- **W3 Total Cache** - Comprehensive caching
- **WP Fastest Cache** - Simple, effective caching
- **WP Super Cache** - Basic page caching

### If Rank Math Issues
- **Yoast SEO** - Alternative SEO plugin
- **SEOPress** - Lightweight SEO solution

### Performance Monitoring
- **Query Monitor** - Debug slow queries (development only)
- **P3 (Plugin Performance Profiler)** - Identify slow plugins

## Plugin Maintenance

### Weekly Tasks
- [ ] Check for plugin updates
- [ ] Review security scan results (Wordfence)
- [ ] Verify backup completion (UpdraftPlus)
- [ ] Check site performance impact

### Monthly Tasks
- [ ] Review plugin necessity
- [ ] Check for conflicting plugins
- [ ] Audit plugin permissions and access
- [ ] Update plugin configurations as needed

### Security Best Practices
1. **Keep plugins updated** - Enable automatic updates for security patches
2. **Remove unused plugins** - Deactivate and delete plugins not in use
3. **Use reputable sources** - Only install plugins from WordPress.org or trusted developers
4. **Regular backups** - Before any plugin updates or installations
5. **Monitor performance** - Track how plugins affect site speed

## Plugin Support

### Documentation Links
- [Wordfence Documentation](https://www.wordfence.com/help/)
- [Rank Math Documentation](https://rankmath.com/kb/)
- [LiteSpeed Cache Guide](https://docs.litespeedtech.com/lscache/lscwp/)
- [WordPress Plugin Best Practices](https://developer.wordpress.org/plugins/plugin-basics/best-practices/)

### Support Contacts
- **WordPress Developer:** Primary contact for plugin issues
- **Technical SEO Specialist:** SEO plugin configurations
- **Site Administrator:** Plugin permissions and access

---

**Total Plugins:** 13 core plugins  
**Installation Time:** 2-3 hours (including configuration)  
**Monthly Cost:** $0 (using free versions)  
**Upgrade Path:** Premium versions available for advanced features
