<?php
/**
 * Block Pattern: Key Takeaways
 * 
 * Callout box for article key takeaways with icon and accent color.
 * 
 * @since 1.0.0
 */

/**
 * Title: Key Takeaways
 * Slug: cnp/key-takeaways
 * Categories: text, callout, cnp
 * Description: A highlighted callout block for article key takeaways
 * Inserter: true
 * Keywords: takeaways, highlights, summary, important
 */
?>
<!-- wp:group {"className":"cnp-callout cnp-takeaways","style":{"spacing":{"padding":{"top":"24px","right":"24px","bottom":"24px","left":"24px"},"margin":{"top":"32px","bottom":"32px"}},"border":{"radius":"8px","left":{"color":"var:preset|color|primary","width":"4px"}}},"backgroundColor":"surface"} -->
<div class="wp-block-group cnp-callout cnp-takeaways has-surface-background-color has-background" style="border-radius:8px;border-left-color:var(--wp--preset--color--primary);border-left-width:4px;margin-top:32px;margin-bottom:32px;padding:24px">
    
    <!-- wp:group {"style":{"spacing":{"blockGap":"12px"}},"layout":{"type":"flex","orientation":"horizontal","justifyContent":"flex-start"}} -->
    <div class="wp-block-group" style="gap:12px">
        <!-- wp:html -->
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--wp--preset--color--primary);flex-shrink:0;margin-top:2px">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <!-- /wp:html -->
        
        <!-- wp:heading {"level":2,"style":{"typography":{"fontSize":"1.125rem"},"spacing":{"margin":{"top":"0","bottom":"12px"}}},"textColor":"primary","className":"cnp-takeaways__title"} -->
        <h2 class="wp-block-heading cnp-takeaways__title has-primary-color has-text-color" style="font-size:1.125rem;margin-top:0;margin-bottom:12px">Key Takeaways</h2>
        <!-- /wp:heading -->
    </div>
    <!-- /wp:group -->
    
    <!-- wp:list {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"className":"cnp-takeaways__list"} -->
    <ul class="cnp-takeaways__list" style="margin-top:0;margin-bottom:0">
        <!-- wp:list-item -->
        <li>Edit this section with your 3–5 key insights</li>
        <!-- /wp:list-item -->
        <!-- wp:list-item -->
        <li>Focus on outcomes, implications, and timeline</li>
        <!-- /wp:list-item -->
        <!-- wp:list-item -->
        <li>Link to sources or internal explainers when helpful</li>
        <!-- /wp:list-item -->
    </ul>
    <!-- /wp:list -->
    
</div>
<!-- /wp:group -->
