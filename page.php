<?php

use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Wordpress;

require_once('header.php');

/**
 * SEO INFOS
 */
# Author
UKMDesign::getHeader()::getSEO()->setAuthor( Wordpress::getPage()->author->display_name );

# Lead or default-description
if( !empty( strip_tags( Wordpress::getPage()->lead ) ) ) {
    Wordpress::getPage()->setDescription(
        strip_tags(Wordpress::getPage()->lead)
    );
} else {
    Wordpress::getPage()->setDescription(
        UKMDesign::getConfig('hvaerukm.slogan_alt')
    );
}

/**
 * PAGE TEMPLATES (UKMviseng-meta)
 */
Wordpress::requireTemplateController();

require_once('render.php');

// TODO 👇🏼


die();
// SELECT CORRECT TEMPLATE, INCLUDE AND RUN CONTROLLER
switch( Wordpress::getPage()->getTemplate() ) {        
	# Mønstringens deltakerprogram
	case 'deltakerprogram':
		define('DELTAKERPROGRAM', true);
	# Mønstringens program
	case 'program':
		require_once('UKMNorge/Wordpress/Controller/monstring/program.controller.php');
		break;
	# Kontaktpersoner på mønstringen
	case 'kontaktpersoner':
		require_once('UKMNorge/Wordpress/Controller/monstring/kontaktpersoner.controller.php');
		break;
	case 'bilder':
		require_once('UKMNorge/Wordpress/Controller/monstring/bilder.controller.php');
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