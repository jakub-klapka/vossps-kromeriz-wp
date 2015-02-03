/* global jQuery */
(function($){

	$( function(){
		$('#wpseo_meta h3:first span').text('SEO nastavení');
		$('#wpseo_meta tr:has(#yoast_wpseo_focuskw)').hide();
		$('#wpseo_meta #linkdex').hide();


		$('#wp-admin-bar-debug-bar').find('.ab-item').click(function(e){ e.preventDefault(); });
	} );

})(jQuery);