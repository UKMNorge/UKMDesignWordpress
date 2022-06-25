<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Hendelser;
use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Post;
use UKMNorge\DesignWordpress\Environment\Posts;
use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Filmer\UKMTV\Direkte\Sendinger;
use Twig\TwigFilter;
use UKMNorge\Twig\Twig;

$arrangement = new Arrangement( get_option('pl_id') );

Wordpress::addViewData('arrangement',$arrangement);

## Skal hente ut programmet for en forestilling
$id = Wordpress::getLastParameter();

$visInterne = defined('DELTAKERPROGRAM') && DELTAKERPROGRAM;
$hendelser = $visInterne ? $arrangement->getProgram()->getAllInkludertInterne() : $arrangement->getProgram()->getAll();

$program = Hendelser::sorterPerDag( $hendelser );
Wordpress::setView('Arrangement/Program/LiveMode');
Wordpress::getPage()
    ->setTitle( 'Program for '.( $arrangement->getEierType() == 'kommune' ? 'UKM ' : ''). $arrangement->getNavn() )
    ->setDescription( 'Vi starter '. $arrangement->getStart()->format('j. M \k\l. H:i') );


$hendelserDato = [];
foreach ($program as $p) {
    foreach($p->forestillinger as $f) {
        $hendelserDato[$f->getStart()->getTimestamp()][] = $f;
    }
}

$filter = function ($datetime) {

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
            if($numberOfUnits > 23 || $passed) {
                $date = new DateTime($datetime);

                return $date->format('d.m H:i');
            }
            $ret = $ret . $numberOfUnits . ' time' . (($numberOfUnits > 1) ? 'r' : '');
        }
        
        return $passed ? $ret . ' siden' : $ret;
    }

  };

Twig::addFilter('timeago', $filter);


Wordpress::addViewData(
    [
        'visInterne' =>$visInterne,
        'program' => $program,
        'hendelserDato' => $hendelserDato,
    ]
);