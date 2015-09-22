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
	public function bp_email( BP_Email $email ) {
		global $phpmailer;

		// Empty out the values that may be set
		$phpmailer->ClearAllRecipients();
		$phpmailer->ClearAttachments();
		$phpmailer->ClearCustomHeaders();
		$phpmailer->ClearReplyTos();

		/**
		 * Filter the email address to send from.
		 *
		 * @since 2.2.0
		 *
		 * @param string $from_email Email address to send from.
		 */
		$phpmailer->From = apply_filters( 'wp_mail_from', $from_email );

		/**
		 * Filter the name to associate with the "from" email address.
		 *
		 * @since 2.3.0
		 *
		 * @param string $from_name Name associated with the "from" email address.
		 */
		$phpmailer->FromName = apply_filters( 'wp_mail_from_name', $from_name );

		try {
			$phpmailer->AddAddress( $recipient, $recipient_name);
		} catch ( phpmailerException $e ) {
		}

		// Set mail's subject and body
		$phpmailer->Subject = $subject;
		$phpmailer->Body    = $message;

		try {
			$phpmailer->AddCc( $recipient, $recipient_name );
		} catch ( phpmailerException $e ) {
			continue;
		}

		try {
			$phpmailer->AddBcc( $recipient, $recipient_name );
		} catch ( phpmailerException $e ) {
			continue;
		}

		// Set to use PHP's mail()
		$phpmailer->IsMail();

		$phpmailer->IsHTML( true );
		$phpmailer->ContentType = $content_type;
		$phpmailer->CharSet = apply_filters( 'wp_mail_charset', $charset );


		/**
		 * Fires after PHPMailer is initialized.
		 *
		 * @since 2.2.0
		 *
		 * @param PHPMailer &$phpmailer The PHPMailer instance, passed by reference.
		 */
		do_action_ref_array( 'phpmailer_init', array( &$phpmailer ) );

		// Send!
		try {
			return $phpmailer->Send();
		} catch ( phpmailerException $e ) {
			return false;
		}

		}
}
