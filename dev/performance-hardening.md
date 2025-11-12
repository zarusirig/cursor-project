# Performance Hardening Guide

Optimising performance ensures our site loads quickly and provides a smooth user experience. This guide focuses on the two most important Core Web Vitals metrics: Interaction to Next Paint (INP) and Largest Contentful Paint (LCP).

## Interaction to Next Paint (INP)
INP measures the total time from a user interaction to the next paint. To minimise INP:

1. **Defer non‑critical resources:** Load analytics, ads, and third‑party widgets after the main content renders【490377143752729†L248-L259】.
2. **Break up long tasks:** Avoid JavaScript tasks longer than 50 ms. Split complex scripts into smaller chunks and schedule them with `requestIdleCallback()`【490377143752729†L248-L259】.
3. **Use passive event listeners:** Mark event listeners as passive where possible to allow the browser to optimise scrolling.
4. **Lightweight DOM:** Keep the DOM shallow and avoid unnecessary wrappers by using Gutenberg and block‑optimised themes【490377143752729†L82-L94】.
5. **Monitor:** Regularly check INP in the Core Web Vitals report in Google Search Console and Lab tools.

## Largest Contentful Paint (LCP)
LCP measures the render time of the largest visible element on the page, usually the hero image. Improve LCP by:

1. **Reducing TTFB:** Use fast hosting, server‑level caching (e.g., Redis/Object Cache), and Cloudflare edge caching.
2. **Optimising images:** Convert hero images to WebP/AVIF. Resize images to their container dimensions. Use responsive `srcset` to serve the right size for each viewport【490377143752729†L302-L317】.
3. **Preloading critical resources:** Use `<link rel="preload" as="image">` and set `fetchpriority="high"` on the LCP image to prioritise it【490377143752729†L302-L317】.
4. **Connection reuse:** Host images on the same domain or a subdomain with connection reuse to avoid TLS handshake delays【490377143752729†L302-L317】.
5. **Eliminating render‑blocking CSS and JS:** Inline critical CSS and defer non‑critical styles. Minify CSS/JS and avoid large frameworks.

## Dual Image Strategy
To satisfy Google Discover’s 1200 px image requirement while maintaining fast load times, implement a dual image strategy: serve a large 1200×630 image in Open Graph tags for social/discovery, and serve a smaller, optimised version in the visible content as the LCP element【490377143752729†L327-L333】.

## Monitoring & Tooling
- Use PageSpeed Insights, WebPageTest, and Lighthouse to test pages before and after publication.
- Use Google Search Console to monitor Core Web Vitals reports and track progress across all URLs.
- Create automated alerts (e.g., in GA4 or via RCI) for pages falling below LCP/INP thresholds.

---

**Owner:** WordPress Developer & Technical SEO Specialist  
**Success Metrics:** ≥80 % of URLs pass Core Web Vitals; LCP < 2.5 s and INP < 200 ms on mobile; continual improvements over time.
