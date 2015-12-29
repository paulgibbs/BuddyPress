/**
 * Customizer implementation for Email.
 *
 * If you're looking to add JS for every instance of a control, don't add it here.
 * The file only implements the Customizer controls for Emails.
 *
 * @since 2.5.0
 */

(function( $ ) {
	wp.customize( 'bp_mailtpl_opts[body_bg]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '.body_bg' ).attr( 'bgcolor', newval );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_mailtpl_opts[header_logo]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '#logo a' ).html( '<img src="' + newval + '">' );
			} else {
				$( '#logo a' ).html( '' );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_mailtpl_opts[header_logo_text]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '#logo a' ).text( newval );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_mailtpl_opts[header_aligment]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '#logo' ).css( 'text-align', newval );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_mailtpl_opts[header_bg]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '#template_header' ).css( 'background-color', newval );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_mailtpl_opts[header_text_size]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '#logo' ).css( 'font-size', newval + 'px' );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_mailtpl_opts[header_text_color]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '#logo_a' ).css( 'color', newval );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_mailtpl_opts[email_body_bg]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '#bp_mailtpl_body_bg' ).css( 'background-color', newval );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_mailtpl_opts[body_text_size]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '#bp_mailtpl_body' ).css( 'font-size', newval + 'px' );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_mailtpl_opts[body_text_color]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '#bp_mailtpl_body' ).css( 'color', newval );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_mailtpl_opts[footer_aligment]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '#credit' ).css( 'text-align', newval );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_mailtpl_opts[footer_bg]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '#template_footer' ).css( 'background-color', newval );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_mailtpl_opts[footer_text_size]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '#credit' ).css( 'font-size', newval + 'px' );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_mailtpl_opts[footer_text_color]', function( value ) {
		value.bind(function( newval ) {
			if ( newval.length ) {
				$( '#credit' ).css( 'color', newval );
			}
		});
	});

	// djpaultodo
	wp.customize( 'bp_mailtpl_opts[footer_text]', function( value ) {
		value.bind(function( newval ) {
			$( '.footer_text' ).text( newval );
		});
	});
})( jQuery );