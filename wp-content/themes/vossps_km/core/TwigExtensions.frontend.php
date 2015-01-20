<?php


namespace Lumi\Frontend;


use TimberPost;
use Twig_SimpleFilter;

class TwigExtensions {

	public function __construct() {
	
		add_filter( 'get_twig', array( $this, 'add_lumi_email_sc' ) );

		add_filter( 'timber_context', array( $this, 'add_current_post' ) );
	
	}

	public function add_lumi_email_sc( $twig ) {
		$filter = new Twig_SimpleFilter( 'lumi_email', array( $this, 'lumi_email_filter_cb' ) );
		$twig->addFilter( $filter );
		return $twig;
	}

	public function lumi_email_filter_cb( $email ) {
		if( !is_email( $email ) ) return '';

		return do_shortcode( '[lumi-email]' . $email . '[/lumi-email]' );
	}

	public function add_current_post( $context ) {
		$context['post'] = new TimberPost();
		return $context;
	}



}