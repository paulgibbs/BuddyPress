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
	 * Send email(s).
	 *
	 * @param BP_Email $email Email to send.
	 * @since 2.4.0
	 */
	public function bp_send_email( BP_Email $email ) {
		//wp_mail();
	}
}
