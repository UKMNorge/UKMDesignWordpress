<?php

use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Geografi\Fylker;

// wp_enqueue_script( 'build-js', get_template_directory_uri() . 'Client/arrangement_finder/dist/assets/build.js', array(), '1.0.0', true );
wp_enqueue_style('UKMvideoArrSysStyle', '//assets.' . UKM_HOSTNAME . '//css/arr-sys.css');
wp_enqueue_script('custom-script', get_template_directory_uri() . '/Client/arrangement_finder/dist/assets/build.js', array('jquery'));
wp_enqueue_style('custom-style', get_template_directory_uri() . '/Client/arrangement_finder/dist/assets/build.css', array());

Wordpress::setView('Norge/Arrangement_finder');
Wordpress::addViewData('fylker', Fylker::getAll());