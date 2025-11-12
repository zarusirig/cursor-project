# WordPress Setup Guide

This document details how to configure WordPress for cnpnews.net/en on an Nginx/Apache stack with Cloudflare and GA4/GSC integrations. Follow these steps during the first week to establish a secure, high‑performance foundation.

## 1. Server & Hosting
- **Choose server software:** We deploy WordPress on Nginx or Apache (choose based on hosting environment). Ensure PHP 8.x, MySQL/MariaDB, and HTTPS by default.
- **Cloudflare integration:** Point DNS to Cloudflare and enable the free tier. Activate SSL/TLS encryption and HTTP/2. Turn on Brotli compression and enable the Automatic Platform Optimization (APO) feature for WordPress, if available.
- **Firewall & security:** Use Cloudflare’s Web Application Firewall (WAF) and rate limiting to mitigate bots and attacks. Set security headers (Content‑Security‑Policy, X‑Frame‑Options, etc.).

## 2. WordPress Installation
1. Download the latest WordPress package and extract it to your web root.
2. Create a dedicated database and user.
3. Configure `wp-config.php` with strong keys and salts. Set `WP_DEBUG` to false on production.
4. Set file and directory permissions securely (e.g., `755` for directories and `644` for files).
5. Install WordPress through the browser and set the site language to English (`/en/`).

## 3. Essential Settings
- **Permalinks:** Use the “Post name” structure.
- **Timezone:** Set to Asia/Kathmandu.
- **Media:** Enable WebP and AVIF support via Performance Lab or WebP Express plugin. Ensure images are resized to appropriate dimensions; avoid heavy originals.
- **Discussion:** Disable pingbacks and trackbacks; moderate comments.
- **User roles:** Create separate roles for Editor, Author, and Contributor with least‑privilege access.

## 4. Plugin Stack (Free‑First)
- **SEO:** Install Rank Math or Yoast (free). Enable NewsArticle schema and configure sitemaps. Disable indexing of thin tag archives and ensure clean canonicals【490377143752729†L194-L203】.
- **Performance:** Use LiteSpeed Cache (if on LiteSpeed server) or use Cloudflare APO + Performance Lab for Nginx/Apache. Configure page caching, object caching, and browser caching.
- **Database & Cleanup:** Install WP‑Optimize to schedule database cleaning.
- **Image optimisation:** If not using core WebP, install WebP Express. Use a same‑origin image directory to maximise connection reuse【490377143752729†L302-L317】.
- **Analytics:** Install CAOS or another locally hosted GA4 script to reduce render blocking.
- **Security:** Use a security plugin (e.g., Wordfence) for additional monitoring and malware scanning.

## 5. Sitemap & Indexing Configuration
- Enable the sitemap index and segment it by posts, pages, categories, and curated tags【490377143752729†L120-L129】.
- Compress sitemaps with Gzip and keep each under 50,000 URLs and 50 MB【490377143752729†L120-L127】.
- Exclude all `noindex`, 404, soft 404, redirects, and thin content from sitemaps【490377143752729†L128-L134】.
- Implement News Sitemaps for time‑sensitive articles and separate Image/Video sitemaps if media is a key growth lever【490377143752729†L141-L145】.
- Ensure `lastmod` timestamps are only updated on meaningful changes【490377143752729†L137-L141】.

## 6. GA4 & GSC
- **GA4:** Create a GA4 property and install the tracking code via a plugin or using CAOS. Configure custom events (scroll depth, engaged time, recirculation clicks, newsletter CTA) as described in `/docs/analytics/ga4-setup.md`.
- **GSC:** Verify ownership of the root domain and the `/en/` subdirectory via DNS or HTML tag. Submit your sitemap index and News Sitemap. Review the Coverage, Performance, and Core Web Vitals reports regularly.

## 7. Cloudflare Workers & Edge Caching (Optional)
For advanced caching, deploy a Cloudflare Worker to cache HTML at the edge and bypass origin on repeated requests. Monitor dynamic updates carefully.

## 8. Backup & Staging
- Set up automated daily backups (files and database) and store them offsite.
- Maintain a staging environment for plugin/theme updates and major changes. Use version control for the theme (Git) and deploy via CI/CD if possible.

---

**Owner:** WordPress Developer  
**Success Metrics:** Successful WordPress installation; clean sitemap and canonical configuration; GA4 and GSC operational; baseline Core Web Vitals metrics recorded.
