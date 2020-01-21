<?php

require_once('UKM/sql.class.php');

$WP_TWIG_DATA['season_start'] = 2010;
$WP_TWIG_DATA['season'] = isset( $_GET['season'] ) ? (int) $_GET['season'] : get_site_option('season');
$WP_TWIG_DATA['real_season'] = get_site_option('season');
$artister = [];

function clean( $field ) {
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

$qry_artister = new SQL(
    "SELECT 
        COUNT(`t_id`) AS `antall`,
        LOWER( ". clean('`t_titleby`') ." ) AS `navn`
    FROM `smartukm_titles_scene` AS `tittel`
    WHERE `season` = '#season'
        AND LOWER(". clean('`t_titleby`') .") NOT IN( '', ' ', 'vet ikke', '?', 'ingen tekst', 'ingen', 'instrumental', '-', 'ukjent')
    GROUP BY LOWER( ".clean('`tittel`.`t_titleby`') ." )
    ORDER BY `antall` DESC
    ", 
    [
        'season' => $WP_TWIG_DATA['season']
    ]
);

$res_artister = $qry_artister->run();

while( $row = SQL::fetch( $res_artister ) ) {
    if( $row['antall'] < 10 ) {
        break;
    }

    $row['sanger'] = [];

    $qry_sanger = new SQL(
        "SELECT 
            LOWER( ". clean('`t_name`') .") AS `tittel`,
            COUNT( `t_id` ) AS `antall`
        FROM `smartukm_titles_scene`
        WHERE LOWER(`t_titleby`) = '#artist'
            AND `season` = '#season'
        GROUP BY LOWER( ". clean('`t_name`'). " )
        ORDER BY (`antall`) DESC
        ",
        [
            'artist' => $row['navn'],
            'season' => $WP_TWIG_DATA['season']
        ]
    );
    $res_sanger = $qry_sanger->run();
    while( $sang = SQL::fetch( $res_sanger ) ) {
        $row['sanger'][] = $sang;   
    }

    $qry_tidligere = new SQL(
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
    while( $tidligere = SQL::fetch( $res_tidligere ) ) {
        $row['tidligere'][ $tidligere['season'] ] = $tidligere['antall'];
    }

    $artister[] = $row;
}

$WP_TWIG_DATA['artister'] = $artister;