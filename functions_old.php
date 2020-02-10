<?php

add_action( "template_include", "UKMresponsive_pageExists", 10000 );

add_filter('ms_site_check', 'UKMresponsive_skip_site_check');

add_action( 'wp_ajax_nopriv_UKMresponsive', 'UKMresponsive_ajax' );
add_action( 'wp_ajax_UKMresponsive', 'UKMresponsive_ajax' );
add_action( 'after_setup_theme', 'UKMresponsive_imageSizes' );
function UKMresponsive_imageSizes() {
	add_image_size( 'lite', 350, 350 );
	add_image_size( 'forsidebilde', 1800, 1800 );
	add_image_size( 'veldigstor', 3000, 3000 );

	update_option( 'medium_size_w', 600 );
	update_option( 'medium_size_h', 600 );

	update_option( 'large_size_w', 1200 );
	update_option( 'large_size_h', 1200 );
}


define('PATH_THEME', TEMPLATEPATH . '/');
define('PATH_DESIGNBUNDLE', PATH_THEME .'UKMNorge/DesignBundle/');
define('PATH_WORDPRESSBUNDLE', PATH_THEME .'UKMNorge/Wordpress/');
define('URL_THEME', get_stylesheet_directory_uri() );
define( 'WP_ENV', (strpos( $_SERVER['HTTP_HOST'], 'ukm.dev' ) !== false || isset($_GET['debug'])) ? 'dev' : 'prod' );

// AUTOLOAD AND SYMFONY EXISTING FILES
require_once(PATH_THEME.'vendor/autoload.php');
require_once( PATH_WORDPRESSBUNDLE . 'Environment/wp_twig.class.php');
require_once( PATH_WORDPRESSBUNDLE . 'Environment/wp_config.class.php');

// MANUALLY LOAD FILES SYMFONY WOULD LOAD BY NAMESPACE
require_once( PATH_DESIGNBUNDLE . 'Utils/Sitemap/Page.php');
require_once( PATH_DESIGNBUNDLE . 'Utils/Sitemap/Pages.php');
require_once( PATH_DESIGNBUNDLE . 'Utils/Sitemap/Section.php');
require_once( PATH_DESIGNBUNDLE . 'Utils/Sitemap.php');
require_once( PATH_DESIGNBUNDLE . 'Utils/SEO.php');
require_once( PATH_DESIGNBUNDLE . 'Utils/SEOImage.php');

// LOAD CONFIG
Sitemap::loadFromYamlFile( PATH_DESIGNBUNDLE . 'Resources/config/sitemap.yml' );

WP_CONFIG::setConfigPath( PATH_DESIGNBUNDLE . 'Resources/config/parameters.yml' );

// TWIG INIT
WP_TWIG::setTemplateDir( PATH_DESIGNBUNDLE .'Resources/views/' );
if( !isset( $_ENV['HOME'] ) && UKM_HOSTNAME == 'ukm.no' ) {
	$_ENV['HOME'] = '/home/ukmno/';	
}
elseif( WP_ENV == 'dev' ) {
	$_ENV['HOME'] = '/temp';
}
WP_TWIG::setCacheDir( $_ENV['HOME'].'/cache/ukmresponsive/');
WP_TWIG::setDebug( WP_ENV == 'dev' );
$WP_TWIG_DATA = [];

function UKMresponsive_pageExists( $template ) {
    // Bloggen er slettet
    if( get_site()->deleted ) {
        return locate_template( ['deleted.php'] );
    }
    // Arrangementet som brukte å være her er borte (pre-2020-problemstilling?)
	if( get_option('status_monstring') != false ) {
		return locate_template( array('monstring-not-here.php') );
    }
    // Business as ususal
	return $template;
}

function UKMresponsive_ajax() {
	global $WP_TWIG_DATA;
	// LOAD CONFIG
	WP_CONFIG::setConfigPath( PATH_DESIGNBUNDLE . 'Resources/config/parameters.yml' );
	
	// TWIG INIT
	WP_TWIG::setTemplateDir( PATH_DESIGNBUNDLE .'Resources/views/' );
	WP_TWIG::setDebug( WP_ENV == 'dev' );
	$WP_TWIG_DATA = [];

	$response = [
		'action' => $_POST['ajaxaction'],
		'trigger' => $_POST['trigger'],
		'success' => false,
	];

	$ajaxFile = PATH_WORDPRESSBUNDLE . 'Ajax/'. basename($response['action']) .'.ajax.php';
	if( file_exists( $ajaxFile ) ) {
		require_once( PATH_WORDPRESSBUNDLE . 'Ajax/'. basename($response['action']) .'.ajax.php');
	}
	
	echo json_encode( $response );
	wp_die();
}

/**
 * Ettersom vi uansett håndterer redirect av slettede blogger i
 * UKMresponsive_pageExists kan alle få lov til å "se" slettede blogger 
 *
 * @return void
 */
function UKMresponsive_skip_site_check() {
    return true;
}