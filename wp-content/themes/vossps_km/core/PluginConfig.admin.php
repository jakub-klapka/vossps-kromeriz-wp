<?php


namespace Lumi\Admin;


class PluginConfig {

	public function __construct() {

		include_once( 'inc/at_glance.php' );
		include_once( 'inc/activity.php' );

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

		/**
		 * Delete cache on save term
		 */
		add_action( 'edit_term', array( $this, 'delete_cache_on_save_post' ) );
		add_action( 'create_term', array( $this, 'delete_cache_on_save_post' ) );
		add_action( 'delete_term', array( $this, 'delete_cache_on_save_post' ) );



		add_filter( 'aiowps_htaccess_rules_before_writing', array( $this, 'aiowps_disable_server_signature' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'global_admin_modifs' ) );


	}

	public function delete_cache_on_save_post( $post_id = null )
	{
		if( function_exists('wp_cache_clear_cache') ){
			wp_cache_clear_cache();
		}
	}

	public function aiowps_disable_server_signature( $rules ) {
		foreach( $rules as $key => $rule ) {
			if( $rule == 'ServerSignature Off' || $rule == 'LimitRequestBody 10240000' ) {
				unset( $rules[$key] );
			}
		}
		return $rules;
	}

	public function global_admin_modifs() {
		global $lumi;
		wp_enqueue_script( 'global_admin_modifs', get_template_directory_uri() . '/assets/admin_js/global_admin_modifs.js', array( 'jquery' ), $lumi['config']['static_ver'], true );
	}

}