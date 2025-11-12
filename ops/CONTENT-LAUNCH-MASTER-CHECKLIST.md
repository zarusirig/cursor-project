# Content Launch Master Checklist — No Docker, No Local Env

**Branch:** `claude/content-launch-batch-1-011CV47fnmm3tx3dn7s5tkN4`

**Launch Window:** 48-72 hours

**Total Estimated Time:** 15-25 hours

---

## 📋 Quick Navigation

- [Phase 0 — Safety & Setup Guide](phase-0-safety-setup-guide.md) (15-30 min)
- [Phase 1 — Cleanup Guide](phase-1-cleanup-guide.md) (30-60 min)
- [Phase 2 — Hubs Guide](phase-2-hubs-guide.md) (4-6 hours)
- [Phase 3 — Evergreen Guide](phase-3-evergreen-guide.md) (6-10 hours)
- [Phase 4-7 — Final Steps Guide](phase-4-7-final-steps-guide.md) (4-6 hours)

**Operational Files:**
- [Run Log](run-log.md) — comprehensive changelog (update throughout)
- [Sources Inventory](sources-inventory.csv) — primary sources per article
- [Deletions Log](deletions.csv) — removed content tracking

---

## 🎯 Launch Objectives (Definition of Done)

By end of launch window, you must have:

- ✅ **Backup created** and logged
- ✅ **Legacy content removed** (counts logged in deletions.csv)
- ✅ **8 core categories** only; authors credible; footer policy links visible
- ✅ **4 Pillar Hubs** published (with sources, hero, QA pass)
- ✅ **12 Cluster Hubs** published (with sources, hero, QA pass)
- ✅ **12-16 Evergreen pieces** published (Explainers, Reviews, Comparisons, Trackers with disclosures)
- ✅ **6-10 News/Analysis** published and recirculating to evergreen
- ✅ **Internal linking triangle** present on all pages (parent + siblings + cross-type)
- ✅ **One newsletter block** per page (consistent placement)
- ✅ **Sitemaps/feeds valid**; News preflight approved
- ✅ **Analytics collecting** QA + linking + recirculation events
- ✅ **/ops/ docs updated** (run-log, sources-inventory, deletions)

---

## 📅 Recommended Timeline

### Day 1 Morning (3-4 hours)
- [ ] **Phase 0:** Create backup, verify setup (15-30 min)
- [ ] **Phase 1:** Remove legacy content, tidy taxonomies, set up authors/policies (30-60 min)
- [ ] **Phase 2:** Publish 4 Pillar Hubs (2-3 hours)

### Day 1 Afternoon - Day 2 (6-8 hours)
- [ ] **Phase 2:** Publish 12 Cluster Hubs (4-6 hours)
- [ ] **Phase 3:** Start publishing Explainers (6-8 pieces) (3-4 hours)

### Day 2 - Day 3 (8-10 hours)
- [ ] **Phase 3:** Finish Explainers, publish Reviews, Comparisons, Trackers (4-6 hours)
- [ ] **Phase 4:** Publish News/Analysis (6-10 pieces spread over 48-72h) (3-5 hours)
- [ ] **Phase 5:** Internal linking pass (1-2 hours)

### Day 3 Final Hours (2-3 hours)
- [ ] **Phase 6:** Verify feeds, sitemaps, News preflight (30-45 min)
- [ ] **Phase 7:** Verify Related Articles, analytics, newsletters (30 min)
- [ ] **Final:** Commit /ops/ docs to GitHub, open PR (30 min)

---

## 🔄 Quick Loop Workflow (Repeat for Each Content Piece)

### For Hubs (16 total: 4 Pillar + 12 Cluster)
1. **Research:** Collect 6-12 primary sources → list with labels
2. **Create:** WP Admin → Pages/Posts → Add New → title + slug
3. **Write:** Dek, Definition, Subtopics, Decision Paths, How to Get Started, Sources
4. **Add:** AI disclosure (top), hero image (1200px+), tags (3+), category, internal links (3+)
5. **QA:** Run checklist (hero ✓, sources ≥6 ✓, tags ≥3 ✓, links ≥3 ✓)
6. **Publish:** Click Publish → copy URL
7. **Log:** Update `/ops/run-log.md` + `/ops/sources-inventory.csv`

