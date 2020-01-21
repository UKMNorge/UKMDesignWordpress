<?php

use UKMNorge\DesignBundle\Utils\Sitemap;
use UKMNorge\DesignBundle\Utils\SEO;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\Extension\DebugExtension;

require_once('wp_twig.dateFilter.inc.php');
require_once('wp_twig.onelineFilter.inc.php');
require_once('wp_twig.ukm.inc.php');

class WP_TWIG {
	private static $debug = false;
	private static $templateDir = false;
	private static $cacheDir = false;
	private static $paths = false;
	private static $functions = false;
	private static $filters = false;
	
	static function setDebug( $status ) {
		self::$debug = $status;
	}
	static function getDebug() {
		return self::$debug;
	}
	
	static function setTemplateDir( $dir ) {
		self::$templateDir = $dir;
	}
	static function getTemplateDir() {
		return self::$templateDir;
	}
	
	static function getCacheDir() {
		return self::$cacheDir;
	}
	static function setCacheDir( $dir ) {
		self::$cacheDir = $dir;
	}
	
	static function addTemplateDir( $path ) {
		if( !is_array( self::$paths ) ) {
			self::$paths = [];
		}
		self::$paths[] = $path;
	}
	static function getTemplateDirectories() {
		if( !is_array( self::$paths ) ) {
			self::$paths = [];
		}
		return self::$paths;
	}
	
	static function addFunction( $twig_name, $callback, $options=false ) {
		self::_initFunctions();
		$function = [
			'twig_name'	=> $twig_name,
			'callback' => $callback,
			'options' => is_array( $options ) ? $options : [],
		];
		self::$functions[ $twig_name ] = $function;
	}
	static function getFunctions() {
		self::_initFunctions();
		return self::$functions;
	}
	
	private static function _initFunctions() {
		if( !is_array( self::$functions ) ) {
			self::$functions = [];
		}
		return true;
	}
	
	
	static function addFilter( $twig_name, $callback, $options=false ) {
		self::_initFilters();
		$filter = [
			'twig_name'	=> $twig_name,
			'callback' => $callback,
			'options' => is_array( $options ) ? $options : [],
		];
		self::$filters[ $twig_name ] = $filter;
	}
	static function getFilters() {
		self::_initFilters();
		return self::$filters;
	}
	
	private static function _initFilters() {
		if( !is_array( self::$filters ) ) {
			self::$filters = [];
		}
		return true;
	}
	


	static function render( $template, $data ) {
		if( false == self::getTemplateDir() ) {
			throw new Exception('Cannot render Twig. Missing templateDir parameter');
		}
		// Loader + filesystem
		#Twig_Autoloader::register();
		$loader = new FilesystemLoader( self::getTemplateDir() );
		if( defined( 'FULLSTORY_PATH' ) ) {
			self::addTemplateDir( FULLSTORY_PATH );
		}
		if( defined( 'UKMKONKURRANSE_PATH' ) ) {
			self::addTemplateDir( UKMKONKURRANSE_PATH .'/twig/' );
		}

		foreach( self::getTemplateDirectories() as $path ) {
			$loader->addPath( $path );
		}
		if( self::getDebug() ) {
			$environment['debug'] = self::getDebug();
		} else {
			$environment = [];
		}

		// Cache
		if( false == self::getCacheDir() ) {
			self::setCacheDir( sys_get_temp_dir() .'/wp_twig_cache/' );
		}
		if( !file_exists( self::getCacheDir() ) ) {
			$res = @mkdir( self::getCacheDir(), 0777, true );
			if( !$res && $_GET['debug']) {
				echo 'Failed to create: '. self::getCacheDir();
			}
		}
		$environment['cache'] = self::getCacheDir();
		$environment['auto_reload'] = true;
		if( self::getDebug() ) {
			$environment['cache'] = false;
		}

		$twig = new Environment($loader, $environment);
		$twig->addGlobal('Sitemap', new Sitemap);
		$twig->addGlobal('SEO', new SEO);
		$twig->addGlobal('THEME_CONFIG', new WP_CONFIG);
		
		// Add dato-filter
		$filter_dato = new TwigFilter('dato', 'WP_TWIG_date');
		$twig->addFilter($filter_dato);
		
		// Add maned-filter
		$filter_maned = new TwigFilter('maned', 'WP_TWIG_maned');
		$twig->addFilter($filter_maned);
		
		// Add path-filter
		$filter_path = new TwigFilter('UKMpath', 'UKMpath');
		$twig->addFilter($filter_path);

		// Add telefon-filter
		$filter_telefon = new TwigFilter('telefon', 'UKMtelefon');
		$twig->addFilter($filter_telefon);
        
        $filter_oneline = new TwigFilter('oneline', 'TWIGoneline');
        $twig->addFilter($filter_oneline);

		// Add asset-function
		$function_ukmasset = new TwigFunction('UKMasset', function( $path ) {
			if( 'ukm.dev' == UKM_HOSTNAME ) {
				return URL_THEME . '/_GRAFIKK_UKM_NO/'. $path;
			}
			return '//grafikk.ukm.no/UKMresponsive/'. $path;
		});
		$twig->addFunction($function_ukmasset);

		// Add GET-function
		$function_get = new TwigFunction('GET', function( $var ) {
			if( !isset( $_GET[ $var ] ) ) {
				return false;
			}
			return $_GET[ $var ];

		});
		$twig->addFunction($function_get);
		
		// Add husk-function
		$function_ukmhusk = new TwigFunction('HUSK', function( $identifier ) {
			return UKMhusk( $identifier );
		});
		$twig->addFunction($function_ukmhusk);

		// Add dynamically added functions
		foreach( self::getFunctions() as $twig_name => $function_data ) {
			$function = new TwigFunction( $twig_name, $function_data['callback'], $function_data['options'] );
			$twig->addFunction( $function );
		}
		
		// Add dynamically added filters
		foreach( self::getFilters() as $twig_name => $filter_data ) {
			$filter = new TwigFilter( $twig_name, $filter_data['callback'], $filter_data['options'] );
			$twig->addFilter( $filter );
		}
		
		// Debug
		if( self::getDebug() ) {
			$twig->addExtension( new DebugExtension() );
		}

		// Set language
		putenv('LC_ALL=nb_NO');
		setlocale(LC_ALL, 'nb_NO');
		
		return $twig->render($template .'.html.twig', $data);
	}
}
