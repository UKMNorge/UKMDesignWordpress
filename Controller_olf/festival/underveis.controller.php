<?php
use UKMNorge\DesignBundle\Utils\SEO;
use UKMNorge\DesignBundle\Utils\SEOImage;

$WP_TWIG_DATA['HEADER']->background->url = '//grafikk.ukm.no/festivalen/2019/apningsshow.jpg';
$WP_TWIG_DATA['HEADER']->hideSection = true;
$WP_TWIG_DATA['HEADER']->hideLogo = true;
$WP_TWIG_DATA['HEADER']->button->background = 'rgba(0,0,47, .6)';
$image = new SEOImage( str_replace('http:','https:', $WP_TWIG_DATA['HEADER']->background->url ) );
SEO::setImage( $image );

require_once(get_template_directory() . '/UKMNorge/Wordpress/Utils/posts.class.php');

$posts = new posts( null, true );
$posts->setCategory( 1 );
$posts->loadPosts();

$WP_TWIG_DATA['posts'] = $posts;
/*$WP_TWIG_DATA['direktesending'] = new stdClass();
$WP_TWIG_DATA['direktesending']->link = "https://festivalen.ukm.no/direkte/";*/