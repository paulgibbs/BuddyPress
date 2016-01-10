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
 * @since 2.5.0
 */
class BP_PHPMailer implements BP_Email_Delivery {

	/**
	 * Constructor.
	 *
	 * @since 2.5.0
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
	 * @since 2.5.0
	 *
	 * @param BP_Email $email Email to send.
	 * @return bool|WP_Error Returns true if email send, else a descriptive WP_Error.
	 */
	public function bp_email( BP_Email $email ) {
		global $phpmailer;

		/*
		 * Resets.
		 */

		$phpmailer->clearAllRecipients();
		$phpmailer->clearAttachments();
		$phpmailer->clearCustomHeaders();
		$phpmailer->clearReplyTos();
		$phpmailer->Sender = '';


		/*
		 * Set up.
		 */

		$phpmailer->IsMail();
		$phpmailer->CharSet  = bp_get_option( 'blog_charset' );
		$phpmailer->Hostname = self::get_hostname();


		/*
		 * Content.
		 */

		$phpmailer->Subject = $email->get( 'subject', 'replace-tokens' );
		$content_plaintext  = PHPMailer::normalizeBreaks( $email->get( 'template_plaintext', 'replace-tokens' ) );

		if ( $email->get( 'content_type' ) === 'html' ) {
			$phpmailer->msgHTML( $email->get( 'template', 'add-content' ), '', 'wp_strip_all_tags' );
			$phpmailer->AltBody = $content_plaintext;

		} else {
			$phpmailer->IsHTML( false );
			$phpmailer->Body = $content_plaintext;
		}

		$recipient = $email->get( 'from' );
		try {
			$phpmailer->SetFrom( $recipient->get_address(), $recipient->get_name() );
		} catch ( phpmailerException $e ) {
		}

		$recipient = $email->get( 'reply_to' );
		try {
			$phpmailer->addReplyTo( $recipient->get_address(), $recipient->get_name() );
		} catch ( phpmailerException $e ) {
		}

		$recipients = $email->get( 'to' );
		foreach ( $recipients as $recipient ) {
			try {
				$phpmailer->AddAddress( $recipient->get_address(), $recipient->get_name() );
			} catch ( phpmailerException $e ) {
			}
		}

		$recipients = $email->get( 'cc' );
		foreach ( $recipients as $recipient ) {
			try {
				$phpmailer->AddCc( $recipient->get_address(), $recipient->get_name() );
			} catch ( phpmailerException $e ) {
			}
		}

		$recipients = $email->get( 'bcc' );
		foreach ( $recipients as $recipient ) {
			try {
				$phpmailer->AddBcc( $recipient->get_address(), $recipient->get_name() );
			} catch ( phpmailerException $e ) {
			}
		}

		$headers = $email->get( 'headers' );
		foreach ( $headers as $name => $content ) {
			$phpmailer->AddCustomHeader( $name, $content );
		}


		/**
		 * Fires after PHPMailer is initialised.
		 *
		 * @since 2.5.0
		 *
		 * @param PHPMailer $phpmailer The PHPMailer instance.
		 */
		do_action( 'bp_phpmailer_init', $phpmailer );

		try {
			return $phpmailer->Send();
		} catch ( phpmailerException $e ) {
			return new WP_Error( $e->getCode(), $e->getMessage(), $email );
		}
	}


	/*
	 * Utility/helper functions.
	 */

	/**
	 * Get an appropriate hostname for the email. Varies depending on site configuration.
	 *
	 * @since 2.5.0
	 *
	 * @return string
	 */
	static public function get_hostname() {
		if ( is_multisite() ) {
			return get_current_site()->domain;  // From fix_phpmailer_messageid()
		}

		return preg_replace( '#^https?://#i', '', bp_get_option( 'home' ) );
	}
}
