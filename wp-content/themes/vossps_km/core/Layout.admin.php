<?php


namespace Lumi\Admin;


class Layout {

	public function __construct() {

		if( function_exists('acf_add_options_page') ) {

			acf_add_options_page( array(
				'page_title' => 'Obecné nastavení'
			) );

			acf_add_options_page( array(
				'page_title' => 'Kontakty na učitele'
			) );


		}

		add_action( 'acf/save_post', array( $this, 'save_sitedesc' ), 20 );
	
	}

	public function save_sitedesc( $page_id ) {
		if( $page_id !== 'options' || !current_user_can( 'edit_posts' ) ) return;

		update_option( 'blogdescription', get_field( 'seo_desc', 'option' ) );
	}

}