<?php
function WP_TWIG_date($time, $format='d.M Y H:i') {
    if( is_null( $time ) ) {
        return '';
    }
	if( !is_string( $time ) && get_class( $time ) == 'DateTime' ) 
	{
		$time = $time->getTimestamp();
	}
	elseif( is_string( $time ) && !is_numeric( $time ) ) 
	{
		$time = strtotime($time);
	}
	$date = date($format, $time);

	return str_replace(array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday',
							 'Mon','Tue','Wed','Thu','Fri','Sat','Sun',
							 'January','February','March','April','May','June',
							 'July','August','September','October','November','December',
							 'Jan','Feb','Mar','Apr','May','Jun',
							 'Jul','Aug','Sep','Oct','Nov','Dec'),
					  array('mandag','tirsdag','onsdag','torsdag','fredag','lørdag','søndag',
					  		'man','tir','ons','tor','fre','lør','søn',
					  		'januar','februar','mars','april','mai','juni',
					  		'juli','august','september','oktober','november','desember',
					  		'jan','feb','mar','apr','mai','jun',
					  		'jul','aug','sep','okt','nov','des'), 
					  $date);
}

function WP_TWIG_maned( $nr ) {
	switch( $nr ) {
		case 1:
		case '01':
			return 'jan';
		case 2:
		case '02':
			return 'feb';
		case 3:
		case '03':
			return 'mar';
		case 4:
		case '04':
			return 'apr';
		case 5:
		case '05':
			return 'mai';
		case 6:
		case '06':
			return 'jun';
		case 7:
		case '07':
			return 'jul';
		case 8:
		case '08':
			return 'aug';
		case 9:
		case '09':
			return 'sep';
		case 10:
			return 'okt';
		case 11:
			return 'nov';
		case 12:
			return 'des';
	}
	return $nr;
}