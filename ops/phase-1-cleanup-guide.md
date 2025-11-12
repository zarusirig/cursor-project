# Phase 1 — Clean Up Legacy Content (WordPress Admin)

**Goal:** Remove demo/test content, tidy taxonomies, set up authors, and establish policy pages + footer navigation.

**Estimated Time:** 30-60 minutes

---

## Step 1: Remove Demo & Old Posts

### In WordPress Admin:

1. **Navigate to Posts**
   - `WP Admin → Posts → All Posts`

2. **Filter Demo Content**
   - Use search or filter by:
     - **Category:** "Uncategorized"
     - **Search term:** "Hello World", "Sample", "Test", "Draft"
     - **Date:** Posts older than 90 days with no featured image or sources

3. **Bulk Delete**
   - Select posts (checkbox at top to select all on page)
   - `Bulk Actions → Move to Trash → Apply`
   - Repeat for multiple pages if needed

4. **Empty Trash**
   - `Posts → Trash`
   - Click **"Empty Trash"** at top
   - ⚠️ **This is permanent** — ensure backup from Phase 0 is complete

5. **Log Deletions**
   - Count how many posts were removed
   - Open `/ops/deletions.csv` (GitHub web editor)
   - Add entries for each deleted post (or add a summary row):
     ```csv
     2025-11-12,123,Hello World,post,Uncategorized,Demo content,Content Ops
     2025-11-12,124,Sample Page,page,N/A,Demo content,Content Ops
     ```
   - Update `/ops/run-log.md`:
     ```markdown
     ### Demo/Old Posts Removed
     - **Date:** 2025-11-12
     - **Count:** 15 posts, 3 pages
     - **Method:** WP Admin → Posts → Bulk Trash → Empty Trash
     - **Status:** ✅ Complete
     - **Notes:** See deletions.csv for details
     ```

---

## Step 2: Tidy Taxonomies (Categories & Tags)

### Categories

**Goal:** Keep only **8 core categories** aligned with your Pillar + Cluster structure.

#### Recommended Core Categories:
1. **Enterprise AI & Automation**
2. **Geopolitics of Tech**
3. **Fintech & Markets**
4. **Foundational Tech**
5. **Analysis** (for deeper dives)
6. **Reviews** (product/service reviews)
7. **News** (breaking updates)
8. **Trackers** (ongoing data series)

#### In WordPress Admin:

1. **Navigate to Categories**
   - `WP Admin → Posts → Categories`

2. **Identify Legacy/Redundant Categories**
   - Examples: "Uncategorized", "General", "Blog", "Updates" (unless strategic)

3. **Merge or Delete**
   - **To Merge:** Edit each post in the old category → reassign to a core category → delete old category
   - **To Delete:** Click category → Delete (only works if no posts assigned)

4. **Set Default Category**
   - `Settings → Writing → Default Post Category`
   - Choose **"Uncategorized"** or your primary category (e.g., "News")

5. **Log Changes**
   - Update `/ops/run-log.md`:
     ```markdown
     ### Taxonomies Tidied
     - **Date:** 2025-11-12
     - **Categories Kept:** 8 (Enterprise AI, Geopolitics, Fintech, Foundational Tech, Analysis, Reviews, News, Trackers)
     - **Categories Removed:** Uncategorized, General, Blog (3 total)
     - **Status:** ✅ Complete
     ```

### Tags (Optional Cleanup)

- **Keep:** Entity tags (companies, people, products), technology tags (LLM, RAG, FedNow)
- **Remove:** Generic tags (admin, test, draft)
- `WP Admin → Posts → Tags → Bulk Actions → Delete`

---

## Step 3: Set Up Authors & Credentials

### For Each Author:

1. **Navigate to Users**
   - `WP Admin → Users → All Users`
   - Click on each author

2. **Update Profile**
   - **Display Name:** Full name (publicly visible)
   - **Bio:** 2-3 sentences highlighting expertise
     - Example: "Jane Doe is a fintech analyst with 10 years covering payments infrastructure. She previously worked at XYZ Bank and holds a CFA charter."
   - **Website:** Link to LinkedIn, personal site, or author page
   - **Avatar:** Upload photo or use Gravatar

3. **Add Credential Link (if supported by theme)**
   - Use custom field or author meta: `credential_url` → LinkedIn/About page
   - If theme doesn't support, mention in bio

4. **Verify E-E-A-T**
   - Ensure each author demonstrates:
     - **Experience** (years, past roles)
     - **Expertise** (credentials, publications)
     - **Authoritativeness** (recognized in field)
     - **Trustworthiness** (transparent bio, contact info)

