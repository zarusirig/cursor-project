# Visual Design System — cnpnews.net/en

*Last updated: {{DATE}}*

**Brand:** cnpnews.net — **Clarity in Tech. Confidence in Business.**
**Goal:** A trustworthy, modern news look that reads fast, scales globally, and maps 1:1 to our block theme tokens.

---

## 1) Brand → UI Principles

* **Authoritative, accessible, rigorous.** No hype; let data and clear language lead.
* **Readability first.** Comfortable line length, generous line-height, consistent rhythm.
* **Calm surfaces.** White/ink or deep navy backgrounds; sparing use of color for signals.
* **Performance-aware visuals.** Imagery enhances understanding; never at the expense of speed.
* **Dark-mode parity.** All colors, elevations, and states mapped for dark.

---

## 2) Design Tokens (map to `theme.json` and CSS vars)

### 2.1 Color Palette

**Light mode**

* `--cnp-bg` `#FFFFFF` — page background
* `--cnp-surface` `#F8FAFC` — cards/sections
* `--cnp-ink` `#0B1220` — primary text
* `--cnp-muted` `#6B7280` — secondary text
* `--cnp-border` `#E5E7EB` — dividers
* `--cnp-primary` `#1D4ED8` — brand action/links
* `--cnp-accent` `#10B981` — positive signals
* `--cnp-warn` `#F59E0B` — caution
* `--cnp-danger` `#DC2626` — destructive

**Dark mode**

* `--cnp-bg` `#0B1220`
* `--cnp-surface` `#0F172A`
* `--cnp-ink` `#F8FAFC`
* `--cnp-muted` `#9CA3AF`
* `--cnp-border` `#1F2937`
* `--cnp-primary` `#60A5FA`
* `--cnp-accent` `#34D399`
* `--cnp-warn` `#FBBF24`
* `--cnp-danger` `#F87171`

**Usage rules**

* Headlines/body = `ink`; avoid pure black on white to reduce glare.
* Links default `primary`; visited is a 10% darker variant; hover underline only.
* Never communicate meaning by color alone; pair with icon/text.

### 2.2 Typography

* **Headlines:** Newsreader, serif (fallback ui-serif, Georgia). Optical sizes where available.
* **Body/UI:** Inter, sans (fallback system-ui).
* **Line-height:** 1.3–1.35 for H1–H3; 1.6 for body.
* **Scale (fluid):**

  * xs 12 → clamp(0.72rem, 0.62rem + 0.4vw, 0.75rem)
  * sm 14 → clamp(0.82rem, 0.72rem + 0.4vw, 0.875rem)
  * base 16 → clamp(0.95rem, 0.84rem + 0.5vw, 1rem)
  * md 18 → clamp(1.05rem, 0.92rem + 0.6vw, 1.125rem)
  * lg 20 → clamp(1.15rem, 1.0rem + 0.7vw, 1.25rem)
  * xl 24 → clamp(1.35rem, 1.14rem + 0.9vw, 1.5rem)
  * 2xl 30 → clamp(1.6rem, 1.35rem + 1.1vw, 1.875rem)
  * 3xl 36 → clamp(1.85rem, 1.56rem + 1.3vw, 2.25rem)
  * 4xl 48 → clamp(2.2rem, 1.9rem + 1.8vw, 3rem)
  * 5xl 60 → clamp(2.6rem, 2.2rem + 2.2vw, 3.75rem)

**Editorial rules**

* H1 once per page; H2/H3 in logical order; avoid H4+ unless necessary.
* Max body width 68–74ch.

### 2.3 Spacing & Layout

* **Spacing scale:** 4 / 8 / 12 / 16 / 24 / 32 / 40 / 48
* **Radii:** 8 (cards), 9999 (pills)
* **Shadows:** subtle elevation for cards (`0 2px 12px rgba(0,0,0,0.06)` light; `0 2px 16px rgba(0,0,0,0.32)` dark)
* **Motion:** 150–200ms ease-out; use `prefers-reduced-motion` to disable nonessential transitions.

### 2.4 Grid & Breakpoints

