<?php

namespace UKMNorge\DesignWordpress\Environment;

class Setup {
    /**
     * Hook all actions related to setup
     *
     * @return void
     */
    public static function hook() {
        add_action( 'after_setup_theme', [static::class, 'postInstall']);
    }

    /**
     * Post theme installation, run these
     *
     * @return void
     */
    public static function postInstall() {
        add_image_size( 'lite', 350, 350 );
        add_image_size( 'forsidebilde', 1800, 1800 );
        add_image_size( 'veldigstor', 3000, 3000 );

        update_option( 'medium_size_w', 600 );
        update_option( 'medium_size_h', 600 );

        update_option( 'large_size_w', 1200 );
        update_option( 'large_size_h', 1200 );
    }

    public function postUnInstall() {

    }
}