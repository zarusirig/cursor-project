# Phase 2 — Pillar & Cluster Hubs (16 Total)

**Goal:** Create and publish 4 Pillar Hubs + 12 Cluster Hubs as the foundational content architecture. These hubs link to all supporting Explainers, Reviews, Comparisons, and News.

**Estimated Time:** 4-6 hours (spread over Day 1-2)

**Publishing Order:** Pillar Hubs first → then Cluster Hubs (so Clusters can link up to Pillars)

---

## Hub Inventory

### Pillar Hubs (4)
1. **Enterprise AI & Automation**
2. **Geopolitics of Tech & Commerce**
3. **Fintech & Markets**
4. **Foundational Tech & Infrastructure**

### Cluster Hubs (12)

| Cluster Hub | Parent Pillar |
|-------------|---------------|
| LLM Governance | Enterprise AI & Automation |
| Agentic Automation | Enterprise AI & Automation |
| RAG Patterns | Enterprise AI & Automation |
| Export Controls | Geopolitics of Tech & Commerce |
| Platform Regulation | Geopolitics of Tech & Commerce |
| Data Localization | Geopolitics of Tech & Commerce |
| Real-Time Payments | Fintech & Markets |
| Banking-as-a-Service | Fintech & Markets |
| Market Data Infra | Fintech & Markets |
| Semiconductors & HBM | Foundational Tech & Infrastructure |
| Cloud AI Platforms | Foundational Tech & Infrastructure |
| Zero-Trust Identity | Foundational Tech & Infrastructure |

---

## Workflow (Per Hub)

Follow this checklist for **each of the 16 hubs**:

### 1. Research & Collect Sources (15-20 min per hub)

**Objective:** Gather 6-12 authoritative primary sources before writing.

**Where to Look:**
- **Government/Regulatory:** Federal Register, EU Commission, BIS.gov (export controls), CFPB (fintech)
- **Industry Standards:** NIST, ISO, IEEE, OWASP, PCI DSS
- **Vendor Docs:** OpenAI API docs, AWS/Azure/GCP docs, Stripe/Adyen docs
- **Research Papers:** arXiv, ACL Anthology, SSRN, academic journals
- **Trade Orgs:** Cloud Security Alliance, FS-ISAC, Semiconductor Industry Association

**Format Your Sources List:**
```
Sources:
1. [NIST AI Risk Management Framework](https://www.nist.gov/ai-rmf) — Official AI governance standard
2. [EU AI Act Full Text](https://eur-lex.europa.eu) — Regulatory requirements for AI systems
3. [OpenAI GPT-4 Technical Report](https://openai.com/research/gpt-4) — Model capabilities and limitations
... (6-12 sources total)
```

**Log Sources:**
- As you create each hub, add row to `/ops/sources-inventory.csv`:
  ```csv
  Enterprise AI & Automation Hub,https://yoursite.com/enterprise-ai-automation/,Pillar Hub,NIST AI RMF,https://nist.gov/ai-rmf,EU AI Act,https://eur-lex.europa.eu,OpenAI GPT-4,https://openai.com/research/gpt-4,,,10,2025-11-12,Primary pillar hub
  ```

---

### 2. Create Hub Page/Post in WordPress

#### In WordPress Admin:

1. **Add New**
   - `WP Admin → Pages → Add New` (if hubs are pages)
   - OR `WP Admin → Posts → Add New` (if hubs are posts)
   - **Recommendation:** Use **Pages** for hubs (evergreen), **Posts** for supporting content

2. **Title**
   - Use exact hub name from inventory
   - Example: "Enterprise AI & Automation"

3. **Permalink/Slug**
   - Click **"Edit"** next to URL
   - Use lowercase, hyphenated slug: `enterprise-ai-automation`

4. **Dek (Subtitle/Excerpt)**
   - Add 1-2 sentence scope statement at the top or in excerpt field
   - Example: "Comprehensive coverage of large language models, agentic systems, and enterprise AI deployment strategies for operators and decision-makers."

---

### 3. Write Hub Content (30-45 min per hub)

**Structure:**

