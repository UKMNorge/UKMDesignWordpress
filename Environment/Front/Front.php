<?php

namespace UKMNorge\DesignWordpress\Environment\Front;

use DateTime;
use Exception;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Design\Header\Banner;
use UKMNorge\Design\Image;
use UKMNorge\Design\Position\Vertical;
use UKMNorge\Design\UKMDesign;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Geografi\Kommune;

class Front
{
    const LENGTH_SLAGORD = 125;
    static $har_arrangement;
    static $start_sesong;
    static $apen_pamelding;
    static $blog_id;
    static $arrangement;
    static $supportInfoside = true;
    static $fylke;
    static $kommune;

    public static function init()
    {
        static::_harArrangement();

        global $blog_id;
        static::$blog_id = intval($blog_id);
        static::setSEODefaults();
    }

    /**
     * Hent hvilken bloggID vi er på
     *
     * @return Int
     */
    public static function getBlogId()
    {
        return static::$blog_id;
    }

    /**
     * Er vi på en arkiv-side? I tilfelle skal denne vises
     *
     * @param bool
     * @return void
     */
    public static function isActiveArkiv()
    {
        return Wordpress::getPosts()->getPaged();
    }

    /**
     * Er vi i sesong for påmeldinger
     *
     * @return bool
     */
    public static function erNasjonalPameldingApen()
    {
        if (is_null(static::$apen_pamelding)) {
            static::_loadSesong();
        }
        return static::$apen_pamelding;
    }

    /**
     * Hent aktiv sesong
     *
     * @return Int
     */
    public static function getSesong()
    {
        return intval(get_site_option('season'));
    }
    /**
     * Hent startdato for sesongen
     *
     * @return void
     */
    public static function getSesongStart()
    {
        if (is_null(static::$start_sesong)) {
            static::_loadSesong();
        }
        return static::$start_sesong;
    }


    /**
     * Last inn info om sesongen
     *
     * @return void
     */
    private static function _loadSesong()
    {
        $start_sesong = strtotime(
            str_replace(
                'YYYY',
                static::getSesong() - 1,
                UKMDesign::getConfig('pamelding.starter')
            )
        );

        # @UNIXTIME funker for å opprette direkte fra unix timestamp
        static::$start_sesong = new DateTime('@' . $start_sesong);

        if (date('m') > 4 && date('m') < date('m', $start_sesong)) {
            static::$apen_pamelding = false;
        } else {
            static::$apen_pamelding = time() > $start_sesong;
        }
    }

    /**
     * Sjekk om denne siden har arrangement, og lagre på objektet
     */
    private static function _harArrangement()
    {
        # (dobbel nekting er riktig)
        static::$har_arrangement = !!get_option('pl_id');
    }

    /**
     * Tilhører forsiden et arrangement
     *
     * @return bool
     */
    public static function harArrangement()
    {
        return static::$har_arrangement;
    }

    /**
     * Hent sidens arrangement
     *
     * @return Arrangement
     */
    public static function getArrangement()
    {
        if (null == static::$arrangement) {
            static::$arrangement = new Arrangement(intval(get_option('pl_id')));
        }
        return static::$arrangement;
    }

    /**
     * Kan forsiden ha en meny?
     *
     * @return bool
     */
    public static function supportMeny()
    {
        return true;
    }

    /**
     * Har forsiden en meny?
     *
     * @return boolean
     */
    public static function hasMeny()
    {
        return !is_null(static::getMeny());
    }

    /**
     * Hent meny-ID
     *
     * @return Int
     */
    public static function getMeny()
    {
        $val = get_option('UKM_front_menu');
        if ($val !== false) {
            return intval($val);
        }
        return $val;
    }

    /**
     * Sett menyID
     *
     * @param Int $meny_id
     * @return void
     */
    public static function setMeny(Int $meny_id)
    {
        return update_option('UKM_front_menu', $meny_id);
    }

    /**
     * Har siden muligheter for et slogan?
     *
     * @return bool
     */
    public static function supportBannerSlagord()
    {
        return !static::erKommuneSide() && !static::erFylkeSide() && !static::harArrangement() && get_current_blog_id() != 1;
    }


    /**
     * Har siden definert et slagord?
     * 
     * OBS: Krever også at siden har angitt et BannerBilde
     *
     * @return bool
     */
    public static function harBannerSlagord()
    {
        return static::supportBannerSlagord() && static::harBannerBilde() && !empty(static::getBannerSlagord());
    }

    /**
     * Hent sidens slagord
     *
     * @return String slagord
     */
    public static function getBannerSlagord()
    {
        return get_option('UKM_banner_slogan');
    }

    /**
     * Sett sidens slagord
     *
     * @param String $slagord
     * @return void
     */
    public static function setBannerSlagord(String $slagord)
    {
        $slagord = substr($slagord, 0, static::LENGTH_SLAGORD);
        return update_option('UKM_banner_slogan', $slagord);
    }

