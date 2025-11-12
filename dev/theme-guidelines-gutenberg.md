# Theme Guidelines: Gutenberg

To achieve high Core Web Vitals and an accessible design, cnpnews.net/en uses a lightweight, block‑based theme. These guidelines describe how to develop and customise the theme.

## Core Principles
- **Block Editor Only:** Use the native Gutenberg editor for all page creation. Avoid third‑party page builders, which bloat the DOM and harm Interaction to Next Paint (INP) scores【490377143752729†L82-L94】.
- **Minimal DOM:** Structure templates with a minimal number of wrapper elements. Each unnecessary `div` adds complexity and slows rendering【490377143752729†L248-L259】.
- **Accessibility by Design:** Use semantic HTML tags (`<main>`, `<nav>`, `<aside>`) and maintain a logical heading hierarchy. Provide descriptive alt text for all images.
- **Responsiveness:** Use fluid layouts and CSS grid/flexbox. Test pages on a range of devices.

## Theme Components
### Header
- Include site logo, navigation menu, and a call‑to‑action (e.g., newsletter signup).
- Use a sticky header only if it does not interfere with LCP measurement.

### Footer
- Provide links to About, Contact, Privacy Policy, Terms of Use, Ethics & Disclosure, and sitemap.
- Include social media icons with accessible labels.

### Typography
- Set base font size for readability (e.g., 16 px). Use fluid type scales for headings. Avoid custom web fonts that slow down rendering; preload fonts if used.

### Colours & Branding
- Align colours with the brand identity: a professional palette that conveys trust and clarity. Ensure sufficient contrast ratios for WCAG compliance.

### Blocks & Patterns
- Register custom block patterns for recurring elements such as callout boxes, value‑add sections, and comparison tables.
- Provide a reusable pattern for Hub pages that includes a summary, category filters, and cluster links.

## Enqueueing Assets
- Use `wp_enqueue_script()` and `wp_enqueue_style()` to load only necessary scripts and styles.
- Defer non‑critical JavaScript and inline critical CSS. Split long tasks to improve processing time【490377143752729†L248-L259】.

## Performance Considerations
- **Image Handling:** Serve images in WebP or AVIF formats. Use responsive `srcset` attributes and avoid oversized images. Preload the LCP image with `fetchpriority="high"`【490377143752729†L302-L317】.
- **Lazy Loading:** Lazy‑load off‑screen images and videos.
- **Third‑Party Scripts:** Load analytics and social widgets asynchronously. Only execute heavy scripts after user interaction.

## Internationalisation & Localisation
- Prepare the theme for translation using `__()` and `_e()` functions. The primary language for `/en/` is English, but the theme should support future locales.

---

**Owner:** WordPress Developer  
**Success Metrics:** Zero page builder usage; DOM remains lean; pages pass INP and LCP thresholds; consistent branding across templates.
