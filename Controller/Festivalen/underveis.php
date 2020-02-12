<?php

use UKMNorge\DesignWordpress\Environment\Posts;
use UKMNorge\DesignWordpress\Environment\Wordpress;

Wordpress::setView('Festivalen/Front/Underveis');

$posts = Posts::getByCategory(1);
Wordpress::setPosts($posts);