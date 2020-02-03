<?php

namespace UKMNorge\TemplateEngine\Interfaces;

interface TemplateRendererInterface {
    public static function render( String $template, Array $template_data);
}