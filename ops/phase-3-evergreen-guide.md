# Phase 3 — Supporting Evergreen Content (12-16 pieces)

**Goal:** Create Explainers, Comparisons, Reviews, and Trackers that link to/from the Pillar and Cluster Hubs. These pieces add depth, decision-making frameworks, and product recommendations.

**Estimated Time:** 6-10 hours (spread over Day 1-3)

**Content Types:**
- **Explainers:** How-to guides, deep dives, architecture patterns (5-8 pieces)
- **Comparisons:** Side-by-side product/service evaluations with decision matrices (4-5 pieces)
- **Reviews:** Hands-on product/service reviews with scores (3-4 pieces)
- **Trackers:** Ongoing data series (pricing, timelines, regulatory changes) (2-4 pieces)

---

## Content Inventory (12-16 pieces)

### Enterprise AI & Automation (3 pieces)
1. **Explainer:** RAG Architecture Patterns → add table: pattern vs latency/cost/quality
2. **Comparison:** Weaviate vs Pinecone vs Milvus → decision matrix + pricing notes
3. **Review:** LangSmith for Evaluation → hands-on; pros/cons; score; affiliate disclosure

### Geopolitics of Tech & Commerce (3 pieces)
4. **Explainer:** DMA for Operators → gatekeeper rules cheat sheet
5. **Tracker:** Advanced Chip Export Controls → timeline; monthly updates
6. **Analysis:** Latest entity-list changes → link back to tracker

### Fintech & Markets (3 pieces)
7. **Explainer:** RTP vs FedNow → settlement, fraud, CX; table
8. **Comparison:** Adyen vs Stripe for B2B → matrix; verdict; affiliate disclosure
9. **Tracker:** Payment Rails Uptime → monthly SLA data (future/optional)

### Foundational Tech & Infrastructure (3 pieces)
10. **Comparison:** Vertex vs SageMaker vs Azure ML → capabilities by use case
11. **Tracker:** GPU Lead Times & Pricing → monthly; methodology section
12. **Explainer:** ITDR (Identity Threat Detection & Response) in 2025 → framework + checklist

**Total:** 12 pieces (minimum)

**Optional additions (to reach 16):**
- Explainer: Prompt Engineering Best Practices
- Comparison: Cloud Data Warehouses (Snowflake vs Databricks vs BigQuery)
- Review: Supabase for Rapid Prototyping
- Tracker: LLM Benchmark Scores (updated quarterly)

---

## Workflow (Per Article)

### 1. Choose Template Based on Content Type

#### **Explainer Template**
- **Focus:** How-to, deep dive, architecture patterns
- **Length:** 1000-2000 words
- **Structure:** Intro → Problem → Solution(s) → Examples → Next Steps → Sources

#### **Comparison Template**
- **Focus:** Side-by-side evaluation of 2-4 products/services
- **Length:** 1200-2500 words
- **Structure:** Intro → Decision Matrix Table → Product 1 → Product 2 → Product 3 → Verdict → Affiliate Disclosure → Sources

#### **Review Template**
- **Focus:** Hands-on evaluation of 1 product/service
- **Length:** 1000-2000 words
- **Structure:** Intro → What It Is → Pros → Cons → Score/Verdict → Who It's For → Affiliate Disclosure → Sources

#### **Tracker Template**
- **Focus:** Ongoing data series (updated monthly/quarterly)
- **Length:** 500-1500 words + data table
- **Structure:** Intro → Current Data Table → Methodology → Historical Trends → Sources → Next Update Date

---

### 2. Research & Collect Sources (20-30 min per article)

**Objective:** Gather 5-10 authoritative primary sources before writing.

**Where to Look:**
- **Product Docs:** Official documentation, pricing pages, changelogs
- **Benchmarks:** MLPerf, TPC-H, real-world case studies
- **Regulatory:** BIS export control lists, ECB payment reports, PCI DSS standards
- **Research Papers:** arXiv, academic journals, vendor whitepapers
- **Vendor Blogs:** Engineering blogs (Stripe, Pinecone, AWS, OpenAI)

