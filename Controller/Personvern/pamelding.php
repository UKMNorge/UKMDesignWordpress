<?php

use Symfony\Component\Yaml\Yaml;
use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Samtykke\Person;

$IP = isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ? 
        $_SERVER['HTTP_CF_CONNECTING_IP'] : 
        $_SERVER['REMOTE_ADDR'];

Wordpress::setView('Personvern/Pamelding');
try {
    /*
     * Sjekk at vi har nødvendig translations-fil fra UKMNorge/UKMdelta.git
     * Hentes inn via UKMNorge/UKMDeltakere.git (cron/samtykke/translations.cron.php)
    */
    if( !file_exists( PATH_PLUGIN_UKMDELTAKERE.'translations/ukmid.en.yml' ) ) {
        throw new Exception('Mangler translations-fil');
    }
    $translations = file_get_contents( PATH_PLUGIN_UKMDELTAKERE.'translations/ukmid.en.yml' );
    $translate = Yaml::parse( $translations );
    Wordpress::addViewData('trans', $translate['personvernAction']);

    /*
     *
    */
	if( !isset( $_GET['id'] ) ) {
		Wordpress::addViewData('skjul_skjema', true);
	} else {
		$data = explode('-', $_GET['id']);
		$mobil = $data[0];
		$id = $data[1];
	
		if( !is_numeric( $mobil ) || !is_numeric( $id ) ) {
			throw new Exception('Mangler numerisk mobil-nummer og ID-felt');
		}
		
		$samtykke = Person::getById( $id );
		if( $samtykke->getMobil() != $mobil ) {
			throw new Exception('Feil i ID-felt.');
		}
		
		$KREV_FORESATT = in_array( $samtykke->getKategori()->getId(), ['u13','u15'] );
		$ER_FORESATT = isset( $_GET['foresatt'] );
		
		if( isset( $_GET['feedback'] ) ) {
			$feedback = $_GET['feedback'];
		} elseif( isset( $_POST['feedback'] ) ) {
			$feedback = $_POST['feedback'];
		} else {
			$feedback = false;
		}
		
		// VI HAR FÅTT INN ET SVAR
		if( $feedback ) {
			/**
			 * FIKK FORESATT-INFO SAMMEN MED FEEDBACK
			 *
			 * Dvs at deltakeren er under 15,
			 * og vi må informere foresatte
			**/
	
			if( isset( $_POST['foresatt_navn'] ) ) {
				$_POST['foresatt_mobil'] = preg_replace('/[^0-9]/', '', $_POST['foresatt_mobil']);
				$samtykke->setForesatt( $_POST['foresatt_navn'], $_POST['foresatt_mobil'] );
				$samtykke->persist();
			}
			/**
			 * VI HAR FÅTT EN GODKJENNING
			**/
			if( $feedback == 'go' ) {
				// Foresatt: it's a GO!
				if( $ER_FORESATT ) {
					$samtykke->setForesattStatus('godkjent', $IP);
				}
				// Deltaker: take pictures of me!
				else {
					$samtykke->setStatus('godkjent', $IP);
				}
				Wordpress::setView('Personvern/Go');
			}
			/**
			 * VI HAR FÅTT EN NO-GO :(
			**/
			else {
				// No-go fra foresatte
				if( $ER_FORESATT ) {
					$samtykke->setForesattStatus('ikke_godkjent', $_SERVER['HTTP_CF_CONNECTING_IP']);
				}
				// No-go fra deltakeren
				else {
					$samtykke->setStatus('ikke_godkjent', $_SERVER['HTTP_CF_CONNECTING_IP']);
				}
				Wordpress::setView('Personvern/Nogo');
			}
			$samtykke->persist();
			
			/**
			 * FIKK FORESATT-INFO SAMMEN MED FEEDBACK
			 *
			 * Faktisk informer den foresatte.
			 * Må gjøres etter lagret samtykke, slik at vi kan gi foresatte
			 * informasjon om hva deltakeren ønsker.
			**/
			if( isset( $_POST['foresatt_navn'] ) ) {
				// SEND SMS TIL FORESATT
                $samtykke = Person::getById( $id );
                $samtykke->getKommunikasjon()->sendMelding('samtykke_foresatt');
			}
		}
		/**
		 * Hvis deltakeren ikke har sett siden før, marker den som lest nå
		**/
		elseif( !$ER_FORESATT && $samtykke->getStatus()->getId() == 'ikke_sett' ) {
			$samtykke->setStatus('ikke_svart', $_SERVER['HTTP_CF_CONNECTING_IP']);
			$samtykke->persist();
		}
		/**
		 * Hvis den foresatte ikke har sett siden før, marker den som lest nå
		**/
		elseif( $ER_FORESATT && $samtykke->getForesatt()->getStatus()->getId() == 'ikke_sett' ) {
			$samtykke->setForesattStatus('ikke_svart', $_SERVER['HTTP_CF_CONNECTING_IP']);
			$samtykke->persist();
		}	
        
        Wordpress::addViewData(
            [
                'samtykke' => $samtykke,
                'krev_foresatt' => $KREV_FORESATT,
                'er_foresatt' => $ER_FORESATT
            ]
        );
	}
} catch( Exception $e ) {
    Wordpress::setView('Personvern/error');
    Wordpress::addViewData('melding', $e->getMessage());
}