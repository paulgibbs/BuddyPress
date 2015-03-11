<?php
/**
 * Core component classes.
 *
 * @package BuddyPress
 * @subpackage Core
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Relations API item wrapper for WordPress Users.
 *
 * @since BuddyPress (2.3.0)
 */
class BP_Relations_Item_User extends BP_Relations_Item {

	/**
	 * Get the user ID.
	 *
	 * @return int
	 * @since BuddyPress (2.3.0)
	 */
	public function get_id() {
		return $this->item->ID;
	}

	/**
	 * Get a link to the user's profile page.
	 *
	 * @return string
	 * @since BuddyPress (2.3.0)
	 */
	public function get_permalink() {
		return bp_core_get_user_domain( $this->item->ID, $this->item->user_nicename, $this->item->user_login ) ;
	}
}
