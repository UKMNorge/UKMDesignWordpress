<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\DesignBundle\Utils\SEO;
use UKMNorge\DesignBundle\Utils\SEOImage;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Innslag\Typer\Typer;

require_once('UKM/Autoloader.php');

$id = $WP_TWIG_DATA['page']->getLastParameter();
$monstring = new Arrangement(get_option('pl_id'));;

## Skal hente ut ETT innslag, gitt i $id.
if( is_numeric( $id ) ) {
	if( isset( $_GET['cid'] ) ) {
		$WP_TWIG_DATA['hendelse'] = $monstring->getProgram()->get( (int) $_GET['cid'] );
	}

	$innslag = $monstring->getInnslag()->get( $id );
	$view_template = 'Monstring/innslag';
	
	SEO::setCanonical( SEO::getCanonical(). $id .'/'); // Already set to correct page, but is missing id
	SEO::setTitle( $innslag->getNavn() .' @ UKM');
	SEO::setDescription( 'Les mer om '. $innslag->getNavn() .' som deltar.' );
	if( $innslag->getBilder()->getAntall() > 0 ) {
		$image = $innslag->getBilder()->first()->getSize('large');
		$SEOimage = new SEOimage( $image->getUrl(), $image->getWidth(), $image->getHeight(), $image->getMimeType() );
		SEO::setImage( $SEOimage );
	}

	$WP_TWIG_DATA['innslag'] = $innslag;
}
## Skal hente ut alle påmeldte innslag til påmeldte-oversikten.
else {
	// /pameldte/ - vil altså laste inn oversikten.
	$WP_TWIG_DATA['monstring'] = new Arrangement(get_option('pl_id'));
	$WP_TWIG_DATA['fylker'] = Fylker::getAllInkludertGjester();
	$WP_TWIG_DATA['kategorier'] = $WP_TWIG_DATA['monstring']->getInnslagTyper()->getAll();
	$view_template = 'Monstring/pameldte';

	SEO::setTitle( 'Påmeldte til '. $WP_TWIG_DATA['monstring']->getNavn() );
	SEO::setDescription( 'Les mer om de som er med' );
}
$WP_TWIG_DATA['monstring'] = $monstring;