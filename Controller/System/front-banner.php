<?php

use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Front\Front;
use UKMNorge\DesignWordpress\Environment\Wordpress;

if( Front::supportBannerBilde() && Front::harBannerBilde() ) {
    # I de tilfeller vi viser en statisk side på fronten,
    # skal ikke den vise page-image, da forsider alltid
    # har et banner på toppen.
    Wordpress::getPage()->setMeta('noImageOnTop',true);
    UKMDesign::setBanner( Front::getBannerBilde() );
    
    # Skal forsiden ha et slagord
    if( Front::harBannerSlagord() ) {
        UKMDesign::getHeader()->setSlogan(
            Front::getBannerSlagord()
        );
        UKMDesign::getHeader()->hideSectionTitle();
    }

    # Skal meny-knappen ha annen farge?
    if( Front::supportBannerMenyFarge() && Front::harBannerMenyFarge() ) {
        UKMDesign::getHeader()->setButtonBackground( Front::getBannerMenyFarge());
    }
}