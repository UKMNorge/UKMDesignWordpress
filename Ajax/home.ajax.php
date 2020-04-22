<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\DesignWordpress\Environment\Ajax;
use UKMNorge\Geografi\Fylke;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Geografi\Kommune;
use UKMNorge\Nettverk\Omrade;

require_once('UKM/Autoloader.php');

$kommune_id = intval($_REQUEST['kommune']);
$fylke_id = intval($_REQUEST['fylke']);

#$kommune_id = 5403;// Alta
#$kommune_id = 5441;// Tana
#$fylke_id = 54; // Troms og Finnmark

$fylke = Fylker::getById($fylke_id);

// Kommunens arrangement
if( !$fylke->erOslo()) {
    $kommune = new Kommune($kommune_id);
    $kommune_omrade = Omrade::getByKommune($kommune_id);
    $arrangementer_kommune = $kommune_omrade->getArrangementer(intval(get_site_option('season')));

    Ajax::addResponseData(
        'kommune',
        Data::kommune($kommune, $arrangementer_kommune->getAll())
    );
}

// Fylkets arrangement
$fylke_omrade = Omrade::getByFylke($fylke_id);
$arrangementer_fylke = $fylke_omrade->getArrangementer(intval(get_site_option('season')));

Ajax::addResponseData(
    'fylke',
    Data::fylke($fylke, $arrangementer_fylke->getAll())
);



class Data {
    public static function fylke_eller_kommune( $geografisk_enhet, Array $arrangementer ) {
        $data = new stdClass();
        $data->id = $geografisk_enhet->getId();
        $data->navn = $geografisk_enhet->getNavn();
        $data->url = $geografisk_enhet->getLink();

        $data->arrangementer = [];
        foreach( $arrangementer as $arrangement ) {
            $data->arrangementer[] = Data::arrangement($arrangement);
        }
        return $data;
    }

    public static function fylke( Fylke $fylke, Array $arrangementer ) {
        $data = static::fylke_eller_kommune($fylke, $arrangementer);
        return $data;
    }
    
    public static function kommune( Kommune $kommune, Array $arrangementer ) {
        $data = static::fylke_eller_kommune($kommune, $arrangementer);
        return $data;
    }

    public static function arrangement( Arrangement $arrangement ) {
        $data = new stdClass();
        $data->id = $arrangement->getId();
        $data->navn = $arrangement->getNavn();
        $data->url = $arrangement->getLink();
        
        $data->erStartet = $arrangement->erStartet();
        $data->erFerdig = $arrangement->erFerdig();
        
        $data->start = $arrangement->getStart();
        $data->startNice = str_replace(['may','oct','dec'],['mai','okt','des'], strtolower($arrangement->getStart()->format('j. M')));
        $data->stop = $arrangement->getStop();
        $data->stopNice = str_replace(['may','oct','dec'],['mai','okt','des'],strtolower($arrangement->getStop()->format('j. M')));
        
        $data->sted = $arrangement->getSted();
        $data->kart = $arrangement->getKart();

        $data->status = static::status( $arrangement );
        return $data;
    }

    public static function status( Arrangement $arrangement ) {
        $data = new stdClass();
        $data->id = $arrangement->getMetaValue('avlys');
        $data->avlyst = false;

        switch( $data->id ) {
            case 'videresending_kanskje':
                $data->navn = 'Videresendingen kan bli avlyst';
            break;
            case 'videresending_sikkert':
                $data->navn = 'Ingen blir videresendt';
            break;
            case 'avlys':
                $data->navn = 'Avlyst!';
                $data->avlyst = true;
            break;
            case 'gjennomforer_med_info':
                $data->navn = 'Viktig info!';
            break;
        }
        $data->melding = $arrangement->getMetaValue('avlys_status_kort');
        $data->obs = !( empty($data->melding) && !$data->id);
            
        return $data;
    
    }

}

