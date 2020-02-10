<?php

use UKMNorge\Database\SQL\Query;
use UKMNorge\DesignWordpress\Environment\Statistikk;
use UKMNorge\DesignWordpress\Environment\Wordpress;

Wordpress::setView('Statistikk/Pameldte');

$selected_month = Statistikk::getValgtManed();
$dager_per_maned = Statistikk::getDagerPerManed();
$alle_ar = Statistikk::getAlleAr();

$qry = "SELECT 	COUNT(`stat_id`) AS `antall`,
				DATE_FORMAT(`time`, '%d') AS `dag`,
				DATE_FORMAT(`time`, '%m') AS `mnd`,
				DATE_FORMAT(`time`, '%Y') AS `ar`,
				DATE_FORMAT(`time`, '%Y-%m-%d') AS `dato`
		FROM `ukm_statistics`
		WHERE `time` > '#start_year-#start_month%'
		GROUP BY `dato`
		ORDER BY `dato` ASC";
$sql = new Query(
    $qry,
    [
        'start_year' => Statistikk::START_AR-1,
        'start_month' => Statistikk::START_MANED
    ]
 );
$res = $sql->run();
while ($r = Query::fetch($res)) {
    if (intval($r['mnd']) < Statistikk::START_MANED && intval($r['mnd']) > 4) {
        continue;
    }
    $per_dag[Statistikk::korrigerDato($r['dag'], $r['mnd'], $r['ar'])] = $r['antall'];
}


$akk = [];
foreach ($alle_ar as $ar) {
    $akk['ar'][$ar] = 0;
    foreach ($dager_per_maned as $mnd => $dager) {
        $akk['mnd'][$ar][$mnd] = 0;
        // For 1 ... ant_dager_i_mnd
        for ($dag = 1; $dag <= $dager; $dag++) {

            // ID og antall-hjelpere
            $dag = $dag < 10 ? '0' . $dag : $dag; # korrigerer for 01..09
            $dato = $ar . '-' . $mnd . '-' . $dag;
            $antall = isset($per_dag[$dato]) ? $per_dag[$dato] : 0;

            // AKKUMULERT ANTALL
            // Akkumulert antall påmeldte per mnd og år (brukes til summering)
            $akk['mnd'][$ar][$mnd] += $antall;
            $akk['ar'][$ar] += $antall;

            // Lagre antall akk per gitt dag
            $akkumulert_per_maned[$dato] = $akk['mnd'][$ar][$mnd];
            $akkumulert_per_ar[$dato] = $akk['ar'][$ar];

            // Påmeldte per uke
            $uke = ((floor(($dag - 1) / 7)) + 1);
            $uke = Statistikk::datoerIUke($uke, $dager_per_maned[$mnd]);
            if (!isset($per_uke[$mnd][$uke][$ar])) {
                $per_uke[$mnd][$uke][$ar] = 0;
            }
            $per_uke[$mnd][$uke][$ar] += $antall;
        }
    }
}

// HVIS VI SKAL VISE OVERSIKTEN FOR KUN ÉN MÅNED
if ($selected_month) {
    Wordpress::addViewData(
        [
            'maned' => $dager_per_maned[$selected_month],
            'maned_id' => $selected_month,
            'maned_dager' => $dager_per_maned[$selected_month]
        ]
    );
}

Wordpress::addViewData(
    [
        'active' => is_numeric($selected_month) ? $selected_month : false,
        'start_maned' => Statistikk::START_MANED,
        'history' => Statistikk::getAntallAr(),
        'stat_ar' => $alle_ar,
        'stat_mnd' => $dager_per_maned,
        'stat' => $per_dag,
        'uker' => $per_uke,
        'akk_mnd' => $akkumulert_per_maned,
        'akk_ar' => $akkumulert_per_ar,
    ]
);