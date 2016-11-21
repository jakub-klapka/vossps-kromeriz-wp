<?php

namespace Lumi\Glob;


class SPP {

	public function __construct() {

		add_filter( 'rewrite_rules_array', [ $this, 'addExceptionForSppTaxRule'] );

	}

	/**
	 * Because we have same post type name as tax rewrite slug, we will get 404 for any root page (since it's lookig for tax)
	 * We will add exception for /spp page, which is only one, which have to be root and also not tax item
	 *
	 * @wp-filter rewrite_rules_array
	 *
	 * @param $rules
	 *
	 * @return array
	 */
	public function addExceptionForSppTaxRule( $rules ) {

		$orig_regex = 'studium/([^/]+)/?$';
		$path = isset( $rules[ $orig_regex ] ) ? $rules[ $orig_regex ] : false;

		if( $path !== false ) {
			$rule_offset = array_search( $orig_regex, array_keys( $rules ) );
			$rules = array_merge(
				array_slice( $rules, 0, $rule_offset, true ),
				[ 'studium\/(?!spp)([^/]+)\/?$' => $path ],
				array_slice( $rules, $rule_offset + 1, null, true )
			);

		}

		return $rules;
	}

}