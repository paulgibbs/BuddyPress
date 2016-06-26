<?php
/**
 * Roles and capabilities logic for the Activity component.
 *
 * @package BuddyPress
 * @subpackage ActivityCaps
 * @since 2.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return an array of capabilities based on the role that is being requested.
 *
 * @since 2.7.0
 *
 * @param array  $caps Array of capabilities.
 * @param string $role      The role currently being loaded.
 * @return array            Capabilities for $role.
 */
function bp_activity_get_caps_for_role( $caps, $role ) {
	$activity_caps = array();

	switch ( $role ) {
		case 'administrator' :
			$activity_caps = array(
				'read_bp_activity'            => true,
				'edit_bp_activity'            => true,
				'edit_bp_activities'          => true,
				'edit_bp_others_activities'   => true,
				'publish_bp_activities'       => true,
				'delete_bp_activity'          => true,
				'delete_bp_activities'        => true,
				'delete_bp_others_activities' => true,
			);
			break;

		// Any other role.
		default :
			$activity_caps = array(
				'read_bp_activity'            => true,
				'edit_bp_activity'            => true,
				'edit_bp_activities'          => false,
				'edit_bp_others_activities'   => false,
				'publish_bp_activities'       => true,
				'delete_bp_activity'          => false,
				'delete_bp_activities'        => false,
				'delete_bp_others_activities' => false,
			);
			break;
	}

	return array_merge( $caps, $activity_caps );
}

/**
 * Maps Activity capabilities to built-in WordPress capabilities.
 *
 * @since 2.7.0
 *
 * @param array  $caps    Capabilities for meta capability.
 * @param string $cap     Capability name.
 * @param int    $user_id User id.
 * @param mixed  $args    Arguments.
 * @return array Actual capabilities for meta capability.
 */
function bp_activity_map_meta_caps( $caps, $cap, $user_id, $args ) {
	switch ( $cap ) {
	}


	/**
	 * Filter Activity capabilities.
	 *
	 * @since 2.7.0
	 *
	 * @param array  $caps    Capabilities for meta capability.
	 * @param string $cap     Capability name.
	 * @param int    $user_id User ID being mapped.
	 * @param mixed  $args    Capability arguments.
	 */
	return apply_filters( 'bp_activity_map_meta_caps', $caps, $cap, $user_id, $args );
}
add_filter( 'bp_map_meta_caps', 'bp_activity_map_meta_caps', 10, 4 );
