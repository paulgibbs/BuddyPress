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
 * @since 2.4.0
 */
class BP_Email {
	/**
	 * The WordPress Post object containing the email text and customisations.
	 *
	 * @since 2.4.0
	 *
	 * @var WP_Post
	 */
	protected $post_obj = null;

	/**
	 * Send from this address.
	 *
	 * @since 2.4.0
	 *
	 * @var string
	 */
	protected $from = '';

	/**
	 * Send to this address.
	 *
	 * @since 2.4.0
	 *
	 * @var string[]
	 */
	protected $to = array();

	/**
	 * CC to this address.
	 *
	 * @since 2.4.0
	 *
	 * @var string[]
	 */
	protected $cc = array();

	/**
	 * BCC to this address.
	 *
	 * @since 2.4.0
	 *
	 * @var string[]
	 */
	protected $bcc = array();


	/**
	 * Constructor
	 *
	 * @since 2.4.0
	 */
	public function __construct() {

		/**
		 * Fires inside __construct() method for BP_Email class.
		 *
		 * @since 2.4.0
		 *
		 * @param BP_Email $this Current instance of the email type class.
		 */
		do_action( 'bp_email', $this );
	}


	/**
	 * Psuedo setters/getters.
	 */

	/**
	 * Set the email's "from" address.
	 *
	 * @since 2.4.0
	 *
	 * @param string $email_address
	 * @return BP_Email
	 */
	public function from( $email_address ) {
		$email_address = sanitize_email( $email_address );

		if ( is_email( $email_address ) ) {
			$this->email = apply_filters( 'bp_email_set_from', $email_address, $this );
		}

		return $this;
	}

	/**
	 * Set the email's "to" address.
	 *
	 * @since 2.4.0
	 *
	 * @param array|string $email_addresses
	 * @return BP_Email
	 */
	public function to( $email_addresses ) {
		if ( ! is_array( $email_addresses ) ) {
			$email_addresses = (array) $email_addresses;
		}

		$email_addresses = array_unique( array_map( 'sanitize_email', $email_addresses ) );
		$email_addresses = array_filter( $email_addresses, 'is_email' );

		if ( $email_addresses ) {
			$this->email = apply_filters( 'bp_email_set_to', $email_addresses, $this );
		}

		return $this;
	}

	/**
	 * Set the email's "cc" address.
	 *
	 * @since 2.4.0
	 *
	 * @param array|string $email_addresses
	 * @return BP_Email
	 */
	public function cc( $email_addresses ) {
		if ( ! is_array( $email_addresses ) ) {
			$email_addresses = (array) $email_addresses;
		}

		$email_addresses = array_unique( array_map( 'sanitize_email', $email_addresses ) );
		$email_addresses = array_filter( $email_addresses, 'is_email' );

		if ( $email_addresses ) {
			$this->email = apply_filters( 'bp_email_set_cc', $email_addresses, $this );
		}

		return $this;
	}

	/**
	 * Set the email's "bcc" address.
	 *
	 * @since 2.4.0
	 *
	 * @param array|string $email_addresses
	 * @return BP_Email
	 */
	public function bcc( $email_addresses ) {
		if ( ! is_array( $email_addresses ) ) {
			$email_addresses = (array) $email_addresses;
		}

		$email_addresses = array_unique( array_map( 'sanitize_email', $email_addresses ) );
		$email_addresses = array_filter( $email_addresses, 'is_email' );

		if ( $email_addresses ) {
			$this->email = apply_filters( 'bp_email_set_bcc', $email_addresses, $this );
		}

		return $this;
	}

	/**
	 * Getter function to expose object properties.
	 *
	 * Unlike most other methods in this class, this one is not chainable.
	 *
	 * @since 2.4.0
	 * @param string $property Name of property to accss.
	 * @return mixed Returns null if property does not exist, otherwise the value.
	 */
	public function get( $property ) {
		if ( ! property_exists( $this, $property ) ) {
			return null;
		}

		$retval = apply_filters( "bp_email_get_{$property}", $this->$property, $this );
		return apply_filters( 'bp_email_get_property', $retval, $property, $this );
	}


	/**
	 * Sanitisation and validation logic.
	 */

