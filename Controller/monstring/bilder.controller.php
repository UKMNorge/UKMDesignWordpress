<?php

if( isset( $_GET['hendelse'] ) ) {
	$WP_TWIG_DATA['vis'] = $_GET['hendelse'];
}
$WP_TWIG_DATA['visAlle'] = !isset( $_GET['hendelse'] );
$WP_TWIG_DATA['monstring'] = new monstring_v2( get_option('pl_id') );