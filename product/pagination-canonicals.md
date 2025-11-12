# Pagination & Canonical Tags

Proper canonicalization ensures search engines index the correct URLs and prevent duplicate content issues. This guide outlines how to handle pagination and canonical tags in WordPress archives.

## Self‑Referencing Canonicals for Paginated Archives
For paginated category, tag, or archive pages (e.g., `/category/page/2/`), each page **must** include a `<link rel="canonical">` pointing to its own URL【490377143752729†L194-L203】. Do **not** canonicalize all pages back to page 1. This prevents older articles from being excluded from the index and replaces the deprecated `rel=prev/rel=next` model【490377143752729†L194-L203】.

Example snippet for `archive.php` or `category.php`:

```php
<?php
if ( is_paged() ) {
  echo '<link rel="canonical" href="' . esc_url( get_pagenum_link( get_query_var( 'paged' ) ) ) . '" />';
} else {
  echo '<link rel="canonical" href="' . esc_url( get_category_link( get_queried_object_id() ) ) . '" />';
}
?>
```

## Absolute Canonical URLs
Ensure that canonical tags output the full absolute URL (including protocol and domain), not a relative path【490377143752729†L186-L190】. SEO plugins like Rank Math and Yoast typically handle this, but verification is required.

## Suppressing Thin Archives
Low‑value tag archives or auto‑generated taxonomies should be set to `noindex` to avoid wasting crawl budget and diluting authority【490377143752729†L205-L213】.

## Parameterised URLs and Tracking Codes
Remove URL parameters used for analytics or tracking from canonical tags. Canonical URLs should represent the clean version of the page without UTM parameters.

## Paginated Posts and Multi‑Page Articles
If an article spans multiple pages, each page should self‑canonicalize. Additionally, include rel="next" and rel="prev" for internal navigation between pages. Use caution: avoid splitting short articles solely for ad revenue or page views.

---

**Owner:** Technical SEO Specialist  
**Success Metrics:** Canonical tags validated across all archives; zero duplicate content warnings in GSC.
