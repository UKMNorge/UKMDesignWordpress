<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Nettverk\Omrade;

class geoController {
    public static $state;
    public static $view;
    public static $pamelding;
    public static $omrade;
    public static $arrangement;

    /**
     * Sett sidens state
     *
     * @param String $state
     * @return void
     */
    public static function setState( $state ) {
        // Vil ikke overstyre state hvis vi er i arkiv-visning (har hentet inn nyheter side 2)
		if( self::$state == 'arkiv' ) {
			return false;
		}
        static::setView( $state );
        static::$state = $state;
    }

    /**
     * Hent sidens state
     *
     * @return String
     */
    public static function getState() {
        return static::$state;
    }

    /**
     * Hent aktivt view-template
     *
     * @return void
     */
    public static function getView() {
        return static::$view;
    }

    /**
     * Hent detaljer om påmeldingen
     *
     * @return void
     */
    public static function getPamelding() {
        if( null == static::$pamelding ) {
            static::$pamelding = new stdClass();
            $configDatePameldingStarter = str_replace('YYYY', (get_site_option('season')-1), WP_CONFIG::get('pamelding')['starter'] );
            static::$pamelding->starter = DateTime::createFromFormat( 'd.m.Y H:i:s', $configDatePameldingStarter .' 00:00:00' );
            static::$pamelding->apen = static::$pamelding->starter < new DateTime('now');
            // DEBUGGER
            #static::$pamelding->apen = true;
            #static::$pamelding->starter = DateTime::createFromFormat( 'd.m.Y H:i:s', '01.11.2018' .' 00:00:00' );
        }
        
        return static::$pamelding;
    }

    /**
     * Er vi på en arkiv-side? I tilfelle skal denne vises
     *
     * @param Array $WP_TWIG_DATA
     * @return void
     */
    public static function isActiveArkiv( $WP_TWIG_DATA ) {
        if( $WP_TWIG_DATA['posts']->paged > 1 ) {
            static::setState('arkiv');
        }
    }

    /**
     * Har siden et registrert arrangement?
     * 
     * Brukes av kommune- og arrangementssider,
     * men ikke av fylkessiden
     *
     * @return Boolean
     */
    public static function harArrangement() {
        return get_option('pl_id') != false;
    }

    /**
     * Hent sidens arrangement
     *
     * @return void
     */
    public static function getArrangement() {
        if( null == static::$arrangement ) {
            static::$arrangement = new Arrangement( get_option('pl_id') );
        }
        return static::$arrangement;
    }

    /**
     * Hent aktiv sesong
     *
     * @return Int
     */
    public static function getSesong() {
        return get_site_option('season');
    }

    /**
     * Sett aktivt område
     *
     * @param Omrade $omrade
     * @return void
     */
    public static function setOmrade( Omrade $omrade ) {
        static::$omrade = $omrade;
    }
 
    /**
     * Hent aktivt område
     *
     * @return Omrade
     */
    public static function getOmrade() {
        return static::$omrade;
    }
}