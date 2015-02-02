<?php


namespace Lumi\Frontend;


use Lumi\Classes\AktualityTheme;
use Lumi\Classes\Breadcrumbs;
use Lumi\Classes\SidebarMenu;
use TimberImage;
use TimberPost;


class Layout {

	private $static_ver;
	private $dokumenty_id;
	private $fotogalerie_id;
	private $ss_id;
	private $vos_id;
	private $dv_id;


	public function __construct() {

		global $lumi;
		$this->static_ver = $lumi['config']['static_ver'];
		$this->dokumenty_id = $lumi['config']['dokumenty_id'];
		$this->fotogalerie_id = $lumi['config']['fotogalerie_id'];
		$this->ss_id = $lumi['config']['ss_id'];
		$this->vos_id = $lumi['config']['vos_id'];
		$this->dv_id = $lumi['config']['dv_id'];


		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter( 'timber_context', array( $this, 'add_custom_context_data' ) );
		
		add_action( 'acf/format_value', array( $this, 'strogify_acf_output' ), 10, 3 );
		add_action( 'acf/format_value', array( $this, 'wrap_school_address' ), 15, 3 );

		add_filter( 'timber_context', array( $this, 'add_sidebar_menu' ) );

		add_filter( 'timber_context', array( $this, 'add_teachers' ) );

		add_filter( 'timber_context', array( $this, 'add_about_school' ) );

		add_filter( 'timber_context', array( $this, 'add_admin_url' ) );

		add_filter( 'timber_context', array( $this, 'add_page_theme' ) );

		add_filter( 'timber_context', array( $this, 'breadcrumbs_items' ) );

		add_filter( 'timber_context', array( $this, 'ped_gallery' ) );

	}

	public function enqueue_scripts() {

		/*
		 * Styles
		 */
		wp_enqueue_style( 'layout', get_template_directory_uri() . '/assets/css/layout.css', array(), $this->static_ver );

		wp_register_style( 'ped_gallery', get_template_directory_uri() . '/assets/css/ped_gallery.css', array(), $this->static_ver );

		/*
		 * Libs
		 */
		wp_deregister_script( 'jquery' );
		wp_register_script( 'jquery', get_template_directory_uri() . '/assets/js/libs/jquery-2.1.3.js', array(), $this->static_ver, true );
		wp_register_script( 'velocity', get_template_directory_uri() . '/assets/js/libs/velocity.js', array( 'jquery' ), $this->static_ver, true );
		wp_register_script( 'modernizr', get_template_directory_uri() . '/assets/js/libs/modernizr.js', array(), $this->static_ver, true );

		/*
		 * Global scripts
		 */
		wp_enqueue_script( 'header_menu', get_template_directory_uri() . '/assets/js/header_menu.js', array( 'jquery', 'velocity' ), $this->static_ver, true );
		wp_enqueue_script( 'scroll_to_top', get_template_directory_uri() . '/assets/js/scroll_to_top.js', array( 'jquery', 'velocity' ), $this->static_ver, true );
		wp_enqueue_script( 'smooth_scrolling', get_template_directory_uri() . '/assets/js/smooth_scroll.js', array( 'jquery', 'velocity' ), $this->static_ver, true );
		wp_enqueue_script( 'modernizr' );

		/*
		 * Specific scripts
		 */
		wp_register_script( 'picturefill', get_template_directory_uri() . '/assets/js/libs/picturefill.js', array(), $this->static_ver, true );
		wp_register_script( 'owl_carousel_lib', get_template_directory_uri() . '/assets/js/libs/owl.carousel.js', array( 'jquery' ), $this->static_ver, true );
		wp_register_script( 'owl_carousel', get_template_directory_uri() . '/assets/js/owl_carousel.js', array( 'jquery', 'owl_carousel_lib', 'picturefill' ), $this->static_ver, true );

		wp_register_script( 'fancybox', get_template_directory_uri() . '/assets/js/libs/jquery.fancybox.js', array( 'jquery' ), $this->static_ver, true );
		wp_register_script( 'ped_gallery', get_template_directory_uri() . '/assets/js/gallery.js', array( 'jquery', 'fancybox' ), $this->static_ver, true );



	}

