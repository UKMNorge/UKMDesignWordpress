<?php

use UKMNorge\Design\Environment\Shortcodes;
use UKMNorge\DesignWordpress\Environment\Admin;
use UKMNorge\DesignWordpress\Environment\Rewrites;

require_once('vendor/autoload.php');

require_once('environment/shortcodes.php');
add_theme_support( 'post-thumbnails' );
add_theme_support( 'menus' );

Admin::hook();
Shortcodes::hook();
#Rewrites::hook();