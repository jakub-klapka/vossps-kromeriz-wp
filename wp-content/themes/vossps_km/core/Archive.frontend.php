<?php

namespace Lumi\Frontend;

use Timber;
use TimberImage;

class Archive {

	public function __construct() {

		add_action( 'wp', array( $this, 'is_archive' ) );

	}

	public function is_archive() {

		if( is_archive() || is_home() ) {

			add_filter( 'timber_context', array( $this, 'add_context_data' ) );

		}

	}

	public function add_context_data( $data ) {
		$queried_object = get_queried_object();
		if( $queried_object ) {
			$data['page_title'] = $queried_object->name;
		}
		$data['posts'] = Timber::get_posts();
		foreach( $data['posts'] as $key => $post ) {
			if( $post->aktualita_image ) {
				$data['posts'][$key]->thumbnail = new TimberImage( $post->aktualita_image );
			}
		}
		$data['pagination'] = $this->get_pagination();
		return $data;
	}

	private function get_pagination() {

		global $wp_query, $wp_rewrite;

		$total        = ( isset( $wp_query->max_num_pages ) ) ? $wp_query->max_num_pages : 1;
		$current      = ( get_query_var( 'paged' ) ) ? intval( get_query_var( 'paged' ) ) : 1;
		$query_args   = array();


		$format = user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' );

		if( is_tax() ) {
			$current_tax = get_queried_object();
			$base = get_term_link( $current_tax );
		} else {
			$base =  trailingslashit( get_post_type_archive_link( 'aktuality' ) );
		}


		$args = array(
			'base' => $base . '%_%', // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
			'format' => $format, // ?page=%#% : %#% is replaced by the page number
			'total' => $total,
			'current' => $current,
			'show_all' => false,
			'prev_next' => true,
			'prev_text' => __('&laquo; Previous'),
			'next_text' => __('Next &raquo;'),
			'end_size' => 1,
			'mid_size' => 2,
			'type' => 'array',
			'add_args' => $query_args, // array of query args to add
			'add_fragment' => '',
			'before_page_number' => '',
			'after_page_number' => ''
		);

		// Who knows what else people pass in $args
		$total = (int) $args['total'];
		if ( $total < 2 ) {
			return;
		}
		$current  = (int) $args['current'];
		$end_size = (int) $args['end_size']; // Out of bounds?  Make it the default.
		if ( $end_size < 1 ) {
			$end_size = 1;
		}
		$mid_size = (int) $args['mid_size'];
		if ( $mid_size < 0 ) {
			$mid_size = 2;
		}
		$add_args = is_array( $args['add_args'] ) ? $args['add_args'] : false;
		$r = '';
		$page_links = array();
		$dots = false;

		if ( $args['prev_next'] && $current && 1 < $current ) :
			$link = str_replace( '%_%', 2 == $current ? '' : $args['format'], $args['base'] );
			$link = str_replace( '%#%', $current - 1, $link );
			if ( $add_args )
				$link = add_query_arg( $add_args, $link );
			$link .= $args['add_fragment'];

			$page_links[] = array(
				'is_previous' => true,
				'url' => $link
			);
		endif;
		for ( $n = 1; $n <= $total; $n++ ) :
			if ( $n == $current ) :
				$page_links[] = array(
					'is_current' => true,
					'title' => $n
				);
				$dots = true;
			else :
				if ( $args['show_all'] || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) :
					$link = str_replace( '%_%', 1 == $n ? '' : $args['format'], $args['base'] );
					$link = str_replace( '%#%', $n, $link );
					if ( $add_args )
						$link = add_query_arg( $add_args, $link );
					$link .= $args['add_fragment'];

					$page_links[] = array(
						'title' => $n,
						'url' => $link
					);
					$dots = true;
				elseif ( $dots && ! $args['show_all'] ) :
					$page_links[] = array(
						'is_dots' => true
					);
					$dots = false;
				endif;
			endif;
		endfor;
		if ( $args['prev_next'] && $current && ( $current < $total || -1 == $total ) ) :
			$link = str_replace( '%_%', $args['format'], $args['base'] );
			$link = str_replace( '%#%', $current + 1, $link );
			if ( $add_args )
				$link = add_query_arg( $add_args, $link );
			$link .= $args['add_fragment'];

			$page_links[] = array(
				'is_next' => true,
				'url' => $link
			);
		endif;

		return $page_links;

	}

}