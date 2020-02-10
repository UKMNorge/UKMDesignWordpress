<?php

namespace UKMNorge\DesignWordpress\Environment;

class Statistikk
{
    const START_AR = 2013;
    const START_MANED = 10;

    static $antall_ar;

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
     * Hent et slags uke-nummer basert på dato-intervall
     *
     * @param [type] $nummer
     * @param [type] $days_in_month
     * @return void
     */
    public static function datoerIUke($nummer, $days_in_month)
    {
        switch ($nummer) {
            case 1:
                return '1.-7.';
            case 2:
                return '8.-14.';
            case 3:
                return '15.-21.';
            case 4:
                return '22.-28.';
        }
        return '29.-' . $days_in_month;
    }

    /**
     * Hent hvilken måned vi skal vises statstikk for
     * 
     * Returnerer false hvis ingen er valgt, og vi skal vise hele året.
     *
     * @return Int|false 
     */
    public static function getValgtManed()
    {
        return isset($_GET['mnd']) ? static::padManed($_GET['mnd']) : false;
    }

    /**
     * Hent antall år vi har statistikk for
     *
     * @return Int
     */
    public static function getAntallAr()
    {
        if (is_null(static::$antall_ar)) {
            static::$antall_ar = static::getSesong() - static::START_AR;
        }
        return static::$antall_ar;
    }

    /**
     * Hent alle år vi skal lage statistikk for
     *
     * @return Array
     */
    public static function getAlleAr()
    {
        $alle_ar = [];
        for ($i = static::START_AR; $i < static::getSesong() + 1; $i++) {
            $alle_ar[] = $i;
        }
        return $alle_ar;
    }

    /**
     * Hent hvor mange dager det er per måned
     *
     * @return Array
     */
    public static function getDagerPerManed()
    {
        return [
            #	'08'=>31,
            #	'09'=>30,
            '10' => 31,
            '11' => 30,
            '12' => 31,
            '01' => 31,
            '02' => 28,
            '03' => 31
        ];
    }

    /**
     * Pad en måned med 0 først
     *
     * @param String $maned
     * @return String $maned
     */
    public static function padManed(String $maned)
    {
        return str_pad((string) $maned, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Korriger dato for statistikk
     * 
     * Aktiv sesong starter på høsten året i forveien.
     * Fra september til desember har statistikken derfor
     * år=forrige_år, og må økes med 1 for å telles inn
     * under aktiv sesong
     *
     * @param String $dag
     * @param String $maned
     * @param String $ar
     * @return String $ar-$maned-$dag
     */
    public static function korrigerDato(String $dag, String $maned, String $ar)
    {
        if (intval($maned) > 8) {
            $ar++;
        }
        return
            $ar . '-' .
            static::padManed($maned) . '-' .
            $dag;
    }
}
