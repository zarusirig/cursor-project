# Page Templates

This guide defines the core page layouts used on cnpnews.net/en. Each template is built with the Gutenberg block editor and respects Core Web Vitals and E‑E‑A‑T requirements.

## Article Template
The standard article template is used for news stories, analyses, and case studies.

### Structure
1. **Hero Section:** Headline (`<h1>`), author byline, publish date, and featured image. The featured image is served in modern formats (WebP/AVIF) and is correctly sized and preloaded for fast LCP【490377143752729†L302-L317】.
2. **Summary/Bullet Block:** A short “What’s new / Why it matters” summary for busy readers.
3. **Body:** Rich text content divided into sections with descriptive subheadings. Use relevant blocks (paragraph, list, quote) and embed videos, charts, and images as needed. Avoid heavy embeds that delay interaction; load third‑party scripts only on demand【490377143752729†L248-L259】.
4. **Value‑Add Section:** For aggregated stories, include context such as timelines, analysis, quotes, or proprietary data to meet our value‑add requirement【490377143752729†L62-L74】.
5. **Further Reading/Hub Links:** Mid‑article callouts that link back to the Pillar Hub and to related Cluster Pages.
6. **Related Articles:** At the end of the article, include a “Related in this hub” block to encourage recirculation.
7. **Disclosure & CTA:** For reviews or affiliate articles, include an affiliate disclosure. Add calls to action for newsletter signups or product demos.
8. **Structured Data:** Include NewsArticle JSON‑LD with accurate `datePublished` and `dateModified` (only update on meaningful changes)【490377143752729†L137-L141】.

## Explainer Template
Explainers are evergreen pages that break down complex topics for learners and researchers.

### Structure
1. **Definition and Overview:** Start with a clear definition of the topic.
2. **Sectioned Body:** Divide the explainer into logical sections (history, key concepts, examples) with descriptive headings.
3. **Visual Aids:** Use diagrams, charts, or infographics to simplify complex ideas.
4. **FAQ Section:** Include frequently asked questions to capture additional search queries.
5. **Hub & Spoke Links:** Link back to the Pillar Hub and to related articles and reviews.
6. **Schema:** Use `FAQPage` or `HowTo` structured data where appropriate, alongside the base `Article` schema.

## Review/Comparison Template
This template powers our affiliate‑focused content and product comparisons.

### Structure
1. **Quick Verdict:** A summary table outlining pros, cons, and our rating.
2. **Hands‑On Analysis:** Detailed sections covering setup, features, pricing, and performance. Provide evidence of first‑hand experience (screenshots, benchmarks).
3. **Use Cases:** Explain who would benefit most from the tool and why.
4. **Affiliate Disclosure:** Clearly disclose affiliate relationships and use `rel="sponsored"` on outbound purchase links【490377143752729†L40-L56】.
5. **Alternatives & Comparisons:** Link to other products in the same category to encourage exploration.
6. **Structured Data:** Implement `Review` or `Product` schema in addition to `Article` and ensure the rating value is supported by the text.

## Live/Tracker Template
Live blogs and trackers provide rolling coverage of evolving stories.

### Structure
1. **Sticky Summary:** At the top, summarise the current state of the event.
2. **Timestamped Updates:** Add updates in reverse chronological order with timestamps and anchor links.
3. **Pinned Resource Section:** Highlight key resources (links, documents, visuals) relevant to the story.
4. **Hub Link:** Include a link back to the Pillar Hub or a master timeline page.
5. **CTA:** Encourage readers to subscribe for updates.
6. **Structured Data:** Use `LiveBlogPosting` schema when appropriate.

## Common Template Guidelines
- **Performance First:** Use block‑based components only. Avoid third‑party page builders to keep the DOM lean【490377143752729†L82-L94】.
- **Preload Critical Resources:** Preload the hero image and fonts with `fetchpriority="high"`【490377143752729†L302-L317】.
- **Image Strategy:** Use dual image sizes: a large 1200+ pixel version for Open Graph and Discover, and a smaller optimised version as the LCP element【490377143752729†L327-L333】.
- **Pagination:** For multi‑page posts, ensure each page has a self‑referencing canonical tag【490377143752729†L194-L203】.
- **Accessibility:** All templates must include alt text, logical heading hierarchy, and accessible colour contrasts.

---

**Owner:** WordPress Developer & Technical SEO Specialist  
**Success Metrics:** Pages pass Core Web Vitals; templates reused consistently; zero page builder usage; accurate schema markup.
