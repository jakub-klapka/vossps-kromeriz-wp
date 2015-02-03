<?php

namespace Lumi\Admin;


class Dokumentace {

	public function __construct() {

		add_action( 'wp_dashboard_setup', array( $this, 'add_widget' ) );

	}

	public function add_widget() {
		wp_add_dashboard_widget('ped_dokumentace', 'Dokumentace', array( $this, 'widget_cb' ));
	}

	public function widget_cb() {
		$filename = glob( dirname( __DIR__ ) . '/dokumentace/dokumentace_v*.pdf' );
		$filename = isset( $filename[0] ) ? $filename[0] : false;
		$filename = basename( $filename );
		printf( '<a href="%s" target="_blank">Zobrazit dokumentaci k ovládání webu.</a>', get_template_directory_uri() . '/dokumentace/' . $filename );
	}

}