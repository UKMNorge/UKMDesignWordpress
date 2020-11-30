<?php

use UKMNorge\Arrangement\Filter;
use UKMNorge\Arrangement\Load;
use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Geografi\Kommune;

require_once('header.php');
require_once('UKM/Autoloader.php');

if (!isset($_GET['retry'])) {
    // Hvis sesong-parameteret mangler, henger dette igjen fra tidligere
    // Hvis det er satt for forrige sesong, så skal det også bort.
    $season = get_option('season');
    if (!$season || get_site_option('season') < date('Y')) {

        $kommune_id = get_option('kommune');
        if ($kommune_id) {
            $kommune = new Kommune($kommune_id);
            update_option('blogname', $kommune->getNavn());
        }

        delete_option('status_monstring');
        header("Location: " . get_bloginfo('url') . '?retry=true');
        echo '<script type="javascript">window.location.href = window.location.href + "?retry=true";</script>';
        exit();
    }
}

Wordpress::setView('Arrangement/Flyttet/' . get_option('status_monstring'));

// Prøv å finn nye mønstringer for kommunen
$arrangementer = [];

/**
 * Hent kommuner fra bloggen
 * Ved avlysning / flytting / endring lagres en liste
 * med kommuneid'er mønstringen har i endringstidspunktet
 **/
$kommuner = explode(',', get_option('kommuner'));
if (is_array($kommuner)) {
    // Loop alle kommuner
    foreach ($kommuner as $kommune_id) {
        // Prøv å finne en mønstring for kommunen
        #echo '<br /> Finn mønstring for kommune '. $kommune_id .' i '. get_site_option('season') .'-sesongen:';
        if (empty($kommune_id)) {
            continue;
        }
        try {
            $sesong = (int) get_site_option('season');

            $filter = new Filter();

            // Årets sesong
            $filter->sesong($sesong);
            $alle_arrangementer = Load::forKommune(new Kommune($kommune_id), $filter);
            foreach ($alle_arrangementer->getAll() as $arrangement) {
                $arrangementer[$arrangement->getId()] = $arrangement;
            }

            // Neste sesong
            $filter->sesong($sesong+1);
            $alle_arrangementer = Load::forKommune(new Kommune($kommune_id), $filter);
            foreach ($alle_arrangementer->getAll() as $arrangement) {
                $arrangementer[$arrangement->getId()] = $arrangement;
            }
            
            // Forrige sesong
            $filter->sesong($sesong-1);
            $alle_arrangementer = Load::forKommune(new Kommune($kommune_id), $filter);
            foreach ($alle_arrangementer->getAll() as $arrangement) {
                $arrangementer[$arrangement->getId()] = $arrangement;
            }

        } catch (Exception $e) {
            // Ignorer mønstringer vi ikke finner
        }
    }
}

/**
 * Hvis vi har funnet én mønstring for denne siden
 * er det dit brukeren skal
 *
 * Burde kanskje ikke gjelde når mønstringen er splittet, da det kan
 * være at deltakeren søker kommunen som er tatt ut. Videresender likevel inntil videre
 **/
if (sizeof($arrangementer) == 1) {
    $arrangement = array_shift($arrangementer);
    wp_redirect($arrangement->getLink());
    exit;
}

switch (get_option('status_monstring')) {
    case 'avlyst':
        try {
            $kommune = new Kommune($kommuner[0]);
            if ($kommune->getId()) {
                Wordpress::addViewData('kommune', $kommune);
            }
        } catch (Exception $e) {
            // Ignorer hvis vi ikke finner gitt kommune - view håndterer det
        }
        break;
    default:
        Wordpress::addViewData('arrangementer', $arrangementer);
        break;
}

require_once('render.php');
