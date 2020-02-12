<?php

use UKMNorge\DesignWordpress\Environment\Front\OmradeFront;
use UKMNorge\DesignWordpress\Environment\Posts;
use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Nettverk\Omrade;

OmradeFront::setOmrade( Omrade::getByFylke( get_option('fylke') ) );

Wordpress::setView('Fylke/FrontLokal');
Wordpress::setPosts(new Posts(8));

if( OmradeFront::harInfoside() ) {
    Wordpress::addViewData('infoside', OmradeFront::getInfoside());
}

#Wordpress::setView('Fylke/FrontFylke');


Wordpress::addViewData(
    [
        'omrade' => OmradeFront::getOmrade()
    ]
);