<?php
use UKMNorge\DesignBundle\Utils\SEO;
use UKMNorge\DesignBundle\Utils\SEOImage;

$WP_TWIG_DATA['HEADER']->hideSection = true;
$WP_TWIG_DATA['HEADER']->hideLogo = true;

$image = new SEOImage( str_replace('http:','https:', $WP_TWIG_DATA['HEADER']->background->url ) );
SEO::setImage( $image );

require_once(get_template_directory() . '/UKMNorge/Wordpress/Utils/posts.class.php');

require_once('UKM/tv.class.php');
// KORSLAGET-TEASER
$WP_TWIG_DATA['openingVideo'] = new TV(13021);