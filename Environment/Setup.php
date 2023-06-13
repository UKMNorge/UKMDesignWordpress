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

    public static function addRewriteRules() {
        #echo '<h1 style="color: #ff00ff">Added</h1>';
        add_rewrite_rule(
            'program/([0-9]+)/?$',
            'index.php?pagename=program&id=$matches[1]',
            'top'
        );
        
        add_rewrite_rule(
            'deltakerprogram/([0-9]+)/?$',
            'index.php?pagename=deltakerprogram&id=$matches[1]',
            'top'
        );

        add_rewrite_rule(
            'pameldte/([0-9]+)/?$',
            'index.php?pagename=pameldte&id=$matches[1]',
            'top'
        );

        add_rewrite_rule(
            'kunstnerinfo/([0-9]+)/?$',
            'index.php?pagename=kunstnerinfo&id=$matches[1]',
            'top'
        );

        global $wp_rewrite; 
        $wp_rewrite->flush_rules();
        flush_rewrite_rules();
    }
}