#!/usr/bin/env php
<?php
/**
 * Moodle White-Label Configuration Applier
 * 
 * This script reads the whitelabel-config.json file and applies the branding
 * to the Moodle installation, generating necessary files and configuration.
 * 
 * Usage: php apply-whitelabel.php [config-file]
 * 
 * @author MyBrand Team
 * @version 1.0.0
 */

// Configuration
$defaultConfigFile = __DIR__ . '/whitelabel-config.json';
$configFile = isset($argv[1]) ? $argv[1] : $defaultConfigFile;

// ==============================================================================
// Check Requirements
// ==============================================================================
if (!file_exists($configFile)) {
    echo "ERROR: Configuration file not found: {$configFile}\n";
    echo "Please create a whitelabel-config.json file or specify the path.\n";
    exit(1);
}

// ==============================================================================
// Load Configuration
// ==============================================================================
echo "Loading white-label configuration from: {$configFile}\n";
$config = json_decode(file_get_contents($configFile), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "ERROR: Invalid JSON in configuration file: " . json_last_error_msg() . "\n";
    exit(1);
}

echo "Brand: {$config['brand']['name']}\n";
echo "Display Name: {$config['brand']['display_name']}\n\n";

// ==============================================================================
// Generate SCSS Variables
// ==============================================================================
echo "Generating SCSS theme file...\n";

$scssContent = <<<SCSS
// ==============================================================================
// {$config['brand']['display_name']} - Theme Styles
// Auto-generated from whitelabel-config.json
// DO NOT EDIT MANUALLY - Run apply-whitelabel.php to regenerate
// ==============================================================================

// Import Google Fonts
@import url('{$config['typography']['google_fonts_url']}');

// ==============================================================================
// Brand Colors
// ==============================================================================
\$brand-primary: {$config['colors']['primary']['main']};
\$brand-primary-light: {$config['colors']['primary']['light']};
\$brand-primary-dark: {$config['colors']['primary']['dark']};
\$brand-primary-contrast: {$config['colors']['primary']['contrast_text']};

\$brand-secondary: {$config['colors']['secondary']['main']};
\$brand-secondary-light: {$config['colors']['secondary']['light']};
\$brand-secondary-dark: {$config['colors']['secondary']['dark']};

\$brand-accent: {$config['colors']['accent']['main']};

// Background colors
\$bg-default: {$config['colors']['background']['default']};
\$bg-paper: {$config['colors']['background']['paper']};
\$bg-dark: {$config['colors']['background']['dark']};

// Text colors
\$text-primary: {$config['colors']['text']['primary']};
\$text-secondary: {$config['colors']['text']['secondary']};
\$text-disabled: {$config['colors']['text']['disabled']};
\$text-inverse: {$config['colors']['text']['inverse']};

// Status colors
\$status-success: {$config['colors']['status']['success']};
\$status-warning: {$config['colors']['status']['warning']};
\$status-error: {$config['colors']['status']['error']};
\$status-info: {$config['colors']['status']['info']};

// Navbar colors
\$navbar-bg: {$config['colors']['navbar']['background']};
\$navbar-text: {$config['colors']['navbar']['text']};
\$navbar-hover: {$config['colors']['navbar']['hover']};

// Footer colors
\$footer-bg: {$config['colors']['footer']['background']};
\$footer-text: {$config['colors']['footer']['text']};
\$footer-link: {$config['colors']['footer']['link']};

// Login page colors
\$login-bg: {$config['colors']['login_page']['background']};
\$login-card-bg: {$config['colors']['login_page']['card_background']};
\$login-gradient-start: {$config['colors']['login_page']['gradient_start']};
\$login-gradient-end: {$config['colors']['login_page']['gradient_end']};

// ==============================================================================
// Typography
// ==============================================================================
\$font-family-base: {$config['typography']['font_family']};
\$font-family-heading: {$config['typography']['heading_font']};
\$font-size-base: {$config['typography']['base_font_size']};

// ==============================================================================
// Theme Variables
// ==============================================================================
\$border-radius-base: {$config['theme']['border_radius']};
\$navbar-height: {$config['theme']['navbar_height']};
\$sidebar-width: {$config['theme']['sidebar_width']};
\$box-shadow: {$config['theme']['shadow']};