### For Evergreen (12-16 pieces: Explainers, Reviews, Comparisons, Trackers)
1. **Research:** Collect 5-10 primary sources → list with labels
2. **Create:** WP Admin → Posts → Add New → title + slug
3. **Write:** AI disclosure, Key Points (≥3), Why This Matters, main content, Sources
4. **Add:** Affiliate disclosure (if Reviews/Comparisons), hero image, tags (4+), category
5. **Add Links:** Parent (hub), 2-4 siblings, 1 cross-type (anchor text ≥18 chars)
6. **Add Artifacts:** Tables (Comparisons), Scores (Reviews), Data (Trackers)
7. **QA:** Run checklist (hero ✓, disclosures ✓, sources ≥5 ✓, links ≥3 ✓)
8. **Publish:** Click Publish → copy URL
9. **Log:** Update `/ops/run-log.md` + `/ops/sources-inventory.csv`

### For News/Analysis (6-10 pieces)
1. **Choose:** Fresh development tied to a cluster
2. **Write:** Headline, AI disclosure, Key Points, What Happened, Context, What's Next, Sources
3. **Add Links:** Link back to 1-2 evergreen pieces (Hubs, Explainers, Reviews)
4. **Add:** Hero image, tags (4+), category (News or cluster category)
5. **Publish:** Click Publish → copy URL
6. **Log:** Update `/ops/run-log.md`

---

## 📊 Content Inventory (What to Create)

### Phase 2 — Hubs (16 total)

#### Pillar Hubs (4)
- [ ] Enterprise AI & Automation
- [ ] Geopolitics of Tech & Commerce
- [ ] Fintech & Markets
- [ ] Foundational Tech & Infrastructure

#### Cluster Hubs (12)
- [ ] LLM Governance (parent: Enterprise AI & Automation)
- [ ] Agentic Automation (parent: Enterprise AI & Automation)
- [ ] RAG Patterns (parent: Enterprise AI & Automation)
- [ ] Export Controls (parent: Geopolitics of Tech & Commerce)
- [ ] Platform Regulation (parent: Geopolitics of Tech & Commerce)
- [ ] Data Localization (parent: Geopolitics of Tech & Commerce)
- [ ] Real-Time Payments (parent: Fintech & Markets)
- [ ] Banking-as-a-Service (parent: Fintech & Markets)
- [ ] Market Data Infra (parent: Fintech & Markets)
- [ ] Semiconductors & HBM (parent: Foundational Tech & Infrastructure)
- [ ] Cloud AI Platforms (parent: Foundational Tech & Infrastructure)
- [ ] Zero-Trust Identity (parent: Foundational Tech & Infrastructure)

### Phase 3 — Evergreen (12-16 pieces)

#### Enterprise AI & Automation (3)
- [ ] **Explainer:** RAG Architecture Patterns (with latency/cost/quality table)
- [ ] **Comparison:** Weaviate vs Pinecone vs Milvus (decision matrix + pricing)
- [ ] **Review:** LangSmith for Evaluation (hands-on, score, affiliate disclosure)

#### Geopolitics of Tech & Commerce (3)
- [ ] **Explainer:** DMA for Operators (gatekeeper rules cheat sheet)
- [ ] **Tracker:** Advanced Chip Export Controls (timeline, monthly updates)
- [ ] **Analysis:** Latest entity-list changes (link to tracker)

#### Fintech & Markets (3)
- [ ] **Explainer:** RTP vs FedNow (settlement, fraud, CX comparison table)
- [ ] **Comparison:** Adyen vs Stripe for B2B (matrix, verdict, affiliate disclosure)
- [ ] **Tracker:** Payment Rails Uptime (optional, if capacity allows)

#### Foundational Tech & Infrastructure (3)
- [ ] **Comparison:** Vertex vs SageMaker vs Azure ML (capabilities by use case)
- [ ] **Tracker:** GPU Lead Times & Pricing (monthly, methodology)
- [ ] **Explainer:** ITDR in 2025 (framework + checklist)

#### Optional Additions (to reach 16)
- [ ] Explainer: Prompt Engineering Best Practices
- [ ] Comparison: Cloud Data Warehouses
- [ ] Review: Supabase for Rapid Prototyping
- [ ] Tracker: LLM Benchmark Scores

### Phase 4 — News/Analysis (6-10 pieces over 48-72h)
- [ ] News piece 1 (tied to cluster, links to evergreen)
- [ ] News piece 2
- [ ] News piece 3
- [ ] News piece 4
- [ ] News piece 5
- [ ] News piece 6
- [ ] Analysis piece 1 (optional)
- [ ] Analysis piece 2 (optional)
- [ ] Analysis piece 3 (optional)
- [ ] Analysis piece 4 (optional)

