<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Innslag\Innslag;
use UKMNorge\DesignWordpress\Environment\Posts;

Wordpress::setView('Arrangement/Kunstner-info.htm.twig');
$arrangement = new Arrangement(get_option('pl_id'));;

$id = Wordpress::getLastParameter();
$innslag = null;
try{
    $innslag = Innslag::getById((int)$id);
}catch(Exception $e) {
    // Ikke nødvendigvis stor feil. Lenken kan inneholde ugyldig id for innslag
    throw new Exception('Ugyldig lenke!');
}


// Kategorien kunstnere-qr-kode brukes for å identifisere posts som tilhører til kunstnere
$catid = get_category_by_slug('kunstnere-qr-kode')->term_id;

// Hent alle posts på kategori
$posts = Posts::getByCategory($catid);
$posts->paged = 0;
$posts->setPostsPerPage(200);
$posts->loadPosts();

$post = null;
if($innslag) {
    foreach($posts->posts as $p) {
        // Sjekker tilpassede felt for innslag_id
        $tilpassedeFelt = get_post_meta($p->ID, 'innslag_id', true);
        if(strval($innslag->getId()) == $tilpassedeFelt) {
            $post = $p;
            continue;
        }
    }
}

Wordpress::addViewData(
    [
        'innslag' => $innslag,
        'arrangement' => $arrangement,
        'post' => $post
    ]
);
