<?php
	
$maned = $WP_TWIG_DATA['page']->getLastParameter();
if( $maned == '111' ) {
    $maned = '01';
}

require_once('UKM/sql.class.php');
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


$qry = "SELECT 	COUNT(`stat_id`) AS `antall`,
				DATE_FORMAT(`time`, '%d') AS `dag`,
				DATE_FORMAT(`time`, '%m') AS `mnd`,
				DATE_FORMAT(`time`, '%Y') AS `ar`,
				DATE_FORMAT(`time`, '%Y-%m-%d') AS `dato`
		FROM `ukm_statistics`
		WHERE `time` > '#start_year-#start_month%'
		GROUP BY `dato`
		ORDER BY `dato` ASC";
$sql = new SQL( $qry, array('start_year' => $ar_start-1, 'start_month' => $start_maned_include) );
$res = $sql->run();
while( $r = SQL::fetch( $res ) ) {
	if( (int) $r['mnd'] < (int) $start_maned_include && (int) $r['mnd'] > 4 ) {
		continue;
	}
	if( $r['mnd'] > 8 ) {
		$r['ar']++;
		$korrigert_dato = $r['ar'].'-'.$r['mnd'].'-'.$r['dag'];
	} else {
		$korrigert_dato = $r['dato'];
	}
	$WP_TWIG_DATA['stat'][ $korrigert_dato ] = $r['antall'];
}

// For des, jan, feb, mar
$WP_TWIG_DATA['akk'] = array();
$WP_TWIG_DATA['uke'] = array();
$akk = array();
foreach( $WP_TWIG_DATA['stat_ar'] as $ar ) {
	$akk['ar'][ $ar ] = 0;
	foreach( $WP_TWIG_DATA['stat_mnd'] as $mnd => $dager ) {
		$akk['mnd'][ $ar ][ $mnd ] = 0;
		// For 1 ... ant_dager_i_mnd
		for( $dag=1; $dag<=$dager; $dag++ ) {
		
			// ID og antall-hjelpere
			$dag = $dag < 10 ? '0'.$dag : $dag; # korrigerer for 01..09
			$dato = $ar .'-'.$mnd.'-'.$dag;
			$antall = isset( $WP_TWIG_DATA['stat'][ $dato ] ) ? $WP_TWIG_DATA['stat'][ $dato ] : 0;
			
			// AKKUMULERT ANTALL
			// Akkumulert antall påmeldte per mnd og år (brukes til summering)
			$akk['mnd'][ $ar ][ $mnd ] += $antall;
			$akk['ar'][ $ar ] += $antall;
			
			// Lagre antall akk per gitt dag
			$WP_TWIG_DATA['akk_mnd'][ $dato ] = $akk['mnd'][ $ar ][ $mnd ];
			$WP_TWIG_DATA['akk_ar'][ $dato ] = $akk['ar'][ $ar ];
			
			// Påmeldte per uke
			$uke = ((floor(($dag-1)/7))+1);
			$uke = uke( $uke, $WP_TWIG_DATA['stat_mnd'][ $mnd ] );
			if( !isset( $WP_TWIG_DATA['uker'][ $mnd ][ $uke ][ $ar ] ) ) {
				$WP_TWIG_DATA['uker'][ $mnd ][ $uke ][ $ar ] = 0;
			}
			$WP_TWIG_DATA['uker'][ $mnd ][ $uke ][ $ar ] += $antall;
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

// HVIS VI SKAL VISE OVERSIKTEN FOR KUN ÉN MÅNED
$id = $WP_TWIG_DATA['page']->getLastParameter();
if( $id == '111' ) {
    $id = '01';
}
if( is_numeric( $id ) ) {
	$mnd = str_pad( (string) $id, 2, '0', STR_PAD_LEFT );
	$WP_TWIG_DATA['maned'] = $WP_TWIG_DATA['stat_mnd'][$mnd];
	$WP_TWIG_DATA['maned_id'] = $mnd;
	$WP_TWIG_DATA['maned_dager'] = $WP_TWIG_DATA['stat_mnd'][$mnd];
}