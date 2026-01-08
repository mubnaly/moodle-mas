<?php
// ==============================================================================
// MasBrand Theme Version
// ==============================================================================

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2024011500;           // Version YYYYMMDDXX
$plugin->requires  = 2022112800;           // Requires Moodle 4.1+
$plugin->component = 'theme_masbrand';     // Full component name
$plugin->maturity  = MATURITY_STABLE;      // This is a stable release
$plugin->release   = '1.1.0';              // Human-readable version

$plugin->dependencies = [
    'theme_boost' => 2022112800,           // Requires Boost theme
];
