<?php
require_once('UKM/kontakt.class.php');
$args = array(
 			'orderby'		 => 'menu_order',
			'order'          => 'ASC',
			'post_type'      => 'page',
			'post_parent'    => $post->ID,
			'numberposts'	 => -1,
		);

$kontakter = get_posts($args);

foreach( $kontakter as $kontakt ) {
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
				
	$kontakt_objekt = new kontakt_v2( $mock_db_row );
	$WP_TWIG_DATA['kontakter'][] = $kontakt_objekt;
}