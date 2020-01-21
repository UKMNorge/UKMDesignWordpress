<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\DesignBundle\Utils\SEO;
use UKMNorge\DesignBundle\Utils\SEOImage;

require_once('UKM/Autoloader.php');

$monstring = new Arrangement( get_option('pl_id') );
$WP_TWIG_DATA['monstring'] = $monstring;
$id = $WP_TWIG_DATA['page']->getLastParameter();

## Skal hente ut programmet for en forestilling
if( is_numeric( $id ) ) {
	// /program/c_id/
	$hendelse = $monstring->getProgram()->get( $id );
	$WP_TWIG_DATA['hendelse'] = $hendelse;

	SEO::setCanonical( SEO::getCanonical(). $id .'/'); // Already set to correct page, but is missing id
	SEO::setTitle( $hendelse->getNavn() );
	SEO::setDescription( $hendelse->getStart()->format('j. M \k\l. H:i') .'. '.( $monstring->getType() == 'kommune' ? 'UKM ' : ''). $monstring->getNavn() );



	switch( $hendelse->getType() ) {
		/**
		 * TYPE: POST ELLER PAGE
		**/
		case 'post':
			$view_template = 'Monstring/Program/post';
			
			global $post, $post_id;
			$post = get_post( $hendelse->getTypePostId() );
			$WP_TWIG_DATA['post'] = new WPOO_Post( $post );
	
			/** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** **
			 * 			KOPIERT FRA SINGLE.PHP
			 ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** **/
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
			/** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** **
			 * 			E.O KOPIERT FRA SINGLE.PHP
			 ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** **/
		break;

		/**
		 * TYPE: KATEGORI
		**/		
		case 'category':
			$view_template = 'Monstring/Program/category';
			
			require_once(get_template_directory() . '/UKMNorge/Wordpress/Utils/posts.class.php');

			$posts = new posts( null, true );
			$posts->setCategory( $hendelse->getTypeCategoryId() );
			$posts->loadPosts();
			
			$WP_TWIG_DATA['kategori'] = get_category( $hendelse->getTypeCategoryId() );
			$WP_TWIG_DATA['posts'] = $posts;
		break;
		
		/**
		 * TYPE: STANDARD
		**/
		default:
			$view_template = 'Monstring/Program/hendelse';
			break;
	}


}
## Skal vise rammeprogram for en gitt dag
elseif( isset($WP_TWIG_DATA['page']->getPage()->meta->dato) && !empty( $WP_TWIG_DATA['page']->getPage()->meta->dato ) ) {
	$visInterne = defined('DELTAKERPROGRAM') && DELTAKERPROGRAM;
	$hendelser = $visInterne ? $monstring->getProgram()->getAllInkludertInterne() : $monstring->getProgram()->getAll();
	
	$dato = DateTime::createFromFormat( 'd_m', $WP_TWIG_DATA['page']->getPage()->meta->dato);
	
	$WP_TWIG_DATA['visInterne'] = $visInterne;
	$WP_TWIG_DATA['program'] = $monstring->getProgram()->filterByDato( $dato, $hendelser );

	$view_template = 'Monstring/Program/oversikt_dag';
}
## Skal vise rammeprogram
else {
	$visInterne = defined('DELTAKERPROGRAM') && DELTAKERPROGRAM;
	$hendelser = $visInterne ? $monstring->getProgram()->getAllInkludertInterne() : $monstring->getProgram()->getAll();
	
	$WP_TWIG_DATA['visInterne'] = $visInterne;
	$WP_TWIG_DATA['program'] = $monstring->getProgram()->sorterPerDag( $hendelser );

	$view_template = 'Monstring/Program/oversikt';
	SEO::setTitle( 'Program for'.( $monstring->getType() == 'kommune' ? ' UKM' : '').' '. $WP_TWIG_DATA['monstring']->getNavn() );
	SEO::setDescription( 'Vi starter '. $monstring->getStart()->format('j. M \k\l. H:i') );
}

#var_dump( $WP_TWIG_DATA['page']->getPage() );