* **Breakpoints:** xs 360, sm 640, md 768, lg 1024, xl 1280, 2xl 1536
* **Containers (max widths):** 100% / 640 / 768 / 1024 / 1200 / 1320
* **Grid:** 12-column desktop; 6 on tablet; 4 on mobile. Gutter 16/24/32 (mobile/tablet/desktop).

---

## 3) Components (specs & states)

### 3.1 Header (sticky)

* Height 56–64px; blur background when scrolled; 1px border using `--cnp-border`.
* Left: logo (home), Center: mega-nav trigger (Categories + Pillar hubs), Right: search, theme toggle, newsletter CTA.
* **States:** hover underline for links; focus visible outline 2px (`--cnp-primary`).

### 3.2 Navigation / Mega-menu

* Two columns: **Categories** and **Pillar Hubs** with brief descriptors.
* Opens on click; focus trapped; closes with ESC; caret key navigation.
* Mobile: full-screen sheet; large tap targets.

### 3.3 Search

* Input with 44px min height; icon button; instant results panel (optional, progressive enhancement).
* Result item: small thumbnail (1:1), title clamp 2 lines, category pill, time.

### 3.4 Story Cards

* **L (Feature):** 16:9 image, kicker pill, H3, two-line dek, subtle shadow on hover.
* **M (Standard):** 4:3 image, title clamp 2, meta row (category, time).
* **S (List):** 1:1 thumb, title 2-line clamp; used in sidebars and lists.
* **States:** hover image scale 1.02 (reduced-motion off); focus outline 2px.

### 3.5 Article Elements

* **Key Takeaways** callout: surface background, left accent bar in `--cnp-primary`.
* **Pull-quote:** larger Newsreader, open-quote mark, credit.
* **Source list / citations:** small type with clear separators.
* **AI disclosure:** label chip preceding body if applicable.
* **Affiliate disclosure:** bordered box above fold on Reviews/Comparisons.

### 3.6 Review Widgets

* **Score ribbon:** rounded badge with numeric/label score; color-coded with text description.
* **Pros/Cons:** two-column list desktop; stacked on mobile.
* **Spec table:** striped rows, sticky header on wide screens.
* **Verdict box:** accent top border; summary + CTA.

### 3.7 Live/Tracker

* Sticky mini-TOC at left on desktop; collapsible on mobile.
* Timestamped entries with clear time zone; “Last updated” chip.

### 3.8 Footer

* Masthead (about, editorial policy), quick links (privacy/terms), newsletter signup, social.

---

## 4) Template Blueprints (text wireframes)

### 4.1 Home

1. **Hero Rail** (L card + 4 M cards)
2. **Top Sections**: AI • Fintech & Markets • Strategy & Analysis (3× grid each)
3. **Editors’ Picks** carousel
4. **Latest** list (paginated or infinite)
5. **Newsletter CTA**

### 4.2 Category

* Title + dek → Filters (Latest / Explainers / Reviews) → 3× grid → pagination at bottom.

### 4.3 Article

* Kicker • H1 • Byline/Editor • Datestamps • Read time
* Hero image (responsive picture)
* Key Takeaways
* Body with h2/h3 rhythm; related-in-hub block after section 2
* Sources/citations; disclosure; newsletter; related stories

### 4.4 Review/Comparison

* Title + Score ribbon → Pros/Cons → Spec table → Alternatives → Verdict → Disclosure.

### 4.5 Live/Tracker

* Sticky TOC • Update stream with timestamps • Pinned context panel.

---

## 5) Imagery & Media

* **Sources:** editorial photos + **fal.ai** conceptual illustrations (never synthetic “documentary” of real events).
* **Aspect ratios:** Feature 16:9; Standard 4:3; List 1:1; In-article images flexible but avoid ultra-wide banners in body.
* **Captions:** always; credit + license.
* **AI labels:** “AI‑generated image for illustration; not a depiction of a real event.”
* **Performance:** provide AVIF/WebP plus correctly sized `img` for LCP; originals (≥1200w) reserved for `og:image` and Discover.

---

## 6) Iconography

* **Set:** Lucide; stroke 1.5; corner radius consistent.
* Sizes: 16/20/24.
* Pair icons with text; never rely on icon color alone.

