<?php

use UKMNorge\DesignWordpress\Environment\Shortcodes;
use UKMNorge\DesignWordpress\Environment\Admin;
use UKMNorge\DesignWordpress\Environment\Ajax;
use UKMNorge\DesignWordpress\Environment\Redirects;
use UKMNorge\DesignWordpress\Environment\Setup;
use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Geografi\Kommune;

require_once('vendor/autoload.php');

// OpenID Connect Server (plugin: https://github.com/Automattic/wp-openid-connect-server)
add_filter('oidc_registered_clients', 'my_oidc_clients');
function my_oidc_clients() {
    return array(
        FSS_OIDC_USER => array(
            'name'         => 'FSS - Festival Styring System',
            'secret'       => FSS_OIDC_SECRET,
            'redirect_uri' => FSS_OIDC_CALLBACK,
            'grant_types'  => array('authorization_code'),
            'scope'        => 'openid profile email phone',
        ),
        DELTA_OIDC_USER => array(
            'name'         => 'UKM Påmeldingssystemet',
            'secret'       => DELTA_OIDC_SECRET,
            'redirect_uri' => DELTA_OIDC_CALLBACK,
            'grant_types'  => array('authorization_code'),
            'scope'        => 'openid profile email phone',
        ),
    );
}
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

add_action('init', [Setup::class, 'addRewriteRules']);

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
Wordpress::includeTwigJs();

if( isset($_COOKIE['lastlocation'])) {
    $kommune = new Kommune($_COOKIE['lastlocation']);
    $mitt_ukm = new stdClass();
    $mitt_ukm->kommunenummer = $kommune->getId();
    $mitt_ukm->fylkesnummer = $kommune->getFylke()->getId();
    $mitt_ukm->kommunenavn = $kommune->getNavn();

    Wordpress::addViewData('last_location', $mitt_ukm);
}


/**
 * @todo Implementer twig-cache?
 */
#WP_TWIG::setCacheDir( $_ENV['HOME'].'/cache/ukmresponsive/');
#WP_TWIG::setDebug( WP_ENV == 'dev' );
