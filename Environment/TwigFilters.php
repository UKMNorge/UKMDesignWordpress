<?php

namespace UKMNorge\DesignWordpress\Environment;

class TwigFilters {
    /**
     * TWIG-filter: |UKMpath
     * Korrigerer path fra Symfony2-standard til current implementation
     * 
     * @param [type] $path
     * @return void
     */
    function UKMpath( $path ) {
        return str_replace(array('UKMDesignBundle',':'), array('','/'), $path );
    }
}