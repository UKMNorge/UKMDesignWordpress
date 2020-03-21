<?php

namespace UKMNorge\DesignWordpress\Environment;

class Admin
{

    public static function hook()
    {
    #    add_action('admin_menu', [static::class, 'menu'], -100);
    }

    public static function menu()
    {}

    /*
     * Håndterer nettside-innstillinger fra UKMnettside-plugin
     * da alle andre innstillinger også ligger der
    public static function renderAdmin() {
        Wordpress::init();
        echo Wordpress::render('Admin/home');
    }
    */
}
