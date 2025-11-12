<?php
/**
 * Block Pattern: Why This Matters
 *
 * Callout box for explaining the significance of the article topic.
 *
 * @since 1.0.0
 */

/**
 * Title: Why This Matters
 * Slug: cnp/why-this-matters
 * Categories: text, callout, cnp
 * Description: A highlighted callout block explaining the significance of the topic
 * Inserter: true
 * Keywords: significance, context, importance, impact
 */
?>
<!-- wp:group {"className":"cnp-callout cnp-why-matters","style":{"spacing":{"padding":{"top":"24px","right":"24px","bottom":"24px","left":"24px"},"margin":{"top":"32px","bottom":"32px"}},"border":{"radius":"8px","left":{"color":"var:preset|color|accent","width":"4px"}}},"backgroundColor":"surface"} -->
<div class="wp-block-group cnp-callout cnp-why-matters has-surface-background-color has-background" style="border-radius:8px;border-left-color:var(--wp--preset--color--accent);border-left-width:4px;margin-top:32px;margin-bottom:32px;padding:24px">

    <!-- wp:group {"style":{"spacing":{"blockGap":"12px"}},"layout":{"type":"flex","orientation":"horizontal","justifyContent":"flex-start"}} -->
    <div class="wp-block-group" style="gap:12px">
        <!-- wp:html -->
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--wp--preset--color--accent);flex-shrink:0;margin-top:2px">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="m9 12 2 2 4-4"></path>
        </svg>
        <!-- /wp:html -->

        <!-- wp:heading {"level":2,"style":{"typography":{"fontSize":"1.125rem"},"spacing":{"margin":{"top":"0","bottom":"12px"}}},"textColor":"accent","className":"cnp-why-matters__title"} -->
        <h2 class="wp-block-heading cnp-why-matters__title has-accent-color has-text-color" style="font-size:1.125rem;margin-top:0;margin-bottom:12px">Why This Matters</h2>
        <!-- /wp:heading -->
    </div>
    <!-- /wp:group -->

    <!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}},"typography":{"lineHeight":"1.6"}},"className":"cnp-why-matters__content"} -->
    <p class="cnp-why-matters__content" style="margin-top:0;margin-bottom:0;line-height:1.6">This context is crucial for understanding the broader implications and potential impact on stakeholders, markets, and future developments in this space.</p>
    <!-- /wp:paragraph -->

</div>
<!-- /wp:group -->
