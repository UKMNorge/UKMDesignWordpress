<?php

use UKMNorge\Arrangement\Arrangement;

if( isset( $_GET['hendelse'] ) ) {
	$WP_TWIG_DATA['vis'] = $_GET['hendelse'];
}
$WP_TWIG_DATA['visAlle'] = !isset( $_GET['hendelse'] );
$WP_TWIG_DATA['monstring'] = new Arrangement( intval(get_option('pl_id') ));