**Format Your Sources List:**
```
Sources:
1. [Pinecone Documentation](https://docs.pinecone.io) — Official API and architecture docs
2. [Weaviate vs Pinecone Benchmark](https://weaviate.io/blog/benchmark) — Performance comparison
3. [RAG Architecture Survey](https://arxiv.org/abs/...) — Academic review of RAG patterns
... (5-10 sources total)
```

---

### 3. Create Article in WordPress

#### In WordPress Admin:

1. **Add New Post**
   - `WP Admin → Posts → Add New`

2. **Title**
   - Use clear, descriptive title
   - Examples:
     - "RAG Architecture Patterns: Latency, Cost, and Quality Trade-offs"
     - "Weaviate vs Pinecone vs Milvus: Vector Database Comparison"
     - "LangSmith for LLM Evaluation: Hands-On Review"

3. **Permalink/Slug**
   - Click **"Edit"** next to URL
   - Use lowercase, hyphenated slug
   - Examples: `rag-architecture-patterns`, `weaviate-pinecone-milvus-comparison`, `langsmith-review`

---

### 4. Write Article Content (45-90 min per article)

**Required Sections (All Content Types):**

#### A. AI Disclosure (Top of Page)
```markdown
## AI Disclosure
> This article uses AI assistance for research, drafting, and editing. All claims are fact-checked against primary sources, and product evaluations are based on hands-on testing. [Learn more](/ai-disclosure/).
```

#### B. Affiliate/Sponsored Disclosure (If Applicable)
**For Reviews & Comparisons with commercial links:**
```markdown
## Affiliate Disclosure
> This article contains affiliate links. If you purchase through these links, we may earn a commission at no additional cost to you. Our editorial judgment is not influenced by affiliate relationships. [Read our full policy](/ethics-disclosures/).
```

#### C. Key Points (≥3 bullet points)
```markdown
## Key Points
- RAG architectures reduce hallucinations by grounding LLM outputs in retrieved documents
- Three main patterns: Naive RAG, Advanced RAG (with reranking), and Modular RAG (multi-hop)
- Latency vs quality trade-off: Naive RAG (fastest) vs Modular RAG (most accurate)
```

#### D. Why This Matters (≥1 paragraph)
```markdown
## Why This Matters

For enterprises deploying LLMs, choosing the right RAG architecture directly impacts user experience, operational cost, and output reliability. A naive RAG setup may satisfy low-stakes use cases (internal FAQs), while mission-critical applications (medical diagnosis support, financial analysis) demand reranking and multi-hop retrieval despite higher latency. This guide provides a decision framework based on your use case, budget, and accuracy requirements.
```

#### E. Main Content
- **Explainer:** Problem → Solution(s) → Examples → Step-by-step guide
- **Comparison:** Decision matrix table → Product 1 deep dive → Product 2 → Product 3 → Verdict
- **Review:** What it is → Pros → Cons → Score/Rating → Who it's for
- **Tracker:** Current data table → Methodology → Historical trends → Interpretation

**Content Tips:**
- **Tables:** Use WordPress table block or HTML for comparison matrices, pricing, benchmarks
- **Code Snippets:** Use code block for technical examples (Python, SQL, API calls)
- **Screenshots:** Add annotated screenshots for reviews (UI walkthrough)
- **Decision Trees:** Text-based flowcharts or embed images

#### F. Internal Links (Add During Writing)
- **Parent Link:** Link to relevant Cluster Hub or Pillar Hub
  - Example: "For more on [RAG Patterns in enterprise contexts](link to RAG Patterns hub)..."
- **Sibling Links:** Link to 2-4 related articles (other Explainers, Comparisons, Reviews)
  - Example: "See also: [Weaviate vs Pinecone Comparison](link)"
