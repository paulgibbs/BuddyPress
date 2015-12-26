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
			var $this = $( this );
			$this.parent().find( '.range-value' ).html( $this.val() );
		});
	});

	$.ajaxPrefilter(function( request ) {
		if ( ! request.hasOwnProperty( 'data' ) ) {
			return;
		}

		var args = request.data.split( '&' );
		if ( $.inArray( 'wp_customize=on', args ) == -1 || $.inArray( 'action=customize_save', args ) == -1 ) {
			return;
		}

		// customize_save. add argument. djpaultodo get from custom JS
	});
})( jQuery );
