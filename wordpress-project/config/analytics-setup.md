# Analytics & Performance Monitoring Setup - CNP News

## 1. Google Analytics 4 (GA4) Configuration

### Step 1: Create GA4 Property

1. Go to [Google Analytics](https://analytics.google.com/)
2. Sign in with Google account
3. Click **Admin** (bottom left)
4. Create new **Property**:
   - Property name: `CNP News`
   - Time zone: Asia/Kathmandu
   - Currency: USD
5. Select **Web** as platform
6. Enter website URL: `https://cnpnews.net`

### Step 2: Get Measurement ID

1. In GA4 property, go to **Data Streams**
2. Click web data stream
3. Copy **Measurement ID** (format: `G-XXXXXXXXXX`)
4. Add to `wp-config.php`:
   ```php
   define('CNP_GA4_ID', 'G-XXXXXXXXXX');
   ```

### Step 3: Install Analytics Plugin

**Option A: CAOS (Recommended)**
```bash
wp plugin install host-analyticsjs-local --activate
```

Then configure:
1. Go to **Settings > Analytics**
2. Paste Measurement ID
3. Select **Local hosting** option
4. Save

**Option B: MonsterInsights**
```bash
wp plugin install google-analytics-for-wordpress --activate
```

### Step 4: Custom Events Configuration

The theme automatically tracks these events:

**Engagement Events:**
- `scroll_25`, `scroll_50`, `scroll_75` - Scroll depth milestones
- `engaged_time_30s`, `engaged_time_60s` - Reading time milestones
- `engaged_time_120s`, `engaged_time_300s` - Extended reading

**Conversion Events:**
- `newsletter_signup` - Newsletter CTA clicks
- `recirculation_click` - Internal link clicks
- `affiliate_click` - Sponsored link clicks

**Performance Events:**
- `LCP` - Largest Contentful Paint
- `INP` - Interaction to Next Paint

### Step 5: Verify GA4 is Working

1. Visit website homepage
2. Go to GA4 **Realtime** report
3. Perform some actions (scroll, click links)
4. Verify events appear in realtime report

---

## 2. Google Search Console (GSC) Setup

### Step 1: Verify Site Ownership

1. Go to [Google Search Console](https://search.google.com/search-console)
2. Add two properties:
   - Domain: `cnpnews.net` (optional, for all subdomains)
   - URL prefix: `https://cnpnews.net` (required)

### Step 2: Verify Ownership

**Method 1: DNS TXT Record (Recommended)**
1. Copy verification code from GSC
2. Go to domain registrar
3. Add DNS TXT record with provided code
4. Wait 24-48 hours for verification
5. Click "Verify" in GSC

**Method 2: HTML File**
1. Download HTML file from GSC
2. Upload to website root (`public_html/`)
3. Click "Verify" in GSC

**Method 3: Google Tag Manager**
1. Use existing GTM container ID
2. Verify through GTM

### Step 3: Add Site URL Parameter

```php
// In wp-config.php or functions.php
define('CNP_GSC_VERIFICATION', 'google-site-verification=XXXXXXXXX');
```

Meta tag is automatically added to `<head>`.

### Step 4: Submit Sitemaps

1. In GSC, go to **Sitemaps**
2. Submit:
   - Sitemap index: `/sitemap_index.xml`
   - News sitemap: `/news-sitemap.xml` (if applicable)
   - Video sitemap: `/video-sitemap.xml` (if applicable)

### Step 5: Monitor Coverage

Regularly check:
- **Coverage** report for indexing errors
- **Performance** report for CTR and ranking
- **Core Web Vitals** report for performance
- **Enhancements** report for structured data

---

## 3. Custom Event Tracking

### Events Automatically Tracked

**Scroll Depth**
```javascript
// Fires at 25%, 50%, 75%, 100% scroll
gtag('event', 'scroll_25', {
  'event_category': 'engagement',
  'event_label': 'scroll_depth',
  'value': 25
});
```

**Reading Time**
```javascript
// Fires at 30s, 60s, 2min, 5min
gtag('event', 'engaged_time_30s', {
  'event_category': 'engagement',
  'event_label': 'reading_time',
  'value': 30
});
```

**Recirculation**
```javascript
// Fires when clicking related articles
gtag('event', 'recirculation_click', {
  'event_category': 'engagement',
  'event_label': 'related_article',
  'link_text': 'Article Title',
  'link_url': 'https://cnpnews.net/article'
});
```

**Newsletter Signup**
```javascript
// Fires on form submission
gtag('event', 'newsletter_signup', {
  'event_category': 'conversion',
  'event_label': 'newsletter'
});
```

### Accessing Events in GA4

1. Go to **Analysis Hub**
2. Create custom report with events
3. Explore event parameters:
   - Event category
   - Event label
   - Event value
   - Custom parameters

---

## 4. Audience Segmentation

### Create Custom Audiences

**Casual Readers**
- Spent less than 1 minute on site
- No recirculation clicks
- First time visitor

**Loyal Readers**
- Spent more than 5 minutes
- 2+ recirculation clicks
- Repeat visitors

**Newsletter Subscribers**
- Triggered `newsletter_signup` event

**Affiliate Engaged**
- Clicked affiliate links

### Create Audience in GA4

1. Go to **Admin > Audiences**
2. Click **Create audience**
3. Define conditions:
   ```
   Event name = engaged_time_300s
   AND event_category = engagement
   ```
4. Save audience

---

## 5. Dashboards & Reporting

### Create Performance Dashboard

In **Looker Studio**:
1. Create new dashboard
2. Add data source: GA4
3. Add cards:
   - Users (with trend)
   - Sessions (with trend)
   - Engagement rate
   - Conversion rate
   - Top pages
   - Top events

### Create Real-Time Dashboard

1. Go to GA4 **Realtime**
2. Monitor:
   - Users on site now
   - Active pages
   - Recent events
   - Scroll depth

### Weekly Reports

Set up automated reports:
1. Go to **Admin > Data management > Explore**
2. Create exploration
3. Schedule email reports for Mondays

---

## 6. Core Web Vitals Monitoring

### Monitor in GSC

1. Go to **Core Web Vitals** report
2. Check:
   - Good (> 75%)
   - Needs improvement (< 50%)
   - Poor (< 25%)

### Monitor in GA4

Custom events track:
- **LCP** - Largest Contentful Paint
- **INP** - Interaction to Next Paint
- **CLS** - Cumulative Layout Shift

### Monitor with PageSpeed Insights

```bash
# Test specific URL
https://pagespeed.web.dev/?url=https://cnpnews.net
```

Targets:
- LCP: < 2.5s ✅
- INP: < 200ms ✅
- CLS: < 0.1 ✅

---

## 7. Performance Monitoring

### Server Monitoring

Track:
- Page load time
- Time to First Byte (TTFB)
- Server response time

### Third-Party Services

**Recommended:**
- Cloudflare Analytics (free)
- Uptime monitoring (e.g., Pingdom)
- Error tracking (e.g., Sentry)

### Custom Performance Tracking

Add to `wp-config.php`:
```php
define('CNP_PERFORMANCE_TRACKING', true);
```

Logs performance metrics to:
- `/wp-content/logs/performance.log`

---

## 8. Alerts & Notifications

### Set Up GSC Alerts

1. Go to **Settings > Notifications**
2. Enable alerts for:
   - Critical issues
   - Index coverage changes
   - Core Web Vitals regression
   - Structured data errors

### Set Up GA4 Alerts

1. Go to **Admin > Alerts**
2. Create alert:
   - Name: "High bounce rate"
   - Metric: Bounce rate
   - Condition: > 60%
   - Notify: email

### Email Notifications

Configure in WordPress:
1. Go to **Settings > Email**
2. Set admin email for alerts
3. Test email delivery

---

## 9. Verification Checklist

- [ ] GA4 property created
- [ ] Measurement ID added to wp-config.php
- [ ] Analytics plugin installed & activated
- [ ] Custom events tracking in realtime
- [ ] GSC property verified
- [ ] Sitemap submitted to GSC
- [ ] Core Web Vitals baseline recorded
- [ ] Audiences created
- [ ] Dashboard created
- [ ] Alerts configured
- [ ] Weekly reports scheduled

---

## 10. Troubleshooting

### GA4 Not Tracking

1. Check Measurement ID in wp-config.php
2. Verify plugin is active
3. Clear browser cache
4. Check browser console for errors
5. Use GA Debugger Chrome extension

### GSC Showing Errors

1. Check sitemap validity
2. Verify canonical tags
3. Check robots.txt
4. Review coverage report

### Core Web Vitals Issues

1. Optimize images (WebP/AVIF)
2. Reduce JavaScript
3. Enable caching
4. Use CDN (Cloudflare)

---

## 📊 Success Metrics

**GA4 Goals:**
- ✅ Pageviews: 10,000+/month
- ✅ Avg session duration: > 2 min
- ✅ Engagement rate: > 50%
- ✅ Newsletter conversions: > 5%

**Core Web Vitals:**
- ✅ LCP: < 2.5s (Good)
- ✅ INP: < 200ms (Good)
- ✅ CLS: < 0.1 (Good)

**SEO:**
- ✅ Indexed pages: > 90%
- ✅ Average position: < 30
- ✅ CTR: > 5%

---

**Owner:** Data Analyst / Technical SEO Specialist  
**Setup Time:** 2-3 hours  
**Review Cadence:** Weekly
