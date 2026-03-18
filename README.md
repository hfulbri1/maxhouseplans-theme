# MaxHousePlans.com — Genesis Child Theme

> **Theme Name:** Genesis Sample (MaxHousePlans customization)  
> **Parent Theme:** Genesis Framework (StudioPress)  
> **Version:** 2.0.1  
> **Live Site:** https://maxhouseplans.com  
> **Staging Site:** https://maxhousetest.wpengine.com  
> **Hosted on:** WP Engine

---

## Overview

This is the custom Genesis child theme powering **MaxHousePlans.com**, a residential architectural design e-commerce site that sells house plans online. The theme is built on the Genesis Framework by StudioPress and extends it with custom templates, house plan archive pages, a flexslider gallery, and Bootstrap-based responsive layouts.

---

## Theme Structure

```
genesis-sample/
├── css/
│   ├── styles.css              # Main custom styles (beyond style.css)
│   ├── bootstrap.min.css       # Bootstrap grid/components
│   ├── bootstrap-responsive.min.css
│   └── flexslider.css          # FlexSlider carousel styles
├── js/
│   ├── responsive-menu.js      # Mobile nav
│   ├── flexslider-init.js      # FlexSlider initialization
│   ├── jquery.flexslider.js    # FlexSlider library
│   ├── jquery.flexslider-min.js
│   ├── bootstrap.min.js        # Bootstrap JS
│   ├── customtabs.js           # Custom tab behavior
│   ├── ClickTabs.js
│   ├── tab.js
│   └── jquery-1.9.1.min.js    # Bundled jQuery (legacy)
├── images/                     # Theme image assets
├── xml/
│   └── sample.xml              # Sample/demo content
├── old templates/              # Archived .phpa template files (not active)
├── style.css                   # Theme header + main stylesheet
├── functions.php               # Core theme setup, hooks, custom functions
├── front-page.php              # Homepage template
├── archive.php                 # Blog/post archive
├── archive-plans.php           # House plans archive (CPT)
├── archive_pages_plan.php      # Paginated plan archive
├── all-home-plans.php          # All plans listing page template
├── single-plans.php            # Single house plan post template
├── plans1.php                  # Plan category template variant 1
├── plans2.php                  # Plan category template variant 2
├── page_blog.php               # Blog page template
├── page_portfolio.php          # Portfolio page template
├── gallery.php                 # Gallery template
├── album.php                   # Album template
├── test_new_tpl.php            # Dev/test template (do not use in prod)
└── screenshot.png              # Theme screenshot for WP admin
```

---

## Key Features

- **Genesis Framework child theme** — inherits Genesis core layout engine
- **FlexSlider** — homepage image carousel for featured house plans
- **Bootstrap 2.x** — responsive grid and component library
- **Custom Post Type: `plans`** — house plans are stored as CPT with ACF custom fields
- **5-column footer widget areas** — via `genesis-footer-widgets` support
- **Custom image sizes:**
  - `home-featured-posts` — 350×216 (cropped)
  - `home-plan-grid` — 542×334 (cropped)
- **Google Fonts: Lato** (300, 400, 700 weights)
- **Custom nav positioning** — Primary nav moved inside `genesis_header` action

---

## Development Setup

### Requirements
- WordPress 5.x or 6.x
- Genesis Framework 2.x (parent theme — must be installed separately)
- PHP 7.4+
- WP Engine hosting (staging: `maxhousetest`, production: `maxhouseplans`)

### Local Development Options

**Option A: WP Engine DevKit / Local**
1. Clone this repo to your local WordPress `wp-content/themes/genesis-sample/`
2. Make sure Genesis parent theme is installed
3. Activate via WordPress Admin > Appearance > Themes

**Option B: Direct SFTP to WP Engine Staging**
- Host: `maxhousetest.sftp.wpengine.com`
- Port: `2222`
- User: `maxhousetest-vegeta` (or your own WP Engine SFTP user)
- Remote path: `/wp-content/themes/genesis-sample/`

### Deployment
Files are deployed via SFTP to WP Engine. There is no build process currently — all CSS/JS is committed directly. To deploy:

```bash
# Example using rsync (from WSL or Mac/Linux)
rsync -avz --delete ./theme/ maxhousetest-vegeta@maxhousetest.sftp.wpengine.com:/wp-content/themes/genesis-sample/
```

Or use a GUI SFTP client (WinSCP, Transmit, Cyberduck) with the credentials above.

---

## Custom Post Type: House Plans

House plans are stored as the `plans` CPT. Custom fields are managed via **Advanced Custom Fields (ACF)**. Key fields include:

- Plan number / SKU
- Square footage
- Bedrooms / Bathrooms
- Stories
- Garage type
- Style tags (craftsman, cottage, mountain, lake, etc.)
- PDF download links
- Price

See `single-plans.php` and `archive-plans.php` for template logic.

---

## Future Development Goals

- [ ] Modernize theme to use block editor / Full Site Editing (FSE)
- [ ] Improve SEO: structured data (JSON-LD) for house plans
- [ ] Automated PDF ingestion pipeline: upload construction docs → parse → auto-fill ACF fields
- [ ] Image enhancement for house plan photos
- [ ] Keyword-driven SEO blog content strategy
- [ ] Upgrade Bootstrap to v5 and remove legacy jQuery bundle

---

## Git Workflow

This repo tracks the **genesis-sample child theme** only. The Genesis parent theme and WordPress core are **not** tracked here.

```
main       — production-ready code
staging    — staging/WP Engine test environment changes
feature/*  — new features
fix/*      — bug fixes
```

Always test on staging (`maxhousetest.wpengine.com`) before pushing to production (`maxhouseplans.com`).

---

## Initial Commit Note

This repository was initialized on **2026-03-18** by pulling live theme files from WP Engine staging via SFTP. The theme is a working, customized Genesis child theme — not a generic starter. All files reflect the current state of `maxhousetest.wpengine.com`.

---

*Maintained by Max Fulbright / MaxHousePlans.com*
