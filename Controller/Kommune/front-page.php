<?php

use UKMNorge\DesignWordpress\Environment\Front\OmradeFront;
use UKMNorge\DesignWordpress\Environment\Posts;
use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Nettverk\Omrade;

$omrade = Omrade::getByKommune( get_option('kommune') );

OmradeFront::setOmrade( $omrade );

Wordpress::setView('Kommune/Front/Front');
Wordpress::setPosts(new Posts(8));

if( OmradeFront::harInfoside() ) {
    Wordpress::addViewData('infoside', OmradeFront::getInfoside());
}


$har_arrangement = $omrade->getArrangementer( OmradeFront::getSesong() )->getAntall() > 0;
$har_infoside = OmradeFront::harInfoside();
$har_posts = Wordpress::getPosts()->getAntall() > 0;
if( !$har_arrangement && !$har_posts ) {
    Wordpress::setView('Kommune/Front/Ingen');
}



Wordpress::addViewData(
    [
        'omrade' => OmradeFront::getOmrade()
    ]
);