<?php

/**
 * Snarvei for å sjekke om get-verdier er satt og ikke tomme
 *
 * @param String $id
 * @return boolean
 */
function hasGET(String $id)
{
    return isset($_GET[$id]) && !empty($_GET[$id]);
}

if (hasGET('wp_id') && hasGet('token_id') && hasGet('token')) {
    if (UKMusers::loginFromDelta($_GET['wp_id'], $_GET['token_id'], $_GET['token'])) {
        # Alt ok, ferdig innlogget. Die her.
        die();
    }
}

header("Location: http://delta." . UKM_HOSTNAME . "/ukmid/");
die("Gå til http://delta." . UKM_HOSTNAME . "/ukmid/ for å logge inn.");
