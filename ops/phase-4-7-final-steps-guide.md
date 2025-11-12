# Phase 4-7 — News, Linking, Verification, Analytics

**Goal:** Publish timely News/Analysis pieces, complete internal linking pass, verify feeds/sitemaps, and confirm analytics are tracking correctly.

**Estimated Time:** 4-6 hours (spread over final 24-48 hours of launch window)

---

## Phase 4 — News/Analysis (6-10 items over 48-72h)

**Goal:** Publish fresh, timely content that recirculates readers back to your evergreen Hubs, Explainers, and Reviews.

**Content Types:**
- **News:** Breaking updates (regulatory changes, product launches, M&A)
- **Analysis:** Commentary on recent developments with deeper context

**Strategy:** Each News/Analysis piece MUST link back to at least 1-2 evergreen pieces (Hubs, Explainers, Reviews) in the same cluster.

---

### Workflow (Per News/Analysis Piece)

#### 1. Choose Fresh Development

**Examples:**
- **Export Controls:** New BIS entity list update → link to "Advanced Chip Export Controls Tracker"
- **Cloud AI Platforms:** AWS announces new Bedrock features → link to "Vertex vs SageMaker vs Azure ML Comparison"
- **LLM Governance:** EU AI Act enforcement deadline → link to "LLM Governance Hub"
- **Real-Time Payments:** FedNow adds new bank participants → link to "RTP vs FedNow Explainer"

**Source:** Monitor RSS feeds, vendor blogs, regulatory announcements, trade publications.

---

#### 2. Write News/Analysis Article (30-45 min per piece)

**Structure:**

```markdown
# [Headline: Clear, Specific, Under 70 Characters]

## AI Disclosure
> This article uses AI assistance for research and drafting. All facts are verified against official sources. [Learn more](/ai-disclosure/).

---

## Key Points
- [Point 1: What happened]
- [Point 2: Why it matters]
- [Point 3: What's next]

## Why This Matters
[1-2 paragraphs: impact on operators, decision-makers, or end-users]

---

## What Happened
[2-3 paragraphs: the facts, timeline, key players]

## Context & Analysis
[3-5 paragraphs: why this is significant, historical precedent, implications]

**Related Reading:**
- [Link to relevant Hub] — "For broader context on [topic], see our [Hub Name]"
- [Link to Explainer] — "Learn more about [specific concept] in our [Explainer Title]"
- [Link to Review] — "We tested [product/service] in our [Review Title]"

---

## What's Next
[1-2 paragraphs: expected timeline, stakeholders to watch, next milestones]

---

## Sources
1. [Official Announcement](URL) — Primary source
2. [Regulatory Filing](URL) — Full text
3. [Vendor Blog](URL) — Technical details
4. [Trade Publication](URL) — Industry reaction
5. [Expert Commentary](URL) — Third-party analysis

---

## Contact & Corrections
Questions or corrections? [Contact us](/contact/) or view our [Corrections Policy](/corrections/).
```

---

#### 3. Add Metadata & Publish

**In WordPress Editor (Sidebar):**

1. **Category:** Assign "News" or relevant cluster category
2. **Tags:** Add 4-6 tags (entities, concepts, locations)
3. **Featured Image:** Use relevant image (product logo, screenshot, stock photo)
4. **Excerpt:** 1-2 sentence summary
5. **SEO Plugin:**
   - **Focus Keyphrase:** Primary topic (e.g., "AWS Bedrock update")
   - **Meta Description:** 150-160 chars
   - **Schema Type:** `NewsArticle` (with `datePublished`, `dateModified`)

**Publish and copy URL.**

---

#### 4. Log Completion

**Update `/ops/run-log.md`:**

```markdown
## Phase 4 — News/Analysis (6-10 items over 48-72h)

| Date | Title | Cluster | Links Back To | Status |
|------|-------|---------|---------------|--------|
| 2025-11-12 | AWS Bedrock Adds Anthropic Claude 3.5 | Cloud AI Platforms | Vertex vs SageMaker Comparison | ✅ |
```

---

### Publishing Cadence (Phase 4)

**Goal:** Space publications to maintain freshness.

**Day 2-3 (48-72 hours):**
- **Morning:** Publish 2 News/Analysis pieces
- **Afternoon:** Publish 2 News/Analysis pieces
- **Evening:** Publish 1-2 News/Analysis pieces

**Total:** 6-10 pieces spread over 2-3 days (2-3 per day)

---

## Phase 5 — Internal Linking Pass

**Goal:** Ensure every page forms a 3-link triangle: Parent (Hub) + Siblings (related content) + Cross-Type (different content type).