- **Cross-Type Link:** Link to a different content type (e.g., from Explainer to Review)
  - Example: "For hands-on testing results, read our [LangSmith Review](link)"

**Anchor Text Rules:**
- Min 18 characters
- Descriptive (not "click here")
- Example: "Learn about RAG architecture patterns" (not "click here")

#### G. Sources (≥5 with labels)
```markdown
## Sources

1. [Pinecone Documentation](https://docs.pinecone.io) — Official API and architecture docs
2. [Weaviate vs Pinecone Benchmark](https://weaviate.io/blog/benchmark) — Performance comparison
3. [RAG Architecture Survey](https://arxiv.org/abs/...) — Academic review of RAG patterns
4. [OpenAI Retrieval Best Practices](https://platform.openai.com/docs) — Official guidance
5. [LangChain RAG Tutorial](https://langchain.com) — Implementation examples
```

#### H. Contact & Corrections (Footer)
```markdown
## Contact & Corrections

Questions or corrections? [Contact us](/contact/) or view our [Corrections Policy](/corrections/).
```

---

### 5. Add Metadata & Media

#### In WordPress Editor (Sidebar):

1. **Featured Image (Hero)**
   - Upload high-quality image (min 1200px wide)
   - **Alt Text:** Descriptive (e.g., "RAG architecture diagram showing retrieval and generation flow")
   - **Caption:** Brief description
   - **Credit:** "Image: [Source] or [Created by Author]"

2. **Category**
   - Assign **one primary category**
   - Example: "Enterprise AI & Automation"

3. **Tags**
   - Add 4-8 tags: entities, technologies, concepts
   - Example: RAG, vector-database, Pinecone, Weaviate, Milvus, LLM, retrieval

4. **Excerpt**
   - 1-2 sentence summary (150-160 chars for meta description)

5. **SEO Plugin (Yoast/Rank Math)**
   - **Focus Keyphrase:** Primary topic (e.g., "RAG architecture patterns")
   - **Meta Description:** 150-160 chars
   - **Schema Type:** `Article` or `NewsArticle` (if timely)
   - **For Reviews:** Add `Review` schema with rating (if supported by plugin)

6. **Custom Fields (If Supported by Theme)**
   - **Score/Rating:** (for Reviews) — e.g., 4.5/5
   - **Pros:** Bullet list
   - **Cons:** Bullet list
   - **Verdict:** 1-2 sentence summary

---

### 6. Add Comparison Table (Comparisons Only)

**Example Decision Matrix (use WordPress Table block or HTML):**

| Feature | Weaviate | Pinecone | Milvus |
|---------|----------|----------|--------|
| **Deployment** | Self-hosted or cloud | Cloud only | Self-hosted or cloud |
| **Pricing** | Free tier + usage | $0.096/hr/pod | Free (self-hosted) |
| **Vector Dimensions** | Up to 65k | Up to 20k | Up to 32k |
| **Filtering** | Native GraphQL | Metadata filtering | Scalar + vector |
| **Latency (p95)** | 15ms | 10ms | 20ms |
| **Best For** | Semantic search + knowledge graphs | Production RAG at scale | Research & experimentation |

**Add Verdict Section:**
```markdown
## Verdict

- **Choose Weaviate** if you need semantic search + knowledge graph features
- **Choose Pinecone** if you prioritize low latency and managed infrastructure
- **Choose Milvus** if you want open-source and self-hosted control
```

---

### 7. Add Review Score Widget (Reviews Only)

**If Theme Supports Review Blocks:**
- Insert custom review block (pros/cons/score)

