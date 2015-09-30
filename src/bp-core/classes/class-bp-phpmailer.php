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
 * Email delivery implementation using PHPMailer.
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

		/**
		 * Set up.
		 */

		$phpmailer->clearAllRecipients();
		$phpmailer->clearAttachments();
		$phpmailer->clearCustomHeaders();
		$phpmailer->clearReplyTos();

		$phpmailer->IsMail();
		$phpmailer->IsHTML( true );

		$phpmailer->CharSet     = get_bloginfo( 'charset' );
		$phpmailer->ContentType = 'text/html';
		$phpmailer->Hostname    = get_current_site()->domain;  // From WPMU


		/**
		 * Email data.
		 */

		$phpmailer->AltBody = $email->get( 'body_plaintext' );
		$phpmailer->Body    = $email->get( 'body' );
		$phpmailer->From    = $email->get( 'from' );
		$phpmailer->Subject = $email->get( 'subject' );

		$to = $email->get( 'to' );
		foreach ( $to as $to_address ) {
			try {
				$phpmailer->AddAddress( $to_address, '$recipient_name_djpaultodo' );
			} catch ( phpmailerException $e ) {
			}
		}

		$cc = $email->get( 'cc' );
		foreach ( $cc as $cc_address ) {
			try {
				$phpmailer->AddCc( $cc_address, '$recipient_name' );
			} catch ( phpmailerException $e ) {
			}
		}

		$bcc = $email->get( 'bcc' );
		foreach ( $bcc as $bcc_address ) {
			try {
				$phpmailer->AddBcc( $bcc_address, '$recipient_name' );
			} catch ( phpmailerException $e ) {
			}
		}

		$headers = $email->get( 'headers' );
		foreach ( $headers as $name => $content ) {
			$phpmailer->AddCustomHeader( sprintf( '%1$s: %2$s', $name, $content ) );
		}


		/**
		 * Fires after PHPMailer is initialised.
		 *
		 * @since 2.4.0
		 *
		 * @param PHPMailer $phpmailer The PHPMailer instance.
		 */
		do_action( 'bp_phpmailer_init', $phpmailer );

		try {
			return $phpmailer->Send();
		} catch ( phpmailerException $e ) {
			return false;
		}
	}
}
