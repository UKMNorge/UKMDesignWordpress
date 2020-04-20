<?php

namespace UKMNorge\DesignWordpress\Environment\Front;

use UKMNorge\DesignWordpress\Environment\Page;
use UKMNorge\Nettverk\Omrade;
use UKMNorge\Wordpress\Blog;

class OmradeFront extends Front
{
    static $infoside;
    static $omrade;
    static $arrangement;
    static $supportInfoside = true;

    /**
     * Skal knapp med lenke til fylkessiden skjules?
     * 
     * Kun kommunesider underlagt falske fylker får mulighet
     * til å skjule denne knappen (via UKMnettside-modulen)
     * 
     * @return void
     */
    public static function skjulFylkeKnapp() {
        return Blog::getOption(get_current_blog_id(),'skjulFylkeKnapp');
    }

    /**
     * Har bloggen en infoside?
     *
     * @return bool
     */
    public static function harInfoside()
    {
        return !is_null(static::getInfoside());
    }

    /**
     * Hent infosiden
     *
     * @return Page
     */
    public static function getInfoside()
    {
        if (is_null(static::$infoside)) {
            static::_loadInfoside();
        }
        return static::$infoside;
    }

    /**
     * Last inn infosiden og cache på objektet
     *
     * @return void
     */
    private static function _loadInfoside()
    {
        if (Blog::harSide(static::getBlogId(), 'info')) {
            $page = new Page(
                Blog::hentSideByPath(
                    static::getBlogId(),
                    'info'
                )
            );
            if( !empty( $page->getContent() ) ) {
                static::$infoside = $page;
            }
        }
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
