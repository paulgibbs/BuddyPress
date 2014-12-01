<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="bpa-header bpa-width-full clearfix">
	<p class="bpa-header-logo" aria-hidden="true">
		<span class="screen-reader-text"><?php _e( 'BuddyPress', 'buddypress' ); ?></span>
	</p>

	<?php
	$nav = bp_core_get_admin_tabs();
	if ( empty( $nav ) ) {
		return;
	}
	?>

	<ul class="bpa-nav" role="navigation">
		<?php foreach ( $nav as $item ) : ?>
			<li><a href="<?php echo esc_url( $item['href'] ); ?>"><?php echo esc_html( $item['name'] ); ?></a></li>
		<?php endforeach; ?>
	</ul><!-- .bpa-nav -->

</div><!-- .bpa-header -->