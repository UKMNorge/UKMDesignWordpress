<?php

use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Blocks;
use UKMNorge\DesignWordpress\Environment\Page;
use UKMNorge\DesignWordpress\Environment\Posts;
use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\DesignWordpress\Environment\Front\OmradeFront;


UKMDesign::getHeader()->hideSectionTitle();
UKMDesign::getHeader()->getLogo()->hide();

Wordpress::setView('Festivalen/Front/DetteErFestivalen');

// Get post for current year
$posts = Posts::getByYear(date("Y"));
Wordpress::setPosts($posts);


function getPageForPart($part) {
    $toppmeny = get_pages(
        [
            'parent' => Wordpress::getPage()->getId(),
            'meta_key' => 'forside-part',
            'meta_value' => $part,
            'sort_column' => 'menu_order',
            'post_status' => 'publish'
        ]
    );
    
    if(is_array($toppmeny) && sizeof($toppmeny) == 1) {
        return Page::loadByPostObject($toppmeny[0]);
    }
    return false;
}

Wordpress::addViewData('page_topp', getPageForPart('toppmeny'));
Wordpress::addViewData('page_deltakere', getPageForPart('for-deltakere'));
Wordpress::addViewData('page_dette_er', getPageForPart('dette-er'));

