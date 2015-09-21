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
 * Email delivery implementation base class.
 *
 * When implementing support for an email delivery service into BuddyPress,
 * you are required to create a class that implements this interface.
 *
 * @since 2.4.0
 */
interface BP_Email_Delivery {

	/**
	 * Send email(s).
	 *
	 * @param BP_Email $email Email to send.
	 * @since 2.4.0
	 */
	public function bp_email( BP_Email $email );
}
