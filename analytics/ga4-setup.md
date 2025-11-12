# GA4 Setup Guide

Google Analytics 4 (GA4) is our primary analytics platform for measuring audience behaviour and content performance. This guide explains how to configure GA4 for cnpnews.net/en.

## 1. Property Creation
1. Sign in to your Google Analytics account and create a new GA4 property named “CNP News”.
2. Select “Web” as the platform and enter `https://cnpnews.net/en/` as the URL.
3. Copy the Measurement ID (e.g., `G‑XXXXXXX`).

## 2. Installation
1. Use a plugin like CAOS or a performance‑friendly GA4 plugin to host the analytics script locally.
2. Add the Measurement ID to the plugin settings. Ensure the script loads asynchronously to minimise blocking.
3. Verify data is flowing by checking the real‑time report.

## 3. Custom Events
Out‑of‑the‑box GA4 does not capture the events a publisher cares about, so configure the following custom events via Google Tag Manager (GTM) or a local script:

| Event Name | Trigger | Purpose |
| --- | --- | --- |
| `scroll_25`, `scroll_50`, `scroll_75` | When the user scrolls past 25 %, 50 %, and 75 % of the article | Measures content consumption depth |
| `engaged_time_30s`, `engaged_time_60s` | When the user spends 30 s or 60 s on the page | Gauges engagement and reader loyalty |
| `recirculation_click` | When a user clicks a related article link or Pillar Hub link | Tracks recirculation effectiveness |
| `newsletter_cta` | When a user clicks on a newsletter signup CTA | Measures conversion funnel |

Implement these triggers via GTM and send them as recommended events to GA4. Include parameters such as `page_category`, `pillar_hub`, and `tag` to support detailed analysis.

## 4. Audiences & Segments
- **Casual vs Loyal Readers:** Build segments using engaged time and recirculation events. Loyal readers may spend >60 s and click at least one recirculation link.
- **Device Breakdown:** Segment by device type to analyse mobile vs desktop performance.
- **Geography:** Create segments for key regions (e.g., global, India) to support targeted content.

## 5. Reporting
Use the GA4 Analysis Hub to create custom reports:
- **Content Engagement Report:** Shows scroll depth, engaged time, and recirculation by article.
- **Conversion Funnel:** Tracks newsletter signups and affiliate link clicks.
- **Real‑Time Dashboard:** Use with RCI to monitor live performance.

Integrate GA4 data with GSC and RCI into a Looker Studio dashboard (see `/docs/analytics/looker-studio-dashboards.md` once created).

## 6. Privacy & Compliance
- Respect privacy laws and consent mechanisms. Display a cookie banner and allow users to opt out of analytics tracking.
- Do not collect personally identifiable information (PII) in GA4.

---

**Owner:** Data Analyst / BI Specialist  
**Success Metrics:** GA4 property installed and validated; custom events tracking properly; dashboards provide actionable insights into engagement and conversions.
