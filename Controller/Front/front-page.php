<?php

use UKMNorge\Design\Menu\Menu;
use UKMNorge\DesignWordpress\Environment\Front\Front;
use UKMNorge\DesignWordpress\Environment\Wordpress;

# Ekstra-meny
if (Front::hasMeny()) {
    Wordpress::addViewData(
        'shortcuts',
        Menu::createFromWpId( Front::getMeny() )
    );
}

# Posts
Wordpress::setPosts();