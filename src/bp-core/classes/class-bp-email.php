<?php
/**
 * Core component classes.
 *
 * @package BuddyPress
 * @subpackage Core
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Represents an email that will be sent to member(s).
 *
 * @since 2.5.0
 */
class BP_Email {
	/**
	 * Addressee details (BCC).
	 *
	 * @since 2.5.0
	 *
	 * @var BP_Email_Recipient[] BCC recipients.
	 */
	protected $bcc = array();

	/**
	 * Addressee details (CC).
	 *
	 * @since 2.5.0
	 *
	 * @var BP_Email_Recipient[] CC recipients.
	 */
	protected $cc = array();

	/**
	 * Email content.
	 *
	 * @since 2.5.0
	 *
	 * @var string
	 */
	protected $content = '';

	/**
	 * Sender details.
	 *
	 * @since 2.5.0
	 *
	 * @var BP_Email_Recipient Sender details.
	 */
	protected $from = null;

	/**
	 * Email headers.
	 *
	 * @since 2.5.0
	 *
	 * @var string[] Associative pairing of email header name/value.
	 */
	protected $headers = array();

	/**
	 * The Post object (the source of the email's content and subject).
	 *
	 * @since 2.5.0
	 *
	 * @var WP_Post
	 */
	protected $post_object = null;

	/**
	 * Reply To details.
	 *
	 * @since 2.5.0
	 *
	 * @var BP_Email_Recipient "Reply to" details.
	 */
	protected $reply_to = null;

	/**
	 * Email subject.
	 *
	 * @since 2.5.0
	 *
	 * @var string
	 */
	protected $subject = '';

	/**
	 * Email template (the HTML wrapper around the email content).
	 *
	 * @since 2.5.0
	 *
	 * @var string
	 */
	protected $template = '{{content}}';

	/**
	 * Addressee details (to).
	 *
	 * @since 2.5.0
	 *
	 * @var BP_Email_Recipient[] Email recipients.
	 * }
	 */
	protected $to = array();

	/**
	 * Unique identifier for this particular type of email.
	 *
	 * @since 2.5.0
	 *
	 * @var string
	 */
	protected $type = '';

	/**
	 * Token names and replacement values for this email.
	 *
	 * @since 2.5.0
	 *
	 * @var string[] Associative pairing of token name (key) and replacement value (value).
	 */
	protected $tokens = array();

	/**
	 * Constructor.
	 *
	 * Set the email type and default "from" and "reply to" name and address.
	 *
	 * @since 2.5.0
	 *
	 * @param string $email_type Unique identifier for a particular type of email.
	 */
	public function __construct( $email_type ) {
		$this->type = $email_type;

		// SERVER_NAME isn't always set (e.g CLI).
		if ( ! empty( $_SERVER['SERVER_NAME'] ) ) {
			$sitename = strtolower( $_SERVER['SERVER_NAME'] );
			if ( substr( $sitename, 0, 4 ) === 'www.' ) {
				$sitename = substr( $sitename, 4 );
			}

		} elseif ( function_exists( 'gethostname' ) && gethostname() !== false ) {
			$sitename = gethostname();

		} elseif ( php_uname( 'n' ) !== false ) {
			$sitename = php_uname( 'n' );

		} else {
			$sitename = 'localhost.localdomain';
		}


		$this->from( "wordpress@$sitename", get_bloginfo( 'name' ) );
		$this->reply_to( bp_get_option( 'admin_email' ), bp_get_option( 'blogname' ) );

		/**
		 * Fires inside __construct() method for BP_Email class.
		 *
		 * @since 2.5.0
		 *
		 * @param string $email_type Unique identifier for this type of email.
		 * @param BP_Email $this Current instance of the email type class.
		 */
		do_action( 'bp_email', $email_type, $this );
	}


	/*
	 * Psuedo setters/getters.
	 */

