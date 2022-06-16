<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Design\Image;
use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Geografi\Fylker;

$id = Wordpress::getLastParameter();
$arrangement = new Arrangement(get_option('pl_id'));;

## Skal hente ut ETT innslag, gitt i $id.
if( is_numeric( $id ) ) {
    
    // Hvis det er en del av en hendelse - send med den også
    if( isset( $_GET['cid'] ) ) {
		Wordpress::addViewData(
            'hendelse',
            $arrangement->getProgram()->get( (int) $_GET['cid'] )
        );
	}

    $innslag = $arrangement->getInnslag()->get( $id );
    Wordpress::setView('Arrangement/Innslag');

    UKMDesign::getHeader()::getSEO()
        ->setCanonical( Wordpress::getPage()->getUrl(). $id .'/') // Already set to correct page, but is missing id
        ->setTitle( $innslag->getNavn() .' @ UKM')
        ->setDescription( 'Les mer om '. $innslag->getNavn() .' som deltar.' );
    
	if( $innslag->getBilder()->getAntall() > 0 ) {
		$image = $innslag->getBilder()->first()->getSize('large');
		$SEOimage = new Image(
            $image->getUrl(),
            $image->getWidth(),
            $image->getHeight(),
            $image->getMimeType()
        );
        UKMDesign::getHeader()::getSEO()
            ->setImage( $SEOimage );
	}

	Wordpress::addViewData('innslag', $innslag);
}
## Skal hente ut alle påmeldte innslag til påmeldte-oversikten.
// /pameldte/ - vil altså laste inn oversikten.
else {
    Wordpress::setView('Arrangement/Sider/Pameldte');
    Wordpress::addViewData(
        [
            'arrangement' => $arrangement,
            'fylker' => Fylker::getAll(),
            'kategorier' => $arrangement->getInnslagTyper()->getAll()
        ]
    );

    Wordpress::getPage()
        ->setTitle( 'Påmeldte til '. $arrangement->getNavn() )
	    ->setDescription( 'Les mer om de som er med' );
}