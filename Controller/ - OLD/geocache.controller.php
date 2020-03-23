<?php
/**
 * VI HAR IKKE MOBILNUMMERET
**/

require_once('UKM/Autoloader.php');

use UKMNorge\Database\SQL\Insert;
use UKMNorge\Database\SQL\Query;

if( !isset( $_COOKIE['UKMMobil'] ) ) {
	if( isset( $_POST['mobil'] ) ) {
		setcookie('UKMMobil', $_POST['mobil'], time()+31536000, '/', 'ukm-festivalen.no', 1);

		header("Location: ". $WP_TWIG_DATA['page']->getPage()->url . ( isset( $_GET['code'] ) ? '?code='. $_GET['code'] : '') );
		exit();
	} else {
		$WP_TWIG_DATA['formAction'] = $WP_TWIG_DATA['page']->getPage()->url . (isset($_GET['code']) ? '?code='. $_GET['code'] : '');
		$view_template = 'Geocache/mobilnummer';
	}
}

/**
 * VI HAR MOBILNUMMERET
**/
else {
	$WP_TWIG_DATA['UKMMobil'] = $_COOKIE['UKMMobil'];

	/**
	 * BRUKEREN HAR ÅPNET EN KODE - REGISTRER OG VIDERESEND
	**/
	if( isset( $_GET['code'] ) ) {
		
		$sql = new Query("
			SELECT `id` 
			FROM `konkurranse_geocache`
			WHERE `code` = '#code'
			",
			[
				'code' => $_GET['code']
			]
		);
		$id = $sql->run('field', 'id');
	 
		$ins = new Insert('konkurranse_geocache_checkin');
		$ins->add('cache', $_GET['code'] );
		$ins->add('mobil', $_COOKIE['UKMMobil']);
#		$ins->add('svar', date('d.m.Y H:i:s'));
		$res = $ins->run();
		
		
		header("Location: ". $WP_TWIG_DATA['page']->getPage()->url .'?success='. $id);
		exit();
	}


	/**
	 * BRUKEREN HAR REGISTRERT EN KODE - VIS SUKSESS OG STATUS
	**/	
	if( isset( $_GET['success'] ) ) {
		$sql = new Query("
			SELECT `navn` 
			FROM `konkurranse_geocache`
			WHERE `id` = '#id'
			",
			[
				'id' => $_GET['success']
			]
		);
		$WP_TWIG_DATA['geocache_success'] = $sql->run('field', 'navn');
	}
	
	

	/**
	 * LAST INN ALLE GEOCACHER FOR VISNING
	**/
	$cacher = new Query("SELECT * FROM `konkurranse_geocache`");
	$cacher = $cacher->run();
	while( $row = Query::fetch( $cacher ) ) {
		$geocacher[ $row['code'] ] = $row;
	}
	
	$sql = new Query("
		SELECT *
		FROM `konkurranse_geocache_checkin`
		WHERE `mobil` = '#mobil'
		",
		[
			'mobil' => $_COOKIE['UKMMobil'],
		]
	);
	$res = $sql->run();
	if( $res ) {
		while( $row = Query::fetch( $res ) ) {
			$geocacher[ $row['cache'] ]['status'] = true;
		}
	}
	$WP_TWIG_DATA['geocacher'] = $geocacher;
}