---

## 7) Data Visualization (lite)

* Axes, grids, and labels always visible; legends outside plot on mobile.
* Palette derived from tokens: primary, accent, muted; ensure ≥ 4.5:1 contrast on text.
* Avoid 3D, heavy gradients, or tiny type.

---

## 8) Tables & Code

* Tables: bordered, zebra rows subtle; header sticky at `lg+`; caption above.
* Code/inline: JetBrains Mono small; use sparingly.

---

## 9) States & Feedback

* **Hover:** subtle elevation/underline;
* **Focus:** 2px outline (`--cnp-primary`), offset 2px; visible for keyboard only.
* **Active/Pressed:** reduce elevation and opacity 0.9.
* **Disabled:** `--cnp-muted` text; 40% opacity.
* **Skeletons:** neutral shimmer bars; respect reduced-motion.

---

## 10) Dark Mode Rules

* Swap to dark tokens; increase border contrast slightly; lighten shadows.
* Images: optional 8–12% wash overlay to limit glare; disable on photos that require color accuracy (charts).
* Persist user choice via `localStorage` but default to `prefers-color-scheme`.

---

## 11) Microcopy & Tone

* Headlines: plain, precise, active voice.
* Dek: 1–2 sentences explaining why it matters.
* Buttons: verbs (“Read analysis”, “Get the briefing”).
* Empty states: supportive (“No results yet—try fewer filters.”).

---

## 12) Accessibility Checklist (visual)

* Text contrast: **WCAG AA** minimum; large text ≥ 3:1.
* Focus states visible on all interactive elements.
* Hit targets ≥ 44×44.
* Motion alternatives via reduced-motion.
* Non-text contrast (borders, focus outlines) ≥ 3:1.

---

## 13) Token-to-Implementation Snippets

### 13.1 CSS Variables (light)

```css
:root{
  --cnp-bg:#fff;--cnp-surface:#F8FAFC;--cnp-ink:#0B1220;--cnp-muted:#6B7280;--cnp-border:#E5E7EB;
  --cnp-primary:#1D4ED8;--cnp-accent:#10B981;--cnp-warn:#F59E0B;--cnp-danger:#DC2626;
}
@media (prefers-color-scheme:dark){
  :root{--cnp-bg:#0B1220;--cnp-surface:#0F172A;--cnp-ink:#F8FAFC;--cnp-muted:#9CA3AF;--cnp-border:#1F2937;
  --cnp-primary:#60A5FA;--cnp-accent:#34D399;--cnp-warn:#FBBF24;--cnp-danger:#F87171}
}
```

### 13.2 Headline & Body classes

```css
.h1{font-family:Newsreader,ui-serif,Georgia,serif;font-size:clamp(2.2rem,1.9rem+1.8vw,3rem);line-height:1.2}
.h2{font-family:Newsreader,ui-serif,Georgia,serif;font-size:clamp(1.85rem,1.56rem+1.3vw,2.25rem);line-height:1.25}
.p{font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;line-height:1.6}
```

### 13.3 Card elevation & states

```css
.card{background:var(--cnp-surface);border:1px solid var(--cnp-border);border-radius:8px;transition:transform .18s ease, box-shadow .18s ease}
.card:hover{transform:translateY(-1px)}
.card:focus-within{outline:2px solid var(--cnp-primary);outline-offset:2px}
```

---

## 14) Acceptance Criteria

* Visual contrast passes AA across light/dark.
* Token coverage: colors, type, spacing, radii, shadows, motion defined and mapped in `theme.json`.
* Component inventory (Header, Cards, Article blocks, Review widgets, Live TOC, Footer) implemented as block patterns.
* Home, Category, Article, Review, Live templates visually match specs.
* Dark mode parity (no invisible text/borders).
* Performance-aware imagery (AVIF/WebP + proper sizes) and font load strategy implemented.

---

## 15) Governance

* **Owner:** Design lead (with Senior WP Dev)
* **Review cadence:** Quarterly or after significant brand/UX changes
* **Changelog:** Maintain version history below.

### Version History

* v1.0 — Initial system ({{DATE}})
