<?php

use UKMNorge\DesignBundle\Utils\Sitemap;
use UKMNorge\DesignBundle\Utils\SEO;
use Symfony\Component\BrowserKit\Request;
use UKMNorge\Geografi\Fylker;

require_once('header.php');
require_once('UKM/Autoloader.php');

require_once('UKMNorge/Wordpress/Utils/page.class.php');
$WP_TWIG_DATA['page'] = new page();

// SET OPENGRAPH AND SEARCH OPTIMIZATION INFOS
SEO::setTitle( $WP_TWIG_DATA['page']->getPage()->title );
SEO::setDescription(
    'Noen deltar på UKM for å vise frem noe de brenner for, '.
    'noen prøver noe helt nytt og andre er med sånn at alle får vist sin beste side.'
);
if( !empty( strip_tags( $WP_TWIG_DATA['page']->getPage()->lead )) ) {
    #SEO::setDescription( addslashes( preg_replace( "/\r|\n/", "", strip_tags( $WP_TWIG_DATA['page']->getPage()->lead ) ) ) );
    SEO::setDescription( strip_tags( $WP_TWIG_DATA['page']->getPage()->lead ) );
}

SEO::setAuthor( $WP_TWIG_DATA['page']->getPage()->author->display_name );

// CHECK TO FIND CUSTOM PAGE CONTROLLER AND VIEW ISSET
if( isset( $WP_TWIG_DATA['page']->getPage()->meta->UKMviseng ) ) {
	$page_template = $WP_TWIG_DATA['page']->getPage()->meta->UKMviseng;
	if( is_array( $page_template ) && isset( $page_template[0] ) ) {
		$page_template = $page_template[0];
	}
} else {
	$page_template = false;
}

