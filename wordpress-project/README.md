# CNP News - WordPress Project

**Domain:** cnpnews.net/en  
**Tagline:** Clarity in Tech. Confidence in Business.

## Project Overview

This is a high-performance WordPress news website focusing on tech business coverage with four main pillars:
- 🤖 Enterprise AI & Automation
- 🌐 Geopolitics of Tech & Commerce  
- 💰 Financial Tech & Investment
- 🛠️ Foundational Tech & Infrastructure

## Tech Stack

- **CMS:** WordPress 6.4+ with Gutenberg
- **Server:** Nginx/Apache + PHP 8.x + MySQL/MariaDB
- **CDN:** Cloudflare with APO
- **Theme:** Custom Gutenberg-based (NO page builders)
- **Performance:** Core Web Vitals optimized (LCP < 2.5s, INP < 200ms)
- **Automation:** n8n workflows for content ingestion
- **Analytics:** GA4 + Google Search Console

## Project Structure

```
wordpress-project/
├── config/              # Server and WordPress configuration
├── theme/               # Custom CNP News theme
├── plugins/             # Essential plugins configuration
├── content/             # Sample content and templates
├── automation/          # n8n workflow configurations
├── scripts/             # Deployment and maintenance scripts
└── docs/               # Implementation documentation
```

## Key Requirements

- **E-E-A-T Compliance:** Experience, Expertise, Authoritativeness, Trustworthiness
- **Value-Add Content:** All aggregated content requires unique analysis
- **Performance First:** Core Web Vitals compliance mandatory
- **SEO Excellence:** Technical SEO, structured data, sitemaps
- **Editorial Quality:** Human oversight for all AI-assisted content

## Quick Start

1. Follow `/config/server-setup.md` for infrastructure
2. Use `/config/wordpress-config.php` for WP setup
3. Install custom theme from `/theme/`
4. Configure plugins per `/plugins/plugin-list.md`
5. Set up automation via `/automation/`

## Success Metrics

- ≥80% pages pass Core Web Vitals
- Zero SEO errors in Google Search Console
- >95% content published without corrections
- Growing organic traffic and engagement

---

**Project Lead:** WordPress Developer  
**Started:** November 2024  
**Status:** In Development