```markdown
# [Hub Name]

[Dek: 1-2 sentence scope statement]

---

## AI Disclosure
> This page uses AI assistance for research and drafting. All claims are fact-checked against primary sources. [Learn more](/ai-disclosure/).

---

## Definition

[2-3 paragraphs defining the topic, its importance, and current state]

Example (for Enterprise AI & Automation):
"Enterprise AI & Automation refers to the adoption of large language models (LLMs), agentic systems, and retrieval-augmented generation (RAG) architectures in business operations..."

---

## Subtopics & Key Questions

[3-5 key subtopics or FAQs]

### 1. LLM Governance & Compliance
What frameworks exist for responsible LLM deployment? (Link to LLM Governance cluster hub)

### 2. Agentic Automation Patterns
How do multi-agent systems differ from single-model workflows? (Link to Agentic Automation cluster hub)

### 3. RAG Architecture Trade-offs
When should you use RAG vs fine-tuning? (Link to RAG Patterns cluster hub)

---

## Decision Paths

[Flowchart or decision tree for choosing between approaches — can be text-based or link to visual]

Example:
- **Need real-time data?** → RAG
- **Need consistent reasoning?** → Fine-tuned model
- **Need task decomposition?** → Agentic system

---

## How to Get Started

[3-5 step checklist for beginners]

1. **Assess your use case:** Identify bottlenecks in operations (customer support, data analysis, content generation)
2. **Choose a platform:** Evaluate OpenAI, Anthropic, Azure OpenAI, AWS Bedrock ([link to Comparison: Cloud AI Platforms])
3. **Run a pilot:** Start with low-risk, high-visibility project
4. **Measure impact:** Track latency, accuracy, cost per query
5. **Iterate & scale:** Expand to adjacent use cases

---

## Related Content

### Cluster Hubs (child pages):
- [LLM Governance](#) (placeholder)
- [Agentic Automation](#) (placeholder)
- [RAG Patterns](#) (placeholder)

### Explainers:
- [RAG Architecture Patterns](#) (will be created in Phase 3)
- [Prompt Engineering Best Practices](#) (future)

### Reviews:
- [LangSmith for Evaluation](#) (will be created in Phase 3)

### Comparisons:
- [Weaviate vs Pinecone vs Milvus](#) (will be created in Phase 3)

---

## Sources

1. [NIST AI Risk Management Framework](https://www.nist.gov/ai-rmf) — Official AI governance standard
2. [EU AI Act Full Text](https://eur-lex.europa.eu) — Regulatory requirements
3. [OpenAI GPT-4 Technical Report](https://openai.com/research/gpt-4) — Model capabilities
4. [Anthropic Claude System Card](https://anthropic.com) — Safety and capabilities
5. [LangChain Documentation](https://langchain.com) — RAG implementation patterns
6. [Microsoft Azure AI](https://azure.microsoft.com/ai) — Enterprise deployment
... (6-12 sources total)

---

## Contact & Corrections

Questions or corrections? [Contact us](/contact/) or view our [Corrections Policy](/corrections/).
```

**Copy-Paste Template:**
- See bottom of this guide for a blank hub template

---

### 4. Add Metadata & Media

#### In WordPress Editor (Sidebar):

1. **Featured Image (Hero)**
   - Upload high-quality image (min 1200px wide)
   - **Alt Text:** Descriptive (e.g., "Enterprise AI dashboard showing LLM metrics")
   - **Caption:** Brief description
   - **Credit:** "Image: [Source] or [Stock site]"

2. **Category**
   - Assign **one primary category**
   - Example: "Enterprise AI & Automation"

3. **Tags**
   - Add 3-6 tags: entities, technologies, concepts
   - Example: LLM, RAG, OpenAI, Claude, agentic-automation, governance

4. **Excerpt (if not using Dek field)**
   - Paste the 1-2 sentence Dek

5. **SEO Plugin (Yoast/Rank Math)**
   - **Focus Keyphrase:** Primary topic (e.g., "enterprise AI automation")
   - **Meta Description:** 150-160 chars summarizing hub
   - **Schema Type:** `WebPage` or `Article` (hubs are evergreen pages)

---

### 5. Run QA Panel (Before Publishing)

**QA Checklist (from publishing-checklist.md):**

| Check | Pass? |
|-------|-------|
| **Hero image** present (min 1200px wide) | ☐ |
| **AI disclosure** at top | ☐ |
| **Key Points** ≥ 3 (or "Subtopics") | ☐ |
| **Why This Matters** ≥ 1 paragraph (or "Definition") | ☐ |
| **Sources** ≥ 6 with descriptive labels + URLs | ☐ |
| **Tags** ≥ 3 | ☐ |
| **Category** assigned (1 primary) | ☐ |
| **Internal links** ≥ 3 (to cluster hubs or placeholders) | ☐ |
| **Anchor text** ≥ 18 characters (not "click here") | ☐ |
| **No broken links** (if linking to existing pages) | ☐ |