**If Not, Use Text:**
```markdown
## Our Score: 4.5/5

### Pros
- ✅ Excellent observability for LLM pipelines
- ✅ Easy integration with LangChain
- ✅ Powerful evaluation metrics (hallucination detection, latency)

### Cons
- ❌ Pricing can escalate with high-volume testing
- ❌ Learning curve for custom evaluators
- ❌ Limited integrations with non-LangChain frameworks

### Verdict
LangSmith is the gold standard for LLM evaluation if you're already in the LangChain ecosystem. For teams using Anthropic, OpenAI, or other frameworks, consider alternatives like Weights & Biases or Helicone.
```

---

### 8. Run QA Panel (Before Publishing)

**QA Checklist:**

| Check | Pass? |
|-------|-------|
| **Hero image** present (min 1200px wide) | ☐ |
| **AI disclosure** at top | ☐ |
| **Affiliate/Sponsored disclosure** (if applicable) | ☐ |
| **Key Points** ≥ 3 | ☐ |
| **Why This Matters** ≥ 1 paragraph | ☐ |
| **Sources** ≥ 5 with descriptive labels + URLs | ☐ |
| **Tags** ≥ 4 | ☐ |
| **Category** assigned (1 primary) | ☐ |
| **Internal links** ≥ 3 (parent/sibling/cross-type) | ☐ |
| **Anchor text** ≥ 18 characters | ☐ |
| **No broken links** | ☐ |
| **Table/matrix** included (for Comparisons) | ☐ |
| **Score/Rating** added (for Reviews) | ☐ |

**Fix any fails before publishing.**

---

### 9. Publish

1. Click **"Publish"** (top-right)
2. Copy the live URL

---

### 10. Log Completion

**Update `/ops/run-log.md`:**

Find the "Phase 3 — Supporting Evergreen Content" section and update the relevant table:

```markdown
### Enterprise AI & Automation
| Title | Type | Date Published | URL | Status |
|-------|------|---------------|-----|--------|
| RAG Architecture Patterns | Explainer | 2025-11-12 | https://site.com/rag-architecture-patterns/ | ✅ |
```

**Update `/ops/sources-inventory.csv`:**
```csv
RAG Architecture Patterns,https://site.com/rag-architecture-patterns/,Explainer,Pinecone Docs,https://docs.pinecone.io,RAG Survey,https://arxiv.org/abs/...,LangChain Tutorial,https://langchain.com,,,8,2025-11-12,Core explainer for RAG hub
```

---

## Publishing Cadence

**Day 1-2 (4-5 hours):**
- Publish 6-8 Explainers
- Publish 2 Reviews

**Day 2-3 (4-5 hours):**
- Publish 4 Comparisons
- Publish 2-4 Trackers

**Spread publications throughout the day:**
- Morning: 2-3 pieces
- Afternoon: 2-3 pieces
- Evening: 1-2 pieces

---

## Acceptance Criteria

| Criterion | Status |
|-----------|--------|
| 6-8 Explainers published with sources + internal links | ☐ |
| 4 Comparisons published with decision matrices + disclosures | ☐ |
| 3-4 Reviews published with scores + disclosures | ☐ |
| 2-4 Trackers published with methodology sections | ☐ |
| All pieces pass QA checklist | ☐ |
| All URLs logged in `/ops/run-log.md` | ☐ |
| All sources logged in `/ops/sources-inventory.csv` | ☐ |

---

## Content Templates

### Explainer Template (Copy-Paste)

```markdown
# [Article Title]

## AI Disclosure
> This article uses AI assistance for research, drafting, and editing. All claims are fact-checked against primary sources. [Learn more](/ai-disclosure/).

---

## Key Points
- [Point 1]
- [Point 2]
- [Point 3]

## Why This Matters

[1-2 paragraphs explaining relevance and impact]

---

## [Main Content Sections]

### Problem
[What challenge does this solve?]

### Solution
[How does this work?]

### Examples
[Code snippets, case studies, screenshots]

### Step-by-Step Guide
1. [Step 1]
2. [Step 2]
3. [Step 3]

---

## Related Content
- [Link to parent hub]
- [Link to sibling explainer]
- [Link to related review]

---

## Sources
1. [Source Title](URL) — Description
2. [Source Title](URL) — Description
3. [Source Title](URL) — Description
4. [Source Title](URL) — Description
5. [Source Title](URL) — Description

---

## Contact & Corrections
Questions or corrections? [Contact us](/contact/) or view our [Corrections Policy](/corrections/).
```

