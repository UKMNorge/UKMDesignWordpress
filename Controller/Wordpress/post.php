<?php
global $post, $post_id;
$post = get_post($hendelse->getTypePostId());
$WP_TWIG_DATA['post'] = new WPOO_Post($post);
