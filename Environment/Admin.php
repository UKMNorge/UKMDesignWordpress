<?php

namespace UKMNorge\DesignWordpress\Environment;

class Admin
{

    public static function hook()
    {
        add_action('admin_menu', [static::class, 'menu'], -100);
    }

    public static function menu()
    {
        if (class_exists('UKMnettside')) {
            // Forside-innstillinger
            add_action(
                'admin_print_styles-' . add_submenu_page(
                    'edit.php',
                    'Forsiden',
                    'Forsiden',
                    'edit_posts',
                    'UKMdesign_nettside',
                    ['UKMnettside', 'renderForside']
                ),
                ['UKMnettside', 'scripts_and_styles']
            );
        }
    }

    /*
     * Håndterer nettside-innstillinger fra UKMnettside-plugin
     * da alle andre innstillinger også ligger der
    public static function renderAdmin() {
        Wordpress::init();
        echo Wordpress::render('Admin/home');
    }
    */
}
