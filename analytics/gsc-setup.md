# Google Search Console (GSC) Setup Guide

Google Search Console is essential for monitoring our site’s health, visibility, and indexing status. Follow these steps to set up and use GSC effectively.

## 1. Verification
1. Go to [Google Search Console](https://search.google.com/search-console).
2. Add a new property and choose “Domain” for holistic coverage (e.g., `cnpnews.net`).
3. Verify ownership via the DNS TXT record method. Add the provided TXT record to your domain registrar and click “Verify”.
4. Add an additional URL prefix property for `https://cnpnews.net/en/` to view segment‑specific reports.

## 2. Sitemap Submission
- Submit the sitemap index (e.g., `/sitemap_index.xml`) and News Sitemap (e.g., `/news-sitemap.xml`).
- Ensure sitemaps are segmented by content type and include only canonical, indexable URLs【490377143752729†L128-L134】.
- Resubmit sitemaps after major updates or structural changes.

## 3. Performance Report
- Analyse total clicks, impressions, CTR, and average position.
- Filter by country (e.g., India vs Global) and device type.
- Identify keywords or pages with high impressions but low CTR for optimization opportunities (see `/docs/ops/headline-ctr-playbook.md`).

## 4. Coverage Report
- Monitor indexing status, errors, and warnings. Common issues include soft 404s, redirects, and server errors.
- Investigate and fix excluded URLs. Remove non‑canonical URLs from the sitemap and ensure canonical tags are correct【490377143752729†L194-L203】.

## 5. Core Web Vitals & Enhancements
- Review the Core Web Vitals report for LCP, INP, and CLS status across mobile and desktop【490377143752729†L248-L259】【490377143752729†L302-L317】.
- Check Enhancements reports for structured data validation.

## 6. Links Report
- Use the Links report to monitor internal and external links. Identify important pages that lack internal links and adjust navigation or templates accordingly【490377143752729†L383-L391】.

## 7. Alerts & Notifications
- Turn on email notifications for issues detected by GSC.
- Set up Slack or email alerts (via GSC API) for indexing errors or structured data warnings.

---

**Owner:** Technical SEO Specialist & Data Analyst  
**Success Metrics:** Successful property verification; sitemaps accepted without errors; coverage issues resolved promptly; improved CTR and Core Web Vitals over time.
