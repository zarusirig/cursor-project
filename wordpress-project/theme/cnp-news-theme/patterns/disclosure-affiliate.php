<?php
/**
 * Block Pattern: Affiliate Disclosure
 * 
 * Clear disclosure for affiliate links with explanation of link types.
 * 
 * @since 1.0.0
 */

/**
 * Title: Affiliate Disclosure
 * Slug: cnp/disclosure-affiliate
 * Categories: text, cnp
 * Description: Affiliate link disclosure block
 * Keywords: affiliate, disclosure, commission, sponsored
 */
?>
<!-- wp:group {"className":"cnp-disclosure cnp-disclosure--affiliate","style":{"spacing":{"padding":{"top":"16px","right":"16px","bottom":"16px","left":"24px"},"margin":{"top":"24px","bottom":"24px"},"blockGap":"0"},"border":{"radius":"8px","left":{"color":"var:preset|color|accent","width":"4px"}}},"backgroundColor":"surface"} -->
<div class="wp-block-group cnp-disclosure cnp-disclosure--affiliate has-surface-background-color has-background" style="border-radius:8px;border-left-color:var(--wp--preset--color--accent);border-left-width:4px;margin-top:24px;margin-bottom:24px;padding:16px 16px 16px 24px">
    
    <!-- wp:paragraph {"style":{"typography":{"fontSize":"0.9375rem","lineHeight":"1.5"}},"textColor":"foreground","className":"cnp-disclosure__text"} -->
    <p class="cnp-disclosure__text has-foreground-color has-text-color" style="font-size:0.9375rem;line-height:1.5">
        <strong>Affiliate Disclosure:</strong> We may earn a commission from purchases made through links on this page. We use <code>rel="sponsored nofollow"</code> on all affiliate links to comply with FTC guidelines. This does not affect our editorial judgment or product recommendations.
    </p>
    <!-- /wp:paragraph -->
    
</div>
<!-- /wp:group -->
