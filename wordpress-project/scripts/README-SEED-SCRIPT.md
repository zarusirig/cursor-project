# Content Launch Seed Script

This directory contains the complete content seeding script for the CNP News launch.

## What Gets Created

The `seed-launch-content-complete.php` script creates **40+ content pieces**:

### Categories & Taxonomy
- ✅ **8 Core Categories:** Enterprise AI, Geopolitics, Fintech, Infrastructure, Analysis, Reviews, News, Trackers
- ✅ **4 Authors:** Sarah Chen (AI Specialist), Marcus Wong (Fintech Analyst), Elena Rodriguez (Infrastructure Reporter), Editorial Desk
- ✅ **50+ Tags:** Entities (OpenAI, Anthropic, NVIDIA) + Technologies (LLM, RAG, semiconductors) + Topics (regulation, fintech)

### Policy & Structure
- ✅ **8 Policy Pages:**
  - About CNP News
  - Contact
  - Editorial Policy
  - AI Disclosure
  - Ethics & Disclosures
  - Corrections Policy
  - Privacy Policy
  - Terms of Use

### Hub Architecture (16 hubs)
- ✅ **4 Pillar Hubs:**
  - Enterprise AI & Automation Hub
  - Geopolitics of Tech & Commerce Hub
  - Fintech & Markets Hub
  - Foundational Tech & Infrastructure Hub

- ✅ **12 Cluster Hubs:**
  - LLM Governance Hub
  - Agentic Automation Hub
  - RAG Patterns Hub
  - Export Controls Hub
  - Platform Regulation Hub
  - Data Localization Hub
  - Real-Time Payments Hub
  - Banking-as-a-Service Hub
  - Market Data Infrastructure Hub
  - Semiconductors & HBM Hub
  - Cloud AI Platforms Hub
  - Zero-Trust Identity Hub

### Evergreen Content (16 pieces)

**Explainers (6):**
1. RAG Architecture Patterns: Latency, Cost, and Quality Trade-offs
2. DMA for Operators: Gatekeeper Rules Cheat Sheet
3. RTP vs FedNow: Settlement, Fraud, and CX Comparison
4. ITDR (Identity Threat Detection & Response) in 2025
5. Prompt Engineering Best Practices for Production
6. Latest BIS Entity List Changes: What Operators Need to Know

**Comparisons (4):**
1. Weaviate vs Pinecone vs Milvus: Vector Database Comparison
2. Adyen vs Stripe for B2B: Decision Matrix
3. Vertex vs SageMaker vs Azure ML: Cloud AI Platform Comparison
4. Claude vs GPT-4 for Data Analysis: Head-to-Head

**Reviews (3):**
1. LangSmith for LLM Evaluation: Hands-On Review
2. AWS Bedrock Review: Enterprise LLM Platform
3. Unit BaaS Platform Review: Banking-as-a-Service

**Trackers (3):**
1. Advanced Chip Export Controls Tracker
2. GPU Lead Times & Pricing Tracker
3. LLM Benchmark Scores Tracker

### News/Analysis (8 pieces)
1. OpenAI Unveils Enterprise Controls for LLM Governance
2. EU AI Act Enforcement Begins: What Enterprises Must Do Now
3. NVIDIA H200 Shipping Delays Push Enterprise AI Timelines
4. FedNow Adds 100 Financial Institutions in Q4
5. BIS Expands Export Controls to Advanced AI Training Chips
6. Anthropic Claude 3.5 Sonnet Outperforms GPT-4 on Code
7. Stripe Expands Banking-as-a-Service to European Markets
8. AWS Bedrock Adds Anthropic Claude 3 Opus Model

---

## How to Run

### Method 1: Docker Compose (Recommended)

```bash
# From project root
docker compose exec wordpress php /var/www/html/seed-launch-content-complete.php
```

### Method 2: WP-CLI

```bash
docker compose exec wordpress wp eval-file /var/www/html/seed-launch-content-complete.php
```

### Method 3: Browser (requires admin login)

1. Copy `seed-launch-content-complete.php` to WordPress root directory
2. Visit: `http://localhost/seed-launch-content-complete.php`
3. Log in as administrator when prompted
4. Script will run and display progress

---

## What Each Content Piece Includes

### All Content Has:
- ✅ **AI Disclosure** at top of every piece
- ✅ **Affiliate Disclosure** on Reviews and Comparisons
- ✅ **Key Points** section (≥3 bullet points)
- ✅ **Why This Matters** section (≥1 paragraph)
- ✅ **Sources** section with citations (placeholders - customize as needed)
- ✅ **Proper categorization** and tags
- ✅ **Author attribution** with credentials

### Hubs Include:
- Comprehensive overview and definition
- Subtopics & key questions
- Decision paths / flowcharts
- "How to Get Started" guide
- Links to related cluster hubs and content
- 6-8 authoritative sources

### Explainers Include:
- Problem definition
- Solution explanation
- Implementation guide
- Step-by-step instructions
- 5-6 sources

