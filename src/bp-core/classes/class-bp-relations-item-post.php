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
 * Relations API item wrapper for WordPress Posts.
 *
 * @since BuddyPress (2.3.0)
 */
class BP_Relations_Item_Post extends BP_Relations_Item {

	/**
	 * Get the post ID.
	 *
	 * @return int
	 * @since BuddyPress (2.3.0)
	 */
	public function get_id() {
		return $this->item->ID;
	}

	/**
	 * Get a permalink to this post.
	 *
	 * @return string
	 * @since BuddyPress (2.3.0)
	 */
	public function get_permalink() {
		return get_permalink( $this->item );
	}
}
