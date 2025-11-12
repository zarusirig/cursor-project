<?php
/**
 * Block Pattern: Sponsored Content Disclosure
 * 
 * Clear disclosure for sponsored content partnerships.
 * 
 * @since 1.0.0
 */

/**
 * Title: Sponsored Content Disclosure
 * Slug: cnp/disclosure-sponsored
 * Categories: text, cnp
 * Description: Sponsored content disclosure block
 * Keywords: sponsored, partnership, disclosure, content
 */
?>
<!-- wp:group {"className":"cnp-disclosure cnp-disclosure--sponsored","style":{"spacing":{"padding":{"top":"16px","right":"16px","bottom":"16px","left":"24px"},"margin":{"top":"24px","bottom":"24px"},"blockGap":"0"},"border":{"radius":"8px","left":{"color":"var:preset|color|warn","width":"4px"}}},"backgroundColor":"surface"} -->
<div class="wp-block-group cnp-disclosure cnp-disclosure--sponsored has-surface-background-color has-background" style="border-radius:8px;border-left-color:var(--wp--preset--color--warn);border-left-width:4px;margin-top:24px;margin-bottom:24px;padding:16px 16px 16px 24px">
    
    <!-- wp:paragraph {"style":{"typography":{"fontSize":"0.9375rem","lineHeight":"1.5"}},"textColor":"foreground","className":"cnp-disclosure__text"} -->
    <p class="cnp-disclosure__text has-foreground-color has-text-color" style="font-size:0.9375rem;line-height:1.5">
        <strong>Sponsored Content:</strong> This article was produced in partnership with a sponsor. Our editorial standards and commitment to accuracy apply to all sponsored content. All sponsored links are marked with <code>rel="sponsored nofollow"</code> per FTC guidelines.
    </p>
    <!-- /wp:paragraph -->
    
</div>
<!-- /wp:group -->
