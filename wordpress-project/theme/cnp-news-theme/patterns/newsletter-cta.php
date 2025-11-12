<?php
/**
 * Block Pattern: Newsletter CTA
 * 
 * Call-to-action for newsletter signup. Can be used inline or at page end.
 * 
 * @since 1.0.0
 */

/**
 * Title: Newsletter CTA
 * Slug: cnp/newsletter-cta
 * Categories: call-to-action, cnp
 * Description: Newsletter signup call-to-action block
 * Keywords: newsletter, signup, subscribe, email
 */
?>
<!-- wp:group {"className":"cnp-cta cnp-newsletter","style":{"spacing":{"padding":{"top":"48px","right":"32px","bottom":"48px","left":"32px"},"margin":{"top":"48px","bottom":"0"}},"border":{"radius":"8px"}},"backgroundColor":"surface"} -->
<div class="wp-block-group cnp-cta cnp-newsletter has-surface-background-color has-background" style="border-radius:8px;margin-top:48px;margin-bottom:0;padding:48px 32px">
    
    <!-- wp:heading {"level":2,"textAlign":"center","style":{"spacing":{"margin":{"bottom":"12px"}}},"className":"cnp-newsletter__title"} -->
    <h2 class="wp-block-heading has-text-align-center cnp-newsletter__title" style="margin-bottom:12px">Get the Briefing</h2>
    <!-- /wp:heading -->
    
    <!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"bottom":"24px"}},"typography":{"fontSize":"1.0625rem"}},"textColor":"muted","className":"cnp-newsletter__description"} -->
    <p class="has-text-align-center cnp-newsletter__description has-muted-color has-text-color" style="margin-bottom:24px;font-size:1.0625rem">Weekly clarity on tech &amp; business. No noise. No spam.</p>
    <!-- /wp:paragraph -->
    
    <!-- wp:group {"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"},"style":{"spacing":{"blockGap":"12px"}},"className":"cnp-newsletter__form"} -->
    <div class="wp-block-group cnp-newsletter__form" style="gap:12px">
        <!-- wp:html -->
        <form class="cnp-newsletter-form" method="POST" action="">
            <div class="cnp-newsletter-form__row" style="display:flex;gap:8px;flex-direction:row">
                <input 
                    type="email" 
                    name="email" 
                    placeholder="Enter your email" 
                    required
                    aria-label="Email address"
                    style="flex:1;padding:12px 16px;border:1px solid var(--wp--preset--color--border);border-radius:6px;font-size:1rem;font-family:inherit"
                >
                <button 
                    type="submit"
                    style="padding:12px 32px;background:var(--wp--preset--color--primary);color:white;border:none;border-radius:6px;font-size:1rem;font-weight:500;cursor:pointer;transition:background 0.2s ease;white-space:nowrap"
                    onmouseover="this.style.opacity='0.9'"
                    onmouseout="this.style.opacity='1'"
                >
                    Subscribe
                </button>
            </div>
            <p style="font-size:0.875rem;color:var(--wp--preset--color--muted);margin:12px 0 0 0;text-align:center">
                No spam. Unsubscribe anytime. Read our <a href="/privacy-policy/" style="color:var(--wp--preset--color--primary)">Privacy Policy</a>.
            </p>
        </form>
        <!-- /wp:html -->
    </div>
    <!-- /wp:group -->
    
</div>
<!-- /wp:group -->
