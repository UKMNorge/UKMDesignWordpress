<?php

use UKMNorge\DesignBundle\Utils\Sitemap;
use UKMNorge\DesignBundle\Utils\SEO;
use UKMNorge\Geografi\Kommune;

require_once('header.php');
require_once('UKMNorge/Wordpress/Utils/page.class.php');
require_once('UKMNorge/Wordpress/Utils/posts.class.php');
require_once('UKM/Autoloader.php');

SEO::setCanonical($WP_TWIG_DATA['blog_url']);
SEO::setDescription(
	'Noen deltar på UKM for å vise frem noe de brenner for, ' .
		'noen prøver noe helt nytt og andre er med sånn at alle får vist sin beste side.'
);
$WP_TWIG_DATA['page'] = new page();
$WP_TWIG_DATA['posts'] = new posts(12);
if( $WP_TWIG_DATA['posts']->paged > 1 ) {
    $WP_TWIG_DATA['page_next'] = get_permalink(get_option('page_for_posts'));
}

// PAGE TEMPLATE - FOR OVERRIDES
if (isset($WP_TWIG_DATA['page']->getPage()->meta->UKMviseng)) {
	$page_template = $WP_TWIG_DATA['page']->getPage()->meta->UKMviseng;
	if (is_array($page_template) && isset($page_template[0])) {
		$page_template = $page_template[0];
	}
} else {
	$page_template = false;
}

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
	case 'ego':
		$view_template = 'Ego/home';
		$section = new stdClass();
		$section->title = 'Redaksjonelt';
		$section->link = Sitemap::getPage('egoego', 'forsiden');
		$WP_TWIG_DATA['section'] = $section; //null; // Fjern section-header på forsiden
		#		$WP_TWIG_DATA['HEADER']->logo->url = '//grafikk.ukm.no/profil/ego/EGO_logo.png';
		#		$WP_TWIG_DATA['HEADER']->logo->link = Sitemap::getPage('egoego', 'forsiden');
		break;
	case 'organisasjonen':
		$view_template = 'Page/home_organisasjonen';
		$WP_TWIG_DATA['section'] = null; // Fjern section-header på forsiden
		$WP_TWIG_DATA['HEADER']->background->url = '//grafikk.ukm.no/forside/newdawn.jpg';
		$WP_TWIG_DATA['HEADER']->background->position = 'top';
		$WP_TWIG_DATA['HEADER']->slogan = WP_CONFIG::get('organisasjonen')['slogan'];
		$WP_TWIG_DATA['HEADER']->button->background = 'rgba(242, 109, 21, 0.44)';
		break;
	case 'media':
		$view_template = 'Media/home';
		require_once('UKMNorge/Wordpress/Controller/menu.controller.php');
		$WP_TWIG_DATA['HEADER']->background->url = '//ukm.no/media/files/2018/05/2016-06-27-14.57.29-1800x1350.jpg';
		$WP_TWIG_DATA['HEADER']->background->position = 'bottom';
		$WP_TWIG_DATA['HEADER']->slogan = 'UKM sin medieavdeling - av og for ungdom';
		$WP_TWIG_DATA['HEADER']->button->background = 'rgba(242, 109, 21, 0.44)';
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
	default:
		$view_template = 'Page/fullpage';
		require_once(PATH_WORDPRESSBUNDLE . 'Controller/banner.controller.php');

		if ($page_template == 'meny' || isset($WP_TWIG_DATA['page']->getPage()->meta->UKM_block) && $WP_TWIG_DATA['page']->getPage()->meta->UKM_block == 'sidemedmeny') {
			require_once('UKMNorge/Wordpress/Controller/menu.controller.php');
			$view_template = 'Page/fullpage_with_menu';
		}
		break;
}
echo WP_TWIG::render($view_template, $WP_TWIG_DATA);

wp_footer();
if (is_user_logged_in()) {
	echo '<style>body {margin-top: 33px;} @media (max-width:782px) {body {margin-top: 48px;}}</style>';
}

if (WP_ENV == 'dev') {
	echo '<script language="javascript">console.debug("' . basename(__FILE__) . '");</script>';
}
