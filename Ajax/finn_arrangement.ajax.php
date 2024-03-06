<?php
use UKMNorge\Geografi\Kommune;


// return $_POST arguments
echo json_encode($_POST);

try{
    $kommune = new Kommune( $_POST['kommunenummer'] );
    echo json_encode($kommune);
} catch( Exception $e ) {
    echo json_encode($e);
}