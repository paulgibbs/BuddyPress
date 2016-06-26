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

