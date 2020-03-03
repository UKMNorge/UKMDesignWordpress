<?php

use UKMNorge\DesignWordpress\Environment\Shortcodes;
use UKMNorge\DesignWordpress\Environment\Admin;
use UKMNorge\DesignWordpress\Environment\Ajax;
use UKMNorge\DesignWordpress\Environment\Redirects;
use UKMNorge\DesignWordpress\Environment\Setup;
use UKMNorge\DesignWordpress\Environment\Wordpress;

require_once('vendor/autoload.php');

add_theme_support( 'post-thumbnails' );
add_theme_support( 'menus' );

/**
 * Disable colors
 */
add_theme_support( 'editor-color-palette' );
add_theme_support( 'disable-custom-colors' );

/**
 * Disable font sizes.
 */
add_theme_support( 'editor-font-sizes', [] );
add_theme_support( 'disable-custom-font-sizes' );

if( function_exists('is_admin') && is_admin() ) {
    Wordpress::initWithoutTwig();
} else {
    Wordpress::init();
}


Ajax::hook();
Admin::hook();
Redirects::hook();
Setup::hook();
Shortcodes::hook();
#Rewrites::hook();

/**
 * @todo Implementer twig-cache?
 */
#WP_TWIG::setCacheDir( $_ENV['HOME'].'/cache/ukmresponsive/');
#WP_TWIG::setDebug( WP_ENV == 'dev' );