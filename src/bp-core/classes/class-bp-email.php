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
	 * The Post object containing the email content and subject.
	 *
	 * @since 2.5.0
	 *
	 * @var WP_Post
	 */
	protected $post_object = null;

	/**
	 * Unique identifier for this particular type of email.
	 *
	 * @since 2.5.0
	 *
	 * @var string
	 */
	protected $type = '';

	/**
	 * Sender details.
	 *
	 * @since 2.5.0
	 *
	 * @var array Associative pairing of "from" name (key) and email addresses (value).
	 */
	protected $from = array();

	/**
	 * Reply To details.
	 *
	 * @since 2.5.0
	 *
	 * @var array Associative pairing of  "reply to" name (key) and email addresses (value).
	 */
	protected $reply_to = array();

	/**
	 * Addressee details (to).
	 *
	 * @since 2.5.0
	 *
	 * @var array Associative pairing of "to" name (key) and email addresses (value).
	 * }
	 */
	protected $to = array();

	/**
	 * Addressee details (CC).
	 *
	 * @since 2.5.0
	 *
	 * @var array Associative pairing of "cc" name (key) and email addresses (value).
	 */
	protected $cc = array();

	/**
	 * Addressee details (BCC).
	 *
	 * @since 2.5.0
	 *
	 * @var array Associative pairing of "bcc" name (key) and email addresses (value).
	 */
	protected $bcc = array();

	/**
	 * Email subject.
	 *
	 * @since 2.5.0
	 *
	 * @var string
	 */
	protected $subject = '';

	/**
	 * Email content.
	 *
	 * @since 2.5.0
	 *
	 * @var string
	 */
	protected $content = '';

	/**
	 * Token names and replacement values for this email.
	 *
	 * @since 2.5.0
	 *
	 * @var array Associative pairing of token name (key) and replacement value (value).
	 */
	protected $tokens = array();

	/**
	 * Email headers.
	 *
	 * @since 2.5.0
	 *
	 * @var array Associative pairing of email header name/value.
	 */
	protected $headers = array();

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


	/**
	 * Psuedo setters/getters.
	 */

	/**
	 * Set the email's "from" address and name.
	 *
	 * @since 2.5.0
	 *
	 * @param string $email_address
	 * @param string $name Optional "from" name.
	 * @return BP_Email
	 */
	public function from( $email_address, $name = '' ) {
		$from = array();

		if ( is_email( $email_address ) ) {
			$from = array( sanitize_email( $email_address ) => $name );
		}

		$this->from = apply_filters( 'bp_email_set_from', $from, $email_address, $name, $this );

		return $this;
	}

	/**
	 * Set the email's "reply to" address and name.
	 *
	 * @since 2.5.0
	 *
	 * @param string $email_address
	 * @param string $name Optional "reply to" name.
	 * @return BP_Email
	 */
	public function reply_to( $email_address, $name = '' ) {
		$reply_to = array();

		if ( is_email( $email_address ) ) {
			$reply_to = array( sanitize_email( $email_address ) => $name );
		}

		$this->reply_to = apply_filters( 'bp_email_set_reply_to', $reply_to, $email_address, $name, $this );

		return $this;
	}

	/**
	 * Set the email's "to" address.
	 *
	 * To set a single address, the first parameter is the address and the second the name.
	 * To set multiple addresses, for each array item, the key is the email address and
	 * the value is the name.
	 *
	 * @since 2.5.0
	 *
	 * @param string|string[] $to_address If array, key is email address, value is the name.
	 *     If string, this is the email address.
	 * @param string $name Optional. If $to_address is not an array, this is the "from" name.
	 *     Otherwise, the parameter is not used.
	 * @return BP_Email
	 */
	public function to( $to_address, $name = '' ) {
		$to       = $this->parse_and_sanitize_addresses( $to_address, $name );
		$this->to = apply_filters( 'bp_email_set_to', $to, $to_address, $name, $this );

		return $this;
	}

	/**
	 * Set the email's "cc" address.
	 *
	 * To set a single address, the first parameter is the address and the second the name.
	 * To set multiple addresses, for each array item, the key is the email address and
	 * the value is the name.
	 *
	 * @since 2.5.0
	 *
	 * @param string|string[] $cc_address If array, key is email address, value is the name.
	 *     If string, this is the email address.
	 * @param string $name Optional. If $cc_address is not an array, this is the "from" name.
	 *     Otherwise, the parameter is not used.
	 * @return BP_Email
	 */
	public function cc( $cc_address, $name = '' ) {
		$cc       = $this->parse_and_sanitize_addresses( $cc_address, $name );
		$this->cc = apply_filters( 'bp_email_set_cc', $cc, $cc_address, $name, $this );

		return $this;
	}

	/**
	 * Set the email's "bcc" address.
	 *
	 * To set a single address, the first parameter is the address and the second the name.
	 * To set multiple addresses, for each array item, the key is the email address and
	 * the value is the name.
	 *
	 * @since 2.5.0
	 *
	 * @param string|string[] $bcc_address If array, key is email address, value is the name.
	 *     If string, this is the email address.
	 * @param string $name Optional. If $bcc_address is not an array, this is the "from" name.
	 *     Otherwise, the parameter is not used.
	 * @return BP_Email
	 */
	public function bcc( $bcc_address, $name = '' ) {
		$bcc       = $this->parse_and_sanitize_addresses( $bcc_address, $name );
		$this->bcc = apply_filters( 'bp_email_set_bcc', $bcc, $bcc_address, $name, $this );

		return $this;
	}

	/**
	 * Set the email subject.
	 *
	 * @since 2.5.0
	 *
	 * @param string $subject
	 * @return BP_Email
	 */
	public function subject( $subject ) {
		$subject       = sanitize_text_field( $subject );
		$this->subject = apply_filters( 'bp_email_set_subject', $subject, $this );

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
		$this->content = apply_filters( 'bp_email_set_content', $content, $this );
		return $this;
	}

	/**
	 * Set the Post object containing the email content template.
	 *
	 * Also sets the email's subject and content from the Post, for convenience.
	 *
	 * @since 2.5.0
	 *
	 * @param WP_Post $post
	 * @return BP_Email
	 */
	public function post_object( WP_Post $post ) {
		$this->post_object = apply_filters( 'bp_email_set_post_object', $post, $this );

		$this->subject( $this->get( 'post_object' )->post_title );
		$this->content( $this->get( 'post_object' )->post_content );

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
	 * @param array $tokens Key/value pairs of token name/value. Values are a string or a callable function.
	 * @return BP_Email
	 */
	public function tokens( array $tokens ) {
		$formatted_tokens = array();

		foreach ( $tokens as $name => $value ) {
			// Wrap token name in {{brackets}}.
			$name                      = '{{' . str_replace( array( '{', '}' ), '', $name ) . '}}';
			$formatted_tokens[ $name ] = $value;
		}

		$this->tokens = apply_filters( 'bp_email_set_tokens', $formatted_tokens, $tokens, $this );

		return $this;
	}

	/**
	 * Set email headers.
	 *
	 * Does NOT let you override to/from, etc. Use the methods provided to set those.
	 *
	 * @since 2.5.0
	 *
	 * @param array $headers Key/value pairs of heade name/values (strings).
	 * @return BP_Email
	 */
	public function headers( array $headers ) {
		$new_headers = array();

		foreach ( $headers as $name => $content ) {
			$content = str_replace( ':', '', $content );
			$name    = str_replace( ':', '', $name );

			$new_headers[ $name ] = $content;
		}

		$this->headers = apply_filters( 'bp_email_set_headers', $new_headers, $headers, $this );

		return $this;
	}

	/**
	 * Getter function to expose object properties.
	 *
	 * Unlike most other methods in this class, this one is not chainable.
	 *
	 * @since 2.5.0
	 * @param string $property Name of property to access.
	 * @param string $tranform Optional. How to transform the return value.
	 *                         Accepts 'raw' (default) or 'replace-tokens'.
	 * @return mixed Returns null if property does not exist, otherwise the value.
	 */
	public function get( $property, $transform = 'raw' ) {
		if ( ! property_exists( $this, $property ) ) {
			return null;
		}

		$retval = apply_filters( "bp_email_get_{$property}", $this->$property, $property, $transform, $this );

		// Replace tokens.
		if ( $transform === 'replace-tokens' ) {
			$retval = self::replace_tokens( $retval, $this->get( 'tokens', 'raw' ) ) {
		}

		return apply_filters( 'bp_email_get_property', $retval, $property, $transform, $this );
	}


	/**
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
		if ( ! $this->get( 'from' ) || ! $this->get( 'to' ) || ! $this->get( 'subject' ) || ! $this->get( 'content' ) ) {
			$retval = new WP_Error( 'missing_parameter', __CLASS__, $this );
		}

		return apply_filters( 'bp_email_validate', $retval, $this );
	}


	/**
	 * Utility functions.
	 *
	 * Unlike other methods in this class, utility functions are not chainable.
	 */

	/**
	 * Parse and sanitize email addresses.
	 *
	 * Unlike most other methods in this class, this one is not chainable.
	 *
	 * @since 2.5.0
	 *
	 * @param string|string[] $raw_address If array, key is email address, value is the name.
	 *     If string, this is the email address.
	 * @param string $name Optional. If $raw_address is not an array, this is the "from" name.
	 *     Otherwise, the parameter is not used.
	 * @return array
	 */
	protected function parse_and_sanitize_addresses( $raw_address, $name = '' ) {
		if ( ! is_array( $raw_address ) ) {
			$raw_address = array( $raw_address => $name );
		}

		$addresses = array();

		foreach ( $raw_address as $email => $recipient ) {
			if ( is_email( $email ) ) {
				$addresses[ sanitize_email( $email ) ] = $recipient;
			}
		}

		return $addresses;
	}

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
		return apply_filters( 'bp_email_replace_tokens', $text, $this );
	}
}
