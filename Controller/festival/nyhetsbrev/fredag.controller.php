<?php
use UKMNorge\DesignBundle\Utils\SEO;
use UKMNorge\DesignBundle\Utils\SEOImage;

#$WP_TWIG_DATA['HEADER']->background->url = 'http://ukm.dev/festivalen/wp-content/uploads/sites/60/2018/06/Screen-Shot-2018-06-19-at-21.11.18.png';#'//grafikk.ukm.no/UKMresponsive/img/festival/forsidebilde.jpg';
$WP_TWIG_DATA['HEADER']->hideSection = true;
$WP_TWIG_DATA['HEADER']->hideLogo = true;

$image = new SEOImage( str_replace('http:','https:', $WP_TWIG_DATA['HEADER']->background->url ) );
SEO::setImage( $image );

require_once(get_template_directory() . '/UKMNorge/Wordpress/Utils/posts.class.php');

$posts = new posts( null, true );
$posts->setCategory( 1 );
$posts->loadPosts();

//	$WP_TWIG_DATA['kategori'] = get_category( 1 );
$WP_TWIG_DATA['posts'] = $posts;

require_once('UKM/tv.class.php');
$WP_TWIG_DATA['video1'] = new TV(13005);
$WP_TWIG_DATA['video2'] = new TV(13007);
$WP_TWIG_DATA['video3'] = new TV(13015);
$WP_TWIG_DATA['video4'] = new TV(13008);
$WP_TWIG_DATA['videogodteri'] = new TV(13026);
