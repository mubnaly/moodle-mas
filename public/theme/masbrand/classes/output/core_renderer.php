<?php
// ==============================================================================
// MasBrand Theme - Core Renderer
// Production-ready with white-label support
// ==============================================================================

defined('MOODLE_INTERNAL') || die();

namespace theme_masbrand\output;

use theme_boost\output\core_renderer as boost_core_renderer;

/**
 * Core renderer override for MasBrand theme
 * 
 * This renderer customizes the Moodle output to support white-labeling
 * and removes references to Moodle branding where configured.
 */
class core_renderer extends boost_core_renderer {

    /**
     * Brand name for replacements
     * Override this in whitelabel config
     */
    protected $brandName = 'ALLAMA';

    /**
     * String replacements for branding
     */
    protected $brandReplacements = [
        'Moodle' => 'ALLAMA',
        'moodle' => 'allama',
        'MOODLE' => 'ALLAMA',
        'Moodle.org' => 'allama.com',
        'moodle.org' => 'allama.com',
        'Moodle Docs' => 'ALLAMA Help',
        'Moodle Mobile' => 'ALLAMA App',
    ];

    /**
     * Return the standard footer html
     *
     * @return string html for the footer
     */
    public function standard_footer_html(): string {
        $output = '<div class="footer-content">';
        $output .= '<div class="footer-branding">';
        $output .= '<p>© ' . date('Y') . ' ' . $this->brandName . '. All rights reserved.</p>';
        $output .= '</div>';
        
        if ($this->is_debug_mode()) {
            $output .= $this->debug_footer_html();
        }
        
        $output .= '</div>';

        return $output;
    }
    
    /**
     * Override login info to replace Moodle references
     *
     * @return string Login info HTML
     */
    public function login_info($withlinks = null): string {
        $content = parent::login_info($withlinks);
        return $this->apply_brand_replacements($content);
    }
    
    /**
     * Override page title
     *
     * @return string Page title
     */
    public function page_title(): string {
        $title = parent::page_title();
        return $this->apply_brand_replacements($title);
    }

    /**
     * Override the home link to use our branding
     *
     * @param bool $footer Whether this is the footer link
     * @return string HTML
     */
    public function home_link(bool $footer = false): string {
        $content = parent::home_link($footer);
        return $this->apply_brand_replacements($content);
    }

    /**
     * Override heading text
     *
     * @param string $text The heading text
     * @param int $level Heading level
     * @param string $classes Additional classes
     * @param string $id Optional ID
     * @return string HTML
     */
    public function heading($text, $level = 2, $classes = '', $id = null): string {
        $text = $this->apply_brand_replacements($text);
        return parent::heading($text, $level, $classes, $id);
    }

    /**
     * Override doc link to hide Moodle docs references
     *
     * @param string $page The doc page
     * @param string $text Link text
     * @param bool $forcepopup Force popup
     * @param array $attributes Additional attributes
     * @return string HTML
     */
    public function doc_link($page, $text = '', $forcepopup = false, array $attributes = []): string {
        // Return empty if we want to hide docs links
        // You can enable this in whitelabel config
        global $CFG;
        if (!empty($CFG->theme_masbrand_hide_docs)) {
            return '';
        }
        
        $content = parent::doc_link($page, $text, $forcepopup, $attributes);
        return $this->apply_brand_replacements($content);
    }

    /**
     * Apply brand string replacements
     *
     * @param string $content Content to process
     * @return string Processed content
     */
    protected function apply_brand_replacements(string $content): string {
        foreach ($this->brandReplacements as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }
        return $content;
    }
    
    /**
     * Check if debug mode is enabled
     *
     * @return bool
     */
    protected function is_debug_mode(): bool {
        global $CFG;
        return !empty($CFG->debug) && $CFG->debug >= E_ALL;
    }

    /**
     * Render the favicon
     *
     * @return string HTML for favicon
     */
    public function favicon(): string {
        return '<link rel="shortcut icon" href="' . 
               $this->image_url('favicon', 'theme') . '" />';
    }

    /**
     * Get the site logo URL
     *
     * @param int $maxwidth Maximum width
     * @param int $maxheight Maximum height
     * @return \moodle_url|false
     */
    public function get_logo_url($maxwidth = null, $maxheight = 200) {
        $logo = $this->image_url('logo', 'theme');
        return $logo ?: parent::get_logo_url($maxwidth, $maxheight);
    }

    /**
     * Get the compact logo URL for mobile
     *
     * @param int $maxwidth Maximum width
     * @param int $maxheight Maximum height
     * @return \moodle_url|false
     */
    public function get_compact_logo_url($maxwidth = 100, $maxheight = 100) {
        $logo = $this->image_url('logo-compact', 'theme');
        if ($logo) {
            return $logo;
        }
        // Fall back to regular logo
        return $this->get_logo_url($maxwidth, $maxheight);
    }
}