5. **Log Completion**
   - Update `/ops/run-log.md`:
     ```markdown
     ### Authors & Policies Updated
     - **Date:** 2025-11-12
     - **Authors Updated:** Jane Doe, John Smith (2 total)
     - **Status:** ✅ Authors complete
     ```

---

## Step 4: Create/Verify Policy Pages

### Required Pages:

| Page Title | Slug | Purpose |
|------------|------|---------|
| **About** | `/about/` | Mission, team, credentials |
| **Editorial Policy** | `/editorial-policy/` | Standards, fact-checking, corrections process |
| **AI Disclosure** | `/ai-disclosure/` | How AI is used (research, drafting, editing) |
| **Ethics & Disclosures** | `/ethics-disclosures/` | Conflicts of interest, funding, transparency |
| **Corrections Policy** | `/corrections/` | How errors are corrected and disclosed |
| **Privacy Policy** | `/privacy/` | GDPR/CCPA compliance, data usage |
| **Terms of Use** | `/terms/` | Legal terms, disclaimers |
| **Contact** | `/contact/` | Email, form, or social links |

### In WordPress Admin:

1. **Check Existing Pages**
   - `WP Admin → Pages → All Pages`
   - Note which pages already exist

2. **Create Missing Pages**
   - `Pages → Add New`
   - **Title:** (from table above)
   - **Permalink:** (click "Edit" next to URL → use slug from table)
   - **Content:**
     - Use templates from `/policies/` folder in GitHub repo
     - Or write 200-500 words covering key points
   - **Publish**

3. **Use Templates (if available)**
   - Check `/policies/editorial-policy.md`, `/policies/ai-disclosure.md`, etc. in GitHub
   - Copy content → paste into WordPress editor
   - Adjust formatting (add headings, bullets, links)

4. **Set Page Attributes**
   - **Template:** Default or "Full Width" (no sidebar)
   - **Parent:** None (unless organizing under an "About" parent)

---

## Step 5: Add Policy Links to Footer

### Method 1: Footer Menu (Block Theme or Widget Areas)

1. **Navigate to Menus**
   - `WP Admin → Appearance → Menus`

2. **Create Footer Menu (if doesn't exist)**
   - Click **"Create a new menu"**
   - Name: **"Footer Links"** or **"Legal"**

3. **Add Pages**
   - From left sidebar → **Pages** → check:
     - About, Editorial Policy, AI Disclosure, Ethics & Disclosures, Corrections, Privacy, Terms, Contact
   - Click **"Add to Menu"**

4. **Assign to Footer Location**
   - Under "Menu Settings" → check **"Footer Menu"** or **"Footer Legal"**
   - Click **"Save Menu"**

### Method 2: Site Editor (Block Theme)

1. **Navigate to Site Editor**
   - `WP Admin → Appearance → Editor`

2. **Edit Footer Template**
   - Click **"Footer"** in template parts
   - Add **"Navigation"** block (or **"Page List"** block)
   - Link pages manually or use dynamic page list

3. **Save**
   - Click **"Save"** in top-right

### Verify Footer

- Visit homepage → scroll to footer
- Confirm all 8 policy links are visible and clickable
- Test on mobile view (responsive menu)

### Log Completion

Update `/ops/run-log.md`:
```markdown
### Authors & Policies Updated
- **Date:** 2025-11-12
- **Authors Updated:** Jane Doe, John Smith (2 total)
- **Policy Pages Created:** About, Editorial Policy, AI Disclosure, Ethics & Disclosures, Corrections, Privacy, Terms, Contact (8 total)
- **Footer Menu:** ✅ Added all policy links
- **Status:** ✅ Complete
```

---

## Acceptance Criteria

| Criterion | Status |
|-----------|--------|
| Demo/test posts removed (count logged in deletions.csv) | ☐ |
| Only 8 core categories remain | ☐ |
| Authors have bios, avatars, and credential links | ☐ |
| All 8 policy pages created and published | ☐ |
| Footer menu displays all policy links | ☐ |
| Changes logged in `/ops/run-log.md` | ☐ |

---

## Next Steps

Once all checkboxes are marked, proceed to:
- **[Phase 2 — Pillar & Cluster Hubs Guide](phase-2-hubs-guide.md)**

---

**Estimated Time:** 30-60 minutes
**Owner:** Content Operations + Editorial
**Last Updated:** 2025-11-12
