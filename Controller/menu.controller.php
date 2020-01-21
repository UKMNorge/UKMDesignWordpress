<?php
	
	$meny = wp_get_nav_menu_object( $WP_TWIG_DATA['page']->getPage()->meta->UKM_nav_menu );	
	$WP_TWIG_DATA['meny'] = wp_get_nav_menu_items( $WP_TWIG_DATA['page']->getPage()->meta->UKM_nav_menu );
	
#	var_dump( $WP_TWIG_DATA['meny']  );