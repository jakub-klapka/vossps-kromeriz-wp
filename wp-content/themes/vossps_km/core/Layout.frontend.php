<?php


namespace Lumi\Frontend;


use Lumi\Classes\SidebarMenu;


class Layout {

	private $static_ver = 1;
	private $dokumenty_id = 12;
	private $fotogalerie_id = 15;
	private $ss_id = 22;
	private $vos_id = 24;
	private $dv_id = 26;


	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter( 'timber_context', array( $this, 'add_custom_context_data' ) );
		
		add_action( 'acf/format_value', array( $this, 'strogify_acf_output' ), 10, 3 );
		add_action( 'acf/format_value', array( $this, 'wrap_school_address' ), 15, 3 );

		add_filter( 'timber_context', array( $this, 'add_sidebar_menu' ) );

		add_filter( 'timber_context', array( $this, 'add_teachers' ) );

		add_action( 'timber_context', array( $this, 'add_about_school' ) );

		add_action( 'timber_context', array( $this, 'add_admin_url' ) );

	}

	public function enqueue_scripts() {

		/*
		 * Styles
		 */
		wp_enqueue_style( 'layout', get_template_directory_uri() . '/assets/css/layout.css', array(), $this->static_ver );

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
		wp_register_script( 'owl_carousel_lib', get_template_directory_uri() . '/assets/js/libs/owl.carousel.js', array( 'jquery' ), $this->static_ver, true );
		wp_register_script( 'owl_carousel', get_template_directory_uri() . '/assets/js/owl_carousel.js', array( 'jquery', 'owl_carousel_lib' ), $this->static_ver, true );


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

}