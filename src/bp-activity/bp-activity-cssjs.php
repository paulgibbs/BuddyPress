<?php

/**
 * Activity component CSS/JS
 *
 * @package BuddyPress
 * @subpackage ActivityScripts
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Enqueue @mentions JS.
 *
 * @since BuddyPress (2.1)
 */
function bp_activity_mentions_scripts() {
	if ( ! bp_is_user_active() || ! ( bp_is_activity_component() || bp_is_blog_page() && is_singular() ) ) {
		return;
	}

	$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	wp_enqueue_script( 'bp-mentions', buddypress()->plugin_url . "bp-activity/js/mentions{$min}.js", array( 'jquery', 'jquery-atwho' ), bp_get_version(), true );
	wp_enqueue_style( 'bp-mentions-css', buddypress()->plugin_url . "bp-activity/css/mentions{$min}.css", array(), bp_get_version() );
}
add_action( 'bp_enqueue_scripts', 'bp_activity_mentions_scripts' );

/**
 * Enqueue @mentions JS in wp-admin.
 *
 * @since BuddyPress (2.1)
 */
function bp_activity_mentions_dashboard_scripts() {
	if ( ! bp_is_user_active() || ! is_admin() ) {
		return;
	}

	// Special handling for New/Edit screens in wp-admin
	if (
		! get_current_screen() ||
		! in_array( get_current_screen()->base, array( 'page', 'post' ) ) || 
		! post_type_supports( get_current_screen()->post_type, 'editor' ) ) {
		return;
	}

	$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	wp_enqueue_script( 'bp-mentions', buddypress()->plugin_url . "bp-activity/js/mentions{$min}.js", array( 'jquery', 'jquery-atwho' ), bp_get_version(), false );
	wp_enqueue_style( 'bp-mentions-css', buddypress()->plugin_url . "bp-activity/css/mentions{$min}.css", array(), bp_get_version() );
}
add_action( 'bp_admin_enqueue_scripts', 'bp_activity_mentions_dashboard_scripts' );