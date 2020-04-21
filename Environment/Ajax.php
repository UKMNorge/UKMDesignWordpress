<?php

namespace UKMNorge\DesignWordpress\Environment;

class Ajax
{

    public static $response;

    /**
     * Hook ajax handler to wordpress
     *
     * @return void
     */
    public static function hook()
    {
        add_action('wp_ajax_nopriv_UKMDesignWordpress', [static::class, 'handle']);
        add_action('wp_ajax_nopriv_UKMresponsive', [static::class, 'handle']);
        add_action('wp_ajax_UKMDesignWordpress', [static::class, 'handle']);
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

        static::$response = [
            'action' => $_REQUEST['ajaxaction'],
            'trigger' => $_REQUEST['trigger'],
            'success' => false,
        ];

        $ajaxFile = Wordpress::getPath() . 'Ajax/' . basename(static::$response['action']) . '.ajax.php';
        if (file_exists($ajaxFile)) {
            require_once($ajaxFile);
        }

        header("Content-type: application/json; charset=utf-8");
        echo json_encode(static::$response);
        wp_die();
    }

    /**
     * Legg til respons-data
     *
     * @param String $key
     * @param [type] $value
     * @return void
     */
    public static function addResponseData(String $key, $value) {
        static::$response[ $key ] = $value;
    }
}