// SELECT CORRECT TEMPLATE, INCLUDE AND RUN CONTROLLER
switch( $page_template ) {
	## TILHØRENDE MØNSTRINGEN
	# Påmeldte til mønstringen
	case 'pameldte':
		require_once("UKMNorge/Wordpress/Controller/monstring/pameldte.controller.php");
		break;
	# Mønstringens deltakerprogram
	case 'deltakerprogram':
		define('DELTAKERPROGRAM', true);
	# Mønstringens program
	case 'program':
		require_once('UKMNorge/Wordpress/Controller/monstring/program.controller.php');
		break;
	# Kontaktpersoner på mønstringen
	case 'kontaktpersoner':
		$view_template = 'Monstring/kontaktpersoner';
		require_once('UKMNorge/Wordpress/Controller/monstring/kontaktpersoner.controller.php');
		break;
	case 'bilder':
		require_once('UKMNorge/Wordpress/Controller/monstring/bilder.controller.php');
		$view_template = 'Monstring/bilder';
		break;

		
	case 'geocache':
		$view_template = 'Geocache/geocache';
		require_once('UKMNorge/Wordpress/Controller/geocache.controller.php');
		break;
	case 'festival/juni':
		$view_template = 'Festival/juni';
		require_once('UKMNorge/Wordpress/Controller/festival/juni.controller.php');
		break;
	case 'festival/mandag':
		$view_template = 'Festival/Nyhetsbrev/mandag';
		require_once('UKMNorge/Wordpress/Controller/festival/nyhetsbrev/mandag.controller.php');
		break;
	case 'festival/tirsdag':
		$view_template = 'Festival/Nyhetsbrev/tirsdag';
		require_once('UKMNorge/Wordpress/Controller/festival/nyhetsbrev/tirsdag.controller.php');
		break;
	case 'festival/onsdag':
		$view_template = 'Festival/Nyhetsbrev/onsdag';
		require_once('UKMNorge/Wordpress/Controller/festival/nyhetsbrev/onsdag.controller.php');
		break;
	case 'festival/torsdag':
		$view_template = 'Festival/Nyhetsbrev/torsdag';
		require_once('UKMNorge/Wordpress/Controller/festival/nyhetsbrev/torsdag.controller.php');
		break;
	case 'festival/fredag':
		$view_template = 'Festival/Nyhetsbrev/fredag';
		require_once('UKMNorge/Wordpress/Controller/festival/nyhetsbrev/fredag.controller.php');
		break;
	case 'festival/onskereprise':
		$view_template = 'Festival/onskereprise';
		require_once('UKMNorge/Wordpress/Controller/festival/onskereprise.controller.php');
		break;

	case 'festival/direkte':
		$view_template = 'Festival/direkte';
		require_once('UKMNorge/Wordpress/Controller/festival/direkte.controller.php');
		break;	

	case 'festival/nyhetsbrev':
		$view_template = 'Festival/nyhetsbrev';
		require_once('UKMNorge/Wordpress/Controller/festival/nyhetsbrev.controller.php');
		break;
	
	case 'festival/underveis':
		$view_template = 'Festival/underveis';
		require_once('UKMNorge/Wordpress/Controller/festival/underveis.controller.php');
		break;
		
	## HOVEDSIDER
	# Norgeskartet
    case 'dinmonstring':
        $WP_TWIG_DATA['fylker'] = Fylker::getAll();
		$view_template = 'Kart/fullpage';
		break;
	# Vis menyen som side
	case 'hovedmeny':
		$view_template = 'Page/meny';
		break;
	# Vis kontakt-side
	case 'kontakt':
		require_once('UKMNorge/Wordpress/Controller/kontakt.controller.php');
		$view_template = 'Kontaktpersoner/liste';
		break;
	# Glemt passord
	case 'glemt-passord':
        $view_template = 'Passord/glemt';
    	break;
	
		
	## ORGANISASJONEN
	case 'org/logoer':
		$view_template = 'GrafiskProfil/logoer';
		break;
	case 'org/fylkeskontakter':
		require_once('UKMNorge/Wordpress/Controller/fylkeskontakter.controller.php');
		$view_template = 'Kontaktpersoner/fylkeskontakter';
		break;
	case 'statistikk/pameldte':
		require_once('UKMNorge/Wordpress/Controller/menu.controller.php');
		$view_menu_template  ='Statistikk/pameldte';
		$view_template = &$view_menu_template;
		require_once('UKMNorge/Wordpress/Controller/statistikk/pameldte.controller.php');
		break;
	case 'statistikk/frister':
	case 'statistikk/monstringer':
		require_once('UKMNorge/Wordpress/Controller/menu.controller.php');
		$view_menu_template = 'Statistikk/monstringer';
		$view_template = &$view_menu_template;
		require_once('UKMNorge/Wordpress/Controller/statistikk/monstringer.controller.php');
		break;
	case 'statistikk/sanger':
		require_once('UKMNorge/Wordpress/Controller/menu.controller.php');
		$view_menu_template  = 'Statistikk/sanger';
		$view_template = &$view_menu_template;
		require_once('UKMNorge/Wordpress/Controller/statistikk/sanger.controller.php');
		break;

	# Vis kontakt-side
	case 'org/styret':
		require_once('UKMNorge/Wordpress/Controller/kontakt.controller.php');
		$view_template = 'Page/styret';
		break;
		
        
	# Samtykke-skjema
	case 'personvern/samtykke':
		$view_template = 'Personvern/samtykke';
		require_once('UKMNorge/Wordpress/Controller/personvern/samtykke.controller.php');
		break;
	case 'personvern/pamelding':
		$view_template = 'Personvern/pamelding';
		require_once('UKMNorge/Wordpress/Controller/personvern/pamelding.controller.php');
		break;

	# Standard wordpress-side
	
	case 'Page/fullpage_wide':
	case 'fullpage_wide':
		$view_template = 'Page/fullpage_wide';
		break;
	case 'liste':
	default:
		$view_template = 'Page/fullpage';
		break;
}

if( $page_template == 'meny' || isset( $WP_TWIG_DATA['page']->getPage()->meta->UKM_block ) && $WP_TWIG_DATA['page']->getPage()->meta->UKM_block == 'sidemedmeny'  ) {
	require_once('UKMNorge/Wordpress/Controller/menu.controller.php');
	if( !empty( $view_menu_template ) ) {
		$view_template = $view_menu_template;
	} else {
		$view_template = 'Page/fullpage_with_menu';
	}
}

/**
 * EXPORT MODE
 * Export basic page data as json
 **/
if( isset( $_GET['exportContent'] ) ) {
	echo WP_TWIG::render('Export/content', ['export' => $WP_TWIG_DATA['page']->page ] );
	die();
}

echo WP_TWIG::render( $view_template, $WP_TWIG_DATA );

wp_footer();
if(is_user_logged_in() ) {
	echo '<style>body {margin-top: 33px;} @media (max-width:782px) {body {margin-top: 48px;}}</style>';
}

if( WP_ENV == 'dev' ) {
	echo '<script language="javascript">console.debug("'.basename(__FILE__).'");</script>';
}