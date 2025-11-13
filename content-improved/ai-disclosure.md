# AI Disclosure Policy

**Last Updated:** November 12, 2024
**Effective Date:** November 1, 2024

CNP News uses AI language models to assist with research and content creation. This page explains exactly how we use AI, what human oversight we apply, and how we ensure accuracy.

---

## Quick Summary

✅ **We use AI for:** Research, outlining, drafting
✅ **We DO NOT use AI for:** Fact verification, editorial decisions, final analysis
✅ **Every AI-assisted article** is fact-checked by human editors
✅ **Every AI-assisted article** displays a disclosure notice
✅ **Human editors make all decisions** on what to publish

---

## How We Use AI

### 1. Research Assistance

**What AI does:**
- Summarizes technical documentation (e.g., AWS docs, API references)
- Identifies relevant academic papers and industry reports
- Extracts key points from long documents

**Human oversight:**
- Editors verify AI summaries against original sources
- Editors independently assess source authority and relevance
- Final source selection is human-only

**Example workflow:**
1. Editor provides research brief: "Find sources on NVIDIA H100 lead times"
2. AI identifies 20 potential sources from documentation, news, reports
3. **Editor reviews all 20** and selects 5 authoritative sources
4. **Editor reads original sources** to verify AI summaries

---

### 2. Outline Generation

**What AI does:**
- Suggests article structure based on topic and audience
- Proposes section headings and logical flow
- Identifies key subtopics to cover

**Human oversight:**
- Editors modify outline based on expertise and reader needs
- Editors add or remove sections based on editorial judgment
- Final outline reflects human editorial priorities

**Example workflow:**
1. Editor briefs AI: "Create outline for RAG architecture comparison article targeting enterprise ML engineers"
2. AI proposes outline with 5 sections
3. **Editor restructures to 7 sections**, adding "Performance Benchmarks" and "Our Testing Methodology"
4. **Editor writes outline** for each section

---

### 3. Draft Generation

**What AI does:**
- Creates initial draft based on human-provided brief
- Generates explanatory text for technical concepts
- Proposes comparisons and examples

**Human oversight:**
- **Editors rewrite 60-80% of AI drafts** to add expertise and nuance
- **Every factual claim verified** against primary sources
- **Editors add experience-based insights** ("In our testing...", "Based on 20+ implementations...")
- **Senior editors review** before publication

**Example workflow:**
1. Editor creates detailed brief with sources and key points
2. AI generates 2,000-word draft
3. **Editor rewrites draft**, adding:
   - First-hand testing results
   - Expert analysis and recommendations
   - Decision frameworks from experience
   - Corrections to any inaccuracies
4. **Senior editor reviews** and approves
5. Article published with AI disclosure

---

### 4. Copy Editing

**What AI does:**
- Suggests grammar and style improvements
- Identifies unclear phrasing
- Checks consistency in terminology

**Human oversight:**
- Editors accept or reject suggestions based on editorial judgment
- Tone and voice remain human-controlled
- Technical accuracy takes precedence over AI style suggestions

---

## What AI Does NOT Do

### ❌ Fact Verification

**AI cannot verify facts.** AI can hallucinate (fabricate) information that sounds plausible but is false.

**Our process:**
- **Human editors verify every factual claim** against primary sources
- **AI-generated data is never published** without human verification
- **Source links are checked** to ensure they exist and support claims

**Example:**
- AI draft claims: "OpenAI released GPT-5 in October 2024"
- **Editor fact-checks:** No such announcement exists
- **Correction:** Editor removes claim or replaces with accurate information

---

### ❌ Editorial Decisions

**AI does not decide:**
- What topics to cover
- What angle or framing to use
- When to publish
- What verdict to reach (in reviews/comparisons)

**Human editors make all editorial decisions** based on:
- Newsworthiness
- Reader value
- Editorial mission
- Journalistic judgment

---

### ❌ Opinion & Analysis

