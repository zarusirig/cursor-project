# CNP News - Next Steps Action Plan

**Date:** November 11, 2025  
**Status:** Code Complete, Ready for Deployment  
**Your Mission:** Take this from local development to live production

---

## 🎯 EXECUTIVE DECISION REQUIRED

**Before you start, choose your path:**

### Path A: Quick Launch (1 Week)
- **Time:** 40 hours
- **Cost:** $200-500
- **Result:** Basic functional site
- **Best for:** Getting started quickly

### Path B: Quality Launch (3 Weeks) ⭐ RECOMMENDED
- **Time:** 110 hours
- **Cost:** $500-2,000
- **Result:** Professional, growth-ready site
- **Best for:** Serious project with staying power

### Path C: Perfect Launch (2 Months)
- **Time:** 200+ hours
- **Cost:** $2,000-5,000
- **Result:** Fully-featured operation
- **Best for:** Well-funded, patient approach

---

## 📋 PATH B: QUALITY LAUNCH (DETAILED PLAN)

### WEEK 1: INFRASTRUCTURE & SETUP

#### Day 1: Server Provisioning (6 hours)

**Morning (3 hours): Sign Up & Create Server**
1. Go to DigitalOcean.com (or Linode, AWS Lightsail)
2. Create account
3. Provision Droplet:
   - OS: Ubuntu 22.04 LTS
   - Plan: $24/month (2GB RAM, 50GB SSD)
   - Datacenter: Choose nearest to target audience
   - Add SSH key (generate if needed)
   - Create Droplet
4. Note the IP address

**Afternoon (3 hours): Server Configuration**
1. SSH into server: `ssh root@YOUR_IP`
2. Follow `/wordpress-project/config/server-setup.md` step by step:
   - Update system: `apt update && apt upgrade -y`
   - Install Nginx
   - Install PHP 8.2 with extensions
   - Install MySQL/MariaDB
   - Configure firewall (ufw)
   - Set up security headers
3. Test: Visit `http://YOUR_IP` (should see Nginx welcome page)

**Resources:**
- Server setup guide: `/wordpress-project/config/server-setup.md`
- Cost: $24/month
- Difficulty: Medium (follow the guide carefully)

---

#### Day 2: WordPress Installation (5 hours)

**Morning (3 hours): WordPress Setup**
1. Follow `/wordpress-project/config/wordpress-install.md`:
   - Download WordPress: `wget https://wordpress.org/latest.tar.gz`
   - Extract to `/var/www/html/`
   - Create MySQL database and user
   - Configure permissions
   - Create `wp-config.php` from template
2. Point domain to server IP:
   - Update A record: `cnpnews.net` → `YOUR_IP`
   - Update A record: `en.cnpnews.net` → `YOUR_IP`
   - Wait for DNS propagation (15 min - 24 hours)
3. Run WordPress installation wizard

**Afternoon (2 hours): SSL & Security**
1. Install Certbot: `apt install certbot python3-certbot-nginx`
2. Get SSL certificate: `certbot --nginx -d cnpnews.net -d en.cnpnews.net`
3. Verify HTTPS works: Visit `https://cnpnews.net`
4. Force HTTPS in WordPress settings
5. Update wp-config.php with security keys

