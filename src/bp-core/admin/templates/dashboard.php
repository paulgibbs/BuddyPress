<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// &#8217;  == apostrophe
?>

<div class="bpa-elevator-pitch">
	<h1><?php _e( 'Welcome! BuddyPress helps you run any kind of social network on your WordPress, with member profiles, activity streams, user groups, messaging, and&nbsp;more.', 'buddypress' ); ?></h1>
</div>

<article class="bpa-card">
	<h3>Getting You Started</h3>
	<p>We&#8217;ve activated our two most popular modules so you can start enjoying BuddyPress immediately; Activity Streams and Community Profiles.
	<p><strong>Activity Streams</strong> give your members a central place to discuss and comment on what other members are doing on your site. Activity Streams also provide Twitter-style @mentions and favouriting.</p>
	<p><strong>Extended Profiles</strong> are fully editadble profile field that allow you to define the fields that your members will use to describe themselves. Tailor profile fields to suit your audience.</p>
</article>

<div class="bpa-buttons">
	<div class="bpa-button-major bpa-button-activity">
		<a href="<?php echo esc_url( bp_get_admin_url( 'admin.php?page=bp-activity' ) ); ?>">
			<div class="bpa-button-major-icon"></div>
			<div class="bpa-button-major-contents">
				<h3>Activity Stream</h3>
				<h3><?php _ex( 'Activity Stream', 'dashboard screen button title', 'buddypress' ); ?></h3>
				<p><?php _ex( 'Dashboard Management', 'dashboard screen button title', 'buddypress' ); ?></p>
			</div>
		</a>
	</div>

	<div class="bpa-button-major bpa-button-xprofile">
		<a href="<?php echo esc_url( bp_get_admin_url( 'admin.php?page=bp-profile-setup' ) ); ?>">
			<div class="bpa-button-major-icon"></div>
			<div class="bpa-button-major-contents">
				<h3><?php _ex( 'Extended Profiles', 'dashboard screen button title', 'buddypress' ); ?></h3>
				<p><?php _ex( 'Create Custom Fields', 'dashboard screen button title', 'buddypress' ); ?></p>
			</div>
		</a>
	</div>
</div>