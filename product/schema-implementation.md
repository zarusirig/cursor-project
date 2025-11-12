# Schema Implementation Guide

Structured data helps search engines understand and feature our content. This guide outlines how to implement JSON‑LD for articles, persons, and other page types on cnpnews.net/en.

## General Principles
- Use JSON‑LD format in a `<script type="application/ld+json">` tag in the page `<head>`.
- Do not include schema in comments or non‑standard formats.
- Only update `dateModified` when the content has undergone a meaningful change【490377143752729†L137-L141】.

## NewsArticle / Article Schema
Each news or evergreen article should embed `NewsArticle` (preferred) or `Article` schema. Fields include:

- `@context`: `https://schema.org`
- `@type`: `NewsArticle`
- `headline`: Article title (max 110 characters)
- `datePublished`: ISO 8601 timestamp of initial publication
- `dateModified`: ISO 8601 timestamp of last substantive update
- `author`: A `Person` object with `name` and `url` fields
- `publisher`: An `Organization` object with `name` and `logo` (image URL)
- `image`: An array of image URLs (optimally sized; preload the LCP image)【490377143752729†L302-L317】
- `mainEntityOfPage`: A `WebPage` object with `@id` equal to the canonical URL

Example:

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "NewsArticle",
  "headline": "{post_title}",
  "datePublished": "{post_date_gmt}",
  "dateModified": "{post_modified_gmt}",
  "author": {
    "@type": "Person",
    "name": "{author_name}",
    "url": "{author_url}"
  },
  "publisher": {
    "@type": "Organization",
    "name": "{site_name}",
    "logo": {
      "@type": "ImageObject",
      "url": "{logo_url}"
    }
  },
  "image": ["{featured_image_url}"],
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "{post_url}"
  }
}
</script>
```

## Person Schema
Author pages must include `Person` schema to reinforce expertise and identity verification. Key fields:

- `@context`: `https://schema.org`
- `@type`: `Person`
- `name`: Full name of the author
- `url`: URL of the author’s profile page on cnpnews.net
- `jobTitle`: Role or title (e.g., “Senior Reporter”) if appropriate
- `sameAs`: Array of external profile URLs (LinkedIn, professional bios) to establish notability

## Review / Product Schema
For reviews and product comparisons, implement `Review` or `Product` schema in addition to `Article` schema. Include rating values, review body, and product identifiers (e.g., SKU, brand). Ensure affiliate links use `rel="sponsored"` and that the review text substantiates the rating【490377143752729†L40-L56】.

## FAQ and HowTo Schema
Use `FAQPage` for articles with frequently asked questions and `HowTo` for step‑by‑step guides. Each question or step must be present in the visible content.

## LiveBlogPosting Schema
For live blogs, use `LiveBlogPosting` schema, including `coverageStartTime` and `coverageEndTime`, and list each update as a `BlogPosting` within the `liveBlogUpdate` array.

## Validation and Testing
- Use Google’s Rich Results Test and Schema.org validator to test markup.
- Fix any warnings or errors before publishing.
- Regularly monitor Google Search Console’s Enhancements report for structured data issues.

---

**Owner:** Technical SEO Specialist  
**Success Metrics:** 100 % of eligible pages include valid structured data; zero structured data errors in GSC.
