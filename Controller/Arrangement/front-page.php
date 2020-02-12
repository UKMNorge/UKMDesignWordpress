<?php

use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Front\OmradeFront;
use UKMNorge\DesignWordpress\Environment\Posts;
use UKMNorge\DesignWordpress\Environment\Wordpress;

$posts = new Posts(8);
Wordpress::setPosts($posts);

if( OmradeFront::harInfoside() ) {
    Wordpress::addViewData('infoside', OmradeFront::getInfoside());
}

if( $posts->getAntall() > 0 ) {
    Wordpress::setView('Arrangement/Front/FrontNyheter');
} else {
    Wordpress::setView('Arrangement/Front/Front');
}


OmradeFront::getArrangement()->getFilmer();

UKMDesign::getHeader()->hideSectionTitle();

Wordpress::addViewData(
    [
        'omrade' => OmradeFront::getOmrade(),
        'arrangement' => OmradeFront::getArrangement()
    ]
);