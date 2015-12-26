/**
 * Customizer controls implementation.
 *
 * If you're looking to add JS for a specific panel or control, don't add it here.
 * The file only implements generic Customizer control implementations.
 */

(function( $ ) {
	$( window ).on( 'load', function() {

		// Range control
		$( '.customize-control-range input' ).on( 'input', function() {
			var val = $( this ).val();
			$( this ).parent().find( '.range-value' ).html( val );
		});

	} );
})( jQuery );
