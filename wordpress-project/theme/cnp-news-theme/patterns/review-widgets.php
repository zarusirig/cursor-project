<?php
/**
 * Block Pattern: Review Widgets (Score, Pros/Cons, Specs, Verdict)
 * 
 * Complete review section with rating, advantages/disadvantages, specifications, and verdict.
 * 
 * @since 1.0.0
 */

/**
 * Title: Review Widgets
 * Slug: cnp/review-widgets
 * Categories: widgets, cnp
 * Description: Complete review section with score, pros/cons, specifications, and verdict
 * Keywords: review, score, rating, pros, cons, table, specs
 */
?>
<!-- wp:group {"className":"cnp-review-widgets","style":{"spacing":{"blockGap":"32px","margin":{"top":"32px","bottom":"32px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group cnp-review-widgets" style="gap:32px;margin-top:32px;margin-bottom:32px">
    
    <!-- Score Ribbon -->
    <!-- wp:group {"className":"cnp-score-section","style":{"spacing":{"blockGap":"0"}}} -->
    <div class="wp-block-group cnp-score-section">
        <!-- wp:group {"className":"cnp-score cnp-score--good","style":{"spacing":{"padding":{"top":"12px","right":"16px","bottom":"12px","left":"16px"},"blockGap":"8"},"border":{"radius":"8px"}},"backgroundColor":"surface","layout":{"type":"flex"}} -->
        <div class="wp-block-group cnp-score cnp-score--good has-surface-background-color has-background" style="border-radius:8px;padding:12px 16px;gap:8px">
            <!-- wp:paragraph {"style":{"typography":{"fontWeight":"600","fontSize":"0.875rem"}},"textColor":"foreground","className":"cnp-score__label"} -->
            <p class="cnp-score__label has-foreground-color has-text-color" style="font-weight:600;font-size:0.875rem;margin:0">Score</p>
            <!-- /wp:paragraph -->
            
            <!-- wp:paragraph {"style":{"typography":{"fontWeight":"700","fontSize":"1.75rem"}},"textColor":"foreground","className":"cnp-score__value"} -->
            <p class="cnp-score__value has-foreground-color has-text-color" style="font-weight:700;font-size:1.75rem;margin:0">8.7/10</p>
            <!-- /wp:paragraph -->
            
            <!-- wp:paragraph {"style":{"typography":{"fontWeight":"500"}},"textColor":"accent","className":"cnp-score__rating"} -->
            <p class="cnp-score__rating has-accent-color has-text-color" style="font-weight:500;margin:0">Excellent</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:group -->
    </div>
    <!-- /wp:group -->
    
    <!-- Pros and Cons -->
    <!-- wp:columns {"style":{"spacing":{"blockGap":"24px"}}} -->
    <div class="wp-block-columns" style="gap:24px">
        <!-- Pros Column -->
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.125rem"}},"textColor":"accent","className":"cnp-pros-title"} -->
            <h3 class="wp-block-heading cnp-pros-title has-accent-color has-text-color" style="font-size:1.125rem">Pros</h3>
            <!-- /wp:heading -->
            
            <!-- wp:list {"style":{"typography":{"fontSize":"0.95rem"}}} -->
            <ul style="font-size:0.95rem">
                <!-- wp:list-item -->
                <li>Fast to deploy with minimal configuration</li>
                <!-- /wp:list-item -->
                <!-- wp:list-item -->
                <li>Excellent user experience and interface design</li>
                <!-- /wp:list-item -->
                <!-- wp:list-item -->
                <li>Comprehensive REST API for integrations</li>
                <!-- /wp:list-item -->
                <!-- wp:list-item -->
                <li>Strong community support and documentation</li>
                <!-- /wp:list-item -->
            </ul>
            <!-- /wp:list -->
        </div>
        <!-- /wp:column -->
        
        <!-- Cons Column -->
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.125rem"}},"textColor":"danger","className":"cnp-cons-title"} -->
            <h3 class="wp-block-heading cnp-cons-title has-danger-color has-text-color" style="font-size:1.125rem">Cons</h3>
            <!-- /wp:heading -->
            
            <!-- wp:list {"style":{"typography":{"fontSize":"0.95rem"}}} -->
            <ul style="font-size:0.95rem">
                <!-- wp:list-item -->
                <li>Free tier limited to 3 active projects</li>
                <!-- /wp:list-item -->
                <!-- wp:list-item -->
                <li>Documentation could be more comprehensive</li>
                <!-- /wp:list-item -->
                <!-- wp:list-item -->
                <li>Support response times variable</li>
                <!-- /wp:list-item -->
                <!-- wp:list-item -->
                <li>Enterprise pricing can be expensive</li>
                <!-- /wp:list-item -->
            </ul>
            <!-- /wp:list -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
    
    <!-- Specifications Table -->
    <!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.125rem"}},"textColor":"foreground","className":"cnp-specs-title"} -->
    <h3 class="wp-block-heading cnp-specs-title has-foreground-color has-text-color" style="font-size:1.125rem">Specifications</h3>
    <!-- /wp:heading -->
    
    <!-- wp:table {"className":"cnp-spec-table"} -->
    <figure class="wp-block-table cnp-spec-table">
        <table>
            <thead>
                <tr>
                    <th>Feature</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Pricing</strong></td>
                    <td>Free tier / $49–299/mo</td>
                </tr>
                <tr>
                    <td><strong>API Available</strong></td>
                    <td>Yes, with rate limiting</td>
                </tr>
                <tr>
                    <td><strong>Support</strong></td>
                    <td>Community + 24/7 on paid plans</td>
                </tr>
                <tr>
                    <td><strong>Best For</strong></td>
                    <td>Small to medium teams</td>
                </tr>
                <tr>
                    <td><strong>Learning Curve</strong></td>
                    <td>Low to moderate</td>
                </tr>
            </tbody>
        </table>
    </figure>
    <!-- /wp:table -->
    
    <!-- Verdict Section -->
    <!-- wp:group {"className":"cnp-verdict","style":{"spacing":{"padding":{"top":"24px","right":"24px","bottom":"24px","left":"24px"},"margin":{"top":"32px","bottom":"0"},"blockGap":"16px"},"border":{"top":{"color":"var:preset|color|accent","width":"3px"},"radius":"8px"}},"backgroundColor":"surface","layout":{"type":"constrained"}} -->
    <div class="wp-block-group cnp-verdict has-surface-background-color has-background" style="border-radius:8px;border-top-color:var(--wp--preset--color--accent);border-top-width:3px;margin-top:32px;margin-bottom:0;padding:24px;gap:16px">
        <!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.125rem"}},"textColor":"foreground","className":"cnp-verdict__title"} -->
        <h3 class="wp-block-heading cnp-verdict__title has-foreground-color has-text-color" style="font-size:1.125rem">Our Verdict</h3>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph {"style":{"typography":{"fontSize":"1.0625rem"}},"className":"cnp-verdict__text"} -->
        <p class="cnp-verdict__text" style="font-size:1.0625rem">Great choice for small to medium-sized teams and startups just getting started with their automation needs. The free tier offers good value for basic use cases, though you'll likely need a paid plan as you scale.</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"flex-start"}} -->
        <div class="wp-block-buttons">
            <!-- wp:button {"backgroundColor":"primary","textColor":"background","style":{"border":{"radius":"6px"},"spacing":{"padding":{"left":"24px","right":"24px","top":"12px","bottom":"12px"}}}} -->
            <div class="wp-block-button">
                <a class="wp-block-button__link has-background-color has-primary-background-color has-text-color wp-element-button" style="border-radius:6px;padding:12px 24px" href="#" rel="sponsored nofollow">Check Current Pricing</a>
            </div>
            <!-- /wp:button -->
        </div>
        <!-- /wp:buttons -->
    </div>
    <!-- /wp:group -->
    
</div>
<!-- /wp:group -->
