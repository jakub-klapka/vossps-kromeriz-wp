<?php

namespace Lumi\Frontend;


use TimberImage;
use WP_Query;

class HomePage {

	public function __construct() {

		add_action( 'parse_query', array( $this, 'check_for_home' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'add_owl_script' ), 20 );

		add_filter( 'timber_context', array( $this, 'add_slider' ) );

	}

	/**
	 * @param $query WP_Query
	 */
	public function check_for_home( $query ) {
		if( !$query->is_main_query() || !$query->is_home() ) return;

		$query->set( 'post_type', 'aktuality' );

	}

	public function add_owl_script() {
		if( !is_home() ) return;

		wp_enqueue_script( 'owl_carousel' );

	}

	public function add_slider( $data ) {
		if( !is_home() ) return $data;

		$slider = get_field( 'home_slider', 'option' );

		foreach( $slider as $key => $item ) {
			$slider[ $key ][ 'image' ] = new TimberImage( $item[ 'image' ] );
		}

		$data['slider'] = $slider;
		$data['slider_timeout'] = ( get_field( 'home_slider_timeout', 'option' ) ) ? ( get_field( 'home_slider_timeout', 'option' ) . '000' ) : '5000';

		return $data;
	}

}