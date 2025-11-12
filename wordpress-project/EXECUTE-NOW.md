# 🚀 CNP News - EXECUTE NOW Guide

**Your theme is LIVE! Let's make it amazing in the next hour.**

---

## ✅ COMPLETED
- Docker running
- WordPress installed
- CNP News theme active
- Basic structure visible

---

## 🎯 PHASE 1: Content Setup (15 minutes)

### Step 1: Create Categories (via WordPress Admin)

1. Go to: **http://localhost/wp-admin**
2. Login with your credentials
3. Navigate to: **Posts → Categories**
4. Create these categories:

| Name | Slug | Description |
|------|------|-------------|
| Strategy & Analysis | strategy-analysis | Business strategy insights and market analysis |
| Artificial Intelligence | artificial-intelligence | AI news, developments, and analysis |
| Startups & Capital | startups-capital | Startup funding and venture capital news |
| Fintech & Markets | fintech-markets | Financial technology and market trends |
| Policy & Regulation | policy-regulation | Tech policy and regulatory developments |
| Reviews & Tools | reviews-tools | Product reviews and tool comparisons |

### Step 2: Create Sample Posts (via WordPress Admin)

1. Go to: **Posts → Add New**
2. Create 10-15 posts with:
   - **Compelling headlines** (examples below)
   - **Category assignment**
   - **Featured image** (use https://source.unsplash.com/1200x675/?technology,business)
   - **Excerpt** (1-2 sentences)

**Sample Headlines:**

**Strategy & Analysis:**
- "How Enterprise AI Adoption Will Reshape Tech Spending in 2025"
- "The New Playbook: Why Strategic Partnerships Trump M&A"
- "Market Analysis: Cloud Infrastructure Spending Hits $200B Milestone"

**Artificial Intelligence:**
- "OpenAI Unveils GPT-5: What Enterprises Need to Know"
- "Inside the AI Regulation Debate: EU vs US Approaches"
- "Machine Learning at Scale: Lessons from Tech Giants"

**Startups & Capital:**
- "Record VC Funding: Climate Tech Attracts $45B in Q4"
- "Unicorn Watch: 15 Startups to Watch in 2025"
- "The Seed Stage Revival: Why Early-Stage is Hot Again"

**Fintech & Markets:**
- "Digital Banking Revolution: Traditional Banks Fight Back"
- "Cryptocurrency Regulation: The Path Forward"
- "Payment Processing Wars: Stripe vs. Square in 2025"

**Policy & Regulation:**
- "Breaking: EU Passes Comprehensive AI Act"
- "Section 230 Reform: What It Means for Platforms"
- "Data Privacy 2025: GDPR's Global Impact"

**Reviews & Tools:**
- "Review: The Best Project Management Tools for 2025"
- "Notion vs. Obsidian: The Ultimate Comparison"
- "Testing the New Microsoft 365 Copilot Features"

### Step 3: Set Featured Images

For each post, use Unsplash URLs:
```
https://source.unsplash.com/1200x675/?ai,technology
https://source.unsplash.com/1200x675/?business,strategy
https://source.unsplash.com/1200x675/?startup,innovation
https://source.unsplash.com/1200x675/?fintech,finance
https://source.unsplash.com/1200x675/?data,security
https://source.unsplash.com/1200x675/?code,developer
```

Or use these placeholder services:
```
https://picsum.photos/1200/675
https://placehold.co/1200x675/1D4ED8/FFFFFF/png
```

---

## 🎨 PHASE 2: Homepage Hero Section (20 minutes)

### Step 1: Update home.html

**File:** `/Users/surajgiri/Downloads/docs/wordpress-project/theme/cnp-news-theme/templates/home.html`

Replace current content with:

```html
<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} -->
<main class="wp-block-group">
  
  <!-- Hero Section: 1 Large + 4 Medium -->
  <!-- wp:group {"className":"hero-rail","layout":{"type":"default"}} -->
  <div class="wp-block-group hero-rail">
    
    <!-- wp:query {"queryId":1,"query":{"perPage":1,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"only","inherit":false}} -->
    <div class="wp-block-query">
      <!-- wp:post-template {"className":"feature-large"} -->
        <!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9"} /-->
        <!-- wp:post-terms {"term":"category","className":"category-badge"} /-->
        <!-- wp:post-title {"isLink":true,"className":"h2"} /-->
        <!-- wp:post-excerpt {"moreText":"Read more","excerptLength":30} /-->
        <!-- wp:group {"layout":{"type":"flex"}} -->
        <div class="wp-block-group">
          <!-- wp:post-author {"showAvatar":false} /-->
          <!-- wp:post-date /-->
        </div>
        <!-- /wp:group -->
      <!-- /wp:post-template -->
    </div>
    <!-- /wp:query -->
    
    <!-- wp:query {"queryId":2,"query":{"perPage":4,"pages":0,"offset":1,"postType":"post","order":"desc","orderBy":"date"}} -->
    <div class="wp-block-query">
      <!-- wp:post-template {"className":"feature-grid"} -->
        <!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3"} /-->
        <!-- wp:post-terms {"term":"category","className":"category-badge-sm"} /-->
        <!-- wp:post-title {"isLink":true,"level":3,"className":"h4"} /-->
        <!-- wp:post-date {"className":"text-sm"} /-->
      <!-- /wp:post-template -->
    </div>
    <!-- /wp:query -->
    
  </div>
  <!-- /wp:group -->
  
  <!-- Latest Articles Section -->
  <!-- wp:heading {"className":"section-title"} -->
  <h2 class="wp-block-heading section-title">Latest News</h2>
  <!-- /wp:heading -->
  
  <!-- wp:query {"queryId":3,"query":{"perPage":9,"pages":0,"offset":5,"postType":"post","order":"desc","orderBy":"date"}} -->
  <div class="wp-block-query">
    <!-- wp:post-template {"className":"articles-grid"} -->
      <!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3"} /-->
      <!-- wp:post-terms {"term":"category","className":"category-badge-sm"} /-->
      <!-- wp:post-title {"isLink":true,"level":3} /-->
      <!-- wp:post-excerpt {"excerptLength":20} /-->
      <!-- wp:group {"layout":{"type":"flex"}} -->
      <div class="wp-block-group">
        <!-- wp:post-author {"showAvatar":false} /-->
        <!-- wp:post-date /-->
      </div>
      <!-- /wp:group -->
    <!-- /wp:post-template -->
    
    <!-- wp:query-pagination -->
    <div class="wp-block-query-pagination">
      <!-- wp:query-pagination-previous /-->
      <!-- wp:query-pagination-numbers /-->
      <!-- wp:query-pagination-next /-->
    </div>
    <!-- /wp:query-pagination -->
  </div>
  <!-- /wp:query -->
  
  <!-- Newsletter CTA -->
  <!-- wp:pattern {"slug":"cnp/newsletter-cta"} /-->
  
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->
```

### Step 2: Add Hero Styles to style.css

**File:** `/Users/surajgiri/Downloads/docs/wordpress-project/theme/cnp-news-theme/style.css`

Add at the bottom:

```css
/* ===================================
   HOMEPAGE HERO SECTION
   =================================== */

.hero-rail {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
  margin-bottom: 3rem;
  padding: 2rem 0;
}

.feature-large {
  grid-column: 1;
}

.feature-large .wp-block-post-featured-image img {
  border-radius: 12px;
  object-fit: cover;
  width: 100%;
  transition: transform 0.3s ease;
}

.feature-large:hover .wp-block-post-featured-image img {
  transform: scale(1.02);
}

.feature-large .h2 {
  font-family: Newsreader, ui-serif, Georgia, serif;
  font-size: clamp(1.85rem, 1.56rem + 1.3vw, 2.25rem);
  line-height: 1.25;
  margin: 1rem 0 0.5rem;
}

.feature-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
  grid-column: 2;
}

.feature-grid article {
  background: var(--cnp-surface, #F8FAFC);
  border: 1px solid var(--cnp-border, #E5E7EB);
  border-radius: 8px;
  padding: 0;
  transition: transform 0.18s ease, box-shadow 0.18s ease;
  overflow: hidden;
}

.feature-grid article:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.feature-grid .wp-block-post-featured-image {
  margin-bottom: 0.75rem;
}

.feature-grid .h4 {
  font-size: 1rem;
  line-height: 1.4;
  margin: 0.75rem 1rem 0.5rem;
}

.feature-grid .wp-block-post-date {
  font-size: 0.875rem;
  color: var(--cnp-muted, #6B7280);
  padding: 0 1rem 1rem;
}

/* Mobile responsive */
@media (max-width: 768px) {
  .hero-rail {
    grid-template-columns: 1fr;
  }
  
  .feature-large,
  .feature-grid {
    grid-column: 1;
  }
  
  .feature-grid {
    grid-template-columns: 1fr;
  }
}

/* ===================================
   LATEST ARTICLES GRID
   =================================== */

.articles-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
  margin-bottom: 2rem;
}

.articles-grid article {
  background: var(--cnp-surface, #F8FAFC);
  border: 1px solid var(--cnp-border, #E5E7EB);
  border-radius: 8px;
  overflow: hidden;
  transition: transform 0.18s ease, box-shadow 0.18s ease;
}

.articles-grid article:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(0,0,0,0.1);
}

.articles-grid .wp-block-post-title {
  font-size: 1.25rem;
  line-height: 1.4;
  margin: 1rem 1rem 0.5rem;
}

.articles-grid .wp-block-post-excerpt {
  margin: 0 1rem;
  font-size: 0.9375rem;
  color: var(--cnp-muted, #6B7280);
}

.articles-grid .wp-block-group {
  padding: 0 1rem 1rem;
  font-size: 0.875rem;
  color: var(--cnp-muted, #6B7280);
  gap: 1rem;
}

@media (max-width: 1024px) {
  .articles-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 640px) {
  .articles-grid {
    grid-template-columns: 1fr;
  }
}

/* ===================================
   CATEGORY BADGES
   =================================== */

.category-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  background: var(--cnp-primary, #1D4ED8);
  color: white;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 0.75rem;
}

.category-badge-sm {
  display: inline-block;
  padding: 0.2rem 0.5rem;
  background: var(--cnp-primary, #1D4ED8);
  color: white;
  border-radius: 4px;
  font-size: 0.6875rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 0.5rem;
}

/* Dark mode adjustments */
@media (prefers-color-scheme: dark) {
  .feature-grid article,
  .articles-grid article {
    background: var(--cnp-surface, #0F172A);
    border-color: var(--cnp-border, #1F2937);
  }
  
  .category-badge,
  .category-badge-sm {
    background: var(--cnp-primary, #60A5FA);
  }
}

/* ===================================
   SECTION TITLES
   =================================== */

.section-title {
  font-family: Newsreader, ui-serif, Georgia, serif;
  font-size: 2rem;
  font-weight: 700;
  margin: 3rem 0 2rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid var(--cnp-border, #E5E7EB);
}
```

---

## 🔤 PHASE 3: Typography Enhancement (10 minutes)

### Step 1: Add Google Fonts

**File:** `/Users/surajgiri/Downloads/docs/wordpress-project/theme/cnp-news-theme/functions.php`

Add this function:

```php
// Enqueue Google Fonts
add_action('wp_enqueue_scripts', 'cnp_enqueue_fonts');
function cnp_enqueue_fonts() {
  wp_enqueue_style(
    'cnp-google-fonts',
    'https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,400;6..72,600;6..72,700&family=Inter:wght@400;500;600;700&display=swap',
    array(),
    null
  );
}
```

### Step 2: Update Typography in style.css

Add after the existing typography section:

```css
/* ===================================
   ENHANCED TYPOGRAPHY
   =================================== */

body {
  font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  line-height: 1.6;
}

h1, h2, h3, h4, h5, h6 {
  font-family: Newsreader, ui-serif, Georgia, serif;
  font-weight: 600;
  line-height: 1.3;
}

.h1 {
  font-size: clamp(2.2rem, 1.9rem + 1.8vw, 3rem);
  line-height: 1.2;
}

.h2 {
  font-size: clamp(1.85rem, 1.56rem + 1.3vw, 2.25rem);
  line-height: 1.25;
}

.h3 {
  font-size: clamp(1.6rem, 1.35rem + 1.1vw, 1.875rem);
  line-height: 1.3;
}

.h4 {
  font-size: clamp(1.35rem, 1.14rem + 0.9vw, 1.5rem);
  line-height: 1.35;
}

/* Text sizes */
.text-sm {
  font-size: 0.875rem;
}

.text-xs {
  font-size: 0.75rem;
}
```

---

## 🎨 PHASE 4: Breaking News Styling (5 minutes)

**File:** `/Users/surajgiri/Downloads/docs/wordpress-project/theme/cnp-news-theme/style.css`

Add:

```css
/* ===================================
   BREAKING NEWS BADGE
   =================================== */

.breaking-badge {
  display: inline-block;
  padding: 0.5rem 1rem;
  background: var(--cnp-danger, #DC2626);
  color: white;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-size: 0.875rem;
  border-radius: 4px;
  animation: pulse 2s ease-in-out infinite;
  margin-right: 1rem;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.8;
  }
}

@media (prefers-reduced-motion: reduce) {
  .breaking-badge {
    animation: none;
  }
}
```

---

## 🧪 PHASE 5: Test & Verify (5 minutes)

### 1. Hard Refresh Your Browser

Press: **Cmd+Shift+R** (Mac) or **Ctrl+Shift+R** (Windows/Linux)

### 2. Check These Elements:

- ✅ Hero section with 1 large + 4 medium cards
- ✅ Categories showing on posts
- ✅ Latest articles grid (3 columns)
- ✅ Newsletter CTA at bottom
- ✅ Proper fonts (Newsreader headlines, Inter body)
- ✅ Dark mode working
- ✅ Mobile responsive

### 3. Mobile Test

Open DevTools (F12) → Toggle device toolbar
Test at: 375px, 768px, 1024px, 1440px

---

## 🚀 IMMEDIATE ACTIONS (Copy & Paste)

### 1. Start by creating posts in WordPress Admin:

```
http://localhost/wp-admin/post-new.php
```

### 2. Create categories:

```
http://localhost/wp-admin/edit-tags.php?taxonomy=category
```

### 3. Edit theme files:

```bash
# On your Mac, open in VS Code:
code /Users/surajgiri/Downloads/docs/wordpress-project/theme/cnp-news-theme/
```

### 4. Key files to edit:

```
templates/home.html        ← Homepage layout
style.css                   ← All styles
functions.php               ← PHP functions
parts/header.html           ← Navigation
parts/footer.html           ← Footer
```

---

## ⚡ QUICK TIPS

### After Editing Files:

1. **Save file** in your editor
2. Go to browser
3. **Hard refresh:** Cmd+Shift+R
4. **See changes instantly!**

### If Changes Don't Show:

```bash
# Clear WordPress cache
docker exec cnp-wordpress rm -rf /var/www/html/wp-content/cache/*

# Restart if needed
cd /Users/surajgiri/Downloads/docs/wordpress-project
docker compose restart wordpress
```

### Check Logs:

```bash
docker compose logs -f wordpress
```

---

## 📊 NEXT PHASE OPTIONS

After completing Quick Win, choose:

### Option A: Content Focus
- Create more sample posts
- Add real images
- Write actual content
- Configure categories

### Option B: Design Polish
- Add more patterns
- Create category template
- Build search page
- Enhance 404 page

### Option C: Performance
- Optimize images
- Add caching
- Implement lazy loading
- Minify assets

### Option D: Features
- Add review system
- Implement schema markup
- Configure SEO plugin
- Set up analytics

---

## 🎯 SUCCESS CHECKLIST

After 1 hour, you should have:

- ✅ 10+ posts created
- ✅ 6 categories active
- ✅ Hero section showing on homepage
- ✅ Proper fonts loaded
- ✅ Card layouts working
- ✅ Mobile responsive
- ✅ Dark mode functional
- ✅ Breaking badge styled

---

**Ready? Start with creating posts in WordPress Admin!**

**Visit:** http://localhost/wp-admin

**Let's build something amazing! 🚀**