**Estimated Time:** 1-2 hours

---

### Workflow

#### 1. Create Linking Checklist

**Spreadsheet or Markdown Table:**

| Page Title | URL | Parent Link | Sibling 1 | Sibling 2 | Cross-Type | Status |
|------------|-----|-------------|-----------|-----------|------------|--------|
| RAG Architecture Patterns | /rag-patterns/ | RAG Hub ✅ | Weaviate Comparison ☐ | LangSmith Review ☐ | Export Controls ☐ | ⏳ |

---

#### 2. Edit Each Page in WordPress

**For Each Article:**

1. **Navigate to Post/Page**
   - `WP Admin → Posts → All Posts` → click article title

2. **Add Missing Links**
   - **Parent Link:** If not already present, add link to relevant Hub
     - Example: "For more on [RAG Patterns in enterprise contexts](/rag-patterns-hub/), see our comprehensive hub."
   - **Sibling Links:** Add 2-4 links to related Explainers, Reviews, Comparisons
     - Example: "Compare [vector databases](/weaviate-pinecone-comparison/) or read our [LangSmith review](/langsmith-review/)."
   - **Cross-Type Link:** Add 1 link to a different content type (e.g., from Explainer to News)
     - Example: "For the latest on LLM governance, see our [recent analysis](/eu-ai-act-update/)."

3. **Verify Anchor Text**
   - Min 18 characters
   - Descriptive (not "click here")

4. **Check for Duplicates**
   - Max 2 links per H2 section
   - No duplicate destinations in the same article

5. **Update Post**
   - Click **"Update"** (top-right)

---

#### 3. Update Hubs to Feature Child Pages

**For Each Hub:**

1. **Navigate to Hub Page**
   - `WP Admin → Pages → All Pages` → click hub title

2. **Add/Update "Related Content" Section**
   - List all child Explainers, Reviews, Comparisons, Trackers
   - Example (for Enterprise AI & Automation Hub):
     ```markdown
     ## Related Content

     ### Explainers
     - [RAG Architecture Patterns](/rag-architecture-patterns/)
     - [Prompt Engineering Best Practices](/prompt-engineering/)

     ### Reviews
     - [LangSmith for Evaluation](/langsmith-review/)

     ### Comparisons
     - [Weaviate vs Pinecone vs Milvus](/weaviate-pinecone-milvus-comparison/)
     ```

3. **Update Page**
   - Click **"Update"**

---

#### 4. Log Completion

**Update `/ops/run-log.md`:**

```markdown
## Phase 5 — Internal Linking Pass

- **Date:** 2025-11-13
- **Pages Updated:** 28 (16 hubs + 12 evergreen)
- **Links Added:** 84 (3 per page average)
- **Status:** ✅ Complete
```

---

## Phase 6 — Feeds, Sitemaps, News Preflight

**Goal:** Verify XML sitemaps, RSS feeds, and News preflight (schema, images, disclosures).

**Estimated Time:** 30-45 minutes

---

### Step 1: Verify Sitemaps

#### In Browser:

1. **Visit XML Sitemaps**
   - `https://yoursite.com/sitemap.xml` (index sitemap)
   - `https://yoursite.com/news-sitemap.xml` (news-specific, if separate)
   - Or navigate via Yoast/Rank Math: `WP Admin → SEO → General → Features → XML Sitemaps → View`

