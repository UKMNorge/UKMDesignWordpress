<?php

use UKMNorge\Database\SQL\Query;
use UKMNorge\DesignWordpress\Environment\Wordpress;

Wordpress::setView('Statistikk/Sanger');

$season = isset($_GET['season']) ? (int) $_GET['season'] : get_site_option('season');
$artister = [];

function clean($field)
{
    return "
        REPLACE( 
            REPLACE( 
                REPLACE( 
                    REPLACE( 
                        REPLACE( 
                            REPLACE( 
                                REPLACE( 
                                    REPLACE( $field, ')', '')
                                , '(', '')
                            , '\"', '')
                        , '\'', '')
                    , '`', '' )
                ,',', ' ' )
            , '  ', ' ')
        , '.', '')
    ";
}

$qry_artister = new Query(
    "SELECT 
        COUNT(`t_id`) AS `antall`,
        LOWER( " . clean('`t_titleby`') . " ) AS `navn`
    FROM `smartukm_titles_scene` AS `tittel`
    WHERE `season` = '#season'
        AND LOWER(" . clean('`t_titleby`') . ") NOT IN( '', ' ', 'vet ikke', '?', 'ingen tekst', 'ingen', 'instrumental', '-', 'ukjent')
    GROUP BY LOWER( " . clean('`tittel`.`t_titleby`') . " )
    ORDER BY `antall` DESC
    ",
    [
        'season' => $season
    ]
);

$res_artister = $qry_artister->run();

while ($row = Query::fetch($res_artister)) {
    if ($row['antall'] < 10) {
        break;
    }

    $row['sanger'] = [];

    $qry_sanger = new Query(
        "SELECT 
            LOWER( " . clean('`t_name`') . ") AS `tittel`,
            COUNT( `t_id` ) AS `antall`
        FROM `smartukm_titles_scene`
        WHERE LOWER(`t_titleby`) = '#artist'
            AND `season` = '#season'
        GROUP BY LOWER( " . clean('`t_name`') . " )
        ORDER BY (`antall`) DESC
        ",
        [
            'artist' => $row['navn'],
            'season' => $season
        ]
    );
    $res_sanger = $qry_sanger->run();
    while ($sang = Query::fetch($res_sanger)) {
        $row['sanger'][] = $sang;
    }

    $qry_tidligere = new Query(
        "SELECT 
            `t_titleby`,
            COUNT( `t_id` ) AS `antall`,
            `season`
        FROM `smartukm_titles_scene`
        WHERE LOWER(`t_titleby`) = '#artist'
            AND `season` != 0
        GROUP BY `season`
        ORDER BY `season` ASC
        ",
        [
            'artist' => $row['navn']
        ]
    );
    $res_tidligere = $qry_tidligere->run();
    while ($tidligere = Query::fetch($res_tidligere)) {
        $row['tidligere'][$tidligere['season']] = $tidligere['antall'];
    }

    $artister[] = $row;
}

Wordpress::addViewData(
    [
        'season_start' => 2010,
        'season' => $season,
        'real_season' => get_site_option('season'),
        'artister' => $artister
    ]
);
