<?php

namespace Lumi\Classes;


use TimberPost;

class AktualityTheme {


	public static function getTheme() {

		global $lumi_aktuality_theme;
		if( isset( $lumi_aktuality_theme ) ) return $lumi_aktuality_theme;

		global $lumi;
		$tax_vos_id = $lumi['config']['tax_vos_id'];
		$tax_ss_id = $lumi['config']['tax_ss_id'];
		$tax_dv_id = $lumi['config']['tax_dv_id'];


		$post = new TimberPost();
		$terms = wp_get_post_terms(
			$post->ID,
			'typ_studia',
			array(
				'orderby' => 'ID',
				'order' => 'ASC'
			)
		);

		$page_theme = 'none'; //default

		if( !empty( $terms ) ){
			$term_that_matter = reset( $terms );
			switch( $term_that_matter->term_id ) {
				case( $tax_ss_id ):
					$page_theme = 'ss';
					break;
				case( $tax_vos_id ):
					$page_theme = 'vos';
					break;
				case( $tax_dv_id ):
					$page_theme = 'dv';
			}
		}

		$lumi_aktuality_theme = $page_theme;
		return $page_theme;

	}

}