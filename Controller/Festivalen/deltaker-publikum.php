<?php

use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Posts;
use UKMNorge\DesignWordpress\Environment\Wordpress;

UKMDesign::getHeader()->hideSectionTitle();
UKMDesign::getHeader()->getLogo()->hide();

Wordpress::setView('Festivalen/Front/Underveis');

// Get alle posts på kategori 'pa-forside'
$catid = get_category_by_slug('pa-forsiden')->term_id;
$posts = Posts::getByCategory($catid);
$posts->paged = 0;
$posts->setPostsPerPage(200);
$posts->loadPosts();


Wordpress::setPosts($posts);


Wordpress::setView('Festivalen/Front/DeltakerPublikum');