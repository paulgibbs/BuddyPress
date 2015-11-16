/**
 * Implementation details for BP Customizer controls.
 *
 * If you're looking to add JS for a specific Customizer panel or a control implementation,
 * don't do it here.
 */

(function( $ ) {
	$( window ).load(function() {

		// Range control
		$( '.customize-control-range input' ).on( 'input', function() {
			var val = $( this ).val();
			$( this ).parent().find( '.range-value' ).html( val );
		});

	});
})( jQuery );
