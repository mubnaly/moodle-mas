<?php
defined('MOODLE_INTERNAL') || die();

class theme_masbrand_core_renderer extends \theme_boost\output\core_renderer {

    /**
     * Return the standard footer html
     *
     * @return string html for the footer
     */
    public function standard_footer_html() {
        global $CFG, $OUTPUT;

        // Custom footer for ALLAMA
        $output = '<div class="footer-content">';
        $output .= '<div class="footer-branding">';
        $output .= '<p>© ' . date('Y') . ' ALLAMA. All rights reserved.</p>';
        $output .= '</div>';
        
        if (isDebug()) {
             // Keep debug info if needed
            $output .= $this->debug_footer_html();
        }
        $output .= '</div>';

        return $output;
    }

    /**
     * Override language strings at runtime if needed, though typically handled via lang packs.
     * But here we can intercept specific outputs if we want.
     */
    
    public function login_info() {
        // Replace "You are not logged in" or standard Moodle login info if desired.
        // For now, let's keep it standard but ensure no "Moodle" text leaks.
        $content = parent::login_info();
        $content = str_replace('Moodle', 'ALLAMA', $content);
        return $content;
    }
}

function isDebug() {
    global $CFG;
    return !empty($CFG->debug) && $CFG->debug >= E_ALL;
}
