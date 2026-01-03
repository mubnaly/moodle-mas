<?php
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

// Register our custom renderer class
// The class file should be in classes/output/core_renderer.php
// which maps to \theme_masbrand\output\core_renderer
// But in config.php for themes, we instruct Moodle to use our renderer.
// Actually, 'theme_overridden_renderer_factory' will look for renderers.php or classes.
// With 'classes' autoloading, we need namespace \theme_masbrand\output;

$THEME->scss = function($theme) {
    return theme_masbrand_get_main_scss_content($theme);
};
