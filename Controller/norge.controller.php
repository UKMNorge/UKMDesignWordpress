<?php

require_once('UKM/fylker.class.php');
$WP_TWIG_DATA['fylker'] = fylker::getAll();
 
$start_sesong = strtotime( str_replace('YYYY', get_site_option('season')-1, WP_CONFIG::get('pamelding')['starter'] ));
$start_sesong_maned = date('m', $start_sesong);

if (date('m') > 4 && date('m') < $start_sesong_maned ) {
    $WP_TWIG_DATA['pamelding_apen'] = false;
} else {
    $WP_TWIG_DATA['pamelding_apen'] = time() > $start_sesong;
}

// PUSH TO FRONT
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
		$WP_TWIG_DATA['PtF_posts'] = $ptf_posts;
	}
}
/*
	$WP_TWIG_DATA['in_season'] = date('m') < 5;
	if( $WP_TWIG_DATA['in_season'] ) {
		$WP_TWIG_DATA['pamelding_apen'] = date('m') < 4;

		require_once('UKM/statistikk.class.php');
		$stat = new statistikk();
		$stat->setLand();
		$total = $stat->getTotal(get_site_option('season'));
		$WP_TWIG_DATA['pameldte'] = $total['persons'];
	}

	switch_to_blog( UKM_HOSTNAME == 'ukm.dev' ? 765 : 3449 );
	$post_query = '?post_status=publish&posts_per_page=1';
	$posts = query_posts($post_query);
	global $post;
	foreach( $posts as $key => $post ) {
		the_post();
		$WP_TWIG_DATA['news'] = new WPOO_Post( $post );
	}

	// VIS FESTIVALINFO PÅ FORSIDEN
	$WP_TWIG_DATA['vis_festival'] = false;
	if( ((int)date('m') == 4 && (int)date('d') > 14) or ((int)date('m') > 4 && (int)date('m') < 8) ) {
		require_once('UKM/monstringer.collection.php');
		$WP_TWIG_DATA['festivalen'] = monstringer_v2::land( date('Y') );
		$WP_TWIG_DATA['vis_festival'] = true;
	}

	// PUSH TO FRONT
	if( function_exists('UKMpush_to_front_load_all_fm_data') ) {	
		if( (int) date('m') > 2 && (int) date('m') < 6 ) {
			$week = (int) date('W');
			$WP_TWIG_DATA['PtF'] = [
				'current' => UKMpush_to_front_load_all_fm_data( date('Y'), $week ),
				'previous' => (int) date('N') > 4 ? false : UKMpush_to_front_load_all_fm_data( date('Y'), $week-1 ), // Forrige uke vises tom onsdag
			];
		}
	}
*/