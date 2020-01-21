<?php
require_once('UKM/Autoloader.php');

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\DesignBundle\Utils\SEO;

$arrangement = new Arrangement( get_option('pl_id' ) );
$WP_TWIG_DATA['arrangement'] = $arrangement;
SEO::setTitle('Kontaktpersoner for '. $arrangement->getNavn());
SEO::setDescription( 'Vi gleder oss til du tar kontakt!' );