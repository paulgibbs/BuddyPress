<?php
/**
 * BuddyPress' implementation of advanced object relationships (many-to-many database cardinality).
 *
 * Based originally on scribu's "Posts to Posts" plugin for WordPress. Big thanks! https://github.com/scribu/
 *
 * @package BuddyPress
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Register a relations connection type.
 *
 * @param array $args {
 *     @type string $name A unique identifier for this connection type.
 *     @type string|array $from The first end of the connection: post type name or 'user'
 *     @type array $from_query_vars Additional query vars to pass to WP_Query. Default: none.
 *     @type string|array $to The second end of the connection: post type name or 'user'
 *     @type array $to_query_vars Additional query vars to pass to WP_Query. Default: none.
 *     @type array $fields Metadata fields editable by the user (key=>value pairs). Default: none.
 *     @type string $cardinality How many connection can each post have:
 *           'one-to-many', 'many-to-one' or 'many-to-many' (the default).
 *     @type bool $duplicate_connections Whether to allow more than one connection between the same
 *           two posts. Default: false.
 *     @type bool $self_connections Whether to allow a post/user to connect to itself.
 *           Default: false.
 *     @type bool $reciprocal For indeterminate connections: True means all connections are
 *           displayed in a single box. False means 'from' connections are shown in one box
 *           and 'to' connections are shown in another box. Default: false.
 * }
 * @return BP_Relations_Connection_Type|bool Object instance on success, false on failure.
 * @since BuddyPress (2.3.0)
 */
function bp_relations_register_connection_type( Array $args ) {
	$obj = P2P_Connection_Type_Factory::register( $args );
	return apply_filters( 'bp_relations_register_connection_type', $ojb );
}


/**
 * Metadata functions.
 */

/**
 * Delete metadata for an object relationship.
 *
 * @param int $object_id Relation object ID.
 * @param string $meta_key Metadata name.
 * @param mixed $meta_value Optional. Metadata value. Must be serializable if non-scalar. Default empty.
 * @return bool True on success, false on failure.
 * @since BuddyPress (2.3.0)
 */
function bp_relations_delete_meta( $object_id, $meta_key, $meta_value = '' ) {
	return delete_metadata( 'relations', $object_id, $meta_key, $meta_value );
}

/**
 * Retrieve an object's metadata.
 *
 * @param int $object_id Relation object ID.
 * @param string $key Optional. The meta key to retrieve. By default, returns data for all keys. Default empty.
 * @param bool $single  Optional. Whether to return a single value. Default false.
 * @return mixed Will be an array if $single is false. Will be value of meta data field if $single is true.
 * @since BuddyPress (2.3.0)
 */
function bp_relations_get_meta( $object_id, $meta_key = '', $single = true ) {
	return get_metadata( 'relations', $object_id, $meta_key, $single );
}

/**
 * Update existing metadata for an object.
 *
 * @param int $object_id Relation object ID.
 * @param string $meta_key Metadata key.
 * @param mixed $meta_value Metadata value. Must be serializable if non-scalar.
 * @param mixed $prev_value Optional. Previous value to check before removing. Default empty.
 * @return int|bool Meta ID if the key didn't exist, true on successful update, false on failure.
 * @since BuddyPress (2.3.0)
 */
function bp_relations_update_meta( $object_id, $meta_key, $meta_value, $prev_value = '' ) {
	return update_metadata( 'relations', $object_id, $meta_key, $meta_value, $prev_value );
}

/**
 * Add a metadata for an object.
 *
 * @param int $object_id Relation object ID.
 * @param string $meta_key Metadata name.
 * @param mixed $meta_value Metadata value. Must be serializable if non-scalar.
 * @param bool $unique Optional. Whether the same key should not be added. Default false.
 * @since BuddyPress (2.3.0)
 */
function bp_relations_add_meta( $object_id, $meta_key, $meta_value, $unique = false ) {
	return add_metadata( 'relations', $object_id, $meta_key, $meta_value, $unique );
}


/**
 * Helper functions.
 */

/**
 * When items have been deleted (Activity, Posts, Users, and so on), tidy up any relationships.
 *
 * @param int|array $objects IDs of the items (of the appropriate type) that have been deleted.
 * @param string $object_type Optional. The registered type of the item that have been deleted.
 *               If not set, uses `current_filter()` to try to find a valid type from the action
 *               that invoked this function.
 * @since BuddyPress (2.3.0)
 */
function bp_relations_delete_connections_for_type( $objects, $object_type = '' ) {
	if ( ! $object_type ) {
		// This function is, by default, hooked to actions such as "deleted_user" and "deleted_post".
		$object_type = preg_replace( '/.*deleted_/i', '', current_filter() );
	}

	if ( ! is_array( $objects ) ) {
		$objects = (array) $objects;
	}

	foreach ( $objects as $object_id ) {
		foreach ( BP_Relations_Connection_Type_Factory::get_all_instances() as $type => $connection ) {
			foreach ( array( 'from', 'to' ) as $direction ) {
				if ( $object_type !== $connection->side[ $direction ]->get_object_type() ) {
					continue;
				}

				bp_relations_delete_connections( $type, array( $direction => $object_id ) );
			}
		}
	}
}


/**
 * Classes (temp, pending core re-org of core-classes.php)
 */
