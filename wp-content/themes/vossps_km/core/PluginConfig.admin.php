<?php


namespace Lumi\Admin;


class PluginConfig {

	public function __construct() {



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

		/*
		Update Nag
		*/
		add_action('admin_menu', function() {
			remove_action( 'admin_notices', 'update_nag', 3 );
		});

		/**
		 * WP SEO metabox lower prio
		 */
		add_filter( 'wpseo_metabox_prio', function() {return 'low';} );

		add_filter( 'wpseo_metabox_prio', function() {return 'low';} );



	}

}