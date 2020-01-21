<?php

use UKMNorge\Arrangement\Load;
use UKMNorge\Geografi\Kommune;

require_once('header.php');
require_once('UKM/Autoloader.php');

if( !isset($_GET['retry'] ) ) {
    // Hvis sesong-parameteret mangler, henger dette igjen fra tidligere
    // Hvis det er satt for forrige sesong, så skal det også bort.
    $season = get_option('season');
    if( !$season || get_site_option('season') < date('Y') ) {

        $kommune_id = get_option('kommune');
        if( $kommune_id ) {
            $kommune = new Kommune( $kommune_id );
            update_option('blogname', $kommune->getNavn());
        }

        delete_option('status_monstring');
        header("Location: ". get_bloginfo('url') .'?retry=true');
        echo '<script type="javascript">window.location.href = window.location.href + "?retry=true";</script>';
        exit();
    }
}

$template = 'Monstring/'.get_option('status_monstring');

// Prøv å finn nye mønstringer for kommunen
$monstringer = [];

/**
 * Hent kommuner fra bloggen
 * Ved avlysning / flytting / endring lagres en liste
 * med kommuneid'er mønstringen har i endringstidspunktet
**/
$kommuner = explode(',', get_option('kommuner'));
if( is_array( $kommuner ) ) {
	// Loop alle kommuner
	foreach( $kommuner as $kommune_id ) {
		// Prøv å finne en mønstring for kommunen
		#echo '<br /> Finn mønstring for kommune '. $kommune_id .' i '. get_site_option('season') .'-sesongen:';
        if( empty( $kommune_id ) ) {
            continue;
        }
		try {
			$monstring = Load::forKommune( (Int) get_site_option('season'), new Kommune( $kommune_id ) );
			$monstringer[ $monstring->getId() ] = $monstring;
		} catch( Exception $e ) {
			// Ignorer mønstringer vi ikke finner
		}
	}
}

/**
 * Hvis vi har funnet én mønstring for denne siden
 * er det dit brukeren skal
 *
 * Burde kanskje ikke gjelde når mønstringen er splittet, da det kan
 * være at deltakeren søker kommunen som er tatt ut. Videresender likevel inntil videre
**/
if( sizeof( $monstringer ) == 1 ) {
	$monstring = array_shift( $monstringer );
	wp_redirect( $monstring->getLink() );
	exit;
}

switch( str_replace('Monstring/', '', $template) ) {
	case 'avlyst':
		try {
			$WP_TWIG_DATA['kommune'] = new Kommune( $kommuner[0] );
		} catch( Exception $e ) {
			// Ignorer hvis vi ikke finner gitt kommune - view håndterer det
		}
		break;
	default:
		$WP_TWIG_DATA['monstringer'] = $monstringer;
		break;
}

echo WP_TWIG::render( $template, $WP_TWIG_DATA );

wp_footer();
if(is_user_logged_in() ) {
	echo '<style>body {margin-top: 33px;} @media (max-width:782px) {body {margin-top: 48px;}}</style>';
}

if( WP_ENV == 'dev' ) {
	echo '<script language="javascript">console.debug("'.basename(__FILE__).'");</script>';
}