	/**
	 * Getter function to expose object properties.
	 *
	 * Unlike most other methods in this class, this one is not chainable.
	 *
	 * @since 2.5.0
	 *
	 * @param string $property_name Property to access.
	 * @param string $tranform Optional. How to transform the return value.
	 *                         Accepts 'raw' (default) or 'replace-tokens'.
	 * @return mixed Returns null if property does not exist, otherwise the value.
	 */
	public function get( $property_name, $transform = 'raw' ) {
		if ( ! property_exists( $this, $property_name ) ) {
			return null;
		}

/*		if ( is_a( $this->$property_name, 'BP_Email_Recipient', true ) ) {
			$retval = $this->$property_name->
		}*/

		/**
		 * Filters the value of the specified email property.
		 *
		 * This is a dynamic filter dependent on the specified key.
		 *
		 * @since 2.5.0
		 *
		 * @param mixed $property_value Property value.
		 * @param string $property_name
		 * @param string $transform How to transform the return value.
		 *                          Accepts 'raw' (default) or 'replace-tokens'.
		 * @param BP_Email $this Current instance of the email type class.
		 */
		$retval = apply_filters( "bp_email_get_{$property_name}", $this->$property_name, $property_name, $transform, $this );

		switch ( $transform ) {
			// Special-case to fill the $template with the email $content.
			case 'add-content':
				$retval = str_replace( '{{content}}', $this->get( 'content', 'replace-tokens' ), $retval );
				// Fall through.

			case 'replace-tokens':
				$retval = self::replace_tokens( $retval, $this->get( 'tokens', 'raw' ) );
				// Fall through.

			case 'raw':
			default:
				// Do nothing.
		}

		/**
		 * Filters the value of the specified email $property.
		 *
		 * @since 2.5.0
		 *
		 * @param string $retval Property value.
		 * @param string $property_name
		 * @param string $transform How to transform the return value.
		 *                          Accepts 'raw' (default) or 'replace-tokens'.
		 * @param BP_Email $this Current instance of the email type class.
		 */
		return apply_filters( 'bp_email_get_property', $retval, $property_name, $transform, $this );
	}

	/**
	 * Set email headers.
	 *
	 * Does NOT let you override to/from, etc. Use the methods provided to set those.
	 *
	 * @since 2.5.0
	 *
	 * @param string[] $headers Key/value pairs of header name/values (strings).
	 * @return BP_Email
	 */
	public function headers( array $headers ) {
		$new_headers = array();

		foreach ( $headers as $name => $content ) {
			$content = str_replace( ':', '', $content );
			$name    = str_replace( ':', '', $name );

			$new_headers[ $name ] = $content;
		}

		/**
		 * Filters the new value of the email's "headers" property.
		 *
		 * @since 2.5.0
		 *
		 * @param string[] $new_headers Key/value pairs of new header name/values (strings).
		 * @param BP_Email $this Current instance of the email type class.
		 */
		$this->headers = apply_filters( 'bp_email_set_headers', $new_headers, $this );

		return $this;
	}

	/**
	 * Set the email's "bcc" address.
	 *
	 * To set a single address, the first parameter is the address and the second the name.
	 * You can also pass a user ID or a WP_User object.
	 *
	 * To set multiple addresses, for each array item, the key is the email address and
	 * the value is the name.
	 *
	 * @since 2.5.0
	 *
	 * @param string|array|int|WP_User $bcc_address Either a email address, user ID, WP_User object,
	 *                                              or an array containg the address and name.
	 * @param string $name Optional. If $bcc_address is a string, this is the recipient's name.
	 * @return BP_Email
	 */
	public function bcc( $bcc_address, $name = '' ) {
		$bcc = array( new BP_Email_Recipient( $bcc_address, $name ) );

		/**
		 * Filters the new value of the email's "BCC" property.
		 *
		 * @since 2.5.0
		 *
		 * @param BP_Email_Recipient[] $bcc BCC recipients.
		 * @param string|array|int|WP_User $bcc_address Either a email address, user ID, WP_User object,
		 *                                              or an array containg the address and name.
		 * @param string $name Optional. If $bcc_address is a string, this is the recipient's name.
		 * @param BP_Email $this Current instance of the email type class.
		 */
		$this->bcc = apply_filters( 'bp_email_set_bcc', $bcc, $bcc_address, $name, $this );

		return $this;
	}

	/**
	 * Set the email's "cc" address.
	 *
	 * To set a single address, the first parameter is the address and the second the name.
	 * You can also pass a user ID or a WP_User object.
	 *
	 * To set multiple addresses, for each array item, the key is the email address and
	 * the value is the name.
	 *
	 * @since 2.5.0
	 *
	 * @param string|array|int|WP_User $cc_address Either a email address, user ID, WP_User object,
	 *                                             or an array containg the address and name.
	 * @param string $name Optional. If $cc_address is a string, this is the recipient's name.
	 * @return BP_Email
	 */
	public function cc( $cc_address, $name = '' ) {
		$cc = array( new BP_Email_Recipient( $cc_address, $name ) );

		/**
		 * Filters the new value of the email's "CC" property.
		 *
		 * @since 2.5.0
		 *
		 * @param BP_Email_Recipient[] $cc CC recipients.
		 * @param string|array|int|WP_User $cc_address Either a email address, user ID, WP_User object,
		 *                                             or an array containg the address and name.
		 * @param string $name Optional. If $cc_address is a string, this is the recipient's name.
		 * @param BP_Email $this Current instance of the email type class.
		 */
		$this->cc = apply_filters( 'bp_email_set_cc', $cc, $cc_address, $name, $this );

		return $this;
	}

