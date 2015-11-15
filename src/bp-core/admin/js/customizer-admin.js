(function( $ ) {
	$( window ).load(function() {
		wp.customize.panel( 'bp_mailtpl' ).focus();

		// Text size
		$( '.customize-control-range input' ).on( 'input', function() {
			var val = $( this ).val();
			$( this ).parent().find( '.range-value' ).html( val );
		});

	});
})( jQuery );
