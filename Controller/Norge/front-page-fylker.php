<?php

// PUSH TO FRONT

use UKMNorge\DesignWordpress\Environment\Wordpress;

if( function_exists('UKMpush_to_front_load_all_fm_data') ) {	
	if( (int) date('m') > 2 && (int) date('m') < 6 ) {
		$ptf_posts = [];
		$week = (int) date('W');
		$festivaler = UKMpush_to_front_load_all_fm_data( date('Y'), $week );

		// Forrige uke vises tom fredag
		if( (int) date('N') < 6 ) {
			$forrige = UKMpush_to_front_load_all_fm_data( date('Y'), $week-1 );
			if( is_array( $forrige ) ) {
				$festivaler = array_merge($festivaler, $forrige );
			}
		}

		if( is_array( $festivaler ) ) {
			foreach( $festivaler as $festival ) {
				if( is_array( $festival->postdata ) ) {
					foreach( $festival->postdata as $post ) {
						$ptf_posts[] = $post;
						$post->meta->repost = $festival->title;
					}
				}
			}
		}
		shuffle( $ptf_posts );
		Wordpress::addViewData('PtF_posts', $ptf_posts);
	}
}