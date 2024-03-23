<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Design\Sitemap\Breadcrumbs;
use UKMNorge\Design\Sitemap\Page;
use UKMNorge\Design\Sitemap\Section;
use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Geografi\Kommune;

header('Content-Type: text/html; charset=utf-8');
session_start();
setlocale(LC_ALL, 'nb_NO', 'nb', 'no');

/**
 * TemplateEngine (i dette tilfellet Wordpress) bygger et array med viewData
 * herunder også viewData['UKMDesign', new UKMDesign() ]
 * 
 * Alt som kan benyttes av andre Design (delta, pressemelding, UKM-TV osv),
 * herunder Header, Banner, SEO osv legges til UKMDesign,
 * mens alt som benyttes kun av wordpress legges til TemplateEngine
 */

/**
 * 
 * CACHING
 * ================================
 */
require_once('cache.php');


/**
 * 
 * SINGLEMODE RENDER 
 * ================================
 * Rendrer uten rammeverk (kun innhold, altså)
 */
if ((isset($_POST['singleMode']) && "true" == $_POST['singleMode']) || (isset($_GET['singleMode']) && "true" == $_GET['singleMode'])) {
    UKMDesign::setRenderWithoutFramework();
}


/**
 * 
 * CURRENT SECTION
 * ================================
 */
// Sjekk om site_type gir oss en eksisterende section,
// sånn som f.eks. organisasjonen
$section = UKMDesign::getSitemap()->getSection(get_option('site_type'));
if (!$section) {
    $section = new Section(
        'current',
        get_bloginfo('url'),
        get_bloginfo('name')
    );
}
UKMDesign::setCurrentSection($section);
Wordpress::setPage(null);


/**
 * 
 * SEARCH ENGINE OPTIMIZATION
 * ================================
 */
# Author
$author = isset(Wordpress::getPage()->author) && !empty(Wordpress::getPage()->author->display_name) ?
    Wordpress::getPage()->author->display_name :
    'UKMNorge';
UKMDesign::getHeader()::getSEO()->setAuthor($author);

# Lead or default-description
if (!empty(strip_tags(Wordpress::getPage()->lead))) {
    Wordpress::getPage()->setDescription(
        strip_tags(Wordpress::getPage()->lead)
    );
} else {
    Wordpress::getPage()->setDescription(
        UKMDesign::getConfig('hvaerukm.slogan_alt')
    );
}

echo '<link rel="stylesheet" id="UKMArrSysStyle-css" href="//assets.'. UKM_HOSTNAME .'//css/arr-sys.css?ver=6.4.3" media="all">';
echo '<div id="messageMainDiv"><div class="container"><div data-v-cb24bd16="" class="info-type vue-componment-notification-message nosh-impt as-card-2 as-padding-space-1 as-padding-left-space-2 as-padding-right-space-2 as-margin-top-space-1"><h6 data-v-cb24bd16="" class="title">Viktig varsel</h6><span data-v-cb24bd16="" class="description nop">På grunn av planlagt vedlikehold vil ukm.no og alle våre systemer være ustabile eller utilgjengelige mandag den 25. mars 2024. Vi beklager eventuelle ulemper dette måtte medføre!</span></div></div></div>';
echo '<style>#messageMainDiv span{font-size: 13px;}#messageMainDiv{padding: 10px; background: #02024b}.vue-componment-notification-message {    background: var(--as-color-primary-warning-lightest);border: solid 2px var(--as-color-primary-warning-light);}</style>';

/**
 * 
 * SETT OPP BREADCRUMBS
 * ================================
 */
# Arrangement
if (get_option('pl_id')) {
    $arrangement = Arrangement::getById((int) get_option('pl_id'));
    if ($arrangement->erFellesmonstring()) {
        # Opprett section
        $section_kommune = new Section(
            'kommuner',
            '#',
            'Velg kommune'
        );
        $section_fylke = new Section(
            'fylker',
            '#',
            'Velg fylke'
        );

        # Finn felles navn, og legg til pages
        $kommuner = [];
        $fylker = [];
        foreach ($arrangement->getKommuner() as $kommune) {
            $kommuner[$kommune->getNavn()] = $kommune;
            $fylke = $kommune->getFylke();
            if (!isset($fylker[$fylke->getNavn()])) {
                $fylker[$fylke->getNavn()] = $fylke;
            }
        }
        ksort($kommuner);
        ksort($fylker);

        foreach ($kommuner as $kommune) {
            $section_kommune->getPages()->add(
                Page::create($kommune->getLink(), $kommune->getNavn(), 'kommune_' . $kommune->getId())
            );
        }

        foreach ($fylker as $fylke) {
            $section_fylke->getPages()->add(
                Page::create($fylke->getLink(), $fylke->getNavn(), 'fylke_' . $fylke->getId())
            );
        }

        Breadcrumbs::addSection($section_fylke);
        Breadcrumbs::addSection($section_kommune);
    } else {
        Breadcrumbs::addArrangement($arrangement);
    }
} else {
    # Fylke, kommune, land
    switch (get_option('site_type')) {
        case 'fylke':
            Breadcrumbs::addFylke(Fylker::getById(get_option('fylke')));
            break;
        case 'kommune':
            Breadcrumbs::addKommune(new Kommune((int) get_option('kommune')));
            break;
        default:
#            var_dump(get_option('site_type'));
            break;
    }
}

if (!is_front_page()) {
    $page = Page::create(
        Wordpress::getPage()->getUrl(),
        Wordpress::getPage()->getTitle()
    );
    Breadcrumbs::setPage($page);
}
