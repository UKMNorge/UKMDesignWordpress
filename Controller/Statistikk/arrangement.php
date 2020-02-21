<?php

/**
 * DENNE SKAL ALDRI ADRESSERES DIREKTE.
 * Filen må inkluderes fra annen kontroller som definerer
 * $COMPARE_FUNCTION først
 * 
 */

use UKMNorge\Arrangement\Load;
use UKMNorge\DesignWordpress\Environment\Statistikk;
use UKMNorge\DesignWordpress\Environment\Wordpress;

global $COMPARE_FUNCTION;

$selected_month = Statistikk::getValgtManed();
$dager_per_maned = Statistikk::getDagerPerManed();

$ar_start = 2013;
$ar_history = get_site_option('season') - $ar_start;
$ar_stop = get_site_option('season');

$alle_ar = Statistikk::getAlleAr();

$per_dag = [];
$per_uke = [];

// Loop ønskede sesonger
foreach ($alle_ar as $ar) {
    $arrangement_collection = Load::bySesong($ar);
    foreach ($arrangement_collection->getAll() as $arrangement) {

        if (!$arrangement->erRegistrert()) {
            continue;
        }
        // STARTER PER DAG
        $startdato = Statistikk::korrigerDato(
            $arrangement->$COMPARE_FUNCTION()->format('d'),
            Statistikk::padManed($arrangement->$COMPARE_FUNCTION()->format('m')),
            $arrangement->$COMPARE_FUNCTION()->format('Y')
        );
        
        // Initier start-container hvis ikke allerede gjort
        if (!isset($per_dag[$startdato])) {
            $per_dag[$startdato] = 0;
        }

        // Tell mønstringen på start-dato
        $per_dag[$startdato]++;

        // STARTER PER UKE
        $starter_dag = $arrangement->$COMPARE_FUNCTION()->format('d');
        $starter_mnd = $arrangement->$COMPARE_FUNCTION()->format('m');
        $starter_uke = ((floor(($starter_dag - 1) / 7)) + 1);
        $starter_uke = Statistikk::datoerIUke($starter_uke, $dager_per_maned[$starter_mnd]);

        // Initier uke-container hvis ikke allerede gjort
        if (!isset($per_uke[$starter_mnd][$starter_uke][$ar])) {
            $per_uke[$starter_mnd][$starter_uke][$ar] = 0;
        }
        // Tell mønstringen på start-uke
        $per_uke[$starter_mnd][$starter_uke][$ar]++;
    }
}

foreach ($dager_per_maned as $mnd => $dager) {
    if (is_array($per_uke[$mnd])) {
        ksort($per_uke[$mnd]);
    }

    // Hvis vi har uker denne måneden, men ikke alle - opprett alle uker
    if (is_array($per_uke[$mnd])) {
        for ($i = 0; $i < $dager_per_maned[$mnd]; $i++) {
            $uke = ((floor(($i - 1) / 7)) + 1);
            $uke = Statistikk::datoerIUke($uke, $dager_per_maned[$mnd]);
            if (!isset($per_uke[$mnd][$uke])) {
                $per_uke[$mnd][$uke] = 0;
            }
        }
    }

    // Sett 0-verdi for uker som ikke har arrangement i det hele tatt
    for ($i = Statistikk::START_AR; $i < Statistikk::getSesong() + 1; $i++) {
        if (!is_array($per_uke[$mnd])) {
            $per_uke[$mnd]['ingen'][$ar] = 0;
        }
    }
}

Wordpress::setView('Statistikk/Arrangement');
Wordpress::addViewData(
    [
        'active' => is_numeric($selected_month) ? $selected_month : false,
        'start_maned' => Statistikk::START_MANED,
        'history' => Statistikk::getAntallAr(),
        'stat_ar' => $alle_ar,
        'stat_mnd' => $dager_per_maned,
        'dato' => $per_dag,
        'uke' => $per_uke,
        'type' => $COMPARE_FUNCTION == 'getStart' ? 'start' : 'frist'
        #        'akk_mnd' => $akkumulert_per_maned,
        #        'akk_ar' => $akkumulert_per_ar,
    ]
);
