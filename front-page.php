<?php

use UKMNorge\DesignWordpress\Environment\Front;
use UKMNorge\DesignWordpress\Environment\Wordpress;

require_once('header.php');
#UKMDesign::getHeader()::getSEO()->setCanonical($WP_TWIG_DATA['blog_url']);

# Dette er en forside - bruk støtteklasse
Front::init();

Wordpress::setView('Front/Info');

# Find and use header image
Wordpress::requireController('System', 'front-banner');

# If this page uses a template, run its controller
# The controller will then set the correct view
$has_template_controller = Wordpress::requireTemplateController();

if(!$has_template_controller) {
    Wordpress::requireController('System','front-page');
}

require_once('render.php');



die();


switch(get_option('site_type')) {
    case 'arrangement':
		require_once(PATH_WORDPRESSBUNDLE . 'Controller/banner.controller.php');
        require_once('UKMNorge/Wordpress/Controller/arrangement.controller.php');
        break;
	case 'fylke':
		require_once(PATH_WORDPRESSBUNDLE . 'Controller/banner.controller.php');
		require_once('UKMNorge/Wordpress/Controller/fylke.controller.php');
		break;
    case 'kommune':
		require_once(PATH_WORDPRESSBUNDLE . 'Controller/banner.controller.php');
        if( get_option('pl_id') ) {
            require_once('UKMNorge/Wordpress/Controller/arrangement.controller.php');
        } else {
            require_once('UKMNorge/Wordpress/Controller/kommune.controller.php');
        }
		break;
	case 'land':
		switch ($page_template) {
			case 'festival/plakat':
				$view_template = 'Festival/plakat';
				#require_once('UKMNorge/Wordpress/Controller/festival/juni.controller.php');
				break;
			case 'festival/juni':
				$view_template = 'Festival/juni';
				require_once('UKMNorge/Wordpress/Controller/festival/juni.controller.php');
				break;
			case 'festival/underveis':
				$view_template = 'Festival/underveis';
				require_once('UKMNorge/Wordpress/Controller/festival/underveis.controller.php');
				break;
			default:
				$view_template = 'Page/fullpage';
				break;
		}
		break;

		break;
	case 'norge':
		$now = new DateTime();

		$start_festivalperiode = DateTime::createFromFormat('m-d H:i', '05-17 00:00');
		$stop_festivalperiode = DateTime::createFromFormat('m-d H:i', '08-01 00:00');

		$start_mgpjr = DateTime::createFromFormat('Y-m-d H:i', '2018-11-03 20:00');
		$stop_mgpjr = DateTime::createFromFormat('Y-m-d H:i', '2018-11-10 23:59');

		$start_fylker = DateTime::createFromFormat('Y-m-d H:i', date('Y') . '-04-01 00:00');
		$stop_fylker = DateTime::createFromFormat('Y-m-d H:i', date('Y') . '-05-16 23:59');

		if ($start_festivalperiode < $now && $stop_festivalperiode > $now || isset($_GET['festivalperiode'])) {
			$view_template = 'Norge/home_festival';
		} elseif (($start_mgpjr < $now && $stop_mgpjr > $now) || isset($_GET['mgpjr'])) {
			$view_template = 'Page/home_norge_mgpjr';
		} elseif (($start_fylker < $now && $stop_fylker > $now) || isset($_GET['fylker'])) {
			$view_template = 'Norge/home_fylke';
		} else {
			$view_template = 'Norge/home';
		}
		require_once('UKMNorge/Wordpress/Controller/norge.controller.php');
		break;
    }