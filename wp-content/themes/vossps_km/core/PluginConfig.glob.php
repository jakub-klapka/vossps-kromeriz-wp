<?php


namespace Lumi\Glob;


class PluginConfig {

	public function __construct() {

		/*
		* Disable WPSEO page score functions and marker
		*/
		add_filter( 'wpseo_use_page_analysis', function() {return false;} );

		add_action( 'init', function() {
			remove_action( 'wpseo_head', array( $GLOBALS['wpseo_front'], 'debug_marker' ), 2 );
		} );

		/*
		* Disable WPSEO search json
		*/
		add_filter( 'disable_wpseo_json_ld_search', '__return_true' );
	
	}

}