### Comparisons Include:
- Decision matrix table structure
- Side-by-side feature comparison
- Pros/cons for each option
- Verdict / recommendations
- "When to use X vs Y" guidance

### Reviews Include:
- Score rating (e.g., 8.5/10)
- Pros and Cons lists
- "Who it's for" section
- Hands-on testing notes
- Verdict and recommendation

### Trackers Include:
- Current data table (placeholder)
- Methodology section
- Update schedule note
- Historical trends

### News/Analysis Include:
- "What Happened" summary
- "Why It Matters" analysis
- "What's Next" timeline
- Context and deeper analysis
- Links back to evergreen content

---

## After Running the Script

### Immediate Next Steps:

1. **Add Featured Images**
   - Go to `WP Admin → Posts → [Post Name] → Set Featured Image`
   - Use 1200px+ wide images (16:9 ratio recommended)
   - Sources: Unsplash, Pexels, custom graphics

2. **Configure Navigation Menus**
   - `WP Admin → Appearance → Menus`
   - Create Primary Navigation with categories and hubs
   - Create Footer Menu with policy pages (About, Contact, Privacy, Terms, etc.)

3. **Customize Content**
   - Replace placeholder source links with actual URLs
   - Add real comparison data to tables
   - Insert actual code snippets, screenshots, charts
   - Expand Key Points and analysis sections

4. **Internal Linking Pass**
   - Update placeholder links (`<a href="#">`) with actual URLs
   - Ensure each piece has:
     - 1 parent link (to Hub)
     - 2-4 sibling links (related content)
     - 1 cross-type link (e.g., Explainer → Review)

5. **SEO Configuration**
   - Install Yoast SEO or Rank Math if not already installed
   - Set focus keyphrases for each piece
   - Verify meta descriptions (excerpts)
   - Check schema markup (Article, Review, NewsArticle)

6. **Homepage Configuration**
   - `WP Admin → Appearance → Editor → Templates → Home`
   - Add hero feature pattern
   - Add featured posts grid
   - Add newsletter CTA

---

## Content Quality Checklist

Before considering content "launch-ready," verify each piece has:

- [ ] Hero image (1200px+ wide)
- [ ] AI disclosure at top
- [ ] Affiliate disclosure (if Reviews/Comparisons)
- [ ] Key Points section (≥3 points)
- [ ] Why This Matters section
- [ ] Real sources with working URLs (not placeholders)
- [ ] Internal links (≥3 per piece)
- [ ] Tags (≥4 per piece)
- [ ] Correct category assignment
- [ ] Author bio with credentials
- [ ] Proper H2/H3 heading structure
- [ ] No broken links
- [ ] Mobile-responsive formatting

---

## Script Performance

- **Execution time:** 30-60 seconds
- **Memory usage:** <50MB
- **Database operations:** ~150 INSERT queries
- **Safe to re-run:** Yes (checks for existing content before creating)

---

## Troubleshooting

### "Cannot connect to database"
- Ensure WordPress is running: `docker compose ps`
- Check database credentials in `wp-config.php`

### "Permission denied"
- Run with admin privileges
- Check file permissions: `chmod +x seed-launch-content-complete.php`

### "Duplicate content" warnings
- Script checks for existing content before creating
- Safe to ignore if re-running after manual deletions

### "Out of memory"
- Increase PHP memory limit in `wp-config.php`:
  ```php
  define('WP_MEMORY_LIMIT', '256M');
  ```

---

## Customization

To customize the seed script:

1. **Add more content pieces:** Add entries to the `$evergreen_content` or `$news_content` arrays
2. **Change categories:** Edit the `$categories` array
3. **Add authors:** Add entries to the `$authors` array
4. **Modify templates:** Edit the content generation sections (lines 200-600)

---

## File Structure

```
wordpress-project/
└── scripts/
    ├── seed-launch-content-complete.php  ← Main seed script
    ├── seed-content.php                   ← Original basic seed script
    ├── seed-simple.php                    ← Minimal seed for testing
    └── README-SEED-SCRIPT.md             ← This file
```

---

## Related Documentation

- **Launch Master Checklist:** `/ops/CONTENT-LAUNCH-MASTER-CHECKLIST.md`
- **Phase Guides:** `/ops/phase-0-safety-setup-guide.md` through `/ops/phase-4-7-final-steps-guide.md`
- **Run Log:** `/ops/run-log.md` (for tracking what's published)
- **Sources Inventory:** `/ops/sources-inventory.csv`
- **Deletions Log:** `/ops/deletions.csv`

---

## Support

Questions or issues with the seed script?

- Check `/ops/` guides for detailed workflows
- Review WordPress logs: `wp-content/debug.log`
- Verify Docker containers are running: `docker compose ps`

---

**Last Updated:** 2025-11-12
**Script Version:** 1.0
**Total Content Pieces:** 40+
**Estimated Review Time:** 2-3 hours to customize all content
