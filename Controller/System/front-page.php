<?php

use UKMNorge\Design\Menu\Link;
use UKMNorge\Design\Menu\Menu;
use UKMNorge\DesignWordpress\Environment\Front;
use UKMNorge\DesignWordpress\Environment\Posts;
use UKMNorge\DesignWordpress\Environment\Wordpress;

# Ekstra-meny
if (Front::hasMeny()) {
    $menu = new Menu();
    foreach( wp_get_nav_menu_items(Front::getMeny()) as $item ) {
        $menu->add(
            new Link(
                $item->title,
                $item->url,
                (
                    !is_null($item->target) && !empty($item->target)
                    ? $item->target : null
                )
            )
        );
    }

    Wordpress::addViewData(
        'shortcuts',
        $menu
    );
}


# Posts
Wordpress::setPosts();