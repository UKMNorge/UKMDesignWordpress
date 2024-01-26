<?php

use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Posts;
use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Twig\Twig;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Hendelser;
use UKMNorge\Twig\Definitions\Filters;


UKMDesign::getHeader()->hideSectionTitle();
UKMDesign::getHeader()->getLogo()->hide();

Wordpress::setView('Festivalen/Front/Underveis');

// Get alle posts på kategori 'pa-forside'
$catid = get_category_by_slug('pa-forsiden')->term_id;

if($catid) {
    $posts = Posts::getByCategory($catid);
    $posts->paged = 0;
    $posts->setPostsPerPage(200);
    $posts->loadPosts();
    Wordpress::setPosts($posts);
}

$arrangement = new Arrangement( get_option('pl_id') );

$hendelser = $visInterne ? $arrangement->getProgram()->getAllInkludertInterne() : $arrangement->getProgram()->getAll();
$program = Hendelser::sorterPerDag( $hendelser );

$has_started_direktesending = false;

$hendelserDato = [];
$hendelserWorkshopDato = [];
foreach ($program as $p) {
    foreach($p->forestillinger as $f) {
        if($f->getType() == 'category') {
            $hendelserWorkshopDato[] = $f;
        } else {
            $hendelserDato[] = $f;
        }

        // If forestilling er aktiv og den har videresending, aktiver has_started_direktesending
        if($f->harSending() && $f->getSending()->erStartet() && !$f->getSending()->erFerdig()) {
            $has_started_direktesending = true;
        }
    }
}

$filter = function ($datetime) {
    $filtersClass = new Filters();
    $passed = time() > strtotime($datetime);
    $time = abs(time() - strtotime($datetime)); 
    
    if($time > 3600) {
        // return $datetime;
    }
    
    $units = array (
        3600 => 'time',
        60 => 'minutt',
        1 => 'sekund'
    );
  
    foreach ($units as $unit => $val) {
        if ($time < $unit) continue;
        
        $numberOfUnits = abs(floor($time / $unit));

        $ret = $passed ? 'Startet for ' : 'Om ';
        $retSek = $passed ? 'Startet for ' : 'Starter Om ';
        

        if($val == 'sekund') {
            $ret = $retSek . 'noen sekunder';
        }
        else if($val == 'minutt') {
            $ret = $ret . $numberOfUnits . ' minutt' . (($numberOfUnits > 1) ? 'er' : '');
        }
        else if($val == 'time') {
            if($numberOfUnits > 11) {
                $date = new DateTime($datetime);
                // return $date->format('d.m H:i');
                return ucfirst($filtersClass->dato($date, 'l')) . ' ' . $date->format('H:i');
            }
            $ret = $ret . $numberOfUnits . ' time' . (($numberOfUnits > 1) ? 'r' : '');
        }
        
        return $passed ? $ret . ' siden' : $ret;
    }
};

Twig::addFilter('timeago', $filter);


function find_closest($array, $date) {
    $currentVal = null;
    $bestKey = 0;
    foreach($array as $key => $hendelse) {
        $val = abs(strtotime($date) - strtotime($hendelse->getStart()->format('Y-m-d H:i:s')));
        
        if($currentVal == null) {
            $currentVal = $val;
        }
        
        if($val < $currentVal) {
            $currentVal = $val;
            $bestKey = $key;
        }
    }

    return $bestKey;
}

$hendelseKey = find_closest($hendelserDato, date("Y-m-d H:i:s"));
$workshopKey = find_closest($hendelserWorkshopDato, date("Y-m-d H:i:s"));


Wordpress::addViewData([
    'hendelserDato' => $hendelserDato,
    'hendelserWorkshopDato' => $hendelserWorkshopDato,
    // Hvis hendelseKey er større enn 0, da går vi en gang tilbake for å starte med en hendelse tilbake
    'hendelseKey' => $hendelseKey > 0 ? ($hendelseKey-1) : $hendelseKey,
    'workshopKey' => $workshopKey > 0 ? ($workshopKey-1) : $workshopKey,
    'arrangement' => $arrangement,
    'has_started_direktesending' => $has_started_direktesending

]);

Wordpress::setView('Festivalen/Front/DeltakerPublikum');