<?php
/**
 * BuddyPress Customize controls.
 *
 * @package BuddyPress
 * @subpackage Core
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class BP_Customize_Control_Font_Size extends WP_Customize_Control {
	public $type = 'bp_mailtpl_font_size';

	/**
	 * Render the control's content.
	 *
	 * @since 2.5.0
	 */
	public function render_content() {
		$id    = 'customize-control-' . str_replace( '[', '-', str_replace( ']', '', $this->id ) );
		$class = 'customize-control customize-control-' . $this->type;

		?><li id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>">
			<label>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<div class="font_value"><?php echo esc_attr( $this->value() ); ?></div><!-- djpaultodo: can this esc_html? -->
				<input <?php $this->link(); ?> type="range" min="1" max="100" step="1" value="<?php echo esc_attr( $this->value() ); ?>" class="bp_mailtpl_range" />

				<?php if ( ! empty( $this->description ) ) : ?>
					<p><span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span></p>
				<?php endif; ?>
			</label>
		</li><?php
	}
}
