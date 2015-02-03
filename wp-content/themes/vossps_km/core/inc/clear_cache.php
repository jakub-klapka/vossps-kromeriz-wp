<?php

$plugin_header_translate = array( __('WP Super Cache - Clear all cache', 'wp-super-cache-clear-cache-menu'), __('Clear all cached files of the WP Super Cache plugin directly from the admin menu (option only available to super admins).', 'wp-super-cache-clear-cache-menu') );

if ( ! function_exists('is_plugin_active')) {
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}

if ( is_plugin_active( 'wp-super-cache/wp-cache.php' ) ) {


	function wpsupercache_clear_cache_text_domain() {
		load_plugin_textdomain( 'wp-super-cache-clear-cache-menu', WPCACHEHOME . 'languages', basename( dirname( __FILE__ ) ) . '/languages' );
	}
	add_action( 'init', 'wpsupercache_clear_cache_text_domain' );

	function clear_all_cached_files_wpsupercache() {
		if( !current_user_can( 'manage_options' ) ) {
			return;
		}

		global $wp_admin_bar;
		if ( !is_super_admin() || !is_admin_bar_showing() )
			return;
		$wp_admin_bar->add_menu( array(
			'parent' => '',
			'id' => 'delete-cache-completly',
			'title' => __( 'Clear all cached files', 'wp-super-cache-clear-cache-menu' ),
			'meta' => array( 'title' => __( 'Clear all cached files of WP Super Cache', 'wp-super-cache-clear-cache-menu' ) ),
			'href' => wp_nonce_url( admin_url('options-general.php?page=wpsupercache&wp_delete_cache=1&tab=contents'), 'wp-cache' )
		) );
	}
	add_action( 'wp_before_admin_bar_render', 'clear_all_cached_files_wpsupercache', 999 );

}

?>