<?php

use UKMNorge\TemplateEngine\TemplateEngine;

require_once('environment.php');
require_once('header.php');

TemplateEngine::setController('Page','Meny');

require_once('render.php');