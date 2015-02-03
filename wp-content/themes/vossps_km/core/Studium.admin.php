<?php

namespace Lumi\Admin;


class Studium {

	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_remove_parent_studium' ) );
		
		add_action( 'init', array( $this, 'restrict_root_items_edit_screen' ) );

		add_action( 'admin_notices', array( $this, 'restrict_notice' ) );

		add_filter( 'page_row_actions', array( $this, 'remove_edit_from_edit_screen' ), 10, 2 );

	}

	public function enqueue_remove_parent_studium() {
		if( !current_user_can( 'edit_posts' ) ) return;

		$current_screen = get_current_screen();
		if( $current_screen->base !== 'post' || $current_screen->post_type !== 'studium' ) return;

		global $lumi;
		wp_enqueue_script( 'ped_remove_parent_studium', get_template_directory_uri() . '/assets/admin_js/remove_parent_studium.js', array( 'jquery' ), $lumi['config']['static_ver'], true );

	}

	public function restrict_root_items_edit_screen() {

		global $lumi;
		$restricted = array( $lumi['config']['ss_id'], $lumi['config']['vos_id'], $lumi['config']['dv_id'] );

		if( is_admin()
		    && isset( $_GET['action'] )
		    && $_GET['action'] === 'edit'
			&& isset( $_GET['post'] )
			&& in_array( $_GET['post'], $restricted )
			&& !current_user_can( 'manage_options' ) )
		{
			wp_redirect( admin_url( 'edit.php?post_type=studium&ped_notice=restricted' ) );
			exit();
		}

	}

	public function restrict_notice() {
		if( isset( $_GET['ped_notice'] ) && $_GET['ped_notice'] === 'restricted' ) {
			?>
			<div class="error">
				<p>Tato stránka slouží pouze jako rozcestník a není možné ji upravovat.</p>
			</div>
			<?php
		}
	}

	public function remove_edit_from_edit_screen( $actions, $post ) {
		global $lumi;
		$restricted = array( $lumi['config']['ss_id'], $lumi['config']['vos_id'], $lumi['config']['dv_id'] );

		if( in_array( $post->ID, $restricted ) ) {
			unset( $actions[ 'edit' ] );
			unset( $actions[ 'inline hide-if-no-js' ] );
		}

		return $actions;
	}

}