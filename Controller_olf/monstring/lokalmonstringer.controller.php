<?php
use UKMNorge\DesignBundle\Utils\SEO;

require_once('UKM/monstringer.collection.php');
require_once('UKM/monstring.class.php');

$FYLKE = new monstring_v2( get_option('pl_id') );

$monstringer = new monstringer_v2( $FYLKE->getSesong() );

$WP_TWIG_DATA['fylke'] 	= $FYLKE;
$WP_TWIG_DATA['lokalt'] = $monstringer->utenGjester( $monstringer->getAllByFylkeSortByStart( $FYLKE->getFylke()->getId() ) );

SEO::setTitle( 'Lokalmønstringer i '. $FYLKE->getFylke() );
SEO::setDescription( '' );
SEO::setAuthor( $WP_TWIG_DATA['page']->getPage()->author->display_name );
