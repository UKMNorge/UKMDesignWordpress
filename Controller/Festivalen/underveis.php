<?php

use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Posts;
use UKMNorge\DesignWordpress\Environment\Wordpress;

UKMDesign::getHeader()->hideSectionTitle();
UKMDesign::getHeader()->getLogo()->hide();

Wordpress::setView('Festivalen/Front/Underveis');

$posts = Posts::getByCategory(1);
Wordpress::setPosts($posts);