---

## ✅ Phase-by-Phase Acceptance Criteria

### Phase 0 — Safety & Setup
- [ ] Full backup (DB + uploads) created
- [ ] Snapshot name logged in `/ops/run-log.md`
- [ ] Editor accounts reviewed/locked (optional)
- [ ] Branches exist: `claude/content-launch-batch-1-011CV47fnmm3tx3dn7s5tkN4` ✅ (done)
- [ ] `/ops/` folder contains: run-log.md, sources-inventory.csv, deletions.csv ✅ (done)

### Phase 1 — Clean Up Legacy
- [ ] Demo/test posts removed (count logged in deletions.csv)
- [ ] Only 8 core categories remain
- [ ] Authors have bios, avatars, credential links
- [ ] All 8 policy pages created: About, Editorial Policy, AI Disclosure, Ethics & Disclosures, Corrections, Privacy, Terms, Contact
- [ ] Footer menu displays all policy links
- [ ] Changes logged in `/ops/run-log.md`

### Phase 2 — Hubs
- [ ] 4 Pillar Hubs published (each with 6-12 sources, hero, QA pass)
- [ ] 12 Cluster Hubs published (each with 6-12 sources, hero, QA pass)
- [ ] Each Cluster Hub links to its parent Pillar Hub
- [ ] All hubs have AI disclosure, tags (≥3), category, internal links (≥3)
- [ ] All URLs logged in `/ops/run-log.md`
- [ ] All sources logged in `/ops/sources-inventory.csv`

### Phase 3 — Evergreen
- [ ] 6-8 Explainers published (sources ≥5, internal links ≥3)
- [ ] 4 Comparisons published (decision matrices, affiliate disclosures)
- [ ] 3-4 Reviews published (scores, pros/cons, affiliate disclosures)
- [ ] 2-4 Trackers published (methodology sections, update schedules)
- [ ] All pieces pass QA checklist
- [ ] All URLs logged in `/ops/run-log.md`
- [ ] All sources logged in `/ops/sources-inventory.csv`

### Phase 4 — News/Analysis
- [ ] 6-10 News/Analysis pieces published
- [ ] Each piece links back to 1-2 evergreen pieces (Hubs, Explainers, Reviews)
- [ ] Publications spread over 48-72h (2-3 per day)
- [ ] All URLs logged in `/ops/run-log.md`

### Phase 5 — Internal Linking
- [ ] Every page has parent link (to Hub)
- [ ] Every page has 2-4 sibling links (related content)
- [ ] Every page has 1 cross-type link (different content type)
- [ ] All anchor text ≥18 characters (descriptive, not "click here")
- [ ] No duplicate destinations in same article
- [ ] Max 2 links per H2 section
- [ ] Hubs updated to feature child pages
- [ ] Changes logged in `/ops/run-log.md`

### Phase 6 — Feeds & Sitemaps
- [ ] /sitemap.xml verified (fresh items present, correct URLs)
- [ ] /news-sitemap.xml verified (≤48h items, sponsored excluded)
- [ ] /feed/ verified (valid XML, latest posts)
- [ ] /feed/news.xml verified (if custom feed)
- [ ] /category/{slug}/feed/ verified (if using category feeds)
- [ ] News preflight tested on 3-5 URLs (title, author, time, hero, schema pass)
- [ ] Changes logged in `/ops/run-log.md`

### Phase 7 — Analytics & Recirculation
- [ ] Related Articles block renders on tested pages (5/5)
- [ ] Admin events incrementing (internal_links_inserted, qa_pass, recirculation)
- [ ] Newsletter blocks present on all articles (consistent placement)
- [ ] GA4 real-time events firing (page_view, scroll, link_click)
- [ ] Changes logged in `/ops/run-log.md`

---

## 🚨 Common Pitfalls & How to Avoid

### 1. **Broken Links from Placeholders**
- **Problem:** You link to pages that don't exist yet (e.g., "Coming Soon" placeholders)
- **Solution:** Either create pages immediately OR use `#` as placeholder and return in Phase 5 to update

### 2. **Affiliate Disclosure Missing**
- **Problem:** Reviews/Comparisons with commercial links lack disclosure
- **Solution:** Always add Affiliate Disclosure block at top of Reviews and Comparisons

### 3. **Sources Not Descriptive**
- **Problem:** Sources listed as just URLs with no context (e.g., "https://example.com")
- **Solution:** Always add label: "[Source Title](URL) — Brief description"

