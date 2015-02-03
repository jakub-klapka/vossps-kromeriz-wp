( function( $ ){

	$( function(){

		if( typeof pagenow !== 'undefined'
			&& pagenow === 'studium'
			&& typeof adminpage !== 'undefined'
			&& adminpage === 'post-php') {

			$( '#parent_id option[value=""]' ).remove();

		}

	} );

} )( jQuery );