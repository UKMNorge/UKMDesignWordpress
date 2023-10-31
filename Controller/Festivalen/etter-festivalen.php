<?php

use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Posts;
use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Arrangement\Program\Hendelser;
use UKMNorge\Arrangement\Arrangement;



// Get alle posts på kategori 'pa-forside'
$catid = get_category_by_slug('pa-forsiden')->term_id;
if($catId) {
    $posts = Posts::getByCategory($catid);
    $posts->paged = 0;
    $posts->setPostsPerPage(200);
    $posts->loadPosts();
    Wordpress::setPosts($posts);
}

$arrangement = new Arrangement( get_option('pl_id') );

$hendelserMedSending = [];
foreach ($arrangement->getProgram()->getAll() as $hendelse) {
    if($hendelse->harSending()) {
        $hendelserMedSending[] = $hendelse;
    }
}


Wordpress::addViewData([
    'hendelserMedSending' => $hendelserMedSending
]);
Wordpress::setView('Festivalen/Front/EtterFestivalen');

