<?php


namespace Lumi\Classes;

use TimberPost;
use WP_Query;

class SidebarMenuAPI {

	private $menu;

	public function __construct() {

		$this->construct_menu();

	}

	private function construct_menu() {

		$pages = $this->query_required_pages();

		$pages = $this->move_children_under_parents( $pages );

		//sort children based on menu order
		$sorted = $this->sort_pages_by_menu_order( $pages );

		$this->menu = $sorted;

	}

	private function query_required_pages() {
		$post = new TimberPost();
		$wp_post = get_post();

		//Add current page as parent to query all it's children
		$parents = array( 0 ); //Also always query top level items
		if( $post->post_type === 'studium' ) {
			$parents[] = $post->ID;
		}

		//Include all current ancestors
		$include_also = array();
		$current_post_parent = $post->parent();
		while( $current_post_parent != false ) {
			$include_also[] = $current_post_parent->ID;
			$current_post_parent = $current_post_parent->parent();
		};

		//Query
		$root_and_children = new WP_Query( array(
			'post_type' => 'studium',
			'posts_per_page' => -1,
			'post_parent__in' => $parents,
		) );

		$current_ancestors = new WP_Query( array(
			'post_type' => 'studium',
			'posts_per_page' => -1,
			'post__in' => $include_also,
			'post_parent__not_in' => array( 0 )
		) );

		//merge results
		return array_merge( $root_and_children->posts, $current_ancestors->posts, array( $wp_post ) );
	}

	private function move_children_under_parents( $pages ) {
		//Create array with keys as IDs
		$temp = array();
		foreach( $pages as $page ) {
			$page->children = array();
			$temp[ $page->ID ] = $page;
		};
		$pages = $temp;

		//move children under parents
		$did_move = true;
		while( $did_move === true ){
			$did_move = false;
			foreach( $pages as $page_id => $page ){

				//find out, if current page has any children
				$has_children = false;
				foreach( $pages as $searching_page ){
					if( $searching_page->post_parent === $page_id ){
						$has_children = true;
					}
				};

				//if not, move it to it's parent
				if( $has_children === false && $page->post_parent !== 0 ) {
					$pages[ $page->post_parent ]->children[] = $page;
					unset( $pages[ $page_id ] );
					$did_move = true;
				};
			};
		};
		return $pages;
	}

	private function sort_pages_by_menu_order( $pages ) {
		$new_pages = array();
		foreach( $pages as $page_id => $page ){

			//sort children
			if( count( $page->children ) > 0 ){
				$page->children = $this->sort_pages_by_menu_order( $page->children );
			}

			//check for existing key
			$new_key = $page->menu_order;
			while( isset( $new_pages[ $new_key ] ) ) {
				$new_key++;
			}
			$new_pages[ $new_key ] = $page;
		};
		ksort( $new_pages );

		return $new_pages;
	}

}