	/**
	 * Set the email content.
	 *
	 * @since 2.5.0
	 *
	 * @param string $content Email content. Assumed to be HTML.
	 * @return BP_Email
	 */
	public function content( $content ) {
		// djpaultodo kses this?

		/**
		 * Filters the new value of the email's "content" property.
		 *
		 * @since 2.5.0
		 *
		 * @param string $content Email content. Assumed to be HTML.
		 * @param BP_Email $this Current instance of the email type class.
		 */
		$this->content = apply_filters( 'bp_email_set_content', $content, $this );

		return $this;
	}

	/**
	 * Set the email's "from" address and name.
	 *
	 * @since 2.5.0
	 *
	 * @param string|array|int|WP_User $email_address Either a email address, user ID, WP_User object,
	 *                                                or an array containg the address and name.
	 * @param string $name Optional. If $email_address is a string, this is the recipient's name.
	 * @return BP_Email
	 */
	public function from( $email_address, $name = '' ) {
		$from = new BP_Email_Recipient( $email_address, $name );

		/**
		 * Filters the new value of the email's "from" property.
		 *
		 * @since 2.5.0
		 *
		 * @param BP_Email_Recipient $from Sender details.
		 * @param string|array|int|WP_User $email_address Either a email address, user ID, WP_User object,
		 *                                                or an array containg the address and name.
		 * @param string $name Optional. If $email_address is a string, this is the recipient's name.
		 * @param BP_Email $this Current instance of the email type class.
		 */
		$this->from = apply_filters( 'bp_email_set_from', $from, $email_address, $name, $this );

		return $this;
	}

	/**
	 * Set the Post object containing the email content template.
	 *
	 * Also sets the email's subject, content, and template from the Post, for convenience.
	 *
	 * @since 2.5.0
	 *
	 * @param WP_Post $post
	 * @return BP_Email
	 */
	public function post_object( WP_Post $post ) {
		/**
		 * Filters the new value of the email's "post object" property.
		 *
		 * @since 2.5.0
		 *
		 * @param WP_Post $post A Post.
		 * @param BP_Email $this Current instance of the email type class.
		 */
		$this->post_object = apply_filters( 'bp_email_set_post_object', $post, $this );

		if ( is_a( $this->post_object, 'WP_Post' ) ) {
			$this->subject( $this->post_object->post_title );
			$this->content( $this->post_object->post_content );

			ob_start();

			// Load the template.
			bp_locate_template( bp_email_get_template( $this->post_object ), true, false );
 			$this->template( ob_get_contents() );

  		ob_end_clean();
		}

		return $this;
	}

	/**
	 * Set the email's "reply to" address and name.
	 *
	 * @since 2.5.0
	 *
	 * @param string|array|int|WP_User $email_address Either a email address, user ID, WP_User object,
	 *                                                or an array containg the address and name.
	 * @param string $name Optional. If $email_address is a string, this is the recipient's name.
	 * @return BP_Email
	 */
	public function reply_to( $email_address, $name = '' ) {
		$reply_to = new BP_Email_Recipient( $email_address, $name );

		/**
		 * Filters the new value of the email's "reply to" property.
		 *
		 * @since 2.5.0
		 *
		 * @param BP_Email_Recipient $reply_to "Reply to" recipient.
		 * @param string|array|int|WP_User $email_address Either a email address, user ID, WP_User object,
		 *                                                or an array containg the address and name.
		 * @param string $name Optional. If $email_address is a string, this is the recipient's name.
		 * @param BP_Email $this Current instance of the email type class.
		 */
		$this->reply_to = apply_filters( 'bp_email_set_reply_to', $reply_to, $email_address, $name, $this );

		return $this;
	}

	/**
	 * Set the email subject.
	 *
	 * @since 2.5.0
	 *
	 * @param string $subject Email subject.
	 * @return BP_Email
	 */
	public function subject( $subject ) {
		$subject = sanitize_text_field( $subject );

		/**
		 * Filters the new value of the subject email property.
		 *
		 * @since 2.5.0
		 *
		 * @param string $subject Email subject.
		 * @param BP_Email $this Current instance of the email type class.
		 */
		$this->subject = apply_filters( 'bp_email_set_subject', $subject, $this );

		return $this;
	}

	/**
	 * Set the email template (the HTML wrapper around the email content).
	 *
	 * @since 2.5.0
	 *
	 * @param string $template Email template. Assumed to be HTML.
	 * @return BP_Email
	 */
	public function template( $template ) {
		// djpaultodo kses this?

		/**
		 * Filters the new value of the template email property.
		 *
		 * @since 2.5.0
		 *
		 * @param string $template Email template. Assumed to be HTML.
		 * @param BP_Email $this Current instance of the email type class.
		 */
		$this->template = apply_filters( 'bp_email_set_template', $template, $this );

		return $this;
	}

