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
 * Represents a recipient that an email will be sent to.
 *
 * @since 2.5.0
 */
class BP_Email_Recipient {

	/**
	 * Recipient's email address.
	 *
	 * @since 2.5.0
	 *
	 * @var string
	 */
	protected $address = '';

	/**
	 * Recipient's name.
	 *
	 * @since 2.5.0
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Optional. A `WP_User` object relating to this recipient.
	 *
	 * @since 2.5.0
	 *
	 * @var WP_User
	 */
	protected $user_object = null;

	/**
	 * Constructor.
	 *
	 * @since 2.5.0
	 *
	 * @param string|array|int|WP_User $email_or_user Either a email address, user ID, WP_User object,
	 *                                                or an array containg the address and name.
	 * @param string $name Optional. If $email_or_user is a string, this is the recipient's name.
	 */
	public function __construct( $email_or_user, $name = '' ) {
		// User ID, WP_User object.
		if ( ctype_digit( $email_or_user ) || is_object( $email_or_user ) ) {
			$this->user_object = is_object( $email_or_user ) ? $email_or_user : get_user_by( 'ID', $email_or_user );

			if ( $this->user_object ) {
				$this->address = $this->user_object->user_email;
				$this->name    = bp_core_get_user_displayname( $this->user_object->ID );
			}

		// Array, address and name.
		} else {
			if ( is_array( $email_or_user ) ) {
				$address = key( $email_or_user );
				$name    = current( $email_or_user );

			} else {
				$address = $email_or_user;
				$name    = $name;
			}

			if ( is_email( $address ) ) {
				$address = sanitize_email( $address );
			}

			$this->address = $address;
			$this->name    = $name;
		}

		/**
		 * Fires inside __construct() method for BP_Email_Recipient class.
		 *
		 * @since 2.5.0
		 *
		 * @param string|array|int|WP_User $email_or_user Either a email address, user ID, WP_User object,
		 *                                                or an array containg the address and name.
		 * @param string $name If $email_or_user is a string, this is the recipient's name.
		 * @param BP_Email_Recipient $this Current instance of the email type class.
		 */
		do_action( 'bp_email_recipient', $email_or_user, $name, $this );
	}

	/**
	 * Get recipient's address.
	 *
	 * @since 2.5.0
	 *
	 * @return string
	 */
	public function get_address() {

		/**
		 * Filters the recipient's address before it's returned.
		 *
		 * @since 2.5.0
		 *
		 * @param string $address Recipient's address.
		 * @param BP_Email $recipient $this Current instance of the email recipient class.
		 */
		return apply_filters( 'bp_email_recipient_get_address', $this->address, $this );
	}

	/**
	 * Get recipient's name.
	 *
	 * @since 2.5.0
	 *
	 * @return string
	 */
	public function get_name() {

		/**
		 * Filters the recipient's name before it's returned.
		 *
		 * @since 2.5.0
		 *
		 * @param string $name Recipient's name.
		 * @param BP_Email $recipient $this Current instance of the email recipient class.
		 */
		return apply_filters( 'bp_email_recipient_get_name', $this->name, $this );
	}

	/**
	 * Get WP_User object for this recipient.
	 *
	 * @since 2.5.0
	 *
	 * @return WP_User|null WP_User object, or null if not set.
	 */
	public function get_user() {

		/**
		 * Filters the WP_User object for this recipient before it's returned.
		 *
		 * @since 2.5.0
		 *
		 * @param WP_User $name WP_User object for this recipient, or null if not set.
		 * @param BP_Email $recipient $this Current instance of the email recipient class.
		 */
		return apply_filters( 'bp_email_recipient_get_name', $this->user_object, $this );
	}
}
