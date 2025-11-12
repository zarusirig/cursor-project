<?php
/**
 * Block Pattern: Hero Feature (1L + 4M)
 * 
 * Displays one large featured post followed by four medium cards.
 * Perfect for homepage hero section.
 * 
 * @since 1.0.0
 */

/**
 * Title: Hero Feature (1L + 4M)
 * Slug: cnp/hero-feature
 * Categories: featured, cnp
 * Block Types: core/post-template
 * Description: Hero section with one large featured post and four medium cards below
 */
?>
<!-- wp:group {"className":"cnp-hero","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group cnp-hero">
    
    <!-- wp:query {"queryId":1,"query":{"perPage":1,"postType":"post","order":"desc","orderBy":"date"},"displayLayout":{"type":"flex"}} -->
    <div class="wp-block-query">
        <!-- wp:post-template {"layout":{"type":"flex","orientation":"vertical"}} -->
            <!-- wp:group {"className":"cnp-card cnp-card--large","style":{"border":{"radius":"8px"},"spacing":{"padding":"0"}},"backgroundColor":"surface"} -->
            <article class="wp-block-group cnp-card cnp-card--large has-surface-background-color has-background" style="border-radius:8px;padding:0">
                <!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9","style":{"border":{"radius":"8px 8px 0 0"}}} /-->
                
                <!-- wp:group {"style":{"spacing":{"padding":{"top":"24px","right":"24px","bottom":"24px","left":"24px"}}},"className":"cnp-card__content"} -->
                <div class="wp-block-group cnp-card__content" style="padding:24px">
                    <!-- wp:post-terms {"term":"category","className":"cnp-card__kicker","fontSize":"xs"} /-->
                    
                    <!-- wp:post-title {"level":3,"isLink":true,"className":"cnp-card__title","fontSize":"2xl","style":{"spacing":{"margin":{"bottom":"12px"}}}} /-->
                    
                    <!-- wp:post-excerpt {"moreText":"Read article","excerptLength":24,"className":"cnp-card__excerpt","fontSize":"base","style":{"spacing":{"margin":{"bottom":"16px"}}}} /-->
                    
                    <!-- wp:group {"style":{"spacing":{"gap":"8px"}},"layout":{"type":"flex","flexWrap":"wrap"},"className":"cnp-card__meta"} -->
                    <div class="wp-block-group cnp-card__meta" style="gap:8px">
                        <!-- wp:post-author {"showAvatar":false,"textAlign":"left","fontSize":"sm"} /-->
                        <!-- wp:group {"style":{"spacing":{"margin":{"left":"8px"}}}} --><div class="wp-block-group" style="margin-left:8px">• </div><!-- /wp:group -->
                        <!-- wp:post-date {"textAlign":"left","fontSize":"sm"} /-->
                    </div>
                    <!-- /wp:group -->
                </div>
                <!-- /wp:group -->
            </article>
            <!-- /wp:group -->
        <!-- /wp:post-template -->
    </div>
    <!-- /wp:query -->
    
    <!-- wp:query {"queryId":2,"query":{"perPage":4,"offset":1,"postType":"post","order":"desc","orderBy":"date"},"displayLayout":{"type":"flex","columns":4},"className":"cnp-hero__rail"} -->
    <div class="wp-block-query cnp-hero__rail">
        <!-- wp:post-template {"layout":{"type":"grid","columnCount":4}} -->
            <!-- wp:group {"className":"cnp-card cnp-card--medium","style":{"border":{"radius":"8px"},"spacing":{"padding":"0"}},"backgroundColor":"surface"} -->
            <article class="wp-block-group cnp-card cnp-card--medium has-surface-background-color has-background" style="border-radius:8px;padding:0">
                <!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3","style":{"border":{"radius":"8px 8px 0 0"}}} /-->
                
                <!-- wp:group {"style":{"spacing":{"padding":{"top":"16px","right":"16px","bottom":"16px","left":"16px"}}},"className":"cnp-card__content"} -->
                <div class="wp-block-group cnp-card__content" style="padding:16px">
                    <!-- wp:post-terms {"term":"category","className":"cnp-card__kicker","fontSize":"xs"} /-->
                    
                    <!-- wp:post-title {"level":4,"isLink":true,"className":"cnp-card__title","fontSize":"lg","style":{"spacing":{"margin":{"bottom":"8px"}}}} /-->
                    
                    <!-- wp:group {"style":{"spacing":{"gap":"8px"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"},"className":"cnp-card__meta"} -->
                    <div class="wp-block-group cnp-card__meta" style="gap:8px">
                        <!-- wp:post-date {"displayType":"relative","fontSize":"xs"} /-->
                    </div>
                    <!-- /wp:group -->
                </div>
                <!-- /wp:group -->
            </article>
            <!-- /wp:group -->
        <!-- /wp:post-template -->
    </div>
    <!-- /wp:query -->
    
</div>
<!-- /wp:group -->
