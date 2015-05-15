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
 * Contains information about a particular connection type.
 *
 * @since BuddyPress (2.3.0)
 */
class BP_Relations_Connection_Type {
	protected $sides;
	protected $cardinality;

	/**
	 * Constructor.
	 *
	 * @param array $args See bp_relations_register_connection_type() for description.
	 * @param array $sides Array of BP_Relations_Side objects, each representing the side of a connection.
	 * @since BuddyPress (2.3.0)
	 */
	public function __construct( array $args, $sides ) {
		$this->sides = $sides;

		$this->set_cardinality( $args['cardinality'] );

		foreach ( $args as $key => $value ) {
			$this->$key = $value;
		}
	}

	public function get_field( $field, $direction ) {
		return $this->$field[ $direction ];
	}

	protected function set_cardinality( $cardinality ) {
		$parts                     = explode( '-', $cardinality );
		$this->cardinality['from'] = $parts[0];
		$this->cardinality['to']   = $parts[2];

		// Check cardinality types are valid.
		foreach ( $this->cardinality as $key => &$value ) {
			if ( 'one' !== $value ) {
				$value = 'many';
			}
		}
	}
}
