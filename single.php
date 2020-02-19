<?php

use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Post;
use UKMNorge\DesignWordpress\Environment\Wordpress;

require_once('header.php');

$post = Post::loadByEnvironment();
Wordpress::addViewData('post', $post);

// Bestem template
if( isset($_POST['contentMode']) && $_POST['contentMode'] == 'true' ) {
    Wordpress::setView('Post/Content');
} elseif( isset($_GET['exportContent'])) {
    Wordpress::setView('Export/Content');
    Wordpress::addViewData('export', $post);
}else {
    Wordpress::setView('Post/Fullpage');
}

UKMDesign::getHeader()::getSEO()
    ->setAuthor( $post->getAuthorList() )
    ->setPublished( new DateTime($post->raw->post_date_gmt) );

if( !is_null($post->getFeaturedImage())) {
    UKMDesign::getHeader()::getSEO()
        ->setImage( $post->getFeaturedImage() );
}

Wordpress::getPage()
    ->setDescription( addslashes( preg_replace( "/\r|\n/", "", strip_tags( $post->lead ) ) ) );

require_once('render.php');