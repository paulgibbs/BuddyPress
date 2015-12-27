/**
 * Customizer controls implementation.
 *
 * If you're looking to add JS for a specific panel or control, don't add it here.
 * The file only implements generic Customizer control implementations.
 *
 * @since 2.5.0
 */

/* global BPEmails */

(function( $, undefined ) {
	$( window ).on( 'load', function() {

		/**
		 * <range> element: update label when value changes.
		 *
		 * @since 2.5.0
		 */
		$( '.customize-control-range input' ).on( 'input', function() {
			var $this = $( this );
			$this.parent().find( '.range-value' ).html( $this.val() );
		});
	});

	/**
	 * Add email ID into outgoing AJAX request when the Customizer "save" button is pressed.
	 *
	 * @param {object} request
	 * @since 2.5.0
	 */
	$.ajaxPrefilter(function( request ) {
		if ( request.data === undefined ) {
			return;
		}

		var args = request.data.split( '&' );
		if ( $.inArray( 'wp_customize=on', args ) === -1 || $.inArray( 'action=customize_save', args ) === -1 ) {
			return;
		}

		// See bp_email_override_customizer_template()
		if ( typeof BPEmails === undefined ) {
			return;
		}

		console.log('kite', request);
		//BPEmails.ID
		//BPEmails.nonce = 'bp-email-' . $post_id
	});
})( jQuery );
