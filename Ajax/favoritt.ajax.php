<?php

if( isset( $_COOKIE['UKMfavoritt'] ) && is_numeric( $_COOKIE['UKMfavoritt'] ) ) {
	require_once('UKM/monstringer.class.php');
	$monstring = new monstring_v2( $_COOKIE['UKMfavoritt'] );
	
	if( $monstring->erFerdig() ) {
		$WP_TWIG_DATA['lokalmonstring'] = $monstring;
		$monstring = monstringer_v2::fylke( $monstring->getFylke(), $monstring->getSesong() );

		require_once( PATH_WORDPRESSBUNDLE .'Controller/monstring/_fylke.class.php' );
		fylkeController::init( $_COOKIE['UKMfavoritt'] );

		$WP_TWIG_DATA['harInfoPage'] 	= fylkeController::harInfoPage();
		$WP_TWIG_DATA['harProgram'] 	= fylkeController::harProgram();
		$WP_TWIG_DATA['direkte']		= fylkeController::getLive();
	} else {
		require_once( PATH_WORDPRESSBUNDLE .'Controller/monstring/_kommune.class.php' );
		kommuneController::init( $_COOKIE['UKMfavoritt'] );
		$WP_TWIG_DATA['harInfoPage'] 	= kommuneController::harInfoPage();
		$WP_TWIG_DATA['harProgram'] 	= kommuneController::harProgram();
		$WP_TWIG_DATA['direkte']		= kommuneController::getLive();

	}
	
	// Hent siste post
	$path = str_replace('//'. UKM_HOSTNAME, '', $monstring->getLink() );
	$local_blog_id = get_blog_id_from_url( UKM_HOSTNAME, $path );
	if( is_numeric( $local_blog_id ) ) {
		switch_to_blog( $local_blog_id);
		$latest_post = wp_get_recent_posts('numberposts=1', OBJECT);
		if( is_array( $latest_post ) && sizeof( $latest_post ) == 1 ) {
			$WP_TWIG_DATA['post'] = new WPOO_Post( $latest_post[0] );
		}
		restore_current_blog();
	}
	
	$WP_TWIG_DATA['linkToWebsite'] = true;
	$WP_TWIG_DATA['monstring'] = $monstring;
	$response['success'] = true;
	$response['html'] = WP_TWIG::render( 'Favoritt/forside'. $view, $WP_TWIG_DATA );
} else {
	$response['success'] = false;
}
?>