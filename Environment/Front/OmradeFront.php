<?php

namespace UKMNorge\DesignWordpress\Environment\Front;

use UKMNorge\DesignWordpress\Environment\Page;
use UKMNorge\Nettverk\Omrade;
use UKMNorge\Wordpress\Blog;

class OmradeFront extends Front
{
    static $omrade;
    static $arrangement;

    /**
     * Skal knapp med lenke til fylkessiden skjules?
     * 
     * Kun kommunesider underlagt falske fylker får mulighet
     * til å skjule denne knappen (via UKMnettside-modulen)
     * 
     * @return Bool
     */
    public static function skjulFylkeKnapp() {
        return Blog::getOption(get_current_blog_id(),'skjulFylkeKnapp');
    }
    
    /**
     * Skal teksten "hva er UKM" skjules?
     * 
     * Kun kommunesider underlagt falske fylker får mulighet
     * til å skjule denne knappen (via UKMnettside-modulen)
     * 
     * @return Bool
     */
    public static function skjulHvaErUKM() {
        return Blog::getOption(get_current_blog_id(),'skjulHvaErUKM');
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
