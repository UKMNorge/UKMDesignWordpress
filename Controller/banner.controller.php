<?php
use UKMNorge\DesignBundle\Utils\SEO;
use UKMNorge\DesignBundle\Utils\SEOImage;

if( get_option('UKM_banner_image') ) {
    
    if( is_front_page() || is_home() ) {
        $WP_TWIG_DATA['page']->getPage()->meta->noImageOnTop = true;
    }
    
    $WP_TWIG_DATA['HEADER']->background->url = get_option('UKM_banner_image');
    if( get_option('UKM_banner_image_position_y' ) ) {
        $pos_y = get_option('UKM_banner_image_position_y');
        if( $pos_y == 'bottom' ) {
            $pos_y = '95%';
        }
        $WP_TWIG_DATA['HEADER']->background->position = '50% '. $pos_y;
    }
    $large_image = get_option('UKM_banner_image_large');
    if( is_string( $large_image ) && !empty( $large_image ) ) {
        $WP_TWIG_DATA['HEADER']->background->url_large = $large_image;
    }
    $image = new SEOImage( str_replace('http:','https:', get_option('UKM_banner_image') ) );
    SEO::setImage( $image );
}