<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Nettverk\Omrade;
use UKMNorge\DesignWordpress\Environment\Wordpress;

$pl_id = get_option('pl_id');
$site_type = get_option('site_type');

Wordpress::getPage()
	    ->setDescription( 'Vi gleder oss til du tar kontakt!' );

// Har vi pl_id er dette et arrangement, ikke en område-side.
if( null != $pl_id ) {
	$arrangement = new Arrangement( get_option('pl_id' ) );

	Wordpress::setView('Arrangement/Sider/Kontaktpersoner');
	Wordpress::addViewData('arrangement', $arrangement);
	Wordpress::getPage()
	    ->setTitle('Kontaktpersoner for '. $arrangement->getNavn());
} elseif($site_type == 'kommune') {
	$omrade = Omrade::getByKommune(get_option('kommune'));
	Wordpress::setView('Kontaktpersoner/Omrade');
	Wordpress::addViewData('omrade', $omrade);
	Wordpress::getPage()
	    ->setTitle('Kontaktpersoner for '. $omrade->getNavn());

} elseif($site_type == 'fylke') {
	$omrade = Omrade::getByFylke(get_option('fylke'));
	Wordpress::setView('Kontaktpersoner/Omrade');
	Wordpress::addViewData('omrade', $omrade);
	Wordpress::getPage()
	    ->setTitle('Kontaktpersoner for '. $omrade->getNavn());
}
