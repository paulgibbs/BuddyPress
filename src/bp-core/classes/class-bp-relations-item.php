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
 * Implements a wrapper for content objects used in the relations API.
 *
 * Makes it easy to add support for other types of content by minimising the amount of implementation-specific
 * code in BuddyPress core.
 *
 * @since BuddyPress (2.3.0)
 */
abstract class BP_Relations_Item {

	/**
	 * The content object (could be anything -- WP_Post, WP_User, etc).
	 *
	 * @since BuddyPress (2.3.0)
	 * @var object
	 */
	protected $item;


	/**
	 * Constructor.
	 *
	 * @param object $item The content object.
	 * @since BuddyPress (2.3.0)
	 */
	public function __construct( $item ) {
		$this->item = $item;
	}

	/**
	 * Magic method for checking the existence of a certain class property.
	 *
	 * @param string $property
	 * @return todo todo.
	 * @since BuddyPress (2.3.0)
	 */
	//public function __isset( $property ) { return isset( $this->item->$property ); }

	/**
	 * Magic method for getting a certain class property.
	 *
	 * @param string $property
	 * @return mixed|null Property value, or null if not set.
	 * @since BuddyPress (2.3.0)
	 */
	//public function __get( $property ) { return isset( $this->item->$property ) ? $this->item->$property : null; }

	/**
	 * Magic method for setting a class property.
	 *
	 * @param string $property
	 * @param mixed $new_value
	 * @return mixed|null Property value, or null if not set.
	 * @since BuddyPress (2.3.0)
	 */
	//public function __set( $property, $new_value ) { $this->item->$property = $new_value; }
	// DJPAULTODO: does this class need setters/getters??


	/**
	 * Get the content object.
	 *
	 * @return object
	 * @since BuddyPress (2.3.0)
	 */
	public function get_object() {
		return $this->item;
	}

	/**
	 * Get the content object's ID.
	 *
	 * @return int
	 * @since BuddyPress (2.3.0)
	 */
	abstract public function get_id();

	/**
	 * Get a permalink to the content object.
	 *
	 * @return string
	 * @since BuddyPress (2.3.0)
	 */
	abstract public function get_permalink();
}
