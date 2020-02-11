<?php

namespace UKMNorge\DesignWordpress\Environment;

use DateTime;
use UKMNorge\Design\Header\Banner;
use UKMNorge\Design\Image;
use UKMNorge\Design\Position\Vertical;
use UKMNorge\Design\UKMDesign;

class Front
{
    const LENGTH_SLAGORD = 125;
    static $har_arrangement;
    static $start_sesong;
    static $apen_pamelding;


    public static function init()
    {
        static::_harArrangement();
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
                get_site_option('season') - 1,
                UKMDesign::getConfig('pamelding.starter')
            )
        );

        # @UNIXTIME funker for å opprette direkte fra unix timestamp
        static::$start_sesong = new DateTime('@'. $start_sesong);

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
        return !static::harArrangement() && get_current_blog_id() != 1;
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
}
