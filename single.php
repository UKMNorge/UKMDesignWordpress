<?php

use UKMNorge\DesignBundle\Utils\Sitemap;
use UKMNorge\DesignBundle\Utils\SEO;
use UKMNorge\DesignBundle\Utils\SEOImage;

require_once('header.php');
$view_template = 'Post/fullpage';
global $post, $post_id;
$WP_TWIG_DATA['post'] = new WPOO_Post( $post );

$authorlist = '';
// LOAD MULTI-AUTHORS LIST
if ( isset( $WP_TWIG_DATA['post']->meta->ukm_ma ) ) {
	$list = @json_decode( $WP_TWIG_DATA['post']->meta->ukm_ma, true);
	$authors = array();
	if( is_array( $list ) ) {
		foreach ($list as $user_login => $role) {
			$user = get_user_by('login', $user_login);
			if ($user) {
				$authors[$user_login] = new WPOO_Author($user);
				$authors[$user_login]->role = $role;
				$authorlist .= ucfirst( $authors[ $user_login ]->display_name ) .', ';
			}
		}
	} else {
		$authors = null;
	}
	$WP_TWIG_DATA['authors'] = $authors;

}
// MULTI-AUTHORS NOT DEFINED, USE POST OWNER
 else {
	$WP_TWIG_DATA['authors'] = $WP_TWIG_DATA['post']->author;
	$authorlist .= ucfirst( $WP_TWIG_DATA['post']->author->display_name ) .', ';
}
$authorlist = rtrim( $authorlist, ', ');

// VIDEO ON TOP (FEATURED VIDEO)
if ( isset( $WP_TWIG_DATA['post']->meta->video_on_top ) ) {
	require_once('UKM/tv.class.php');
	$selected = $WP_TWIG_DATA['post']->meta->video_on_top;
	if($selected == 'egendefinert') {
		$url = $WP_TWIG_DATA['post']->meta->video_on_top_URL;
		// Find ID from URL
		$url = rtrim($url, '/').'/';
		$url = explode ('/', $url);
		$url = $url[count($url)-2];
		$url = explode ('-', $url);
		$selected = $url[0]; 
	}
	// Finn tv-objektet.
	$tv = new TV($selected);
	$WP_TWIG_DATA['featured_video'] = $tv->embedCodeVH();
}

$image = $WP_TWIG_DATA['post']->image;

if( is_object( $image ) ) {
	if( isset( $image->forsidebilde ) ) {
		$image = $image->forsidebilde;
	} elseif( isset( $image->large ) ) {
		$image = $image->large;
	} else {
		// $image = $image;
	}
	$SEOimage = new SEOimage( $image->src, $image->width, $image->height );
	SEO::setImage( $SEOimage );
}

SEO::setType('article');
SEO::setTitle( $WP_TWIG_DATA['post']->title );
SEO::setDescription( addslashes( preg_replace( "/\r|\n/", "", strip_tags( $WP_TWIG_DATA['post']->lead ) ) ) );
SEO::setDescription( strip_tags( $WP_TWIG_DATA['post']->lead ) );
SEO::setAuthor( $authorlist );
SEO::setPublished( $WP_TWIG_DATA['post']->raw->post_date_gmt );


if( isset($_POST['contentMode']) && $_POST['contentMode'] == 'true' ) {
	$view_template = 'Post/content';
}

if( (isset($_POST['hideTopImage']) && $_POST['hideTopImage'] == 'true') || (isset($_GET['hideTopImage']) && $_GET['hideTopImage'] == 'true') ) {
	$WP_TWIG_DATA['hideTopImage'] = true;
}

/**
 * EXPORT MODE
 * Export basic page data as json
 **/
if( isset( $_GET['exportContent'] ) ) {
	echo WP_TWIG::render('Export/content', ['export' => $WP_TWIG_DATA['post'] ] );
	die();
}

echo WP_TWIG::render( $view_template, $WP_TWIG_DATA );
wp_footer();
if(is_user_logged_in() ) {
	echo '<style>body {margin-top: 33px;} @media (max-width:782px) {body {margin-top: 48px;}}</style>';
}

if( WP_ENV == 'dev' ) {
	echo '<script language="javascript">console.debug("'.basename(__FILE__).'");</script>';
}