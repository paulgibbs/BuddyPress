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
 * Relations API item wrapper for generic connection types (i.e. 'any').
 *
 * @since BuddyPress (2.3.0)
 */
class BP_Relations_Item_Any extends BP_Relations_Item {

	/**
	 * Constructor.
	 *
	 * Overriden from parent class; doesn't do anything.
	 *
	 * @since BuddyPress (2.3.0)
	 */
	public function __construct() {
	}

	/**
	 * Get the content object.
	 *
	 * @return object
	 * @since BuddyPress (2.3.0)
	 */
	public function get_object() {
		return 'any';
	}

	/**
	 * Get the content object's ID.
	 *
	 * @return int
	 * @since BuddyPress (2.3.0)
	 */
	public function get_id() {
		return 0;
	}

	/**
	 * Get a permalink to the content object.
	 *
	 * @return string
	 * @since BuddyPress (2.3.0)
	 */
	public function get_permalink() {
		return '';
	}
}
