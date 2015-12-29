/**
 * Customizer implementation for Email.
 *
 * @since 2.5.0
 */

(function( $ ) {
	/**
	 * Add email ID into outgoing AJAX request when the Customizer "save" button is pressed.
	 *
	 * @param {object} request
	 * @since 2.5.0
	 */
	$.ajaxPrefilter(function( request ) {
		if ( request.data === 'undefined' ) {
			return;
		}

		var args = request.data.split( '&' );
		if ( $.inArray( 'wp_customize=on', args ) === -1 || $.inArray( 'action=customize_save', args ) === -1 ) {
			return;
		}

		// See bp_email_override_customizer_template()
		if ( typeof window.BPEmails === 'undefined' ) {
			return;
		}

		request.data = request.data + '&BPEmails=' + encodeURIComponent( JSON.stringify( window.BPEmails ) );
	});
})( jQuery );
