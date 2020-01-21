<?php
## HEADERS, SESSIONS AND SHIT
header('Content-Type: text/html; charset=utf-8');
session_start();
setlocale(LC_ALL, 'nb_NO', 'nb', 'no');

## CURRENT ENVIRONMENT
if( strpos( $_SERVER['HTTP_HOST'], 'ukm.dev' ) !== false || isset($_GET['debug']) ) {
	error_reporting(E_ALL ^ E_DEPRECATED);
	ini_set('display_errors',1);
	define('CURRENT_UKM_DOMAIN', 'ukm.dev');
	define('CURRENT_ENVIRONMENT', 'dev');
	define('IN_PRODUCTION_ENVIRONMENT', false);
} else {
	error_reporting(0);
	ini_set('display_errors',0);
	define('CURRENT_UKM_DOMAIN', 'ukm.no');
	define('CURRENT_ENVIRONMENT', 'prod');
	define('IN_PRODUCTION_ENVIRONMENT',true);
}
define('THEME_PATH', TEMPLATEPATH . '/' );
define('PATH_DESIGNBUNDLE', THEME_PATH .'/UKMNorge/DesignBundle/');
define('TWIG_PATH', THEME_PATH );
define('TWIG_CACHE_PATH', sys_get_temp_dir().'/cache_twig/');

## THEME REQUIREMENTS
require_once('WPOO/WPOO/Post.php');
require_once('WPOO/WPOO/Author.php');
require_once('UKM/inc/twig-admin.inc.php');

## THEME ALWAYS REQUIRED CLASSES
require_once(PATH_DESIGNBUNDLE . 'class/_collection.class.php');
require_once(PATH_DESIGNBUNDLE . 'class/blog.class.php');
require_once(PATH_DESIGNBUNDLE . 'class/path.class.php');
require_once(PATH_DESIGNBUNDLE . 'class/nav.class.php');
require_once(PATH_DESIGNBUNDLE . 'class/seo.class.php');
require_once(PATH_DESIGNBUNDLE . 'class/UKMresponsive.class.php');

## CACHE MODULE MANUAL HOOK
if ( IN_PRODUCTION_ENVIRONMENT && is_user_logged_in() ) {
	do_action( 'UKMcache_clean_url', $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] );
} else {
	do_action( 'UKMcache_exists', $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] );
}

$DATA = array('TWIG_PATH_LAYOUT' => 'UKMDesignBundle/Layout/');
## SELECT VIEW OF CURRENT PAGE
$page_type = false;

if( is_page() ) {
	$page_type = 'page';
	$UKMmodule = get_post_meta( $post->ID, 'UKMviseng', true);
	if( $UKMmodule ) {
		$page_type = $UKMmodule;
	}
}

if( is_front_page() ) {
		wp_reset_query();
		wp_reset_postdata();
		$page_type = 'homepage';
}
switch( $page_type ) {
	case 'hovedmeny':
		require_once(THEME_PATH .'controller/hovedmeny.controller.php');
		$DATA['view'] = new hovedmeny( $page_type );
		break;
	default:
		the_post();
		require_once(THEME_PATH .'controller/page.controller.php');
		$DATA['view'] = new page( $page_type );
		break;
}

## SELECT CORRECT VIEW
$VIEW = $DATA['view']->getView().'.html.twig';

require_once( TEMPLATEPATH. '/inc/post_controller.framework.inc.php');

<?php
## SELECT CORRECT VIEW
$VIEW = $DATA['view']->getView().'.html.twig';

## OUTPUT PAGE
ob_start();
## EXPORT PAGE AS XML?
if( isset($_GET['exportContent']) ) {
	echo TWIGrender('export_content',$DATA,$DEBUG);
## PRINT PAGE AS USUAL
} else {
	echo TWIGrender($VIEW, $DATA, CURRENT_ENVIRONMENT=='dev');
	wp_footer();
	## BUMP PAGE A BIT DOWN BECAUSE OF ADMINBAR
	if(is_user_logged_in() ) {
		echo '<style>body {margin-top: 33px;}</style>';
	}
}
$output = ob_get_clean();
## CALCULATE AND STORE CACHE DATA
$cacheData = array( 'pl_id' => get_option('pl_id'),
					'post_id' => $post->ID,
					'view' => $VIEW,
					'url' => $_SERVER['REQUEST_URI']
				 );
if( IN_PRODUCTION_ENVIRONMENT && $VIEW != '404' && !is_user_logged_in() ) {
	do_action('UKMcache_create', $post->ID, get_option('pl_id'), $VIEW, $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], $output );
}
echo $output;
die();