2. **Check Contents**
   - **Fresh items:** Articles published ≤48h should appear
   - **Correct URLs:** All URLs should be absolute (https://yoursite.com/slug/)
   - **No excluded pages:** Verify sponsored posts are excluded from news sitemap (if applicable)

3. **Test in Google Search Console (Optional)**
   - `Search Console → Sitemaps → Submit sitemap URL`
   - Check for errors (4xx, 5xx, redirect chains)

---

### Step 2: Verify RSS Feeds

#### In Browser or Feed Reader:

1. **Visit Feeds**
   - `https://yoursite.com/feed/` (main site feed)
   - `https://yoursite.com/feed/news.xml` (news-only feed, if custom)
   - `https://yoursite.com/category/[slug]/feed/` (category feeds)

2. **Check Contents**
   - **Latest posts appear:** Articles published ≤24h should be at top
   - **Correct formatting:** Title, excerpt, full content (or summary), images, author, date
   - **No errors:** Valid XML (use validator: https://validator.w3.org/feed/)

3. **Verify Enclosures (for Podcast/Media Feeds)**
   - If using custom feeds for newsletters or podcasts, verify enclosures/attachments

---

### Step 3: Run News Preflight (Plugin or Manual Check)

**If Using News Preflight Plugin:**

1. **Navigate to Plugin**
   - `WP Admin → Plugins → [News Preflight Plugin] → Test URL`

2. **Test 3-5 Recent Articles**
   - Paste URL → Click **"Run Preflight"**

3. **Check for Passes:**
   - ✅ **Title:** Present and unique
   - ✅ **Author:** Byline with valid author page
   - ✅ **Publish Time:** `datePublished` in schema
   - ✅ **Hero Image:** Min 1200px wide, valid URL
   - ✅ **Schema:** `Organization` + `WebSite` + `BreadcrumbList` + `NewsArticle` (or `Article`)
   - ✅ **NewsArticle Genre:** Labeled (if News/Analysis content type)
   - ✅ **Corrections Policy:** Link present in footer

4. **Fix Any Failures**
   - **Missing hero:** Upload featured image
   - **Missing genre:** Add custom field `articleGenre: "News"` (or set in SEO plugin)
   - **Missing corrections link:** Verify footer menu includes `/corrections/`

**Manual Check (If No Plugin):**

1. **View Page Source**
   - Visit article → Right-click → "View Page Source"

2. **Search for Schema**
   - `Ctrl+F` → search for `"@type": "NewsArticle"` or `"@type": "Article"`
   - Verify required fields: `headline`, `author`, `datePublished`, `image`, `publisher`

3. **Verify Images**
   - Open Network tab (F12) → reload page → check hero image loads (200 status)
   - Confirm image width ≥ 1200px (right-click image → "Open in new tab" → check dimensions)

---

### Step 4: Log Completion

**Update `/ops/run-log.md`:**

```markdown
## Phase 6 — Feeds, Sitemaps, News Preflight

- **Date:** 2025-11-13
- **Sitemap Verified:** /sitemap.xml, /news-sitemap.xml — ✅ All fresh items present
- **Feeds Verified:** /feed/, /feed/news.xml, /category/{slug}/feed/ — ✅ Valid XML
- **News Preflight:** 5 URLs tested — ✅ All passed (title, author, time, hero, schema)
- **Status:** ✅ Complete
```

---

## Phase 7 — Analytics & Recirculation

**Goal:** Confirm Related Articles blocks render, admin events track, and newsletter placements are consistent.

**Estimated Time:** 30 minutes

---

### Step 1: Verify Related Articles Block

#### In Browser:

1. **Visit 3-5 Published Articles**
   - Open in incognito/private window (to avoid caching)

2. **Scroll to Related Articles Section**
   - Usually at end of article (before footer)
   - Verify:
     - **Block renders:** Articles are visible (not empty)
     - **Relevant links:** Related articles match the topic (same cluster/category)
     - **Correct count:** 3-6 related articles (per theme config)
     - **Clickable:** Links work and open correct pages

3. **If Related Articles Don't Render:**
   - **Check plugin settings:** `WP Admin → Plugins → [Related Posts Plugin] → Settings`
   - **Verify taxonomy:** Related posts usually pull by category or tag (ensure articles have shared categories/tags)
   - **Clear cache:** If using caching plugin, clear cache and revisit page

---

### Step 2: Confirm Admin Events/Metrics Incrementing

#### In WordPress Admin or Analytics Dashboard:

1. **Check Internal Linking Metrics (If Plugin Installed)**
   - `WP Admin → Analytics → Internal Links` (or custom dashboard)
   - Verify:
     - **internal_links_inserted:** Count increments as you add links
     - **Click-through rates:** Impressions and clicks tracked (may take 24-48h)

2. **Check QA Pass Events (If Plugin Installed)**
   - `WP Admin → Analytics → QA Logs`
   - Verify:
     - **qa_pass:** Articles that passed QA checklist are logged
     - **qa_fail:** Articles that failed are flagged with reasons

3. **Check Recirculation Events (If Plugin Installed)**
   - `WP Admin → Analytics → Recirculation`
   - Verify:
     - **related_articles_impressions:** Blocks are being viewed
     - **related_articles_clicks:** Users are clicking related links

4. **Google Analytics 4 (Optional)**
   - Open GA4 → **Real-time** report
   - Visit a few articles in incognito window
   - Verify events fire: `page_view`, `scroll`, `link_click` (if configured)

---

### Step 3: Verify Newsletter Block Placement

#### In Browser:

1. **Visit 3-5 Published Articles**
   - Open in incognito/private window

2. **Scroll to Newsletter Block**
   - Usually at footer (end of article) or mid-article (after Key Points)
   - Verify:
     - **Block renders:** Newsletter signup form visible
     - **Correct copy:** Heading, description, CTA button text
     - **Form works:** Enter test email → submit → verify confirmation (check inbox or thank-you message)

3. **Check Consistency**
   - **One newsletter block per page** (not multiple)
   - **Same placement** across all articles (footer or mid-article, consistently)
   - If inconsistent, edit articles to add/remove blocks as needed

---

### Step 4: Log Completion

**Update `/ops/run-log.md`:**

```markdown
## Phase 7 — Analytics & Recirculation

- **Date:** 2025-11-13
- **Related Articles:** ✅ Rendering on all tested pages (5/5)
- **Admin Events:** ✅ Incrementing (internal_links_inserted, qa_pass, recirculation_impressions)
- **Newsletter Blocks:** ✅ Consistent placement (footer on all articles)
- **Status:** ✅ Complete
```

---

## Final Acceptance Criteria (All Phases)

| Criterion | Status |
|-----------|--------|
| **Phase 0:** Backup created and logged | ☐ |
| **Phase 1:** Legacy content removed; counts logged | ☐ |
| **Phase 1:** Only 8 core categories; authors credible; footer shows policy links | ☐ |
| **Phase 2:** 4 Pillar + 12 Cluster hubs published with sources, hero, QA pass | ☐ |
| **Phase 3:** 12-16 Explainers, Reviews, Comparisons, Trackers published with disclosures | ☐ |
| **Phase 4:** 6-10 News/Analysis published and recirculating to evergreen | ☐ |
| **Phase 5:** Internal linking triangle present on all pages | ☐ |
| **Phase 6:** Sitemaps/feeds valid; News preflight approved | ☐ |
| **Phase 7:** Related Articles + analytics + newsletter verified | ☐ |
| **Ops Docs:** /ops/run-log.md, sources-inventory.csv, deletions.csv updated | ☐ |

---

## Quick Loop Checklist (For Claude or Human Operator)

**Use this checklist to loop through each content piece:**

### Delete Old Content (Phase 1)
1. Posts → filter + bulk trash → empty trash → log count in deletions.csv

### Create Hub (Phase 2)
1. Research: collect 6-12 sources
2. Write: title, dek, sections (Definition, Subtopics, Decision Paths, How to Get Started, Sources)
3. Add: hero, tags, category, AI disclosure, internal links (3+)
4. QA: run checklist (hero, sources, tags, links)
5. Publish: copy URL
6. Log: update run-log.md + sources-inventory.csv

### Create Supporting Page (Phase 3)
1. Research: collect 5-10 sources
2. Write: title, Key Points, Why This Matters, main content, disclosures (AI + affiliate), sources
3. Add: hero, tags, category, internal links (parent + siblings + cross-type)
4. QA: run checklist
5. Publish: copy URL
6. Log: update run-log.md + sources-inventory.csv

### Create News/Analysis (Phase 4)
1. Choose fresh development
2. Write: headline, Key Points, What Happened, Context, What's Next, sources
3. Add: links back to evergreen (Hubs, Explainers, Reviews)
4. Add: hero, tags, category
5. Publish: copy URL
6. Log: update run-log.md

### Link Pass (Phase 5)
1. Open article → edit
2. Add: parent link, 2-4 sibling links, 1 cross-type link
3. Verify: anchor text ≥18 chars, no duplicates
4. Update: save changes
5. Repeat for all articles

### Verify (Phase 6-7)
1. Visit: /sitemap.xml, /feed/, sample articles
2. Check: sitemaps valid, feeds valid, Related Articles render, newsletter present
3. Test: News preflight on 3-5 URLs
4. Log: update run-log.md

**Repeat until launch set complete.**

---

## Next Steps: Post-Launch

**Once all phases are complete:**

1. **Create Pull Request (if using GitHub for theme/config changes)**
   - Commit /ops/ docs
   - Open PR: `ops: content launch batch 1 complete`
   - Merge to main

2. **Monitor for 48-72 hours:**
   - Google Search Console: indexing status
   - GA4: traffic patterns, recirculation CTR
   - WordPress Admin: QA logs, internal link metrics

3. **Plan Batch 2:**
   - Identify gaps (underserved clusters, missing content types)
   - Expand hubs with additional Explainers, Reviews, Trackers
   - Launch 2nd batch of News/Analysis

---

**Estimated Total Time (All Phases):** 15-25 hours over 48-72 hours

**Owner:** Content Operations + Editorial + Technical SEO

**Last Updated:** 2025-11-12
