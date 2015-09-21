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
 * Email delivery implementation using `wp_mail()` aka PHPMailer.
 *
 * @since 2.4.0
 */
class BP_PHPMailer implements BP_Email_Delivery {

	/**
	 * Constructor.
	 *
	 * @since 2.4.0
	 */
	public function __construct() {
		global $phpmailer;

		// We'll try to use the PHPMailer object that might have been created by WordPress.
		if ( ! ( $phpmailer instanceof PHPMailer ) ) {
			require_once ABSPATH . WPINC . '/class-phpmailer.php';
			require_once ABSPATH . WPINC . '/class-smtp.php';
			$phpmailer = new PHPMailer( true );
		}
	}

	/**
	 * Send email(s).
	 *
	 * @param BP_Email $email Email to send.
	 * @since 2.4.0
	 */
		//wp_mail();
	}
	public function bp_email( BP_Email $email ) {
}