### Comparison Template (Copy-Paste)

```markdown
# [Product A] vs [Product B] vs [Product C]: Comparison

## AI Disclosure
> This article uses AI assistance for research and drafting. All product evaluations are based on official documentation and hands-on testing. [Learn more](/ai-disclosure/).

## Affiliate Disclosure
> This article contains affiliate links. If you purchase through these links, we may earn a commission at no additional cost to you. [Read our full policy](/ethics-disclosures/).

---

## Key Points
- [Point 1]
- [Point 2]
- [Point 3]

## Why This Matters
[1-2 paragraphs]

---

## Decision Matrix

| Feature | Product A | Product B | Product C |
|---------|-----------|-----------|-----------|
| **Feature 1** | ... | ... | ... |
| **Feature 2** | ... | ... | ... |
| **Pricing** | ... | ... | ... |
| **Best For** | ... | ... | ... |

---

## Product A: [Name]
[Deep dive: features, pros, cons, pricing]

## Product B: [Name]
[Deep dive]

## Product C: [Name]
[Deep dive]

---

## Verdict
- **Choose Product A** if [use case]
- **Choose Product B** if [use case]
- **Choose Product C** if [use case]

---

## Related Content
- [Link to parent hub]
- [Link to related explainer]
- [Link to related review]

---

## Sources
1. [Source Title](URL) — Description
2. [Source Title](URL) — Description
3. [Source Title](URL) — Description
4. [Source Title](URL) — Description
5. [Source Title](URL) — Description

---

## Contact & Corrections
Questions or corrections? [Contact us](/contact/) or view our [Corrections Policy](/corrections/).
```

### Review Template (Copy-Paste)

```markdown
# [Product Name] Review: [Subtitle]

## AI Disclosure
> This review uses AI assistance for drafting and editing. All product evaluations are based on hands-on testing. [Learn more](/ai-disclosure/).

## Affiliate Disclosure
> This review contains affiliate links. If you purchase through these links, we may earn a commission at no additional cost to you. [Read our full policy](/ethics-disclosures/).

---

## Our Score: [X]/5

## Key Points
- [Point 1]
- [Point 2]
- [Point 3]

## Why This Matters
[1-2 paragraphs]

---

## What Is [Product Name]?
[Overview: what it does, who makes it, primary use cases]

## Pros
- ✅ [Pro 1]
- ✅ [Pro 2]
- ✅ [Pro 3]

## Cons
- ❌ [Con 1]
- ❌ [Con 2]
- ❌ [Con 3]

## Features & Performance
[Detailed sections: UI/UX, performance benchmarks, integrations, pricing]

## Who It's For
- **Best for:** [Use case 1], [Use case 2]
- **Not recommended for:** [Use case 3]

---

## Verdict
[2-3 sentences summarizing recommendation]

---

## Related Content
- [Link to parent hub]
- [Link to comparison article]
- [Link to related explainer]

---

## Sources
1. [Official Documentation](URL)
2. [Vendor Blog](URL)
3. [Third-Party Review](URL)
4. [Benchmark](URL)
5. [User Forum](URL)

---

## Contact & Corrections
Questions or corrections? [Contact us](/contact/) or view our [Corrections Policy](/corrections/).
```

---

## Next Steps

Once all 12-16 evergreen pieces are published and logged, proceed to:
- **[Phase 4 — News/Analysis Publishing Schedule](phase-4-news-schedule.md)**

---

**Estimated Time:** 6-10 hours (30-60 min per article × 12-16)
**Owner:** Content Operations + Editorial
**Last Updated:** 2025-11-12
