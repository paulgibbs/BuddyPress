(function( $ ) {
	$( window ).load(function() {
		wp.customize.panel( 'bp_mailtpl' ).focus();

		// djpaultodo: move this into a universal/shared file, or move the above into something email-specific.
		// Range slider
		$( '.customize-control-range input' ).on( 'input', function() {
			var val = $( this ).val();
			$( this ).parent().find( '.range-value' ).html( val );
		});

	});
})( jQuery );
