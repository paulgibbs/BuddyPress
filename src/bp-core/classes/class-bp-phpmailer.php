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
	 * @return bool False if some error occurred.
	 * @since 2.4.0
	 */
	public function bp_email( BP_Email $email ) {
		global $phpmailer;

		/**
		 * Resets.
		 */

		$phpmailer->clearAllRecipients();
		$phpmailer->clearAttachments();
		$phpmailer->clearCustomHeaders();
		$phpmailer->clearReplyTos();
		$phpmailer->Sender = '';

		$phpmailer->IsHTML( true );

		/**
		 * Set up.
		 */

		$phpmailer->IsMail();                                      // Use PHP's mail()
		$phpmailer->addReplyTo( bp_get_option( 'admin_email' ) );
		$phpmailer->CharSet  = bp_get_option( 'blog_charset' );
		$phpmailer->Hostname = self::get_hostname();


		/**
		 * Content.
		 */

		$phpmailer->AltBody = $email->get( 'body' );  // djpaultodo: make this plain text
		$phpmailer->Body    = $email->get( 'body' );
		$phpmailer->SetFrom( $email->get( 'from' ), $email->get( 'from_name' ) );
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


	/**
	 */

	/**
	 * Get an appropriate hostname for the email. Varies depending on site configuration.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	static public function get_hostname() {
		if ( is_multisite() ) {
			return get_current_site()->domain;  // From fix_phpmailer_messageid()
		}

		return preg_replace( '^https?://', '', bp_get_option( 'home' ) );
	}
}
