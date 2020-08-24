<?php

namespace UKMNorge\DesignWordpress\Environment;

class Redirects
{

    /**
     * Hook inn på wordpress
     *
     * @return void
     */
    public static function hook()
    {
        add_action('template_include', [static::class, 'pageExists'], 10000);
        add_filter('ms_site_check', [static::class, 'skip_site_check']);
    }

    /**
     * Sjekk om bloggen har blitt slettet (deaktivert)
     * før vi viser noe som helst
     *
     * @param String $template
     * @return String
     */
    public static function pageExists($template)
    {
        // Bloggen er slettet
        if (get_site()->deleted) {
            return locate_template(['arrangement-deleted.php']);
        }
        // Arrangementet som brukte å være her er borte (pre-2020-problemstilling?)
        if (get_option('status_monstring') != false) {
            return locate_template(array('arrangement-not-here.php'));
        }
        // Arrangør-siden
        if( get_option('site_type') == 'arrangor' ) {
            return wp_redirect('https://'. UKM_HOSTNAME .'/wp-admin/user/');
        }
        // Business as ususal
        return $template;
    }

    /**
     * Ettersom vi uansett håndterer redirect av slettede blogger i
     * UKMresponsive_pageExists kan alle få lov til å "se" slettede blogger 
     *
     * @return void
     */
    public static function skip_site_check()
    {
        return true;
    }
}
