<?php


namespace Lumi\Glob;


class Studium {

	public function __construct() {
	
		add_action( 'init', array( $this, 'register_cpt' ) );
	
	}

	public function register_cpt() {

		$labels = array(
			'name'               => 'Studium',
			'singular_name'      => 'Stránka',
			'menu_name'          => 'Studium',
			'name_admin_bar'     => 'Nová stránka Studium',
			'add_new'            => 'Přidat',
			'add_new_item'       => 'Přidat stránku',
			'new_item'           => 'Nová stránka Studium',
			'edit_item'          => 'Upravit stránku',
			'view_item'          => 'Ukázat stránku',
			'all_items'          => 'Všechny stránky',
			'search_items'       => 'Hledat stránky',
			'parent_item_colon'  => 'Nadřazené stránky:',
			'not_found'          => 'Stránky nenalezeny.',
			'not_found_in_trash' => 'Stránky nenalezeny ani v koši.'
		);

		register_post_type( 'studium', array(
			'labels' => $labels,
			'public' => true,
			'hierarchical' => true,
			'supports' => array( 'title', 'editor', 'revisions', 'page-attributes' ),
			'rewrite' => array(
				//'slug' => '/',
				'with_front' => false,
				'feeds' => false,
				'pages' => false
			)
		) );

	}

}