<?php

namespace Lumi\Classes;


use TimberPost;

class Breadcrumbs {

	private $breadcrumbs;

	public function __construct() {

		//init
		$this->breadcrumbs = array(
			array(
				'name' => 'Hlavní strana',
				'url' => get_bloginfo( 'url' )
			)
		);

		//routes
		if( is_singular( 'page' ) ) {
			$this->set_page_bc();
		}

		if( is_singular( 'studium' ) ) {
			$this->set_studium_bc();
		}

		if( is_singular( 'aktuality' ) ) {
			$this->set_aktuality_bc();
		}

		if( is_tax( 'typ_studia' ) ) {
			$this->set_typ_studia_bc();
		}


	}

	public function get_breadcrumbs() {
		return $this->breadcrumbs;
	}

	private function set_page_bc() {
		$current_page = new TimberPost();
		$this->breadcrumbs[] = array(
			'name' => $current_page->title(),
			'url' => $current_page->permalink()
		);
	}

	private function set_studium_bc() {

		$current_page = new TimberPost();

		//add current page
		$studium_parents = array(
			array(
				'name' => $current_page->title(),
				'url' => $current_page->permalink()
			)
		);

		$parent = $current_page;
		while( ( $parent = $parent->parent() ) !== false ) {
			array_unshift( $studium_parents, array(
				'name' => $parent->title(),
				'url' => $parent->permalink()
			) );
		};

		$this->breadcrumbs = array_merge( $this->breadcrumbs, $studium_parents );

	}

	private function set_aktuality_bc() {
		$akt_theme = AktualityTheme::getTheme();

		if( $akt_theme !== 'none' ) {

			global $lumi;
			$parent_page_id = $lumi['config'][ $akt_theme . '_id' ];
			$parent_page = new TimberPost( $parent_page_id );

			//Parent category
			$this->breadcrumbs[] = array(
				'name' => $parent_page->title(),
				'url' => $parent_page->permalink()
			);

		}

		//Current page
		$post = new TimberPost();
		$this->breadcrumbs[] = array(
			'name' => $post->title(),
			'url' => $post->permalink()
		);


	}

	private function set_typ_studia_bc() {

		$current_term = get_queried_object();
		$name = $current_term->name;
		$id = $current_term->term_id;

		$this->breadcrumbs[] = array(
			'name' => $name,
			'url' => get_term_link( $current_term )
		);

	}

}