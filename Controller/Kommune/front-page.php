<?php

use UKMNorge\DesignWordpress\Environment\Front\OmradeFront;
use UKMNorge\DesignWordpress\Environment\Posts;
use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Nettverk\Omrade;
use UKMNorge\Wordpress\Blog;

Wordpress::requireController('Kommune','redirecter');

$omrade = Omrade::getByKommune( get_option('kommune') );


// Redirect hvis kommune har modifisert path
if($omrade->getKommune()->getModifiedPath() && $omrade->getKommune()->getModifiedPath() != $_SERVER['REQUEST_URI']) {
    header("Location: " . $omrade->getKommune()->getPath());
    die();
}

OmradeFront::setOmrade( $omrade );
Wordpress::setPosts(new Posts(8));

$har_infoside = OmradeFront::harInfoside();
$har_posts = Wordpress::getPosts()->getAntall() > 0;

Wordpress::setView('Kommune/Front');

if( OmradeFront::harInfoside() ) {
    Wordpress::addViewData('infoside', OmradeFront::getInfoside());
}
# Ekstra-meny
if (OmradeFront::hasMeny()) {
    Wordpress::addViewData(
        'menu',
        Wordpress::createMenuFromId( OmradeFront::getMeny() )
    );
}

Wordpress::addViewData(
    [
        'omrade' => OmradeFront::getOmrade(),
        'skjulFylkeKnapp' => OmradeFront::skjulFylkeKnapp(),
        'skjulHvaErUKM' => OmradeFront::skjulHvaErUKM()
    ]
);