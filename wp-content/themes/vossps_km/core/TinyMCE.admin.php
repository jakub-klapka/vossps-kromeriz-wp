<?php

namespace Lumi\Admin;


class TinyMCE {

	public function __construct() {

		add_action( 'admin_init', array( $this, 'add_stylesheet' ) );

		add_filter( 'tiny_mce_before_init', array( $this, 'add_important_heading' ) );

	}

	public function add_stylesheet() {
		add_editor_style( 'assets/css/editor-style.css' );
	}

	public function add_important_heading( $settings ) {
		$settings['style_formats'] = json_encode(
			array(
				array(
					'title' => 'Zvýrazněný nadpis',
					'block' => 'h2',
					'classes' => 'important_heading'
				)
			)
		);

		return $settings;
	}

}