    /**
     * Støtter siden egendefinert farge på hovedmeny-knappen
     *
     * @return void
     */
    public static function supportBannerMenyFarge()
    {
        return static::supportBannerSlagord();
    }

    /**
     * Har hovedmeny-knappen spesifisert en farge?
     *
     * @return Bool
     */
    public static function harBannerMenyFarge()
    {
        return !empty(static::getBannerMenyFarge());
    }

    /**
     * Hent hovedmeny-knappens farge
     *
     * @return String|false
     */
    public static function getBannerMenyFarge()
    {
        return get_option('UKM_banner_menu_color');
    }

    /**
     * Oppdater hovedmeny-knappens farge
     *
     * @param String $farge
     * @return void
     */
    public static function setBannerMenyFarge(String $farge)
    {
        return update_option('UKM_banner_menu_color', $farge);
    }

    /**
     * Støtter denne forsiden banner-bilder
     *
     * @return bool
     */
    public static function supportBannerBilde()
    {
        return true;
    }

    /**
     * Har denne siden definert et banner-bilde
     *
     * @return bool
     */
    public static function harBannerBilde()
    {
        # (dobbel nekting er riktig)
        return !!get_option('UKM_banner_image');
    }

    /**
     * Hent banner-bildet
     *
     * @return Banner
     */
    public static function getBannerBilde()
    {
        # Hent banner
        $banner = new Banner(
            str_replace('http:', 'https:', get_option('UKM_banner_image'))
        );

        # Posisjoner banner (hvis angitt)
        if (get_option('UKM_banner_image_position_y')) {
            $banner->setPosY(
                new Vertical(
                    get_option('UKM_banner_image_position_y')
                )
            );
        }

        # Sett stort bilde (fordi banner liker visst det)
        $large_image = get_option('UKM_banner_image_large');
        if (is_string($large_image) && !empty($large_image)) {
            $banner->setLarge(
                new Image($large_image)
            );
        }
        return $banner;
    }


    /**
     * Støtter denne forsiden banner-bilder
     *
     * @return bool
     */
    public static function supportInfoside()
    {
        return static::$supportInfoside;
    }

    /**
     * Sett SEO-defaults
     *
     * @return void
     */
    public static function setSEODefaults()
    {
        if (static::harArrangement()) {
            $arrangement = static::getArrangement();
            UKMDesign::getHeader()::getSEO()
                ->setTitle('UKM ' . $arrangement->getNavn())
                ->setDescription(
                    ($arrangement->harKart()
                        ? $arrangement->getKart()->getName()
                        : $arrangement->getSted()) . ', ' .
                        $arrangement->getStart()->format('j. M Y \k\l. H:i')
                )
                ->setUrl(get_home_url());
        } else {
            UKMDesign::getHeader()::getSEO()
                ->setTitle(get_bloginfo('name'))
                ->setDescription(get_bloginfo('description'))
                ->setUrl(get_home_url());
        }
    }
    /**
     * Er dette en kommuneside? (med eller uten arrangement)
     *
     * @return Bool
     */
    public static function erKommuneSide()
    {
        return get_option('site_type') == 'kommune';
    }

    /**
     * Er dette en fylkesside?
     *
     * @return Bool
     */
    public static function erFylkeSide()
    {
        return get_option('site_type') == 'fylke';
    }

    /**
     * Hent fylket for denne siden
     *
     * @return Fylke
     * @throws Exception
     */
    public static function getFylke() {
        if( is_null(static::$fylke)) {
            if( static::erFylkeSide()) {
                static::$fylke = Fylker::getById(get_option('fylke'));
            }
            elseif( static::erKommuneSide() ) {
                static::$fylke = static::getKommune()->getFylke();
            }
            else {
                throw new Exception(
                    'Kan ikke bruke getFylke() på sider som ikke er fylke- eller kommunesider'
                );
            }
        }
        return static::$fylke;
    }

    /**
     * Hent kommunen for denne siden
     *
     * @return Kommune
     */
    public static function getKommune() {
        if( !static::erKommuneSide()) {
            throw new Exception(
                'Kan ikke bruke getKommune() på sider som ikke er kommunesider'
            );
        }
        if( is_null(static::$kommune) ) {
            static::$kommune = new Kommune(get_option('kommune'));
        }
        return static::$kommune;
    }

    /**
     * Støtter denne siden en custom section?
     * 
     * Ordinære fylkes- og kommune-sider støtter ikke dette,
     * da kun falske kommuner og fylker kan benytte denne funksjonen.
     *
     * @return Bool
     */
    public static function supportSection() {
        if( static::erFylkeSide() && static::getFylke()->erFalskt() ) {
            return true;
        }
        if( static::erKommuneSide() && static::getFylke()->erFalskt() ) {
            return true;
        }
        
        return false;
    }
}
