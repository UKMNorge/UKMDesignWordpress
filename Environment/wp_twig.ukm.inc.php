<?php
function UKMpath( $path ) {
	return str_replace(array('UKMDesignBundle',':'), array('','/'), $path );
}

function UKMtelefon( $number ) {
	return substr( $number, 0, 3 ) .' '. substr( $number, 3, 2 ) .' '. substr( $number, 5, 3 );
}

function UKMhusk( $identifier ) {
	return isset( $_COOKIE['UKMhusk-'. $identifier] ) && $_COOKIE['UKMhusk-'. $identifier];
}