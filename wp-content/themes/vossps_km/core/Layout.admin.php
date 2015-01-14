<?php


namespace Lumi\Admin;


class Layout {

	public function __construct() {

		if( function_exists('acf_add_options_page') ) {

			acf_add_options_page( array(
				'page_title' => 'Obecné nastavení'
			) );

		}
	
	}

}