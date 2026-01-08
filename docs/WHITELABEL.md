# 🎨 White-Labeling Guide

## Create Your Own Brand

This guide explains how to customize the Moodle backend with your own branding.

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Quick Start](#quick-start)
3. [Configuration File](#configuration-file)
4. [Logo Requirements](#logo-requirements)
5. [Color Customization](#color-customization)
6. [String Replacements](#string-replacements)
7. [Advanced Customization](#advanced-customization)

---

## Overview

The white-labeling system allows you to:

- ✅ Replace all "Moodle" references with your brand name
- ✅ Customize colors, fonts, and styling
- ✅ Add your logos and icons
- ✅ Configure company information
- ✅ Set up mobile app branding
- ✅ Customize email templates

---

## Quick Start

### Step 1: Edit Configuration

Open `whitelabel-config.json` (located in docs folder or project root):

```json
{
  "brand": {
    "name": "YourBrand",
    "display_name": "YourBrand Learning Platform",
    "tagline": "Empowering Education Through Technology"
  }
}
```

### Step 2: Add Your Logos

Place your logo files in `public/theme/masbrand/pix/`:

```
public/theme/masbrand/pix/
├── logo.png           # Main logo (200x50px)
├── favicon.ico        # Browser icon (32x32px)
└── logo-compact.png   # Mobile logo (40x40px)
```

### Step 3: Apply Branding

Run the apply script from project root:

```bash
php apply-whitelabel.php
```

### Step 4: Rebuild

```bash
docker-compose up -d --build
```

---

## Configuration File

The `whitelabel-config.json` file contains all branding options:

### Brand Section

```json
{
  "brand": {
    "name": "YourBrand",
    "display_name": "YourBrand Learning Platform",
    "short_name": "YB",
    "tagline": "Your tagline here",
    "description": "Platform description",

    "legal": {
      "company_name": "YourBrand Education Inc.",
      "company_address": "123 Main Street, City",
      "company_email": "legal@yourbrand.com",
      "terms_of_service_url": "https://yourbrand.com/terms",
      "privacy_policy_url": "https://yourbrand.com/privacy",
      "copyright_text": "© 2024 YourBrand. All rights reserved."
    }
  }
}
```

### Colors Section

```json
{
  "colors": {
    "primary": {
      "main": "#2563EB",
      "light": "#3B82F6",
      "dark": "#1D4ED8",
      "contrast_text": "#FFFFFF"
    },
    "secondary": {
      "main": "#10B981",
      "light": "#34D399",
      "dark": "#059669"
    },
    "navbar": {
      "background": "#1E293B",
      "text": "#FFFFFF"
    },
    "footer": {
      "background": "#0F172A",
      "text": "#94A3B8"
    },
    "login_page": {
      "gradient_start": "#2563EB",
      "gradient_end": "#7C3AED"
    }
  }
}
```

### Website URLs

```json
{
  "website": {
    "url": "https://yourbrand.com",
    "lms_url": "https://lms.yourbrand.com",
    "support_url": "https://support.yourbrand.com",
    "docs_url": "https://docs.yourbrand.com"
  }
}
```

### Contact Information

```json
{
  "contact": {
    "support_email": "support@yourbrand.com",
    "sales_email": "sales@yourbrand.com",
    "admin_email": "admin@yourbrand.com",
    "support_phone": "+1 (555) 123-4567"
  }
}
```

### Social Media

```json
{
  "social_media": {
    "facebook": "https://facebook.com/yourbrand",
    "twitter": "https://twitter.com/yourbrand",
    "linkedin": "https://linkedin.com/company/yourbrand",
    "instagram": "https://instagram.com/yourbrand",
    "youtube": "https://youtube.com/@yourbrand"
  }
}
```

### Mobile App Settings

```json
{
  "mobile_app": {
    "ios_app_store_url": "https://apps.apple.com/app/yourbrand/id123456789",
    "google_play_url": "https://play.google.com/store/apps/details?id=com.yourbrand.lms",
    "package_name": "com.yourbrand.lms",
    "bundle_id": "com.yourbrand.lms",
    "app_scheme": "yourbrandlms://",
    "default_site_url": "https://lms.yourbrand.com"
  }
}
```

---

## Logo Requirements

### Main Logo (`logo.png`)

- **Size:** 200x50 pixels (recommended)
- **Format:** PNG with transparent background
- **Usage:** Navbar, login page header

### Favicon (`favicon.ico`)

- **Size:** 32x32 pixels
- **Format:** ICO or PNG
- **Usage:** Browser tab icon

### Compact Logo (`logo-compact.png`)

- **Size:** 40x40 pixels
- **Format:** PNG with transparent background
- **Usage:** Mobile navbar, small spaces

### Email Logo (`email-logo.png`)

- **Size:** 200x50 pixels
- **Format:** PNG (no transparency for email compatibility)
- **Usage:** Email headers

---

## Color Customization

### Primary Color

Your main brand color. Used for:

- Buttons
- Links
- Active states
- Progress bars

### Secondary Color

Accent color. Used for:

- Secondary buttons
- Success states
- Highlights

### Navbar Colors

- `background`: Navigation bar background
- `text`: Navigation text color
- `hover`: Hover state background

### Login Page

- `gradient_start`: Top-left gradient color
- `gradient_end`: Bottom-right gradient color

### Finding Your Brand Colors

1. Use your main brand color as `primary.main`
2. Generate lighter/darker shades:
   - Light: 10-15% lighter
   - Dark: 10-15% darker
3. Use a color contrast checker to ensure readability

**Color Tools:**

- [Coolors](https://coolors.co/) - Color palette generator
- [Contrast Checker](https://webaim.org/resources/contrastchecker/) - Accessibility checking

---

## String Replacements

Replace Moodle references with your brand:

```json
{
  "string_replacements": {
    "Moodle": "YourBrand",
    "moodle": "yourbrand",
    "MOODLE": "YOURBRAND",
    "Moodle.org": "yourbrand.com",
    "moodle.org": "yourbrand.com",
    "Moodle Docs": "YourBrand Help Center",
    "Moodle Mobile": "YourBrand App"
  }
}
```

---

## Advanced Customization

### Typography

```json
{
  "typography": {
    "font_family": "Inter, -apple-system, sans-serif",
    "heading_font": "Inter, -apple-system, sans-serif",
    "google_fonts_url": "https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap",
    "base_font_size": "16px"
  }
}
```

**Popular Font Choices:**

- Inter (modern, clean)
- Roboto (Google standard)
- Open Sans (readable)
- Poppins (geometric)

### Features Toggle

```json
{
  "features": {
    "show_moodle_branding": false,
    "show_moodle_docs_links": false,
    "show_powered_by": false,
    "custom_footer": true,
    "custom_login_page": true,
    "enable_guest_access": false,
    "enable_self_registration": true
  }
}
```

### Theme Settings

```json
{
  "theme": {
    "name": "masbrand",
    "parent": "boost",
    "border_radius": "8px",
    "navbar_height": "60px",
    "sidebar_width": "285px"
  }
}
```

---

## Applying Changes

After modifying `whitelabel-config.json`:

1. **Run the apply script:**

   ```bash
   php apply-whitelabel.php
   ```

2. **Review generated files:**

   - `public/theme/masbrand/scss/post.scss` - Styles
   - `public/theme/masbrand/classes/output/core_renderer.php` - Renderer
   - `.env.generated` - Environment template

3. **Rebuild Docker:**

   ```bash
   docker-compose up -d --build
   ```

4. **Clear caches (if already running):**
   ```bash
   docker exec moodle_app php /var/www/html/public/admin/cli/purge_caches.php
   ```

---

## Testing Your Brand

After deployment:

1. **Check login page:**

   - Logo displays correctly
   - Colors match your brand
   - No "Moodle" text visible

2. **Check dashboard:**

   - Navbar shows your logo
   - Colors are consistent

3. **Check footer:**

   - Shows your copyright
   - No Moodle links

4. **Mobile view:**
   - Responsive design works
   - Compact logo displays

---

## Troubleshooting

### Logo not showing

1. Check file exists: `public/theme/masbrand/pix/logo.png`
2. Check file permissions
3. Clear browser cache
4. Purge Moodle caches

### Colors not applying

1. Verify JSON syntax is valid
2. Run `php apply-whitelabel.php` again
3. Rebuild Docker image
4. Clear caches

### Moodle text still visible

1. Add more string replacements
2. Check the core_renderer.php was generated
3. Some deep text may require language pack edits

---

**Make your brand shine! 🌟**
