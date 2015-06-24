<?php


namespace Lumi\Glob;


class Layout {

	public function __construct() {
	
		add_theme_support( 'menus' );

		add_action( 'delete_post', array( $this, 'delete_cache_on_save_post' ) );

		/**
		 * ACF cache delete
		 */
		add_filter( 'acf/save_post', function($post_id) {
			if( $post_id === 'options' ) {
				if( function_exists( 'wp_cache_clear_cache' ) ){
					wp_cache_clear_cache();
				}
			}
		} );

	}

	public function delete_cache_on_save_post( $post_id = null )
	{
		if( function_exists('wp_cache_clear_cache') ){
			wp_cache_clear_cache();
		}
	}

}