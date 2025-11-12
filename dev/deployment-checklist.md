# Deployment Checklist

This checklist ensures safe and consistent deployment of updates and new features to the live site. Use it before every release.

## Pre‑Deployment
1. **Code Review:** Ensure all code is reviewed and tested on staging. Check theme, plugins, and custom scripts.
2. **Backup:** Create a full backup of the database and files. Verify the integrity of the backup.
3. **Plugin & Theme Updates:** Update plugins and themes only on staging. Check for conflicts.
4. **Performance Tests:** Run PageSpeed Insights and WebPageTest on staging. Confirm that Core Web Vitals meet targets.
5. **Security Audit:** Run a security scan. Verify that all forms are protected against injection and XSS attacks.
6. **Robots.txt & Sitemap:** Confirm robots.txt is correct and that sitemap index contains only canonical, indexable URLs【490377143752729†L128-L134】.
7. **Canonical Tags:** Verify self‑referencing canonicals on paginated archives【490377143752729†L194-L203】.
8. **Schema Validation:** Test structured data in Google’s Rich Results Test to ensure there are no errors.
9. **404 & Redirects:** Test common URLs and ensure no broken links. Update redirects as needed.
10. **Accessibility Check:** Verify headings, alt text, and keyboard navigation.

## Deployment
1. **Maintenance Mode:** Enable a maintenance or holding page to prevent user disruptions during deployment.
2. **Push Code:** Deploy updated code and database changes from staging to production using version control or your deployment pipeline.
3. **Flush Caches:** Purge server, CDN (Cloudflare), and plugin caches. Preload key pages.
4. **Database Migrations:** Run any required migrations or updates. Confirm success in logs.

## Post‑Deployment
1. **Smoke Test:** Visit the home page, category pages, sample articles, and special templates (review, live blog) to ensure pages render correctly.
2. **Performance Check:** Re‑run PageSpeed Insights to verify that LCP and INP remain within targets【490377143752729†L302-L317】【490377143752729†L248-L259】.
3. **Monitoring:** Check error logs and Cloudflare analytics for anomalies. Confirm GA4 and GSC are collecting data.
4. **Notify Stakeholders:** Inform the team that the deployment is complete. Summarise any issues encountered.

---

**Owner:** WordPress Developer & Technical SEO Specialist  
**Success Metrics:** Zero downtime; deployment completed within maintenance window; no critical post‑deployment bugs; Core Web Vitals maintained or improved.
