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
		$output = array();

		$parents = array( 0 ); //Also always query top level items

		if( $post->post_type === 'studium' ) {

			//Add current page as parent to query all it's children
			$parents[] = $post->ID;

			//Include all current ancestors
			$include_also = array();
			$current_post_parent = $post->parent();
			while( $current_post_parent != false ) {
				$include_also[] = $current_post_parent->ID;
				$current_post_parent = $current_post_parent->parent();
			};

			$output = array_merge( $output, array( $wp_post ) );

		}


		//Query
		$root_and_children = new WP_Query( array(
			'post_type' => 'studium',
			'posts_per_page' => -1,
			'post_parent__in' => $parents,
		) );

		$output = array_merge( $output, $root_and_children->posts );

		if( !empty( $include_also ) ){

			$current_ancestors = new WP_Query( array(
				'post_type' => 'studium',
				'posts_per_page' => -1,
				'post__in' => $include_also,
				'post_parent__not_in' => array( 0 )
			) );

			$output = array_merge( $output, $current_ancestors->posts );

		}

		return $output;

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

	public function getFlatteredMenu() {

		$output = array();

		foreach( $this->menu as $page ) {

			//create children array
			$children = array();

			//if current page is top-level menu page, rest won't be active, otherwise it could
			$current_page = get_post();
			$is_active = $page->ID === $current_page->ID ? false : true;

			$this->generate_children_array( $page, $children, $is_active );

			//construct toplevel item
			$output[] = array(
				'id' => $page->ID,
				'name' => $page->post_title,
				'url' => get_permalink( $page->ID ),
				'children' => $children
			);

		};

		return $output;

	}

	private function generate_children_array( $page, &$children, &$is_active ) {

		foreach( $page->children as $child ){
			$children[] = array(
				'name' => $child->post_title,
				'url' => get_permalink( $child->ID ),
				'is_active' => $is_active
			);

			//Once we hit current post in menu, all following are not active for sure
			$current_post = get_post();
			if( $child->ID === $current_post->ID ){
				$is_active = false;
			}

			if( !empty( $child->children ) ) {
				$this->generate_children_array( $child, $children, $is_active );
			}
		}

	}

}