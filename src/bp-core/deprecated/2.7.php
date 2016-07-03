<?php
/**
 * Deprecated functions.
 *
 * @deprecated 2.7.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Get the DB schema to use for BuddyPress components.
 *
 * @since 1.1.0
 * @deprecated 2.7.0
 *
 * @return string The default database character-set, if set.
 */
function bp_core_set_charset() {
	global $wpdb;

	_deprecated_function( __FUNCTION__, '2.7', 'wpdb::get_charset_collate()' );

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	return !empty( $wpdb->charset ) ? "DEFAULT CHARACTER SET {$wpdb->charset}" : '';
}

/**
 * Return community capabilities.
 *
 * @since 1.6.0
 * @deprecated 2.7.0
 *
 * @return array Community capabilities.
 */
function bp_get_community_caps() {
	_deprecated_function( __FUNCTION__, '2.7' );

	// Forum meta caps.
	$caps = array();

	/**
	 * Filters community capabilities.
	 *
	 * @since 1.6.0
	 *
	 * @param array $caps Array of capabilities to add. Empty by default.
	 */
	return apply_filters( 'bp_get_community_caps', $caps );
}
