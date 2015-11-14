(function( $ ) {
	$( window ).load(function() {
		wp.customize.panel( 'bp_mailtpl' ).focus();

		// Text size
		$( '.bp_mailtpl_range' ).on( 'input', function() {
			var val = $( this ).val();
			$( this ).parent().find( '.font_value' ).html( val );
		});

		//
		$( '#bp-mailtpl-send_mail' ).on( 'click', function( e ) {
			e.preventDefault();
			$( '#bp-mailtpl-spinner' ).fadeIn();

			$.ajax({
				url  : ajaxurl,
				data : { action: 'bp_mailtpl_send_email' }
			}).done(function() {
				$( '#bp-mailtpl-spinner' ).fadeOut();
				$( '#bp-mailtpl-success' ).fadeIn().delay( 3000 ).fadeOut();
			});
		});

	});
})( jQuery );
