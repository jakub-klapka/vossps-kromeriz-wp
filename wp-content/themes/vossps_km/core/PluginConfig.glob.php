<?php


namespace Lumi\Glob;


class PluginConfig {

	public function __construct() {

		include_once( 'inc/clear_cache.php' );

		/*
		* Disable WPSEO page score functions and marker
		*/
		add_filter( 'wpseo_use_page_analysis', function() {return false;} );

		add_action( 'init', function() {
			if( isset( $GLOBALS['wpseo_front'] ) ) {
				remove_action( 'wpseo_head', array( $GLOBALS['wpseo_front'], 'debug_marker' ), 2 );
			}
		} );

		/*
		* Disable WPSEO search json
		*/
		add_filter( 'disable_wpseo_json_ld_search', '__return_true' );


		add_action( 'login_enqueue_scripts', array( $this, 'my_login_logo' ) );

		add_action( 'init', array( $this, 'remove_post_type_support' ) );

		/**
		 * User switching capabilities
		 */
		global $user_switching;
		remove_filter( 'user_has_cap', array( $user_switching, 'filter_user_has_cap' ), 10, 3 );
		add_filter( 'user_has_cap', array( $this, 'user_switching_filter_user_has_cap' ), 10, 3 );


		add_action('user_register', array( $this, 'set_user_metaboxes' ) );
		
		/*
		 * Zalomeni
		 */
		add_filter( 'zalomeni_filtry', array( $this, 'add_custom_zalomeni_filter' ) );


	}

	public function add_custom_zalomeni_filter( $filters ) {
		$filters[] = 'lumi_zalomeni';
		return $filters;
	}

	public function my_login_logo() { ?>
		<style type="text/css">
			body.login div#login h1 a {
				background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/assets/login_logo.png);
				background-size: 165px 95px;
				width: 165px;
				height: 95px;
			}
		</style>
	<?php }


	public function remove_post_type_support() {
		remove_post_type_support( 'page', 'comments' );
		remove_post_type_support( 'page', 'author' );
	}

	public function user_switching_filter_user_has_cap( array $user_caps, array $required_caps, array $args ) {
		$required_capability = 'manage_options';
		if ( 'switch_to_user' == $args[0] ) {
			$user_caps['switch_to_user'] = ( user_can( $args[1], $required_capability, $args[2] ) and ( $args[2] != $args[1] ) );
		} else if ( 'switch_off' == $args[0] ) {
			$user_caps['switch_off'] = user_can( $args[1], $required_capability );
		}
		return $user_caps;
	}

	public function set_user_metaboxes($user_id=NULL) {
		$defaults = array(
			'meta-box-order_dashboard' => 'a:4:{s:6:"normal";s:45:"dashboard_right_now,custom_dashboard_activity";s:4:"side";s:13:"gadash-widget";s:7:"column3";s:0:"";s:7:"column4";s:0:"";}',
			'closedpostboxes_studium' => 'a:0:{}',
			'metaboxhidden_studium' => 'a:6:{i:0;s:23:"acf-group_54ca6ca7c953a";i:1;s:23:"acf-group_54be75ae0ca39";i:2;s:23:"acf-group_54be5feea3991";i:3;s:23:"acf-group_54b638d65150b";i:4;s:12:"revisionsdiv";i:5;s:7:"slugdiv";}',
			'manageuserscolumnshidden' => 'a:4:{i:0;s:5:"posts";i:1;s:9:"ure_roles";i:2;s:0:"";i:3;s:0:"";}'
		);

		// So this can be used without hooking into user_register
		if ( ! $user_id)
			$user_id = get_current_user_id();
		foreach( $defaults as $key => $value ){
		// Set the default order if it has not been set yet
			if ( ! get_user_meta( $user_id, $key, true) ) {
				$meta_value = unserialize( $value );
				update_user_meta( $user_id, $key, $meta_value );
			}
		}
	}


}