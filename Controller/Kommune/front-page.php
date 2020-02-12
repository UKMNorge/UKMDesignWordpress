<?php

use UKMNorge\Design\Menu\Menu;
use UKMNorge\DesignWordpress\Environment\Front\OmradeFront;
use UKMNorge\DesignWordpress\Environment\Posts;
use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Nettverk\Omrade;

$omrade = Omrade::getByKommune( get_option('kommune') );

OmradeFront::setOmrade( $omrade );
Wordpress::setPosts(new Posts(8));

$har_arrangement = $omrade->getArrangementer( OmradeFront::getSesong() )->getAntall() > 0;
$har_infoside = OmradeFront::harInfoside();
$har_posts = Wordpress::getPosts()->getAntall() > 0;

if( !$har_arrangement && !$har_posts ) {
    Wordpress::setView('Kommune/Front/Ingen');
} else {
    Wordpress::setView('Kommune/Front/Front');
}

if( OmradeFront::harInfoside() ) {
    Wordpress::addViewData('infoside', OmradeFront::getInfoside());
}
# Ekstra-meny
if (OmradeFront::hasMeny()) {
    Wordpress::addViewData(
        'menu',
        Menu::createFromWpId( OmradeFront::getMeny() )
    );
}

Wordpress::addViewData(
    [
        'omrade' => OmradeFront::getOmrade()
    ]
);