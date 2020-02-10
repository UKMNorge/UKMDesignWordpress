<?php

use UKMNorge\Arrangement\Kontaktperson\Kontaktperson;
use UKMNorge\DesignWordpress\Environment\Wordpress;

$args = array(
 			'orderby'		 => 'menu_order',
			'order'          => 'ASC',
			'post_type'      => 'page',
			'post_parent'    => Wordpress::getPage()->ID,
			'numberposts'	 => -1,
		);

$kontakt_sider = get_posts($args);

foreach( $kontakt_sider as $kontakt ) {
	$post_thumbnail_id = get_post_thumbnail_id( $kontakt->ID ); 
	$post_thumbnail = wp_get_attachment_image_src( $post_thumbnail_id, 'medium' );

	$mock_db_row = array(
					'id' 			=> -1,
					'firstname' 	=>  $kontakt->post_title,
					'lastname' 		=> '',
					'tlf' 			=> get_post_meta( $kontakt->ID, 'UKMkontakt_mobil', true),
					'email'			=> get_post_meta( $kontakt->ID, 'UKMkontakt_epost', true),
					'title'			=> get_post_meta( $kontakt->ID, 'UKMkontakt_tittel', true),
					'facebook'		=> get_post_meta( $kontakt->ID, 'UKMkontakt_facebook', true),
					'picture' 		=> wp_get_attachment_image_src( $post_thumbnail_id, 'medium' )[0],
					'beskrivelse'	=> $kontakt->post_content,
					'adress'		=> null,
					'postalcode'	=> null,
					'kommune'		=> null,
					'last_updated'	=> null,
					'system_locked'	=> null,
				);
				
	$kontakter[] = new Kontaktperson( $mock_db_row );
}
Wordpress::addViewData('kontakter', $kontakter);
Wordpress::setView('Kontaktpersoner/Liste');