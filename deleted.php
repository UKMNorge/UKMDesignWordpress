<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Load;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Geografi\Kommune;
use UKMNorge\Wordpress\Blog;
use UKMNorge\Database\SQL\Query;

require_once('header.php');
require_once('UKM/Autoloader.php');

$template = '404/deleted';

$links = [];

$site_type = get_option('site_type');
if( get_option('kommuner') ) {
    $site_type = 'kommune';   
}
switch ($site_type) {
        // Dette var en fylkesside
    case 'fylke':
        $fylke = Fylker::getById(get_option('fylke'));
        // Vi vet at dette fylket er overtatt av et annet - go there
        if ($fylke->erOvertatt()) {
            $ny_url = $fylke->getOvertattAv()->getLink(false);
            if( Blog::eksisterer( $ny_url ) ) {
                header("Location: " . $ny_url);
                echo '<script type="javascript">window.location.href = "' . $ny_url . '";</script>';
                exit();
            }
        }
        if( Blog::eksisterer( $fylke->getLink(false) ) ) {
            $links[] = [
                'link' => $fylke->getLink(false),
                'navn' => $fylke->getNavn()
            ];
        }
        break;

        // Dette var en kommuneside
    case 'kommune':
        $kommuner = get_option('kommuner');

        if( !$kommuner ) {
            // Hoi, nå begynner vi å bli desperate nå!
            // Prøv å finne ut hvilket arrangement hadde denne path'en før, og
            // via det finne en kommune eller flere å sende de til.
            $query = new Query(
                "SELECT `smartukm_rel_pl_k`.`k_id`
                FROM `smartukm_place`
                JOIN `smartukm_rel_pl_k`
                    ON(`smartukm_rel_pl_k`.`pl_id` = `smartukm_place`.`pl_id`)
                WHERE `pl_link` = '#link'
                AND `smartukm_place`.`season` = '#sesong'",
                [
                    'link' => trim(
                        str_replace(
                            ['https://','http://',UKM_HOSTNAME],
                            '',
                            $WP_TWIG_DATA['blog_url']),
                        '/'),
                    'sesong' => get_site_option('season')-1
                ]
            );
            $res = $query->run();
            $kommuner = [];
            while( $row = Query::fetch($res) ) {
                $kommune = new Kommune( $row['k_id']);
                if($kommune->erOvertatt()) {
                    $kommune = $kommune->getOvertattAv();
                }
                $kommuner[] = $kommune->getId();
            }
        } else {
            $kommuner = explode(',', $kommuner);
        }

        $kommuner = array_unique($kommuner);

        // Fant ingen kommuner
        if( sizeof( $kommuner ) == 0 ) {
            $template = '404/404';
        }
        // Komuneside for én kommune - go there
        elseif (sizeof($kommuner) == 1 ) {
            $kommune = new Kommune($kommuner[0]);
            if( $kommune->erOvertatt() ) {
                $kommune = $kommune->getOvertattAv();
            }
            $ny_url = $kommune->getLink();
            if( Blog::eksisterer( $ny_url ) ) {
                header("Location: " . $ny_url);
                echo '<script type="javascript">window.location.href = "' . $ny_url . '";</script>';
                exit();
            }
        }
        // Vi har funnet flere kommuner (oh the possibilities 🤩)
        else {
            // Kommuneside for flere kommuner - list
            foreach ($kommuner as $kommune_id) {
                $kommune = new Kommune($kommune_id);
                if( $kommune->erOvertatt() ) {
                    $kommune = $kommune->getOvertattAv();
                }
                if( Blog::eksisterer( $kommune->getLink() ) ) {
                    $links[] = [
                        'link' => $kommune->getLink(),
                        'navn' => $kommune->getNavn(),
                        'tidligere' => $kommune->getTidligereNavnListe()
                    ];
                }
            }

            // Info om fylket kommunen er i
            if( get_option('fylke') ) {
                $fylke = Fylker::getById(get_option('fylke'));
                // Hvis fylket har blitt overtatt, vis dette
                if ($fylke->erOvertatt()) {
                    $fylke = $fylke->getOvertattAv();
                }

                if( Blog::eksisterer( $fylke->getLink(false) ) ) {
                    $links[] = [
                        'link' => $fylke->getLink(false),
                        'navn' => $fylke->getNavn()
                    ];
                }
            }
        }
        break;
        // Dette var et arrangement
    case 'arrangement':
        // og vi vet hvilket arrangement det var (pre 2020 kunne dette oppstå)
        if (get_option('pl_id')) {
            $arrangement = new Arrangement(get_option('pl_id'));

            // det var et fylkesarrangement
            if ($arrangement->getEierType() == 'fylke') {
                $fylke = $arrangement->getFylke();
                // Vi vet at dette fylket er overtatt av et annet - go there
                if ($fylke->erOvertatt() && Blog::eksisterer($fylke->getOvertattAv()->getLink())) {
                    $ny_url = $fylke->getOvertattAv()->getLink(false);
                    header("Location: " . $ny_url);
                    echo '<script type="javascript">window.location.href = "' . $ny_url . '";</script>';
                    exit();
                }
                // Hvis fylket ikke er overtatt, foreslå fylkessiden (skal vel egentlig ikke skje)
                if( Blog::eksisterer( $fylke->getLink(false) ) ) {
                    $links[] = [
                        'link' => $fylke->getLink(false),
                        'navn' => $fylke->getNavn()
                    ];
                }
            } elseif ($arrangement->getEierType() == 'kommune') {
                // Kommuneside for flere kommuner - list
                foreach ($arrangement->getKommuner()->getAll() as $kommune) {
                    if( $kommune->erOvertatt() ) {
                        $kommune = $kommune->getOvertattAv();
                    }
                    if( Blog::eksisterer( $kommune->getLink() ) ) {
                        $links[] = [
                            'link' => $kommune->getLink(),
                            'navn' => $kommune->getNavn(),
                            'tidligere' => $kommune->getTidligereNavnListe()
                        ];
                    }
                }
            }
            // Lands-arrangement?
            else {
                $links[] = [
                    'link' => UKM_HOSTNAME,
                    'navn' => 'UKM.no - forsiden'
                ];
            }
        }

        if (get_option('fylke')) { }
        break;
}


$WP_TWIG_DATA['links'] = $links;
echo WP_TWIG::render($template, $WP_TWIG_DATA);

wp_footer();
if (is_user_logged_in()) {
    echo '<style>body {margin-top: 33px;} @media (max-width:782px) {body {margin-top: 48px;}}</style>';
}