	/**
	 * Set the email's "to" address.
	 *
	 * To set a single address, the first parameter is the address and the second the name.
	 * You can also pass a user ID or a WP_User object.
	 *
	 * To set multiple addresses, for each array item, the key is the email address and
	 * the value is the name.
	 *
	 * @since 2.5.0
	 *
	 * @param string|array|int|WP_User $to_address Either a email address, user ID, WP_User object,
	 *                                             or an array containg the address and name.
	 * @param string $name Optional. If $to_address is a string, this is the recipient's name.
	 * @return BP_Email
	 */
	public function to( $to_address, $name = '' ) {
		$to = new BP_Email_Recipient( $to_address, $name );

		/**
		 * Filters the new value of the email's "to" property.
		 *
		 * @since 2.5.0
		 *
		 * @param BP_Email_Recipient[] "To" recipients.
		 * @param string $to_address "To" address.
		 * @param string $name "To" name.
		 * @param BP_Email $this Current instance of the email type class.
		 */
		$this->to = apply_filters( 'bp_email_set_to', $to, $to_address, $name, $this );

		return $this;
	}

	/**
	 * Set token names and replacement values for this email.
	 *
	 * In templates, tokens are inserted with a Handlebars-like syntax, e.g. `{{token_name}}`.
	 * { and } are reserved characters. There's no need to specify these brackets in your token names.
	 *
	 * @since 2.5.0
	 *
	 * @param string[] $tokens Associative array, contains key/value pairs of token name/value.
	 *                         Values are a string or a callable function.
	 * @return BP_Email
	 */
	public function tokens( array $tokens ) {
		$formatted_tokens = array();

		foreach ( $tokens as $name => $value ) {
			// Wrap token name in {{brackets}}.
			$name                      = '{{' . str_replace( array( '{', '}' ), '', $name ) . '}}';
			$formatted_tokens[ $name ] = $value;
		}

		/**
		 * Filters the new value of the email's "tokens" property.
		 *
		 * @since 2.5.0
		 *
		 * @param string[] $formatted_tokens Associative pairing of token names (key) and replacement values (value).
		 * @param string[] $tokens Associative pairing of unformatted token names (key) and replacement values (value).
		 * @param BP_Email $this Current instance of the email type class.
		 */
		$this->tokens = apply_filters( 'bp_email_set_tokens', $formatted_tokens, $tokens, $this );

		return $this;
	}


	/*
	 * Sanitisation and validation logic.
	 */

	/**
	 * Check that we'd be able to send this email.
	 *
	 * Unlike most other methods in this class, this one is not chainable.
	 *
	 * @since 2.5.0
	 *
	 * @return bool|WP_Error Returns true if validation succesful, else a descriptive WP_Error.
	 */
	public function validate() {
		$retval = true;

		// BCC, CC, and token properties are optional.
		if (
			! $this->get( 'from' ) ||
			! $this->get( 'to' ) ||
			! $this->get( 'subject' ) ||
			! $this->get( 'content' ) ||
			! $this->get( 'template' )
		) {
			$retval = new WP_Error( 'missing_parameter', __CLASS__, $this );
		}

		/**
		 * Filters whether the email passes basic validation checks.
		 *
		 * @since 2.5.0
		 *
		 * @param bool|WP_Error $retval Returns true if validation succesful, else a descriptive WP_Error.
		 * @param BP_Email $this Current instance of the email type class.
		 */
		return apply_filters( 'bp_email_validate', $retval, $this );
	}


	/*
	 * Utility functions.
	 *
	 * Unlike other methods in this class, utility functions are not chainable.
	 */

	/**
	 * Replace all tokens in the input with appropriate values.
	 *
	 * Unlike most other methods in this class, this one is not chainable.
	 *
	 * @since 2.5.0
	 *
	 * @param string $text
	 * @param array $tokens Token names and replacement values for the $text.
	 * @return string
	 */
	public static function replace_tokens( $text, $tokens ) {
		foreach ( $tokens as $token => &$replacement ) {
			if ( is_callable( $replacement ) ) {
				$replacement = call_user_func( $replacement );
			}
		}

		$text = strtr( $text, $tokens );

		/**
		 * Filters text that has had tokens replaced.
		 *
		 * @since 2.5.0
		 *
		 * @param string $text
		 * @param array $tokens Token names and replacement values for the $text.
		 */
		return apply_filters( 'bp_email_replace_tokens', $text, $tokens );
	}
}
