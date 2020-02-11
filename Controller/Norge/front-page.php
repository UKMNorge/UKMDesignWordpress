<?php

use UKMNorge\DesignWordpress\Environment\Front;
use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Geografi\Fylker;

$now = new DateTime();

/**
 * STANDARD-VISNING
 */
Wordpress::addViewData(
    [
        'pamelding_apen' => Front::erNasjonalPameldingApen(),
        'fylker' => Fylker::getAll()
    ]
);

$start_festivalperiode = DateTime::createFromFormat('m-d H:i', '05-18 00:00');
$stop_festivalperiode = DateTime::createFromFormat('m-d H:i', '08-01 00:00');

$start_nasjonaldag = DateTime::createFromFormat('m-d H:i', '05-17 00:00');
$stop_nasjonaldag = DateTime::createFromFormat('m-d H:i', '05-18 00:00');

$start_mgpjr = DateTime::createFromFormat('Y-m-d H:i', '2018-11-03 20:00');
$stop_mgpjr = DateTime::createFromFormat('Y-m-d H:i', '2018-11-10 23:59');

$start_fylker = DateTime::createFromFormat('Y-m-d H:i', date('Y') . '-04-01 00:00');
$stop_fylker = DateTime::createFromFormat('Y-m-d H:i', date('Y') . '-05-16 23:59');

if( $start_nasjonaldag < $now && $stop_nasjonaldag > $now || isset($_GET['nasjonaldag'])) {
    Wordpress::setView('Norge/Front/Nasjonaldagen');
} elseif ($start_festivalperiode < $now && $stop_festivalperiode > $now || isset($_GET['festival'])) {
    Wordpress::setView('Norge/Front/Festival');
} elseif (($start_mgpjr < $now && $stop_mgpjr > $now) || isset($_GET['mgpjr'])) {
    Wordpress::setView('Norge/Front/MGPjr');
} elseif (($start_fylker < $now && $stop_fylker > $now) || isset($_GET['fylker'])) {
    Wordpress::requireController('Norge','front-page-fylker');
    Wordpress::setView('Norge/Front/Fylker');
} else {
    Wordpress::setView('Norge/Front/Standard');
}