**AI-generated analysis is not published.**

**All analysis and opinion comes from human editors** who have:
- Domain expertise
- Real-world experience
- Editorial judgment

**Example (from RAG explainer):**
> "**Our recommendation:** Start with Advanced RAG for most production use cases. It offers the best balance of accuracy, latency, and cost."

This verdict is based on:
- Sarah Chen's 12 years covering ML infrastructure
- Testing 30+ RAG systems
- Interviewing 50+ ML engineers
- Analyzing production deployments

AI did not generate this recommendation.

---

## Human Oversight Requirements

Every AI-assisted article must pass these checkpoints:

### ☑️ Checkpoint 1: Brief Creation (Human-only)
- Editor creates detailed brief with:
  - Topic and angle
  - Key sources (verified authoritative)
  - Key points to cover
  - Audience and use case

### ☑️ Checkpoint 2: AI Draft (AI + Human)
- AI generates draft based on human brief
- **Editor rewrites 60-80%** to add:
  - Expertise and nuance
  - First-hand experience
  - Editorial judgment

### ☑️ Checkpoint 3: Fact-Check (Human-only)
- **Editor verifies every factual claim** against primary sources
- AI-generated citations are checked (links work, sources support claims)
- Data and statistics cross-referenced with authoritative sources

### ☑️ Checkpoint 4: Experience Layer (Human-only)
- Editor adds experience-based insights:
  - "In our testing..."
  - "Based on interviews with 20+ operators..."
  - "We've seen this pattern in 15+ deployments..."

### ☑️ Checkpoint 5: Senior Review (Human-only)
- Senior editor reviews for:
  - Accuracy
  - Editorial quality
  - Appropriate framing
  - Disclosure compliance

### ☑️ Checkpoint 6: Disclosure (Automated)
- AI disclosure notice added to top of article
- Article tagged as AI-assisted in CMS

---

## Disclosure Standards

### On-Page Disclosure

Every AI-assisted article displays this notice at the top:

> **AI Disclosure:** This article uses AI assistance for research and drafting. All facts have been verified by human editors. [Learn more](/ai-disclosure).

**Variations by content type:**

**For Reviews:**
> **AI Disclosure:** AI helped draft this review. We conducted all testing manually and made all editorial decisions. All facts verified. [Learn more](/ai-disclosure).

**For Trackers:**
> **AI Disclosure:** AI assists with data compilation for this tracker. All sources are independently verified. [Learn more](/ai-disclosure).

**For News:**
> **AI Disclosure:** AI-assisted research. All facts verified against primary sources by human editors. [Learn more](/ai-disclosure).

### Transparency Commitment

We commit to:
- ✅ Disclosing AI use on every assisted article
- ✅ Linking to this detailed policy
- ✅ Updating this policy as practices evolve
- ✅ Answering reader questions about AI use

---

## AI Models We Use

### Current Models (as of November 2024)

1. **Anthropic Claude 3.5 Sonnet**
   - Use: Research, drafting, technical explanations
   - Strengths: Long context, technical accuracy
   - Limitations: Knowledge cutoff (January 2025)

2. **OpenAI GPT-4 Turbo**
   - Use: Research, outlining, copy editing
   - Strengths: Broad knowledge, coding examples
   - Limitations: Knowledge cutoff (April 2023)

### Why These Models

We chose these models because:
- Best performance on technical content
- Longest context windows (for processing documentation)
- Strong safety guardrails
- API access for workflow integration

### Model Limitations

All AI models have limitations:

**Hallucinations:**
- AI can fabricate facts that sound plausible
- **Mitigation:** Human fact-checking of every claim

**Bias:**
- AI training data reflects human biases
- **Mitigation:** Human editors apply editorial judgment, seek diverse sources

**Staleness:**
- AI knowledge has cutoff dates (months or years old)
- **Mitigation:** We cite current sources, verify recent developments

