<?php


namespace Lumi\Frontend;


use Twig_SimpleFilter;

class TwigExtensions {

	public function __construct() {
	
		add_filter( 'get_twig', array( $this, 'add_lumi_email_sc' ) );
	
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



}