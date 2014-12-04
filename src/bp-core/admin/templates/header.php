<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="bpa-header clearfix">
	<nav role="navigation">
		<ul class="bpa-nav">
			<li class="bpa-header-logo">
				<a href="#"><?php _e( 'BuddyPress', 'buddypress' ); ?></a>
			</li>

			<li>
				<a class="bpa-button" href="#"><?php _e( 'Documentation', 'buddypress' ); ?></a>
			</li>

			<li>
				<a class="bpa-button" href="#"><?php _e( 'Support', 'buddypress' ); ?></a>
			</li>

			<?php
			$nav = bp_core_get_admin_tabs();
			if ( ! empty( $nav ) ) : ?>
				<?php foreach ( $nav as $item ) : ?>
					<li>
						<a class="bpa-button" href="<?php echo esc_url( $item['href'] ); ?>"><?php echo esc_html( $item['name'] ); ?></a>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>

		</ul><!-- .bpa-nav -->
	</nav>
</div><!-- .bpa-header -->
