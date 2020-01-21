<?php

$menu_locations = get_nav_menu_locations();
if( is_array( $menu_locations ) && isset( $menu_locations['ukm-meny'] ) ) {
	$meny = wp_get_nav_menu_object( $menu_locations['ukm-meny'] );
	$WP_TWIG_DATA['meny'] = wp_get_nav_menu_items( $meny );
}