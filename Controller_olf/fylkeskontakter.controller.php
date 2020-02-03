<?php
require_once('UKM/kontakt.class.php');
require_once(WP_PLUGIN_DIR . '/UKMkart/config.php');
require_once(WP_PLUGIN_DIR . '/UKMkart/functions.inc.php');

$WP_TWIG_DATA['kontaktkart'] = visitor_map('fylkeskontaktene', 'ukm.no');
