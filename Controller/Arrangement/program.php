<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Hendelser;
use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Post;
use UKMNorge\DesignWordpress\Environment\Posts;
use UKMNorge\DesignWordpress\Environment\Wordpress;

$arrangement = new Arrangement( get_option('pl_id') );

Wordpress::addViewData('arrangement',$arrangement);

## Skal hente ut programmet for en forestilling
$id = Wordpress::getLastParameter();
if( is_numeric( $id ) ) {
	// /program/c_id/
	$hendelse = $arrangement->getProgram()->get( $id );
    Wordpress::addViewData('hendelse', $hendelse);

    // Siden har riktig URL, men mangler ID-parameter
    Wordpress::getPage()
        ->setUrl(Wordpress::getPage()->getUrl(). $id .'/')
        ->setTitle( $hendelse->getNavn() )
        ->setDescription( 
            $hendelse->getStart()->format('j. M \k\l. H:i') .'. '.
            ( $arrangement->getEierType() == 'kommune' ? 'UKM ' : ''). 
            $arrangement->getNavn()
        );

	switch( $hendelse->getType() ) {
		# TYPE: POST ELLER PAGE
        case 'post':
            $post = Post::loadByPostId($hendelse->getTypePostId());
            
            Wordpress::setView('Arrangement/Program/Post');
            Wordpress::addViewData('post', $post);
            
            // Set featured image for SEO (hvis vi har et)
            if( $post->hasFeaturedImage() ) {
                UKMDesign::getHeader()::getSEO()->setImage(
                    $post->getFeaturedImage()
                );
            }
        break;
        
		# TYPE: KATEGORI
        case 'category':
            $posts = Posts::getByCategory( $hendelse->getTypeCategoryId() );
            
            Wordpress::setView('Arrangement/Program/Kategori');
            Wordpress::addViewData(
                [
                    'kategori' => get_category( $hendelse->getTypeCategoryId() ),
                    'posts' => $posts
                ]
            );
        break;
        
		# TYPE: STANDARD
		default:
			Wordpress::setView('Arrangement/Program/Hendelse');
		break;
	}
}
else {
    $visInterne = defined('DELTAKERPROGRAM') && DELTAKERPROGRAM;
	$hendelser = $visInterne ? $arrangement->getProgram()->getAllInkludertInterne() : $arrangement->getProgram()->getAll();
    
    # Skal vise program for en gitt dag
    if( Wordpress::getPage()->hasMeta('dato') ) {
        $dato = DateTime::createFromFormat( 'd_m', Wordpress::getPage()->getMeta('dato'));
        $program = Hendelser::filterByDato($dato, $hendelser);

        Wordpress::setView('Arrangement/Program/Dag');
        Wordpress::getPage()
            ->setTitle( 
                ( $arrangement->getEierType() == 'kommune' ? 'UKM' : ''). $arrangement->getNavn() .
                ' ' . strtolower($dato->format('j. M'))
            )
            ->setDescription( $dato->format('j. M \k\l.') );
    } 
    # Skal vise program for arrangementet
    else {
        $program = Hendelser::sorterPerDag( $hendelser );
        Wordpress::setView('Arrangement/Program/Oversikt');
        Wordpress::getPage()
            ->setTitle( 'Program for '.( $arrangement->getEierType() == 'kommune' ? 'UKM' : ''). $arrangement->getNavn() )
            ->setDescription( 'Vi starter '. $arrangement->getStart()->format('j. M \k\l. H:i') );
    }
    
    Wordpress::addViewData(
        [
            'visInterne' =>$visInterne,
            'program' => $program
        ]
    );
}