<?php

use UKMNorge\Design\Sitemap\Section;
use UKMNorge\Design\UKMDesign;

header('Content-Type: text/html; charset=utf-8');
session_start();
setlocale(LC_ALL, 'nb_NO', 'nb', 'no');

// CHECK CACHE (OG DØ HVIS DEN ER FUNNET)
require_once('cache.php');

// RENDER MED ELLER UTEN FRAMEWORK-HTML
if( ( isset($_POST['singleMode']) && "true" == $_POST['singleMode'] ) || ( isset($_GET['singleMode']) && "true" == $_GET['singleMode']) ) {
    UKMDesign::setRenderWithoutFramework();
}

// SETT OPP CURRENT SECTION
// Sjekk om site_type gir oss en eksisterende section,
// sånn som f.eks. organisasjonen
$section = UKMDesign::getSitemap()->getSection(get_option('site_type'));
if( !$section ) {
    $section = new Section(
        'current',
        get_bloginfo('url'),
        get_bloginfo('name')
    );
}
UKMDesign::setCurrentSection($section);