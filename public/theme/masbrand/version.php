<?php
defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2023112800;
$plugin->requires  = 2022112800; // Moodle 4.1+
$plugin->component = 'theme_masbrand';
$plugin->dependencies = [
    'theme_boost' => 2022112800,
];
$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = '1.0.0';
