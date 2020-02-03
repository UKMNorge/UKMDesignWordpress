<?php

require_once('vendor/autoload.php');

if( !is_admin() ) {
    require_once('environment.php');
}