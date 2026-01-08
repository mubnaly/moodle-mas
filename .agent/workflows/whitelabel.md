---
description: How to apply white-labeling to create a new brand
---

# White-Labeling Workflow

## Overview

This workflow guides you through applying white-label branding to the Moodle backend.

## Prerequisites

- Logo files ready (PNG format, transparent background)
- Brand colors decided
- Company information prepared

// turbo-all

## Steps

### 1. Edit whitelabel-config.json

Open `whitelabel-config.json` and update:

```json
{
  "brand": {
    "name": "YourBrand",
    "display_name": "YourBrand Learning Platform",
    "tagline": "Your tagline"
  },
  "colors": {
    "primary": { "main": "#YOUR_HEX_COLOR" }
  },
  "website": {
    "url": "https://yourbrand.com"
  }
}
```

### 2. Add Logo Files

Place these files in `public/theme/masbrand/pix/`:

- `logo.png` - Main logo (200x50px recommended)
- `favicon.ico` - Browser favicon (32x32px)
- `logo-compact.png` - Small logo for mobile (40x40px)

### 3. Apply White-Label Configuration

Run the apply script:

```bash
php apply-whitelabel.php
```

### 4. Review Generated Files

Check the generated files:

- `public/theme/masbrand/scss/post.scss` - Theme styles
- `public/theme/masbrand/classes/output/core_renderer.php` - Renderer
- `.env.generated` - Environment template

### 5. Update Environment

Rename and customize the environment file:

```bash
mv .env.generated .env
nano .env  # Update passwords
```

### 6. Rebuild and Deploy

```bash
docker-compose up -d --build
```

### 7. Verify Branding

1. Access your site
2. Check login page shows your logo
3. Verify navbar displays your brand
4. Check footer shows your copyright

## Customization Options

### Colors

Edit the `colors` section in whitelabel-config.json:

- `primary.main` - Main brand color
- `secondary.main` - Secondary accent
- `navbar.background` - Navigation bar color
- `login_page.gradient_start/end` - Login background

### Typography

Edit the `typography` section:

- `font_family` - Main font
- `google_fonts_url` - Google Fonts import URL

### Features

Toggle features in `features` section:

- `show_moodle_branding` - Show/hide Moodle references
- `show_powered_by` - Show/hide powered by text
- `enable_guest_access` - Allow guest access

### String Replacements

Add to `string_replacements` to replace text:

```json
"string_replacements": {
  "Moodle": "YourBrand",
  "Moodle Docs": "Help Center"
}
```
