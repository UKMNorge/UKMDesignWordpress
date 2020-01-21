<?php
	
use UKMNorge\DesignBundle\Utils\Sitemap;
use UKMNorge\DesignBundle\Utils\SEO;
use UKMNorge\DesignBundle\Utils\SEOImage;

header('Content-Type: text/html; charset=utf-8');
session_start();
setlocale(LC_ALL, 'nb_NO', 'nb', 'no');

// CHECK CACHE (AND DIE IF FOUND CACHE)
require_once('cache.php');

$WP_TWIG_DATA['HEADER'] = new stdClass();
$WP_TWIG_DATA['HEADER']->background = new stdClass();
$WP_TWIG_DATA['HEADER']->button = new stdClass();
$WP_TWIG_DATA['HEADER']->logo = new stdClass();
$WP_TWIG_DATA['UKM_HOSTNAME'] = UKM_HOSTNAME;
$WP_TWIG_DATA['blog_url'] = get_bloginfo('url');
$WP_TWIG_DATA['ajax_url'] = admin_url( 'admin-ajax.php' );

if( ( isset($_POST['singleMode']) && "true" == $_POST['singleMode'] ) || ( isset($_GET['singleMode']) && "true" == $_GET['singleMode']) ) {
	$WP_TWIG_DATA['singleMode'] = true;
}

// SEO INIT
$SEOImage = new SEOImage( WP_CONFIG::get('SEOdefaults')['image']['url'], 
						  WP_CONFIG::get('SEOdefaults')['image']['width'],
						  WP_CONFIG::get('SEOdefaults')['image']['height'],
						  WP_CONFIG::get('SEOdefaults')['image']['type'] );

SEO::setCanonical( get_permalink() );
SEO::setSiteName( WP_CONFIG::get('SEOdefaults')['site_name'] );
SEO::setSection( get_bloginfo('name') );
SEO::setType('website');
SEO::setTitle( WP_CONFIG::get('SEOdefaults')['title'] );
SEO::setDescription( WP_CONFIG::get('slogan') );
SEO::setAuthor( WP_CONFIG::get('SEOdefaults')['author'] );

SEO::setFBAdmins( WP_CONFIG::get('facebook')['admins'] );
SEO::setFBAppId( WP_CONFIG::get('facebook')['app_id'] );

SEO::setGoogleSiteVerification( WP_CONFIG::get('google')['site_verification'] );
SEO::setImage( $SEOImage );

/**
 * TEMA-INNSTILLINGER
 *
 * 1: UKM.no hovedside
 * 2: Fylkesside eller lokalside
 * 3: EGO
**/
// 1: UKM.no hovedside
if( get_current_blog_id() == 1 ) {
	$WP_TWIG_DATA['THEME'] = '';
} else {
    $section = new stdClass();
    $section->title = get_bloginfo('name');
    $section->url = get_bloginfo('url');
    $WP_TWIG_DATA['section'] = $section;
    
	switch( get_option('site_type') ) {
		// 2: Fylkesside eller lokalside
		// 3: ORGANISASJONEN
		case 'fylke':
		case 'kommune':
        case 'land':
        case 'arrangement':
			break;
		case 'organisasjonen':
			$WP_TWIG_DATA['THEME'] = 'cherry';
			break;
        case 'ego':
            $header = new stdClass();
			#$header->logo = 'EGO';
			$header->config = 'hvaerego';
			$WP_TWIG_DATA['header'] = $header;
			$WP_TWIG_DATA['THEME'] = 'ego';
			break;
		default:
            $WP_TWIG_DATA['THEME'] = '';
			break;
    }
    $WP_TWIG_DATA['section'] = $section;
}