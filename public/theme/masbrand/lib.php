<?php
// ==============================================================================
// MasBrand Theme Library Functions
// Production-ready with SCSS callbacks and settings
// ==============================================================================

defined('MOODLE_INTERNAL') || die();

/**
 * Get the main SCSS content for the theme
 *
 * @param theme_config $theme The theme config object
 * @return string SCSS content
 */
function theme_masbrand_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();
    $context = context_system::instance();

    // Load the preset
    if ($filename == 'default.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_masbrand', 'preset', 0, '/', $filename))) {
        $scss .= $presetfile->get_content();
    } else {
        // Fallback to default boost preset
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }

    // Append our custom overrides
    $postscss = $CFG->dirroot . '/theme/masbrand/scss/post.scss';
    if (file_exists($postscss)) {
        $scss .= file_get_contents($postscss);
    }

    return $scss;
}

/**
 * Get the pre-SCSS content for variable definitions
 *
 * @param theme_config $theme The theme config object
 * @return string Pre-SCSS content
 */
function theme_masbrand_get_pre_scss($theme) {
    $scss = '';
    
    // Get brand color from settings or use default
    $brandcolor = isset($theme->settings->brandcolor) ? $theme->settings->brandcolor : '#2563EB';
    
    $scss .= '$brand-primary: ' . $brandcolor . ';' . PHP_EOL;
    $scss .= '$primary: ' . $brandcolor . ';' . PHP_EOL;
    
    return $scss;
}

/**
 * Get extra SCSS content to append after everything else
 *
 * @param theme_config $theme The theme config object
 * @return string Extra SCSS content
 */
function theme_masbrand_get_extra_scss($theme) {
    $scss = '';
    
    // Add any custom SCSS from settings
    if (!empty($theme->settings->scss)) {
        $scss .= $theme->settings->scss;
    }
    
    return $scss;
}

/**
 * Serves the files from the theme masbrand file areas
 *
 * @param stdClass $course The course object
 * @param stdClass $cm Course module object
 * @param context $context The context
 * @param string $filearea The file area
 * @param array $args Extra arguments
 * @param bool $forcedownload Whether to force download
 * @param array $options Additional options
 * @return bool|void
 */
function theme_masbrand_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        $theme = theme_config::load('masbrand');
        
        // Serve theme setting files
        if ($filearea === 'logo' || $filearea === 'backgroundimage' || $filearea === 'loginbackgroundimage') {
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        }
        
        // Serve preset files
        if ($filearea === 'preset') {
            return $theme->setting_file_serve('preset', $args, $forcedownload, $options);
        }
    }
    
    send_file_not_found();
}

/**
 * Returns the main SCSS content with all includes
 *
 * @param theme_config $theme The theme config object
 * @return string Compiled SCSS
 */
function theme_masbrand_get_scss($theme) {
    return theme_masbrand_get_main_scss_content($theme);
}

/**
 * Callback to add CSS to the page
 *
 * @param renderer_base $output The renderer
 * @return string Extra CSS
 */
function theme_masbrand_before_standard_html_head($output) {
    global $CFG;
    
    $css = '';
    
    // Add Google Fonts if not using SCSS import
    $css .= '<link rel="preconnect" href="https://fonts.googleapis.com">';
    $css .= '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
    $css .= '<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">';
    
    return $css;
}
