<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Nettverk\Omrade;

class geoController {
    public static $state;
    public static $view;
    public static $pamelding;
    public static $omrade;

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
}

if( isActiveArkiv() ) {
    static::setState('arkiv');
}