// Override Bootstrap variables
\$primary: \$brand-primary;
\$secondary: \$brand-secondary;
\$success: \$status-success;
\$info: \$status-info;
\$warning: \$status-warning;
\$danger: \$status-error;
\$body-bg: \$bg-paper;
\$body-color: \$text-primary;

// ==============================================================================
// Global Styles
// ==============================================================================
body {
    font-family: \$font-family-base;
    background-color: \$bg-paper;
    color: \$text-primary;
}

// ==============================================================================
// Navbar Styling
// ==============================================================================
.navbar-bootswatch,
.navbar {
    background-color: \$navbar-bg !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: \$box-shadow;
    
    .navbar-brand {
        background-image: url('[[pix:theme|logo]]');
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center left;
        width: 180px;
        height: 45px;
        text-indent: -9999px;
    }
    
    .nav-link {
        color: \$navbar-text !important;
        transition: all 0.2s ease;
        
        &:hover, &:focus {
            color: \$brand-primary-light !important;
            background-color: \$navbar-hover;
            border-radius: 4px;
        }
    }
}

// ==============================================================================
// Buttons
// ==============================================================================
.btn-primary {
    background-color: \$brand-primary;
    border-color: \$brand-primary;
    color: \$brand-primary-contrast;
    border-radius: \$border-radius-base;
    transition: all 0.3s ease;
    
    &:hover, &:focus, &:active {
        background-color: \$brand-primary-dark !important;
        border-color: \$brand-primary-dark !important;
        box-shadow: 0 4px 12px rgba(\$brand-primary, 0.4);
    }
}

.btn-secondary {
    background-color: \$brand-secondary;
    border-color: \$brand-secondary;
    border-radius: \$border-radius-base;
    transition: all 0.3s ease;
    
    &:hover, &:focus, &:active {
        background-color: \$brand-secondary-dark !important;
        border-color: \$brand-secondary-dark !important;
    }
}

.btn-outline-primary {
    color: \$brand-primary;
    border-color: \$brand-primary;
    border-radius: \$border-radius-base;
    
    &:hover, &:focus {
        background-color: \$brand-primary;
        color: \$brand-primary-contrast;
    }
}

// ==============================================================================
// Cards
// ==============================================================================
.card {
    border-radius: \$border-radius-base;
    border: 1px solid rgba(0, 0, 0, 0.08);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    
    &:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }
}

// ==============================================================================
// Login Page
// ==============================================================================
#page-login-index {
    background: linear-gradient(135deg, \$login-gradient-start 0%, \$login-gradient-end 100%);
    min-height: 100vh;
    
    .card {
        border: none;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        border-radius: 16px;
        backdrop-filter: blur(10px);
        
        .card-header {
            background-color: transparent;
            border-bottom: none;
            padding-top: 2rem;
            
            &:before {
                content: "";
                display: block;
                background-image: url('[[pix:theme|logo]]');
                background-size: contain;
                background-repeat: no-repeat;
                background-position: center;
                height: 80px;
                width: 100%;
                margin-bottom: 1.5rem;
            }
            
            img {
                display: none;
            }
        }
        
        .card-body {
            padding: 2rem;
        }
    }
    
    // Hide Moodle branding on login
    .logininfo a[href*="moodle.org"] {
        display: none !important;
    }
}

// ==============================================================================
// Footer
// ==============================================================================
#page-footer {
    background-color: \$footer-bg;
    color: \$footer-text;
    padding: 2rem 0;
    
    a {
        color: \$footer-link;
        transition: color 0.2s ease;
        
        &:hover {
            color: lighten(\$footer-link, 15%);
        }
    }
    
    .footer-content {
        text-align: center;
    }
}

// ==============================================================================
// Hide Moodle Branding (if configured)
// ==============================================================================
SCSS;

// Add conditional Moodle branding hiding
if (!$config['features']['show_moodle_branding']) {
    $scssContent .= <<<SCSS

// Hide all Moodle references
.tool_usertours-resettourcontainer,
a[href*="moodle.org"],
a[href*="docs.moodle.org"],
.moodlelogo {
    display: none !important;
}
SCSS;
}

if (!$config['features']['show_powered_by']) {
    $scssContent .= <<<SCSS

// Hide powered by Moodle
.poweredby,
.footer-content-debugging {
    display: none !important;
}
SCSS;
}

