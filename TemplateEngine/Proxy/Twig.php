<?php

namespace UKMNorge\TemplateEngine\Proxy;

use UKMNorge\TemplateEngine\Interfaces\TemplateRendererInterface;
use UKMNorge\Twig\Twig as UKMNorgeTwig;

class Twig extends UKMNorgeTwig implements TemplateRendererInterface {

    public static function render(String $template, Array $data ) {
        if( strpos( $template, '.twig' ) === false ) {
            $template .= '.html.twig';
        }
        return parent::render($template, $data);
    }
}