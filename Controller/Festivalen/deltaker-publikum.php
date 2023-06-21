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
$posts = Posts::getByCategory($catid);
$posts->paged = 0;
$posts->setPostsPerPage(200);
$posts->loadPosts();

$arrangement = new Arrangement( get_option('pl_id') );

$hendelser = $visInterne ? $arrangement->getProgram()->getAllInkludertInterne() : $arrangement->getProgram()->getAll();
$program = Hendelser::sorterPerDag( $hendelser );


$hendelserDato = [];
foreach ($program as $p) {
    foreach($p->forestillinger as $f) {
        $hendelserDato[$f->getStart()->getTimestamp()][] = $f;
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

        $ret = $passed ? 'Startet for ca. ' : 'Om ca. ';
        $retSek = $passed ? 'Startet for ' : 'Starter Om ';
        

        if($val == 'sekund') {
            $ret = $retSek . 'noen sekunder';
        }
        else if($val == 'minutt') {
            $ret = $ret . $numberOfUnits . ' minutt' . (($numberOfUnits > 1) ? 'er' : '');
        }
        else if($val == 'time') {
            if($numberOfUnits > 5 || !$passed) {
                $date = new DateTime($datetime);
                
                // return $date->format('d.m H:i');
                return ucfirst($filtersClass->dato(date("Y-m-d H:i:s"), 'l')) . ' ' . $date->format('H:i');
            }
            $ret = $ret . $numberOfUnits . ' time' . (($numberOfUnits > 1) ? 'r' : '');
        }
        
        return $passed ? $ret . ' siden' : $ret;
    }
};

Twig::addFilter('timeago', $filter);

Wordpress::setPosts($posts);


function find_closest($array, $date) {
    foreach($array as $hendelse) {   
        $val = (strtotime($date) - strtotime($hendelse[0]->getStart()->format('Y-m-d H:i:s')));
        $retArr[] = [
            'valueReal' => $val, 
            'value' => abs($val),
            'hendelse' => $hendelse[0]
        ];
    }

    usort($retArr, function($a, $b) { return $a['value'] > $b['value'] ? 1 : ($b['value'] < $a['value'] ? -1 : 0); });


    return $retArr;
}

$henselserSorter = find_closest($hendelserDato, date("Y-m-d H:i:s"));

Wordpress::addViewData([
    'henselserSorter' => $henselserSorter
]);

Wordpress::setView('Festivalen/Front/DeltakerPublikum');