$scssContent .= <<<SCSS

// ==============================================================================
// Course Card Styling
// ==============================================================================
.course-card,
.dashboard-card {
    border-radius: \$border-radius-base;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    
    &:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }
    
    .course-card-img {
        height: 160px;
        object-fit: cover;
    }
}

// ==============================================================================
// Mobile Responsive Adjustments
// ==============================================================================
@media (max-width: 768px) {
    .navbar-brand {
        width: 120px;
        height: 35px;
    }
    
    #page-login-index .card {
        margin: 1rem;
        border-radius: 12px;
        
        .card-header:before {
            height: 60px;
        }
    }
}

// ==============================================================================
// Custom Animations
// ==============================================================================
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.card, .alert, .modal {
    animation: fadeIn 0.3s ease;
}

// Progress bar styling
.progress {
    height: 8px;
    border-radius: 4px;
    overflow: hidden;
    
    .progress-bar {
        background: linear-gradient(90deg, \$brand-primary 0%, \$brand-primary-light 100%);
    }
}

// ==============================================================================
// Form Styling
// ==============================================================================
.form-control {
    border-radius: 6px;
    border: 2px solid #e2e8f0;
    padding: 0.75rem 1rem;
    transition: all 0.2s ease;
    
    &:focus {
        border-color: \$brand-primary;
        box-shadow: 0 0 0 3px rgba(\$brand-primary, 0.15);
    }
}

// ==============================================================================
// Alert Styling
// ==============================================================================
.alert {
    border-radius: \$border-radius-base;
    border: none;
    
    &.alert-success {
        background-color: rgba(\$status-success, 0.1);
        color: darken(\$status-success, 15%);
    }
    
    &.alert-warning {
        background-color: rgba(\$status-warning, 0.1);
        color: darken(\$status-warning, 20%);
    }
    
    &.alert-danger {
        background-color: rgba(\$status-error, 0.1);
        color: darken(\$status-error, 10%);
    }
    
    &.alert-info {
        background-color: rgba(\$status-info, 0.1);
        color: darken(\$status-info, 15%);
    }
}
SCSS;

// Write SCSS file
$scssPath = __DIR__ . '/public/theme/masbrand/scss/post.scss';
file_put_contents($scssPath, $scssContent);
echo "Generated: {$scssPath}\n";

// ==============================================================================
// Generate Core Renderer Override
// ==============================================================================
echo "Generating core renderer override...\n";

$brandName = $config['brand']['name'];
$copyrightText = $config['brand']['legal']['copyright_text'];
$stringReplacements = $config['string_replacements'];

$rendererContent = <<<PHP
<?php
// ==============================================================================
// {$brandName} Theme Core Renderer
// Auto-generated from whitelabel-config.json
// ==============================================================================

defined('MOODLE_INTERNAL') || die();

/**
 * Core renderer override for {$brandName} theme
 */
class theme_masbrand_core_renderer extends \theme_boost\output\core_renderer {

    /**
     * Custom footer HTML
     *
     * @return string HTML for the footer
     */
    public function standard_footer_html() {
        global \$CFG;

        \$output = '<div class="footer-content">';
        \$output .= '<div class="footer-branding">';
        \$output .= '<p>{$copyrightText}</p>';
        \$output .= '</div>';
        
        if (\$this->is_debug_mode()) {
            \$output .= \$this->debug_footer_html();
        }
        
        \$output .= '</div>';

        return \$output;
    }
    
    /**
     * Override login info to replace Moodle references
     *
     * @return string Login info HTML
     */
    public function login_info() {
        \$content = parent::login_info();
        \$content = \$this->apply_brand_replacements(\$content);
        return \$content;
    }
    
    /**
     * Override page title
     *
     * @return string Page title
     */
    public function page_title() {
        \$title = parent::page_title();
        \$title = \$this->apply_brand_replacements(\$title);
        return \$title;
    }
    
    /**
     * Apply brand string replacements
     *
     * @param string \$content Content to process
     * @return string Processed content
     */
    protected function apply_brand_replacements(\$content) {
        \$replacements = [

PHP;

foreach ($stringReplacements as $search => $replace) {
    $rendererContent .= "            '{$search}' => '{$replace}',\n";
}

$rendererContent .= <<<PHP
        ];
        
        foreach (\$replacements as \$search => \$replace) {
            \$content = str_replace(\$search, \$replace, \$content);
        }
        
        return \$content;
    }
    
