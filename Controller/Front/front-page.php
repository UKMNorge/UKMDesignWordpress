<?php

use UKMNorge\DesignWordpress\Environment\Front\Front;
use UKMNorge\DesignWordpress\Environment\Wordpress;

# Ekstra-meny
if (Front::hasMeny()) {
    Wordpress::addViewData(
        'shortcuts',
        Wordpress::createMenuFromId( Front::getMeny() )
    );
}

# Posts
Wordpress::setPosts();
if(Wordpress::getPosts()->getAntall() == 0 ) {
    Wordpress::setView('Page/Fullpage');
}