### 4. **QA Fails Not Fixed**
- **Problem:** Publishing articles without running QA or ignoring fails
- **Solution:** Run QA checklist before every publish; fix fails immediately

### 5. **Internal Links Too Short**
- **Problem:** Anchor text like "here", "click", "read more" (< 18 chars)
- **Solution:** Use descriptive phrases: "Learn about RAG architecture patterns" (≥18 chars)

### 6. **News Not Recirculating**
- **Problem:** News/Analysis pieces don't link back to evergreen content
- **Solution:** Every News/Analysis MUST link to at least 1 Hub and 1 Explainer/Review

### 7. **Duplicate Links in Same Article**
- **Problem:** Same destination URL linked multiple times (dilutes link equity)
- **Solution:** Max 2 links per H2 section; no duplicate destinations in same article

### 8. **Cache Not Cleared**
- **Problem:** Changes don't appear (Related Articles, menus, feeds)
- **Solution:** Clear cache after major changes: `WP Admin → Plugins → [Cache Plugin] → Clear Cache`

### 9. **Incomplete Logging**
- **Problem:** Forgetting to update `/ops/run-log.md` or `/ops/sources-inventory.csv`
- **Solution:** Log immediately after publishing (before moving to next piece)

### 10. **No Backup Before Starting**
- **Problem:** Making changes without a backup (irreversible mistakes)
- **Solution:** Phase 0 MUST be completed first (backup created and verified)

---

## 📝 Commit Messages (For GitHub)

When committing /ops/ docs to GitHub, use these message formats:

```bash
# After Phase 0
git add ops/
git commit -m "ops: initialize content launch tracking (run-log, sources, deletions)"

# After Phase 1
git commit -m "ops: log Phase 1 cleanup (23 posts removed, 8 categories finalized)"

# After Phase 2
git commit -m "content(hubs): publish 4 pillar + 12 cluster hubs (urls in run-log)"

# After Phase 3
git commit -m "content(evergreen): add 12 explainers, reviews, comparisons, trackers"

# After Phase 4
git commit -m "content(news): publish 8 news/analysis pieces (recirculation active)"

# After Phase 5
git commit -m "ops: complete internal linking pass (84 links added across 28 pages)"

# After Phase 6-7
git commit -m "ops: verify feeds, sitemaps, analytics (launch complete)"
```

---

## 🎉 Post-Launch (After All Phases Complete)

### 1. Commit & Push Documentation

```bash
cd /home/user/cursor-project
git add ops/
git commit -m "ops: content launch batch 1 complete (all phases verified)"
git push -u origin claude/content-launch-batch-1-011CV47fnmm3tx3dn7s5tkN4
```

### 2. Monitor for 48-72 Hours

- **Google Search Console:** Check indexing status (Coverage report)
- **GA4:** Track traffic patterns, recirculation CTR, event firing
- **WordPress Admin:** Review QA logs, internal link metrics, recirculation impressions

### 3. Plan Batch 2

- Identify gaps: underserved clusters, missing content types
- Expand hubs with additional supporting content
- Launch 2nd batch of News/Analysis

---

## 📞 Support & Resources

- **Documentation:** All guides in `/ops/` folder
- **Operational Files:** `/ops/run-log.md`, `/ops/sources-inventory.csv`, `/ops/deletions.csv`
- **Policy Templates:** `/policies/` folder (editorial-policy.md, ai-disclosure.md, etc.)
- **Publishing Checklist:** `/ops/publishing-checklist.md`
- **Recirculation Playbook:** `/ops/recirculation-playbook.md`
- **Value-Add Checklist:** `/ops/value-add-checklist.md`

---

## ✅ Final Sign-Off

Once all checkboxes are complete:

- [ ] All Phase 0-7 acceptance criteria met
- [ ] All content logged in `/ops/run-log.md`
- [ ] All sources logged in `/ops/sources-inventory.csv`
- [ ] All deletions logged in `/ops/deletions.csv`
- [ ] Documentation committed and pushed to GitHub
- [ ] Monitoring dashboard reviewed (GA4, GSC, WP Admin)

**Launch Status:** 🚀 COMPLETE

**Date Completed:** [TO BE FILLED]

**Completed By:** [NAME/TEAM]

---

**Last Updated:** 2025-11-12
**Branch:** `claude/content-launch-batch-1-011CV47fnmm3tx3dn7s5tkN4`
**Total Content Published:** 16 Hubs + 12-16 Evergreen + 6-10 News/Analysis = **34-42 pieces**
