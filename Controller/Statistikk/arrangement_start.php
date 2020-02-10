<?php

use UKMNorge\DesignWordpress\Environment\Wordpress;

global $COMPARE_FUNCTION;
$COMPARE_FUNCTION = 'getStart';

Wordpress::requireController('Statistikk', 'arrangement');