    /**
     * Check if debug mode is enabled
     *
     * @return bool
     */
    protected function is_debug_mode() {
        global \$CFG;
        return !empty(\$CFG->debug) && \$CFG->debug >= E_ALL;
    }
}
PHP;

$rendererPath = __DIR__ . '/public/theme/masbrand/classes/output/core_renderer.php';
// Ensure directory exists
if (!is_dir(dirname($rendererPath))) {
    mkdir(dirname($rendererPath), 0755, true);
}
file_put_contents($rendererPath, $rendererContent);
echo "Generated: {$rendererPath}\n";

// ==============================================================================
// Generate .env file from configuration
// ==============================================================================
echo "Generating .env file...\n";

$envContent = <<<ENV
# ==============================================================================
# {$config['brand']['display_name']} - Environment Configuration
# Auto-generated from whitelabel-config.json
# ==============================================================================

# General
COMPOSE_PROJECT_NAME={$config['docker']['compose_project_name']}
DOCKER_IMAGE_NAME={$config['docker']['image_name']}
DOCKER_IMAGE_TAG={$config['docker']['image_tag']}
TIMEZONE={$config['locale']['default_timezone']}

# Application
APP_PORT={$config['docker']['app_port']}
DOMAIN={$config['docker']['domain']}

# SSL/Proxy
MOODLE_SSL_PROXY={$config['docker']['ssl_enabled']}

# Database (CHANGE THESE FOR PRODUCTION!)
MOODLE_DBTYPE=pgsql
MOODLE_DBHOST=db
MOODLE_DBNAME=moodle
MOODLE_DBUSER=moodleuser
MOODLE_DBPASSWORD=CHANGE_THIS_PASSWORD
MOODLE_DBPORT=5432
MOODLE_DBPREFIX=mdl_

# Site Configuration
MOODLE_WWWROOT=https://{$config['docker']['domain']}
MOODLEDATA_PATH=/var/www/moodledata
MOODLE_FULLNAME={$config['brand']['display_name']}
MOODLE_SHORTNAME={$config['brand']['short_name']}

# Admin (CHANGE THESE FOR PRODUCTION!)
MOODLE_ADMIN={$config['deployment']['admin_username']}
MOODLE_ADMIN_PASSWORD=CHANGE_THIS_PASSWORD
MOODLE_ADMIN_EMAIL={$config['deployment']['admin_email']}

# Redis
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=
REDIS_MAXMEMORY=256mb

# PHP
PHP_MEMORY_LIMIT={$config['docker']['php_memory_limit']}
PHP_MAX_EXECUTION_TIME=300
PHP_MAX_INPUT_VARS=5000
PHP_UPLOAD_MAX_FILESIZE={$config['files']['max_upload_size_mb']}M
PHP_POST_MAX_SIZE={$config['files']['max_upload_size_mb']}M

# Branding
BRAND_NAME={$config['brand']['name']}
ENV;

$envPath = __DIR__ . '/.env.generated';
file_put_contents($envPath, $envContent);
echo "Generated: {$envPath}\n";
echo "Note: Rename to .env and update passwords before deployment!\n\n";

// ==============================================================================
// Summary
// ==============================================================================
echo "============================================\n";
echo "White-labeling applied successfully!\n";
echo "============================================\n";
echo "Brand: {$config['brand']['name']}\n";
echo "Display Name: {$config['brand']['display_name']}\n";
echo "Primary Color: {$config['colors']['primary']['main']}\n";
echo "Domain: {$config['docker']['domain']}\n";
echo "============================================\n\n";

echo "Files generated:\n";
echo "  - {$scssPath}\n";
echo "  - {$rendererPath}\n";
echo "  - {$envPath}\n\n";

echo "Next steps:\n";
echo "  1. Review the generated files\n";
echo "  2. Add your logo files to: public/theme/masbrand/pix/\n";
echo "  3. Rename .env.generated to .env and update passwords\n";
echo "  4. Build and deploy with: docker-compose up --build -d\n";
echo "  5. After first login, go to Site Admin > Appearance > Themes and set masbrand as default\n\n";
