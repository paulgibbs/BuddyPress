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
				'manage_bp_activities' => true,  // wp-admin
				'edit_bp_activity'     => true,
				'edit_bp_activities'   => true,
				'create_bp_activities' => true,
				'delete_bp_activity'   => true,
				'delete_bp_activities' => true,
			);
			break;

		// Any other role.
		default :
			$activity_caps = array(
				'manage_bp_activities' => false,  // wp-admin
				'edit_bp_activity'     => true,
				'edit_bp_activities'   => false,
				'create_bp_activities' => true,
				'delete_bp_activity'   => true,
				'delete_bp_activities' => false,
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
	$activity       = null;
	$user_is_active = bp_is_user_active( $user_id );

	// $args[0], if set, is always an activity ID.
	if ( isset( $args[0] ) ) {
		$activity = bp_activity_get( array(
			'in'          => absint( $args[0] ),
			'show_hidden' => true,
			'spam'        => 'all'
		) );

		$activity = empty( $activity['activities'] ) ? null : $activity['activities'][0];
	}

	switch ( $cap ) {
		case 'edit_bp_activity' :
			if ( $activity && $user_id === $activity->user_id || bp_user_can( $user_id, 'edit_bp_activities' ) ) {
				$caps = array( $cap );
			} else {
				$caps = array( 'do_not_allow' );
			}
		break;

		case 'delete_bp_activities' :
		case 'edit_bp_activities' :
		case 'manage_bp_activities' :
			if ( bp_is_network_activated() && bp_user_can( $user_id, 'manage_network_options' ) ) {
				$caps = array( $cap );
			} elseif ( ! bp_is_network_activated() && bp_user_can( $user_id, 'manage_options' ) ) {
				$caps = array( $cap );
			} else {
				$caps = array( 'do_not_allow' );
			}
		break;

		case 'create_bp_activities' :
			$caps = array( $cap );
		break;

		case 'delete_bp_activity' :
			if ( $activity && $user_id === $activity->user_id || bp_user_can( $user_id, 'delete_bp_activities' ) ) {
				$caps = array( $cap );
			} else {
				$caps = array( 'do_not_allow' );
			}
		break;

		// Don't process any other capabilities further.
		default :
			return $caps;
		break;
	}

	if ( ! $user_is_active ) {
		$caps = array( 'do_not_allow' );
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
