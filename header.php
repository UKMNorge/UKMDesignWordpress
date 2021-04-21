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


/**
 * 
 * SETT OPP BREADCRUMBS
 * ================================
 */
# Arrangement
if (get_option('pl_id')) {
    Breadcrumbs::addArrangement(Arrangement::getById((int) get_option('pl_id')));
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
