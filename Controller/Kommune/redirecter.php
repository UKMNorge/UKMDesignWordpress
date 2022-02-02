<?php

use UKMNorge\DesignWordpress\Environment\Front\OmradeFront;
use UKMNorge\Nettverk\Omrade;
use UKMNorge\Wordpress\Blog;

# 2020 bruker pl_owner_id
$kommune_id = get_option('pl_owner_id');
if( !$kommune_id ) {
    # Pre 2020 ble kommuner lagret som array
    $kommune_id = explode(',', get_option('kommuner'));
    if( is_array( $kommune_id ) ) {
        $kommune_id = $kommune_id[0];
    }
}
# På et eller annet tidspunkt lagret vi også kommune som kommune
if(!$kommune_id ) {
    $kommune_id = get_option('kommune');
}

if( !$kommune_id ) {
    // NÅ er det på tide å markere denne som slettet, og reloade siden.
    // deleted.php bør være i stand til å finne ut hvor brukeren skal,
    // eller kaste en seriøs exception
    $path = str_replace(
        [
            'https://',
            'http://',
            UKM_HOSTNAME
        ],
        '',
        get_site_url()
    );
    Blog::deaktiver( 
        Blog::getIdByPath( $path )
    );
    header("Location: ". get_site_url() );
    exit();
}

// Sjekk om kommunen har ett arrangement, og t det er en fellesmønstring,
// for da skal vi videresende til arrangementssiden.
$omrade = Omrade::getByKommune( intval($kommune_id) );

// Hvis kommunen har ett kommende arrangement, send direkte til det
// Kunne strengt tatt sendt vedkommende til kommune-siden, men 
// inntil videre forsøker vi denne strategien.

/* Fjern komentarer for å sende brukere til arrangement i steden for kommune */
// if( $omrade->getKommendeArrangementer()->getAntall() == 1 ) {
//     $arrangement = $omrade->getKommendeArrangementer( )->getFirst();
//     if( $arrangement->erFellesmonstring() ) {
//         header("Location: ". $arrangement->getLink());
//         echo '<script type="text/javascript">window.location.href = "'. $arrangement->getLink() .'";</script>';
//         exit();
//     }
// }
