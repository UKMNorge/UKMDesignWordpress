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
	echo '<style>body {margin-top: 33px;} @media (max-width:782px) {body {margin-top: 48px;}}</style>';
}

if( UKMDesign::isDevEnv() ) {
	echo '<script language="javascript">console.debug("'.basename(__FILE__).'");</script>';
}