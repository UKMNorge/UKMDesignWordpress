<?php

namespace UKMNorge\DesignWordpress\Environment;

class Ajax
{

    /**
     * Hook ajax handler to wordpress
     *
     * @return void
     */
    public static function hook()
    {
        add_action('wp_ajax_nopriv_UKMresponsive', [static::class, 'handle']);
        add_action('wp_ajax_UKMresponsive', [static::class, 'handle']);
    }

    /**
     * Handle ajax request
     *
     * @return void
     */
    public static function handle()
    {
        Wordpress::init();

        $response = [
            'action' => $_POST['ajaxaction'],
            'trigger' => $_POST['trigger'],
            'success' => false,
        ];

        $ajaxFile = Wordpress::getPath() . 'Ajax/' . basename($response['action']) . '.ajax.php';
        if (file_exists($ajaxFile)) {
            require_once($ajaxFile);
        }

        echo json_encode($response);
        wp_die();
    }
}
