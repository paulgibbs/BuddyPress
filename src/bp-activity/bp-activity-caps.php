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
				'bp_read_activity'            => true,
				'bp_edit_activity'            => true,
				'bp_edit_activities'          => true,
				'bp_edit_others_activities'   => true,
				'bp_publish_activities'       => true,
				'bp_delete_activity'          => true,
				'bp_delete_activities'        => true,
				'bp_delete_others_activities' => true,
			);
			break;

		// Any other role.
		default :
			$activity_caps = array(
				'bp_read_activity'            => true,
				'bp_edit_activity'            => true,
				'bp_edit_activities'          => false,
				'bp_edit_others_activities'   => false,
				'bp_publish_activities'       => true,
				'bp_delete_activity'          => false,
				'bp_delete_activities'        => false,
				'bp_delete_others_activities' => false,
			);
			break;
	}

	return array_merge( $caps, $activity_caps );
}

