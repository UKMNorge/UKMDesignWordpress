<?php

use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Posts;
use UKMNorge\DesignWordpress\Environment\Wordpress;


$category = get_queried_object();
#if (is_category()) {
    Wordpress::setPosts(
        Posts::getByCategory(get_queried_object_id())
    );
    Wordpress::addViewData('category', $category);
#}

Wordpress::setView('Kategori/Liste');
UKMDesign::getHeader()::getSEO()
    ->setTitle($category->name)
    ->setDescription(addslashes(preg_replace("/\r|\n/", "", strip_tags($WP_TWIG_DATA['category']->description))));
