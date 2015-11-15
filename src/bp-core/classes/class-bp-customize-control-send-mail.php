<?php
/**
 * Core component class.
 *
 * @package BuddyPress
 * @subpackage Core
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * BuddyPress Customizer "send mail" control.
 *
 * @since 2.5.0
 */
class BP_Customize_Control_Send_Mail extends WP_Customize_Control {
	/**
	 * @var string
	 */
	public $type = 'bp_mailtpl_send_mail';

	/**
	 * Render the control.
	 *
	 * @since 2.5.0
	 */
	public function render_content() {
		$class = 'customize-control customize-control-' . $this->type;
		$id    = 'customize-control-' . str_replace( '[', '-', str_replace( ']', '', $this->id ) );

		?><li id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>">
			<label>
				<button class="button button-primary" id="bp-mailtpl-send_mail"><?php _e( 'Send', 'buddypress' ); ?></button>
				<img id="bp-mailtpl-spinner" src="<?php echo esc_url( admin_url( 'images/spinner.gif' ) );?>" style="display: none"/>
				<span id="bp-mailtpl-success" style="display: none"><?php _e( 'Email sent!', 'buddypress');?></span>

				<?php if ( ! empty( $this->description ) ) : ?>
					<p><span class="description customize-control-description"><?php echo $this->description; ?></span></p>
				<?php endif; ?>

			</label>
		</li><?php
	}
}
