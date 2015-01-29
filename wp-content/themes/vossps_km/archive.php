<?php
$data = Timber::get_context();
$data['page_title'] = get_queried_object()->name;
$data['posts'] = Timber::get_posts();
foreach( $data['posts'] as $key => $post ) {
	if( $post->aktualita_image ) {
		$data['posts'][$key]->thumbnail = new TimberImage( $post->aktualita_image );
	}
}
Timber::render( 'archive.twig', $data );