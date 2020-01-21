<?php
	
	
if( $WP_TWIG_DATA['page']->getPage()->meta->UKMviseng == 'statistikk/monstringer' ) {
	$function = 'getStart';
	$WP_TWIG_DATA['statistikk'] = 'monstringer';
} else {
	$function = 'getFrist1';
	$WP_TWIG_DATA['statistikk'] = 'frister';
}
	
require_once('UKM/monstringer.class.php');
require_once('UKM/monstring.class.php');

$start_maned_include = '10'; // Før var det fra og med desember
$WP_TWIG_DATA['active'] = is_numeric( $maned ) ? $maned : false;

$WP_TWIG_DATA['start_maned'] = $start_maned_include;
$WP_TWIG_DATA['stat_mnd'] = array(
#	'08'=>31,
#	'09'=>30,
'10'=>31,
'11'=>30,
'12'=>31,
'01'=>31,
'02'=>28,
'03'=>31
);

$ar_start = 2013;
$ar_history = get_site_option('season') - $ar_start;
$ar_stop = get_site_option('season');

$WP_TWIG_DATA['history'] = $ar_history;

for( $i=$ar_start; $i < $ar_stop+1; $i++ ) {
    $WP_TWIG_DATA['stat_ar'][] = $i;
}

$WP_TWIG_DATA['dato'] = [];
$WP_TWIG_DATA['uke'] = [];
// Loop ønskede sesonger
foreach( $WP_TWIG_DATA['stat_ar'] as $ar ) {
	$monstringer = stat_monstringer_v2::getAllBySesong( $ar );
	foreach( $monstringer as $monstring ) {
	
		if( !$monstring->erRegistrert() ) {
			continue;
		}
		// STARTER PER DAG
		$startdato = $monstring->$function()->format('Y-m-d');

		// Initier start-container hvis ikke allerede gjort
		if( !isset( $WP_TWIG_DATA['dato'][ $startdato ] ) ) {
			$WP_TWIG_DATA['dato'][ $startdato ] = 0;
		}

		// Tell mønstringen på start-dato
		$WP_TWIG_DATA['dato'][ $startdato ]++;
		

		// STARTER PER UKE
		$starter_dag = $monstring->$function()->format('d');
		$starter_mnd = $monstring->$function()->format('m');
		$starter_uke = ((floor(($starter_dag-1)/7))+1);
		$starter_uke = uke( $starter_uke, $WP_TWIG_DATA['stat_mnd'][ $starter_mnd ] );

		// Initier uke-container hvis ikke allerede gjort
		if( !isset( $WP_TWIG_DATA['uke'][ $starter_mnd ][ $starter_uke ][ $ar ] ) ) {
			$WP_TWIG_DATA['uke'][ $starter_mnd ][ $starter_uke ][ $ar ] = 0;
		}
		// Tell mønstringen på start-uke
		$WP_TWIG_DATA['uke'][ $starter_mnd ][ $starter_uke ][ $ar ]++;
	}
}

foreach( $WP_TWIG_DATA['stat_mnd'] as $mnd => $dager ) {
	if( is_array( $WP_TWIG_DATA['uke'][ $mnd ] ) ) {
		ksort( $WP_TWIG_DATA['uke'][ $mnd ] );
	}
	
	// Hvis vi har uker denne måneden, men ikke alle - opprett alle uker
	if( is_array( $WP_TWIG_DATA['uke'][ $mnd ] ) ) {
		for( $i=0; $i<$WP_TWIG_DATA['stat_mnd'][ $mnd ]; $i++ ) {
			$uke = ((floor(($i-1)/7))+1);
			$uke = uke( $uke, $WP_TWIG_DATA['stat_mnd'][ $mnd ] );
			if( !isset( $WP_TWIG_DATA['uke'][ $mnd ][ $uke ] ) ) {
				$WP_TWIG_DATA['uke'][ $mnd ][ $uke ] = 0;
			}
		}
	}
	
	// Sett 0-verdi for uker som ikke har påmeldte i det hele tatt
	for( $i=$ar_start; $i < $ar_stop+1; $i++ ) {
		if( !is_array( $WP_TWIG_DATA['uke'][ $mnd ] ) ) {
			$WP_TWIG_DATA['uke'][ $mnd ]['ingen'][ $ar ] = 0;
		}
	}
}

function uke( $nummer, $days_in_month ) {
	switch( $nummer ) {
		case 1:
			return '1.-7.';
		case 2:
			return '8.-14.';
		case 3:
			return '15.-21.';
		case 4:
			return '22.-28.';
	}
	return '29.-'.$days_in_month;
}