**Lack of Expertise:**
- AI lacks domain expertise and real-world experience
- **Mitigation:** Human editors add technical depth from years of experience

**No Accountability:**
- AI cannot be held accountable for errors
- **Mitigation:** Human editors take full responsibility for published content

---

## Quality Assurance

### How We Ensure Quality

1. **Fact-checking:** Every claim verified against primary sources
2. **Experience layer:** Editors add first-hand insights AI cannot provide
3. **Senior review:** All content reviewed by senior editors before publication
4. **Reader feedback:** We investigate reader-reported errors within 4 hours
5. **Regular audits:** Monthly review of AI-assisted articles for quality

### Quality Metrics We Track

- **Fact error rate:** Target <0.1% (< 1 error per 1,000 facts)
- **Correction rate:** Target <1 correction per 100 articles
- **Reader satisfaction:** Tracked via feedback emails
- **Time saved:** Tracked for efficiency (but quality is priority #1)

---

## Why We Use AI

We use AI because it allows us to:

✅ **Accelerate research:** Quickly identify relevant sources from vast documentation
✅ **Maintain quality:** Human editors can focus on expertise and analysis, not formatting
✅ **Cover more topics:** Editors can publish more frequently without sacrificing quality
✅ **Lower costs:** Sustainable journalism requires efficiency

**What we will NOT compromise:**
❌ Accuracy
❌ Editorial independence
❌ Human expertise
❌ Reader trust

---

## Industry Standards

Our AI disclosure practices align with or exceed:

- **AP Stylebook (2024):** "Disclose AI use in journalism"
- **Reuters Principles:** "Transparency in news gathering methods"
- **BBC Editorial Guidelines:** "Declare use of automated content generation"
- **News Media Alliance:** "Best practices for AI in journalism" (2024)

---

## Future Changes

As AI technology evolves, our practices may change. We commit to:

- **Transparency:** Announce major changes to this policy
- **Reader input:** Consider feedback on AI use
- **Best practices:** Adopt industry standards as they emerge
- **Continuous improvement:** Refine processes based on learnings

---

## Frequently Asked Questions

### Q: Why not just write articles without AI?

**A:** We could, but AI allows us to research faster and cover more topics without compromising quality. The alternative is fewer articles or higher costs (which would require paywalls).

### Q: How do I know you're being honest about AI use?

**A:** We disclose AI use because transparency builds trust. Hiding AI use would risk our credibility. Our reputation depends on honesty.

### Q: Can I tell when an article used AI?

**A:** Not reliably. AI-generated text can sound human, and human text can sound robotic. That's why we disclose explicitly.

### Q: What if AI makes a mistake that gets published?

**A:** Human editors are responsible for all published content. If an error occurs, we correct it within 24 hours and log it publicly. See [Corrections Policy](/corrections).

### Q: Will you use AI-generated images?

**A:** Not currently. We use licensed stock photos, original photography, or credited illustrations. If we ever use AI images, we will disclose prominently.

### Q: Can I opt out of AI-assisted articles?

**A:** Not currently, but you can filter by author (each author's page shows their AI usage frequency). We may add filtering in the future.

### Q: Do other publications use AI like this?

**A:** Yes. Many major publications use AI for research and drafting (with human oversight). Few disclose as transparently as we do.

---

## Contact

### Questions About This Policy
**Email:** editors@cnpnews.net

### Report Suspected AI Errors
**Email:** corrections@cnpnews.net

### General Feedback on AI Use
**Email:** feedback@cnpnews.net

---

## Related Policies

- [Editorial Policy](/editorial-policy) — Our overall editorial standards
- [Corrections Policy](/corrections) — How we handle errors
- [About CNP News](/about) — Our team and mission

---

**Last Updated:** November 12, 2024
**Previous Version:** [October 2024](/ai-disclosure-archive-2024-10)

---

*CNP News is committed to transparent, responsible use of AI in journalism. We welcome your feedback on how we can improve.*