**Resources:**
- WordPress install guide: `/wordpress-project/config/wordpress-install.md`
- SSL tutorial: Built into Certbot
- Cost: $0 (Let's Encrypt is free)

---

#### Day 3: Theme Deployment & Configuration (4 hours)

**Morning (2 hours): Deploy Theme**
1. Connect via SFTP (FileZilla, Cyberduck, or command line)
2. Upload theme folder:
   - Local: `/Users/surajgiri/Downloads/docs/wordpress-project/theme/cnp-news-theme/`
   - Server: `/var/www/html/wp-content/themes/cnp-news-theme/`
3. Set correct permissions:
   ```bash
   chown -R www-data:www-data /var/www/html/wp-content/themes/cnp-news-theme
   chmod -R 755 /var/www/html/wp-content/themes/cnp-news-theme
   ```
4. Activate theme in WordPress admin

**Afternoon (2 hours): Plugin Installation**
1. Log into WordPress admin: `https://cnpnews.net/wp-admin`
2. Install essential plugins from `/wordpress-project/plugins/plugin-list.md`:
   - Wordfence Security
   - WP Super Cache (or W3 Total Cache)
   - Yoast SEO (or Rank Math)
   - Classic Editor (if needed)
   - Redirection
3. Configure each plugin (follow plugin-list.md notes)
4. Test: Visit homepage, check for errors

**Resources:**
- Plugin list: `/wordpress-project/plugins/plugin-list.md`
- Theme files: Already on your machine
- Cost: $0 (all free plugins for now)

---

### WEEK 2: CONTENT CREATION

#### Day 4: Category Structure & Pages (3 hours)

**Morning (1 hour): Create Categories**
1. In WordPress admin, go to Posts → Categories
2. Create 8 main categories:
   - Strategy & Analysis
   - Artificial Intelligence
   - Startups & Capital
   - Policy & Regulation
   - Fintech & Markets
   - Reviews & Tools
   - Cybersecurity
   - Career & Learning
3. Add descriptions to each (50-100 words)
4. Note category IDs for later

**Afternoon (2 hours): Create Essential Pages**
1. Create these pages in WordPress:
   - About CNP News
   - Contact
   - Privacy Policy (use `/legal/privacy-policy-draft.md`)
   - Terms of Use (use `/legal/terms-of-use-draft.md`)
   - Editorial Policy (use `/policies/editorial-policy.md`)
   - AI Disclosure (use `/policies/ai-disclosure.md`)
2. Add to footer menu
3. Test all links

**Resources:**
- IA Blueprint: `/product/ia-blueprint.md`
- Legal drafts: `/legal/` folder
- Policy docs: `/policies/` folder

---

#### Days 5-7: Content Sprint (30 hours total)

**Goal: Create 20-30 Initial Articles**

**Daily Schedule (10 hours/day):**

**Content Types Needed:**
- 8 articles: One for each category (introduce the category)
- 12 articles: Mix of news analysis and evergreen content
- 4 articles: Product reviews (activate review system)
- 6 articles: In-depth guides/explainers

**Writing Process (per article, ~2 hours):**
1. **Research (30 min):**
   - Pick topic from target category
   - Research using Google, industry sites
   - Find 3-5 credible sources
   
2. **Write (60 min):**
   - Headline (50-60 chars)
   - Introduction (2-3 paragraphs)
   - Body (800-1,500 words)
   - Conclusion with key takeaways
   - Follow value-add checklist: `/ops/value-add-checklist.md`
   
3. **Format (15 min):**
   - Add featured image (use Unsplash, Pexels for free)
   - Format with headings (H2, H3)
   - Add internal links (to other articles/categories)
   - Add external source links
   - Use block patterns where appropriate
   
4. **Optimize (15 min):**
   - Write meta description (150-160 chars)
   - Add focus keyphrase
   - Assign category and 2-3 tags
   - Set excerpt
   - Preview on mobile and desktop

**Content Shortcuts:**
- Use AI tools (Claude, ChatGPT) for draft generation
- Always add human analysis and context (value-add requirement)
- Repurpose press releases with commentary
- Curate round-ups ("5 AI Tools to Watch")
- Interview format with Q&A

**Resources:**
- Value-add checklist: `/ops/value-add-checklist.md`
- Publishing checklist: `/ops/publishing-checklist.md`
- Page templates: `/product/page-templates.md`
- Free images: unsplash.com, pexels.com

**Cost Options:**
- DIY: $0 but 30 hours of work
- Freelance writers: $25-100/article = $500-3,000 total
- Mix: Write 10 yourself, outsource 20 = $500-2,000

---

### WEEK 3: OPTIMIZATION & LAUNCH

#### Day 8: Author Profiles & E-E-A-T (4 hours)

**Morning (2 hours): Create Author Profiles**
1. In WordPress, Users → Add New
2. For each author, add:
   - Full name
   - Professional bio (150-200 words)
   - Credentials and expertise
   - LinkedIn profile URL
   - Twitter/X handle
   - Professional headshot
3. Fill out E-E-A-T custom fields (if theme supports):
   - Years of experience
   - Relevant certifications
   - Published works
   - Areas of expertise

**Afternoon (2 hours): Apply to Articles**
1. Edit each article
2. Assign correct author
3. Verify author bio appears at bottom
4. Add author schema to articles
5. Test author archive pages

**Resources:**
- Editorial policy: `/policies/editorial-policy.md`
- Author schema: See Person schema in `/product/schema-implementation.md`

---

#### Day 9: Pillar Hub Pages (8 hours)

**Goal: Create 4 Comprehensive Hub Pages**

**For Each Hub (2 hours):**
1. **Enterprise AI & Automation Hub**
2. **Geopolitics of Tech & Commerce Hub**
3. **Financial Tech & Investment Hub**
4. **Foundational Tech & Infrastructure Hub**

**Hub Page Structure (1,500-2,500 words each):**
- Hero section with value proposition
- Overview of the topic (200-300 words)
- Why it matters (200-300 words)
- Key subtopics (300-500 words)
- Latest articles in this hub (automated query)
- Related hubs and categories
- Newsletter signup CTA

**Internal Linking:**
- Link to all articles in the hub
- Link hubs to each other
- Link back to hub from articles

**Resources:**
- IA Blueprint: `/product/ia-blueprint.md`
- Hero pattern: `/wordpress-project/theme/cnp-news-theme/patterns/hero-feature.php`

---

#### Day 10: Analytics & Monitoring (4 hours)

**Morning (2 hours): Google Analytics 4**
1. Go to analytics.google.com
2. Create new GA4 property: "CNP News"
3. Select Web platform
4. Get Measurement ID (G-XXXXXXXXXX)
5. Add to wp-config.php:
   ```php
   define('CNP_GA4_ID', 'G-XXXXXXXXXX');
   ```
6. Install GA4 plugin (or use CAOS for local hosting)
7. Test: Visit site, check Real-time report in GA4
8. Verify custom events fire:
   - Scroll to 25%, 50%, 75%
   - Stay on page 30s, 60s
   - Click related article

**Afternoon (2 hours): Google Search Console**
1. Go to search.google.com/search-console
2. Add property: cnpnews.net
3. Verify ownership (DNS TXT record or HTML file)
4. Submit sitemap: `https://cnpnews.net/sitemap_index.xml`
5. Check URL inspection for 5-10 pages
6. Set up email alerts
7. Monitor for crawl errors

**Resources:**
- GA4 setup: `/analytics/ga4-setup.md`
- GSC setup: `/analytics/gsc-setup.md`
- Analytics code: Already in theme (`inc/analytics.php`)

---

#### Day 11: SEO Completion (6 hours)

**Morning (3 hours): Schema Markup**
1. Install Rich Results Test extension
2. Test each page type:
   - Article pages (NewsArticle schema)
   - Review pages (Review schema)
   - Author pages (Person schema)
   - Home page (Organization schema)
3. Fix any errors found
4. Add breadcrumb schema
5. Add FAQ schema to relevant articles
6. Validate in Google Rich Results Test

**Afternoon (3 hours): Technical SEO**
1. Generate XML sitemap (Yoast/Rank Math)
2. Create robots.txt:
   ```
   User-agent: *
   Allow: /
   Disallow: /wp-admin/
   Disallow: /wp-includes/
   Sitemap: https://cnpnews.net/sitemap_index.xml
   ```
3. Add canonical tags to paginated archives
4. Test all internal links (Broken Link Checker plugin)
5. Optimize top 10 pages:
   - Meta titles
   - Meta descriptions
   - Focus keyphrases
   - Image alt text
6. Set up redirects for common URLs

**Resources:**
- Schema guide: `/product/schema-implementation.md`
- Pagination: `/product/pagination-canonicals.md`
- Rich Results Test: search.google.com/test/rich-results

---

#### Day 12: Cloudflare & CDN (3 hours)

**Morning (2 hours): Cloudflare Setup**
1. Sign up at cloudflare.com (free plan is fine)
2. Add site: cnpnews.net
3. Update nameservers at domain registrar
4. Wait for activation (5-30 minutes)
5. Configure settings:
   - SSL/TLS: Full (strict)
   - Always Use HTTPS: On
   - Auto Minify: On (CSS, JS, HTML)
   - Brotli: On
   - Rocket Loader: Off (can cause issues)
   - Polish (Image Optimization): Lossless
   - Mirage: On

**Afternoon (1 hour): Performance Rules**
1. Set up Page Rules:
   - Cache Everything for static assets
   - Browser Cache TTL: 4 hours
2. Enable Cloudflare APO (Automatic Platform Optimization) - $5/month
3. Purge cache
4. Test: Visit site, check response headers
5. Run PageSpeed Insights: Should see improvement

**Cost:** $0 (free plan) or $5/mo (APO recommended)

---

#### Day 13: Performance Optimization (6 hours)

**Morning (3 hours): Image Optimization**
1. Install image optimization plugin (ShortPixel, Imagify, or EWWW)
2. Bulk optimize all existing images
3. Set future uploads to auto-optimize
4. Convert to WebP format
5. Implement lazy loading (built into WordPress 5.5+)
6. Preload LCP images on key pages

**Afternoon (3 hours): Caching & Speed**
1. Configure WP Super Cache or W3 Total Cache:
   - Enable page caching
   - Enable browser caching
   - Enable GZIP compression
   - Minify HTML, CSS, JS
2. Implement object caching (Redis/Memcached) - optional but recommended
3. Defer non-critical JavaScript
4. Inline critical CSS (above-the-fold)
5. Remove unused CSS/JS
6. Optimize database (WP-Optimize plugin)

**Testing:**
1. Run Lighthouse audit (should hit 90+)
2. Test on PageSpeed Insights (mobile & desktop)
3. Verify Core Web Vitals:
   - LCP < 2.5s ✅
   - INP < 200ms ✅
   - CLS < 0.1 ✅

**Resources:**
- Performance guide: `/dev/performance-hardening.md`
- Theme already optimized, just need server-side caching

---

#### Day 14: QA & Pre-Launch Testing (8 hours)

**Morning (4 hours): Functionality Testing**

**Use checklist from:** `/wordpress-project/PHASE-5-TESTING-LAUNCH.md`

1. **Cross-Browser Testing:**
   - Chrome (Mac, Windows, Android)
   - Safari (Mac, iOS)
   - Firefox (Mac, Windows)
   - Edge (Windows)

2. **Device Testing:**
   - Desktop (1920x1080, 2560x1440)
   - Laptop (1366x768)
   - Tablet (iPad, Android tablet)
   - Mobile (iPhone, Android phones)

3. **Feature Testing:**
   - [ ] Homepage loads correctly
   - [ ] All templates work (article, category, search, 404)
   - [ ] Navigation functions (desktop & mobile)
   - [ ] Dark mode toggle works
   - [ ] Search returns results
   - [ ] Newsletter signup works
   - [ ] Related articles appear
   - [ ] Comments work (if enabled)
   - [ ] Forms submit correctly
   - [ ] Social sharing works

**Afternoon (4 hours): Accessibility & SEO Audit**

1. **Accessibility (WCAG AA):**
   - Install axe DevTools extension
   - Run audit on 10 key pages
   - Fix critical/serious issues
   - Test keyboard navigation (Tab key only)
   - Test with screen reader (NVDA, VoiceOver)
   - Verify color contrast (4.5:1 minimum)
   - Check all images have alt text
   - Verify form labels

2. **SEO Audit:**
   - All pages have unique meta titles
   - All pages have unique meta descriptions
   - All images have alt text
   - Internal linking present
   - No broken links (404s)
   - Schema validates (Rich Results Test)
   - Sitemap submitted to GSC
   - robots.txt correct

3. **Performance Audit:**
   - Lighthouse score 90+ on key pages
   - Core Web Vitals all green
   - Mobile-friendly test passes
   - HTTPS everywhere (no mixed content)

**Create Testing Report:**
- Document any issues found
- Prioritize: Critical, High, Medium, Low
- Fix all critical and high priority issues
- Plan fixes for medium/low post-launch

---

#### Day 15: LAUNCH DAY 🚀 (4 hours)

**Pre-Launch Checklist (1 hour):**
```
Infrastructure:
[ ] Server running smoothly
[ ] SSL certificate valid
[ ] DNS propagated
[ ] Cloudflare active
[ ] Backups configured

Content:
[ ] 30+ articles published
[ ] 8 categories created
[ ] 4 Pillar Hubs live
[ ] All essential pages live (About, Contact, Privacy, Terms)
[ ] Author profiles complete

Technical:
[ ] Theme activated
[ ] Plugins configured
[ ] Analytics tracking
[ ] Search Console verified
[ ] Sitemaps submitted
[ ] Schema validated

Legal & Compliance:
[ ] Privacy Policy published
[ ] Terms of Use published
[ ] Cookie consent (if required)
[ ] Affiliate disclosures in place

Performance:
[ ] Core Web Vitals passing
[ ] Lighthouse score 90+
[ ] Mobile-friendly
[ ] CDN active
[ ] Caching configured

QA:
[ ] Cross-browser tested
[ ] Mobile tested
[ ] All links working
[ ] Forms tested
[ ] No critical bugs
```

**Launch Procedure (30 min):**
1. Final backup of database and files
2. Clear all caches (server, plugin, CDN)
3. Test 5 key pages as final check
4. Announce launch:
   - Social media posts
   - Email to subscribers (if any)
   - Submit to relevant directories
5. Monitor in real-time:
   - GA4 Real-time report
   - Server logs
   - Error logs

**Post-Launch Monitoring (2.5 hours):**

**First Hour:**
- Watch GA4 real-time (are visitors coming?)
- Check server load (is it handling traffic?)
- Monitor error logs (any PHP errors?)
- Test key user flows (article reading, navigation)
- Respond to any critical issues immediately

**Hour 2-4:**
- Check GSC for crawl errors
- Verify pages are indexing
- Monitor performance metrics
- Check social media for feedback
- Make notes of any issues to fix

**Resources:**
- Launch checklist: `/wordpress-project/PHASE-5-TESTING-LAUNCH.md`

---

## 🎉 POST-LAUNCH (Weeks 4+)

### Week 4: Monitor & Optimize

**Daily Tasks (30 min/day):**
- Check analytics (visitors, top pages)
- Monitor GSC for errors
- Check server health
- Review comments/feedback
- Publish 1-2 new articles

**Weekly Tasks (2 hours/week):**
- Analyze top performing content
- Optimize underperforming pages
- Update outdated content
- Build backlinks (guest posts, outreach)
- A/B test headlines using playbook

**Resources:**
- Headline CTR playbook: `/ops/headline-ctr-playbook.md`
- Recirculation playbook: `/ops/recirculation-playbook.md`

---

### Weeks 5-8: Growth & Automation

**Content Growth:**
- Aim for 3-5 articles per week
- Expand Pillar Hubs (add more cluster content)
- Build topic clusters around key terms
- Guest posts and collaborations

**Set Up n8n Automation:**
1. Install n8n (self-hosted or cloud)
2. Follow `/automation/n8n-flow-news-intake.md`
3. Configure RSS sources
4. Build content pipeline
5. Test automated drafts
6. Launch automated news intake

**Monetization:**
- Join affiliate programs (Amazon, ShareASale, CJ Affiliate)
- Add affiliate links to review content
- Ensure proper disclosures (already in theme)
- Track conversions in GA4

**Resources:**
- n8n overview: `/automation/n8n-overview.md`
- n8n workflow: `/automation/n8n-flow-news-intake.md`
- Affiliate policy: `/policies/sponsored-affiliate-policy.md`

---

### Month 3+: Scale

**Hire Team:**
- Content writer(s) - $500-2,000/month
- Editor - $1,000-3,000/month
- Social media manager - $500-1,500/month

**Advanced Features:**
- Email newsletter (ConvertKit, Mailchimp)
- Advanced search (Algolia)
- Membership/subscriptions
- Comments community
- Video content
- Podcasts

**Success Metrics:**
- 10,000+ monthly visitors
- 500+ email subscribers
- $500-2,000 monthly affiliate revenue
- 100+ articles published
- Top 10 rankings for key terms

---

## 💰 BUDGET SUMMARY

### Months 1-3 Costs

**One-Time:**
- Legal review: $300-1,000
- Initial content (if outsourced): $500-2,000
- Total: $800-3,000

**Monthly Recurring:**
- VPS hosting: $24
- Cloudflare APO: $5
- Image optimization: $10
- Email service: $20
- Backup service: $10
- **Total per month:** ~$70

**Optional:**
- Freelance writers: $500-2,000/month
- n8n cloud: $20/month
- Premium plugins: $10-50/month

---

## ⏱️ TIME TRACKING

| Week | Focus Area | Hours | Cumulative |
|------|------------|-------|------------|
| Week 1 | Infrastructure & Setup | 22 | 22 |
| Week 2 | Content Creation | 40 | 62 |
| Week 3 | Optimization & Launch | 35 | 97 |
| Week 4+ | Ongoing Operations | 10/week | N/A |

**Total to Launch:** ~100 hours over 3 weeks

---

## 📊 SUCCESS METRICS

### Week 1 Goals
- [ ] Site live and accessible
- [ ] SSL working
- [ ] Theme activated
- [ ] Basic content (5+ articles)

### Week 2 Goals
- [ ] 30+ articles published
- [ ] 8 categories created
- [ ] Analytics tracking
- [ ] Essential pages live

### Week 3 Goals
- [ ] Pillar Hubs complete
- [ ] SEO optimized
- [ ] Performance 90+ Lighthouse
- [ ] All QA passed
- [ ] LAUNCHED! 🚀

### Month 1 Goals
- [ ] 50+ articles
- [ ] 1,000+ visitors
- [ ] 50+ email subscribers
- [ ] Indexed in Google
- [ ] No critical issues

### Month 3 Goals
- [ ] 100+ articles
- [ ] 10,000+ visitors
- [ ] 500+ email subscribers
- [ ] $100-500 revenue
- [ ] Automation running

---

## 🎓 TIPS FOR SUCCESS

### Do's ✅
- Follow the checklists religiously
- Test on mobile constantly
- Publish consistently (quality > quantity)
- Monitor analytics daily
- Backup before major changes
- Document everything you do
- Focus on value-add content
- Build email list from day one
- Engage with your audience
- Be patient with growth

### Don'ts ❌
- Don't skip the legal pages
- Don't publish without QA
- Don't ignore performance
- Don't over-optimize for SEO (write for humans)
- Don't copy content (always add value)
- Don't ignore security updates
- Don't forget backups
- Don't expect overnight success
- Don't launch without analytics
- Don't go live with < 20 articles

---

## 🆘 HELP & RESOURCES

### When You Get Stuck

**Technical Issues:**
- WordPress Support Forums: wordpress.org/support
- Stack Overflow: stackoverflow.com
- DigitalOcean Community: digitalocean.com/community

**Content Help:**
- SEMrush Writing Assistant
- Grammarly for editing
- Hemingway Editor for readability
- AI tools (Claude, ChatGPT) for drafts

**SEO Tools:**
- Google Search Console
- Ahrefs (paid) or Ubersuggest (free)
- Answer The Public (content ideas)
- Google Trends

**Performance:**
- PageSpeed Insights
- GTmetrix
- WebPageTest
- Chrome DevTools

### Project Documentation
All your documentation is in `/Users/surajgiri/Downloads/docs/`:
- Server setup: `wordpress-project/config/`
- Policies: `policies/`
- Operations: `ops/`
- Product specs: `product/`
- Automation: `automation/`
- Analytics: `analytics/`

---

## ✅ YOUR IMMEDIATE NEXT STEPS

**Right Now (Next 2 Hours):**

1. **Decision:** Which path are you taking? (A, B, or C)
2. **Budget:** Allocate funds (minimum $200-500)
3. **Time:** Block out calendar (40-110 hours over 1-3 weeks)
4. **Account:** Sign up for DigitalOcean or hosting provider
5. **Start:** Day 1, Step 1 - Provision that server!

**Tomorrow:**
- Day 2: Install WordPress
- Set up SSL
- Deploy theme

**This Week:**
- Complete Week 1 tasks
- Have live (but empty) site by end of week

**Next 2 Weeks:**
- Write/source content
- Configure everything
- Launch!

---

## 🎯 THE BOTTOM LINE

You have everything you need:
- ✅ Perfect theme (built and tested)
- ✅ Complete documentation (every step documented)
- ✅ Clear plan (this action plan)
- ✅ Working code (production-ready)

All you need is:
- ⏱️ Time (100 hours over 3 weeks)
- 💰 Money ($200-500 minimum)
- 🎯 Focus (follow this plan exactly)
- 🚀 Action (just start!)

**The hardest part is done. Now just execute.**

---

**Your Action:** Open DigitalOcean.com right now and create an account. That's step 1. Everything else follows.

🚀 **Let's launch CNP News!**

---

**Generated:** November 11, 2025  
**Plan Type:** Detailed 21-Day Launch Plan  
**Status:** Ready to Execute  
**Next Action:** Provision server (Day 1, Step 1)