**Fix any fails before publishing.**

---

### 6. Publish

1. Click **"Publish"** (top-right)
2. Copy the live URL (e.g., `https://yoursite.com/enterprise-ai-automation/`)

---

### 7. Log Completion

**Update `/ops/run-log.md`:**

Find the "Phase 2 — Pillar & Cluster Hubs" section and update the relevant table:

```markdown
### Pillar Hubs (4)
| Hub Name | Date Published | URL | Sources Count | Status |
|----------|---------------|-----|---------------|--------|
| Enterprise AI & Automation | 2025-11-12 | https://site.com/enterprise-ai-automation/ | 10 | ✅ |
```

**Update `/ops/sources-inventory.csv`:**
Add row with article title, URL, type, and primary sources (see Step 1).

---

## Internal Linking Strategy (As You Go)

**For Pillar Hubs:**
- Link to each Cluster Hub (3-4 clusters per pillar)
- Add placeholders if cluster hubs aren't published yet (you'll return in Phase 5)

**For Cluster Hubs:**
- Link **UP** to parent Pillar Hub (1 link)
- Link **SIDEWAYS** to 2-3 sibling cluster hubs (same pillar)
- Add placeholders for supporting content (Explainers, Reviews, Comparisons) — you'll fill these in Phase 3-5

**Anchor Text Rules:**
- Min 18 characters
- Descriptive (not "click here" or "read more")
- Example: "Learn about LLM Governance frameworks" (links to LLM Governance hub)

---

## Publishing Cadence

**Day 1 Morning (2-3 hours):**
- Publish all 4 Pillar Hubs

**Day 1 Afternoon - Day 2 (4-6 hours):**
- Publish all 12 Cluster Hubs
- Update Pillar Hubs with links to Cluster Hubs (edit each pillar → add links → Update)

---

## Acceptance Criteria

| Criterion | Status |
|-----------|--------|
| 4 Pillar Hubs published with 6-12 sources each | ☐ |
| 12 Cluster Hubs published with 6-12 sources each | ☐ |
| Each Cluster Hub links to its parent Pillar Hub | ☐ |
| Each hub has hero image, AI disclosure, tags, category | ☐ |
| All hubs pass QA checklist | ☐ |
| All URLs logged in `/ops/run-log.md` | ☐ |
| All sources logged in `/ops/sources-inventory.csv` | ☐ |

---

## Blank Hub Template (Copy-Paste)

```markdown
# [Hub Name]

[Dek: 1-2 sentence scope statement]

---

## AI Disclosure
> This page uses AI assistance for research and drafting. All claims are fact-checked against primary sources. [Learn more](/ai-disclosure/).

---

## Definition

[2-3 paragraphs]

---

## Subtopics & Key Questions

### 1. [Subtopic Name]
[Question or description]

### 2. [Subtopic Name]
[Question or description]

### 3. [Subtopic Name]
[Question or description]

---

## Decision Paths

[Flowchart or decision tree]

---

## How to Get Started

1. [Step 1]
2. [Step 2]
3. [Step 3]
4. [Step 4]
5. [Step 5]

---

## Related Content

### Cluster Hubs:
- [Cluster 1](#)
- [Cluster 2](#)
- [Cluster 3](#)

### Explainers:
- [Explainer 1](#)

### Reviews:
- [Review 1](#)

### Comparisons:
- [Comparison 1](#)

---

## Sources

1. [Source Title](URL) — Description
2. [Source Title](URL) — Description
3. [Source Title](URL) — Description
4. [Source Title](URL) — Description
5. [Source Title](URL) — Description
6. [Source Title](URL) — Description

---

## Contact & Corrections

Questions or corrections? [Contact us](/contact/) or view our [Corrections Policy](/corrections/).
```

---

## Next Steps

Once all 16 hubs are published and logged, proceed to:
- **[Phase 3 — Supporting Evergreen Content Guide](phase-3-evergreen-guide.md)**

---

**Estimated Time:** 4-6 hours (20-30 min per hub × 16)
**Owner:** Content Operations + Editorial
**Last Updated:** 2025-11-12
