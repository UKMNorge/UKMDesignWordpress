<?php

use UKMNorge\Arrangement\Arrangement;

require_once('UKM/Autoloader.php');

$monstring = new Arrangement( get_option('pl_id') );
	$WP_TWIG_DATA['monstring'] = $monstring;
	$hendelse = $monstring->getProgram()->get( $WP_TWIG_DATA['page']->getPage()->meta->hendelse );
	$WP_TWIG_DATA['hendelse'] = $hendelse;
	$WP_TWIG_DATA['reprise_id'] = 'onskereprise-'. $hendelse->getId();
