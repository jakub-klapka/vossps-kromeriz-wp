<?php

namespace Lumi\Classes;


use TimberPost;

class StudiumTheme {

	public static function getTheme() {
		global $lumi;
		if( isset( $lumi['studium_theme'] ) ) return $lumi['studium_theme'];

		if( is_home() ) {
			$lumi['studium_theme'] = 'none';
			return 'none';
		}

		//if aktuality
		$aktuality_theme = AktualityTheme::getTheme();
		if( $aktuality_theme !== 'none' ) {
			$lumi['studium_theme'] = $aktuality_theme;
			return $aktuality_theme;
		}

		//if studium single
		if( is_singular( 'studium' ) ) {
			$current_parent = new TimberPost();
			$last_parent = $current_parent;
			while( ( $current_parent = $current_parent->parent() ) !== false ) {
				$last_parent = $current_parent;
			}

			switch( $last_parent->ID ) {
				case( $lumi['config']['ss_id'] ):
					$theme = 'ss';
					break;
				case( $lumi['config']['vos_id'] ):
					$theme = 'vos';
					break;
				case( $lumi['config']['dv_id'] ):
					$theme = 'dv';
					break;
				default:
					$theme = 'none';
			};

			$lumi['studium_theme'] = $theme;
			return $theme;
		}


		//is tax
		if( is_tax( 'typ_studia' ) ) {
			$current_term = get_queried_object();

			switch( $current_term->term_id ){
				case( $lumi['config']['tax_ss_id'] ):
					$theme = 'ss';
					break;
				case( $lumi['config']['tax_vos_id'] ):
					$theme = 'vos';
					break;
				case( $lumi['config']['tax_dv_id'] ):
					$theme = 'dv';
					break;
				default:
					$theme = 'none';
			};

			$lumi['studium_theme'] = $theme;
			return $theme;
		}

		return 'none';

	}

}