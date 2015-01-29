<?php


namespace Lumi\Glob;


class Aktuality {

	public function __construct() {
	
		add_action( 'init', array( $this, 'register_cpt' ) );
	
	}

	public function register_cpt() {
		$labels = array(
			'name'               => 'Aktuality',
			'singular_name'      => 'Aktualita',
			'menu_name'          => 'Aktuality',
			'name_admin_bar'     => 'Nová aktualita',
			'add_new'            => 'Přidat',
			'add_new_item'       => 'Přidat aktualitu',
			'new_item'           => 'Nová aktualita',
			'edit_item'          => 'Upravit aktualitu',
			'view_item'          => 'Ukázat aktualitu',
			'all_items'          => 'Všechny aktuality',
			'search_items'       => 'Hledat aktuality',
			'parent_item_colon'  => 'Nadřazené aktuality:',
			'not_found'          => 'Aktuality nenalezeny.',
			'not_found_in_trash' => 'Aktuality nenalezeny ani v koši.'
		);

		register_post_type( 'aktuality', array(
			'labels' => $labels,
			'public' => true,
			'supports' => array( 'title', 'editor', 'revisions' ),
			'has_archive' => true,
			'taxonomies' => array( 'typ_studia' ),
			'rewrite' => array(
				'feeds' => false
			)
		) );

		$tax_labels = array(
			'name'              => 'Typ studia',
			'singular_name'     => 'Typ',
			'search_items'      => 'Hledat typy',
			'all_items'         => 'Všechny typy',
			'parent_item'       => 'Nadřazený typ',
			'parent_item_colon' => 'Nadřazený typ:',
			'edit_item'         => 'Upravit typ',
			'update_item'       => 'Aktualizovat typ',
			'add_new_item'      => 'Přidat typ studia',
			'new_item_name'     => 'Nový typ studia',
			'menu_name'         => 'Typy studia'
		);

		register_taxonomy( 'typ_studia', 'aktuality', array(
			'labels' => $tax_labels,
			'show_tagcloud' => false,
			'show_admin_column' => true,
			'hierarchical' => true,
			'capabilities' => array(
				'manage_terms' => 'manage_options',
				'edit_terms' => 'manage_options',
				'delete_terms' => 'manage_options',
				'assign_terms' => 'edit_posts'
			),
			'rewrite' => array(
				'slug' => '/',
				'with_front' => false
			)
		) );

	}

}