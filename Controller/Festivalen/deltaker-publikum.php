<?php

use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Posts;
use UKMNorge\DesignWordpress\Environment\Wordpress;

UKMDesign::getHeader()->hideSectionTitle();
UKMDesign::getHeader()->getLogo()->hide();

Wordpress::setView('Festivalen/Front/Underveis');

// Get post for current year
$posts = Posts::getByYear(date("Y"));
Wordpress::setPosts($posts);

Wordpress::setView('Festivalen/Front/DeltakerPublikum');