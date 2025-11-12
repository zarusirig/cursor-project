# Phase 0 — Safety & Setup Guide (15-30 min)

**Goal:** Establish backup, editor controls, and operational tracking before making any content changes.

---

## Step 1: Create Backup (REQUIRED FIRST STEP)

### In WordPress Admin:

1. **Navigate to Backup Plugin**
   - `WP Admin → Plugins` OR `WP Admin → Tools → Backup` (varies by backup solution)
   - Common backup plugins: UpdraftPlus, BackWPup, WP Time Capsule, VaultPress

2. **Run Full Backup**
   - Select: **Database + Uploads (Media Library)**
   - **DO NOT** proceed with any other phase until backup completes successfully

3. **Verify Backup**
   - Download backup file OR verify it's saved to cloud storage (Dropbox, Google Drive, S3)
   - Note the **snapshot name/timestamp**

4. **Log Snapshot Information**
   - Open `/ops/run-log.md` (GitHub web editor)
   - Update "Phase 0 — Safety & Setup" section:
     ```
     ### Backup Created
     - **Date:** 2025-11-12 [actual time]
     - **Method:** [Plugin name + version]
     - **Snapshot Name:** [exact name from backup]
     - **Size:** [file size or "Cloud storage"]
     - **Status:** ✅ Complete
     ```

### ⚠️ CRITICAL: Do NOT proceed to Phase 1 without a verified backup.

---

## Step 2: Lock Editor Scope (Optional but Recommended)

### In WordPress Admin:

1. **Navigate to Users**
   - `WP Admin → Users → All Users`

2. **Review Active Users**
   - Identify users with Editor, Administrator, or Author roles
   - **Temporarily disable** or **downgrade** any unknown/inactive accounts

3. **Document Changes**
   - Note which accounts were modified in `/ops/run-log.md`

**Purpose:** Prevent accidental edits during the launch window.

---

## Step 3: Verify Core Settings

### In WordPress Admin:

1. **Permalinks**
   - `Settings → Permalinks`
   - Confirm structure is: `/%postname%/` or `/%category%/%postname%/`
   - **Do NOT change** if already set (will break existing URLs)

2. **Site Title & Tagline**
   - `Settings → General`
   - Verify site title, tagline, and timezone are correct

3. **Reading Settings**
   - `Settings → Reading`
   - Confirm:
     - Homepage displays: **Latest posts** (or a static page if using one)
     - Blog pages show at most: **10 posts** (adjust per preference)
     - Search engine visibility: **UNCHECKED** (unless intentionally blocking)

---

## Step 4: Confirm Branch & Ops Structure (GitHub)

Already completed! ✅ Confirmed:
- Branch: `claude/content-launch-batch-1-011CV47fnmm3tx3dn7s5tkN4`
- `/ops/run-log.md` — comprehensive changelog
- `/ops/sources-inventory.csv` — primary sources tracking
- `/ops/deletions.csv` — removal log

---

## Step 5: Install/Activate Required Plugins (if not already)

### Must-Have Plugins:

| Plugin | Purpose | Verify Active |
|--------|---------|---------------|
| **Yoast SEO** or **Rank Math** | Schema, meta, sitemaps | ☐ |
| **Backup Solution** | UpdraftPlus, BackWPup, etc. | ☐ |
| **Performance** | WP Rocket, W3 Total Cache, LiteSpeed | ☐ |
| **Security** | Wordfence, iThemes Security | ☐ |
| **Custom Post Types** (if needed) | CPT UI or custom plugin | ☐ |

### In WordPress Admin:
1. `Plugins → Installed Plugins`
2. Activate any required plugins
3. **Run basic config** (SEO plugin: verify Organization schema, logo, social profiles)

---

## Step 6: Pre-Flight Checklist

Before moving to Phase 1, confirm:

- ✅ **Backup created** and logged in `/ops/run-log.md`
- ✅ **User accounts** reviewed (optional)
- ✅ **Permalinks** confirmed (do not change)
- ✅ **SEO plugin** active and configured (Organization schema, logo)
- ✅ **Performance/Cache plugin** active (but **clear cache** before QA)
- ✅ **GitHub `/ops/` structure** ready (run-log, sources-inventory, deletions)

---

## Acceptance Criteria

| Criterion | Status |
|-----------|--------|
| Full backup (DB + uploads) created | ☐ |
| Snapshot name logged in `/ops/run-log.md` | ☐ |
| Editor accounts reviewed/locked | ☐ |
| Branches exist: `claude/content-launch-batch-1-011CV47fnmm3tx3dn7s5tkN4` | ✅ |
| `/ops/` folder contains: run-log.md, sources-inventory.csv, deletions.csv | ✅ |

---

## Next Steps

Once all checkboxes are marked, proceed to:
- **[Phase 1 — Clean Up Legacy Content Guide](phase-1-cleanup-guide.md)**

---

**Estimated Time:** 15-30 minutes
**Owner:** Content Operations + WP Admin
**Last Updated:** 2025-11-12
