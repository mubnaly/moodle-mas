<?php
// ==============================================================================
// MasBrand Theme Configuration
// Production-ready Moodle theme with white-label support
// ==============================================================================

defined('MOODLE_INTERNAL') || die();

$THEME->name = 'masbrand';
$THEME->sheets = [];
$THEME->editor_sheets = [];
$THEME->parents = ['boost'];
$THEME->enable_dock = false;
$THEME->yuicssmodules = [];
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->requiredblocks = '';
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;
$THEME->haseditswitch = true;
$THEME->usescourseindex = true;

// SCSS configuration
$THEME->scss = function($theme) {
    return theme_masbrand_get_main_scss_content($theme);
};

// Pre-SCSS callback for variables
$THEME->prescsscallback = 'theme_masbrand_get_pre_scss';

// Extra SCSS callback
$THEME->extrascsscallback = 'theme_masbrand_get_extra_scss';

// Icon system
$THEME->iconsystem = \core\output\icon_system::FONTAWESOME;

// Enable removal of all blocks (for mobile-friendly layouts)
$THEME->removedprimarynavitems = [];
