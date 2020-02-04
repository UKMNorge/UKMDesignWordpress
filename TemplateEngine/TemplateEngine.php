<?php

namespace UKMNorge\TemplateEngine;

use UKMNorge\TemplateEngine\Interfaces\FlashbagInterface;
use UKMNorge\TemplateEngine\Interfaces\TemplateRendererInterface;

class TemplateEngine
{
    
    static $flashbag = null;
    static $template_engine = null;
    static $view_data = [];
    static $view;
    static $path;

    /**
     * Initier TemplateEngine
     *
     * @param String $dir
     */
    public static function init( String $dir ) {
        static::$path = rtrim($dir,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }

    /**
     * Hent basepath (i wordpress, theme_dir)
     *
     * @return String $path
     */
    public static function getPath() {
        return static::$path;
    }

    /**
     * Hent view path
     *
     * @return String path
     */
    public static function getViewPath() {
        return static::$path .'Views/';
    }

    /**
     * Set flashbag-handler
     * 
     * @param FlashbagInterface $flashbag
     * @return void
     */
    public static function setFlashbag(FlashbagInterface $flashbag)
    {
        static::$flashbag = $flashbag;
    }

    /**
     * Get Flashbag
     * 
     * @return Flashbag
     */
    public static function getFlashbag()
    {
        return static::$flashbag;
    }

    /**
     * Set the template engine
     *
     * @param TemplateRendererInterface $template_engine
     * @return void
     */
    public function setTemplateRenderer(TemplateRendererInterface $template_engine)
    {
        static::$template_engine = $template_engine;
    }

    /**
     * Get the template engine
     *
     * @return TemplateRendererInterface
     */
    public function getTemplateRenderer()
    {
        return static::$template_engine;
    }

    /**
     * Render current view
     *
     * @return String html
     */
    public static function renderCurrent() {
        return static::render(
            static::getView()
        );
    }

    /**
     * Render a specific view
     *
     * @param String $template
     * @return String html
     */
    public static function render(String $template) {
        return static::getTemplateRenderer()->render($template, static::getViewData());
    }

    /**
     * Hent alle view-data
     *
     * @return array
     **/
    public static function getViewData()
    {
        static::$view_data['flashbag'] = static::getFlashbag();
        return static::$view_data;
    }

    /**
     * Legg til viewdata
     * 
     * Tar i mot array med flere keys (ett parameter)
     * eller key, value (to parameter)
     *
     * @param [string|array] key eller [key => val]
     * @param [null|array] data hvis oppgitt key som string
     * @return void
     **/
    public static function addViewData($key_or_array, $data = null)
    {
        if (is_array($key_or_array)) {
            static::$view_data = array_merge(static::$view_data, $key_or_array);
        } else {
            static::$view_data[$key_or_array] = $data;
        }
    }

    /**
     * Sett hvilket view som skal vises
     *
     * OBS: bruk requireController hvis view'et også trenger
     * at controlleren kjøres
     *
     * @param String $template
     * @return void
     */
    public static function setView( String $view ) {
        static::$view = $view;
    }

    /**
     * Hent hvilket view som skal vises
     *
     * @return String view
     */
    public static function getView() {
        return static::$view;
    }

    /**
     * Require a controller
     *
     * @param String $group
     * @param String $controller
     * @return void
     */
    public static function requireController( String $group, String $controller) {
        require( static::getPath() .'Controller/'. basename($group) .'/'. basename($controller) .'.php');
        
    }
}