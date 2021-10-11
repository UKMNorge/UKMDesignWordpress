<?php

use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Wordpress;

echo Wordpress::renderCurrent();

wp_footer();

if( Wordpress::getIncludeTwigJs() ) {
    require_once('UKM/inc/twig-js.inc.php');
    echo TWIGjs_simple(Wordpress::getPath().'Views/');
}

if(is_user_logged_in() ) {
	echo '<style>.geolocation-banner {margin-top: 2rem;} @media (max-width:782px) {.geolocation-banner {margin-top: 2.9rem;}}</style>';
}

if( UKMDesign::isDevEnv() ) {
	echo '<script language="javascript">console.debug("'.basename(__FILE__).'");</script>';
}