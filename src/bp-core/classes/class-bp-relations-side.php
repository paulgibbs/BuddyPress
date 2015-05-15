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
 * An object representing a side of a relations connection.
 *
 * @since BuddyPress (2.4.0)
 */
abstract class BP_Relations_Side {
	protected $relations_query_vars = array();

	public function __construct( array $query_vars = array() ) {
		$this->relations_query_vars = $query_vars;
	}
}
