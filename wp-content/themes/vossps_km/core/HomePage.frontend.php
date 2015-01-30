<?php

namespace Lumi\Frontend;


use WP_Query;

class HomePage {

	public function __construct() {

		add_action( 'parse_query', array( $this, 'check_for_home' ) );

	}

	/**
	 * @param $query WP_Query
	 */
	public function check_for_home( $query ) {
		if( !$query->is_main_query() || !$query->is_home() ) return;

		$query->set( 'post_type', 'aktuality' );

	}

}