	/**
	 * Something.
	 *
	 * @since 2.4.0
	 *
	 * @return bool|WP_Error Returns true if validation succesful, else a descriptive WP_Error.
	 */
	public function validate() {
	}
}


$email = bp_get_email( 'new_user' );
// subject + body set via WP_Post, but methods to override.
$email->to( 'example@djpaul.com' );
$email->bcc( 'your@mom.com' );
$email->tokens( $some_kv_array );
// $email->validate()


$email_provider->send( $email->validate()->get_text(), 'html/plaintext' )
->get_subject()

etc



	/**
	 * Set a regex that profile data will be asserted against.
	 *
	 * You can call this method multiple times to set multiple formats. When validation is performed,
	 * it's successful as long as the new value matches any one of the registered formats.
	 *
	 * @param string $format Regex string
	 * @param string $replace_format Optional; if 'replace', replaces the format instead of adding to it. Defaults to 'add'.
	 * @return BP_XProfile_Field_Type
	 * @since BuddyPress (2.0.0)
	 */
	public function set_format( $format, $replace_format = 'add' ) {

		/**
		 * Filters the regex format for the field type.
		 *
		 * @since BuddyPress (2.0.0)
		 *
		 * @param string                 $format         Regex string.
		 * @param string                 $replace_format Optional replace format If "replace", replaces the
		 *                                               format instead of adding to it. Defaults to "add".
		 * @param BP_XProfile_Field_Type $this           Current instance of the BP_XProfile_Field_Type class.
		 */
		$format = apply_filters( 'bp_xprofile_field_type_set_format', $format, $replace_format, $this );

		if ( 'add' === $replace_format ) {
			$this->validation_regex[] = $format;
		} elseif ( 'replace' === $replace_format ) {
			$this->validation_regex = array( $format );
		}

		return $this;
	}

	/**
	 * Check the given string against the registered formats for this field type.
	 *
	 * This method doesn't support chaining.
	 *
	 * @param string|array $values Value to check against the registered formats
	 * @return bool True if the value validates
	 * @since BuddyPress (2.0.0)
	 */
	public function is_valid( $values ) {
		$validated = false;

		// Some types of field (e.g. multi-selectbox) may have multiple values to check
		foreach ( (array) $values as $value ) {

			// Validate the $value against the type's accepted format(s).
			foreach ( $this->validation_regex as $format ) {
				if ( 1 === preg_match( $format, $value ) ) {
					$validated = true;
					continue;

				} else {
					$validated = false;
				}
			}
		}

		// Handle field types with accepts_null_value set if $values is an empty array
		if ( ( false === $validated ) && is_array( $values ) && empty( $values ) && $this->accepts_null_value ) {
			$validated = true;
		}

		// If there's a whitelist set, also check the $value.
		if ( ( true === $validated ) && ! empty( $values ) && ! empty( $this->validation_whitelist ) ) {
			foreach ( (array) $values as $value ) {
				$validated = in_array( $value, $this->validation_whitelist, true );
			}
		}

		/**
		 * Filters whether or not field type is a valid format.
		 *
		 * @since BuddyPress (2.0.0)
		 *
		 * @param bool                   $validated Whether or not the field type is valid.
		 * @param string|array           $values    Value to check against the registered formats.
		 * @param BP_XProfile_Field_Type $this      Current instance of the BP_XProfile_Field_Type class.
		 */
		return (bool) apply_filters( 'bp_xprofile_field_type_is_valid', $validated, $values, $this );
	}

	/**
	 * Allow field types to modify submitted values before they are validated.
	 *
	 * In some cases, it may be appropriate for a field type to catch
	 * submitted values and modify them before they are passed to the
	 * is_valid() method. For example, URL validation requires the
	 * 'http://' scheme (so that the value saved in the database is always
	 * a fully-formed URL), but in order to allow users to enter a URL
	 * without this scheme, BP_XProfile_Field_Type_URL prepends 'http://'
	 * when it's not present.
	 *
	 * By default, this is a pass-through method that does nothing. Only
	 * override in your own field type if you need this kind of pre-
	 * validation filtering.
	 *
	 * @since BuddyPress (2.1.0)
	 *
	 * @param mixed $submitted_value Submitted value.
	 * @return mixed
	 */
	public static function pre_validate_filter( $field_value ) {
		return $field_value;
	}
