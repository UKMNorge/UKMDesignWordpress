<?php
use Symfony\Component\Yaml\Yaml;

class WP_CONFIG {
	private static $configPath = false;
	private static $keys = null;
	
	public static function set( $key, $val ) {
		self::$keys[ $key ] = $val;
		return self;
	}
	
	public static function get( $key ) {
		if( isset( self::$keys[ $key ] ) ) {
			return self::$keys[ $key ];
		}
		return false;
	}
	
	public static function getAll() {
		return self::$keys;
	}
	
	public static function setConfigPath( $filepath ) {
		self::$configPath = $filepath;
		self::_loadFromYamlfile();
	}
	
	private static function _loadFromYamlfile() {
		if( !self::$configPath ) {
			throw new Exception('Missing basic config file path');
		}
		$parameters = Yaml::parse( file_get_contents( self::$configPath ) );
		self::$keys = $parameters['parameters'];
	}
}