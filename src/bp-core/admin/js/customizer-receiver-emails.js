/**
 * Customizer implementation for Email.
 *
 * If you're looking to add JS for every instance of a control, don't add it here.
 * The file only implements the Customizer controls for Emails.
 *
 * @since 2.5.0
 */

(function( $ ) {
	wp.customize( 'bp_email_options[body_bg]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '.body_bg' ).attr( 'bgcolor', newval );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_email_options[header_bg]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '#template_header' ).css( 'background-color', newval );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_email_options[header_text_size]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '#logo' ).css( 'font-size', newval + 'px' );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_email_options[header_text_color]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '#logo_a' ).css( 'color', newval );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_email_options[email_body_bg]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '#bp_mailtpl_body_bg' ).css( 'background-color', newval );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_email_options[body_text_size]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '#bp_mailtpl_body' ).css( 'font-size', newval + 'px' );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_email_options[body_text_color]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '#bp_mailtpl_body' ).css( 'color', newval );
			}
		});
	});

	wp.customize( 'bp_email_options[footer_bg]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '.footer_bg' ).attr( 'bgcolor', newval );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_email_options[footer_text_size]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '#credit' ).css( 'font-size', newval + 'px' );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_email_options[footer_text_color]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '#credit' ).css( 'color', newval );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_email_options[footer_text]', function( value ) {
		value.bind(function( newval ) {
			$( '.footer_text' ).text( newval );
		});
	});
})( jQuery );