	public function add_custom_context_data( $data ) {
		$data['dokumenty_permalink'] = get_permalink( $this->dokumenty_id );
		$data['fotogalerie_permalink'] = get_permalink( $this->fotogalerie_id );
		$data['school_address'] = get_field( 'school_address', 'option' );
		$data['school_tel'] = get_field( 'school_tel', 'option' );
		$data['school_email'] = get_field( 'school_email', 'option' );
		return $data;
	}

	public function strogify_acf_output( $value, $post_id, $field ) {
		if( $field['name'] !== 'school_address' && $field['name'] !== 'school_tel' ) return $value;

		$value = preg_replace( '/\*\*([\s\S]*?)\*\*/', '<strong>$1</strong>', $value );
		return $value;
	}

	public function wrap_school_address( $value, $post_id, $field ) {
		if( $field['name'] !== 'school_address' ) return $value;

		$value = str_replace( '<strong>', '<strong><address>', $value );
		$value = str_replace( '</strong>', '</address></strong>', $value );
		return $value;
	}

	public function add_sidebar_menu( $data ) {

		$menu = SidebarMenu::getMenu();
		$flatered_menu = $menu->getFlatteredMenu();

		foreach( $flatered_menu as $key => $item ) {
			switch( $item['id'] ){
				case( $this->ss_id ):
					$flatered_menu[$key]['is_ss'] = true;
					break;
				case( $this->vos_id ):
					$flatered_menu[$key]['is_vos'] = true;
					break;
				case( $this->dv_id ):
					$flatered_menu[$key]['is_dv'] = true;
			}
		}

		$data['sidebar_menu'] = $flatered_menu;

		return $data;
	}

	public function add_teachers( $data ) {
		$data['teachers'] = get_field( 'teachers', 'option' );

		return $data;
	}

	public function add_about_school( $context ) {
		$context['about_school'] = get_field( 'about_school', 'option' );
		return $context;
	}

	public function add_admin_url( $context ) {
		$context['admin_url'] = admin_url();
		return $context;
	}

	public function add_page_theme( $context ) {

		$page_theme = 'ss'; //default

		if( is_singular( 'page' ) ){
			$page_theme = ( get_field( 'page_theme' ) ) ? get_field( 'page_theme' ) : 'ss';
		}

		if( is_singular( 'studium' ) ){

			$tpost = new TimberPost();

			$iterative_parent = $tpost;
			while( $iterative_parent !== false ){
				$top_level_parent = $iterative_parent;
				$iterative_parent = $iterative_parent->parent();
			};

			switch( $top_level_parent->ID ){
				case( $this->ss_id ):
					$page_theme = 'ss';
					break;
				case( $this->vos_id ):
					$page_theme = 'vos';
					break;
				case( $this->dv_id ):
					$page_theme = 'dv';
					break;
				default:
					$page_theme = 'ss';
			}
		}

		if( is_singular( 'aktuality' ) ) {
			$page_theme = AktualityTheme::getTheme();
		}

		if( is_tax( 'typ_studia' ) ){

			global $lumi;
			$current_term = get_queried_object();
			$id = $current_term->term_id;

			switch( $id ) {
				case( $lumi['config']['tax_vos_id'] ):
					$page_theme = 'vos';
					break;
				case( $lumi['config']['tax_ss_id'] ):
					$page_theme = 'ss';
					break;
				case( $lumi['config']['tax_dv_id'] ):
					$page_theme = 'dv';
					break;
				default:
					$page_theme = 'none';
			};

		}

		$context['page_theme'] = $page_theme;
		return $context;
	}

	public function breadcrumbs_items( $data ) {
		$breadcrumbs = new Breadcrumbs();
		$data['breadcrumbs'] = $breadcrumbs->get_breadcrumbs();
		return $data;
	}

	public function ped_gallery( $data ){
		if( !get_field( 'use_gallery' ) ) return $data;
		if( !is_singular( array( 'page', 'studium', 'aktuality' ) ) ) return $data;


		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_ped_gallery' ) );

		$gallery = get_field( 'gallery' );
		$images = array();
		foreach( $gallery as $item ) {
			$images[] = new TimberImage( $item['ID'] );
		}
		$data['gallery'] = $images;

		return $data;
	}

	public function enqueue_ped_gallery() {
		wp_enqueue_style( 'ped_gallery' );
		wp_enqueue_script( 'ped_gallery' );

	}

}