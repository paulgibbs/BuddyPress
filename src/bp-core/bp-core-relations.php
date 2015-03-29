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
 *     Describes the connection type.
 *
 *     @type string $name A unique identifier for this connection type.
 *     @type string $from The object type of the first end of the connection.
 *           Values: any post type name, or 'user'.
 *     @type string $to The object type of the second end of the connection.
 *           Values: any post type name, or 'user'.
 *
 *     @type array $from_query_vars Additional query vars to use when fetching the object. Optional.
 *     @type array $to_query_vars Additional query vars to use when fetching the object. Optional.
 *     @type string $cardinality Either "one-to-many", "many-to-one", or "many-to-many" (default).
 *     @type bool $duplicate_connections Allow > 1 connection between the same two objects.
 *           Default: false.
 *     @type bool $self_connections Allow an object to connect to itself. Default: false.
 * }
 * @return BP_Relations_Connection_Type|WP_Error Connection type object on success, WP_Error on failure.
 * @since BuddyPress (2.3.0)
 */
function bp_relations_register_connection_type( array $args ) {
	$defaults = array(
		// Required,
		'name' => '',
		'from' => 'post',
		'to'   => 'post',

		// Optional.
		'from_query_vars'       => array(),
		'to_query_vars'         => array(),
		'cardinality'           => 'many-to-many',
		'duplicate_connections' => false,
		'self_connections'      => false,
	);

	$args = bp_parse_args( $defaults, $args, 'relations_register_connection_type' );

	if ( ! $args['name'] || ! $args['from'] || ! $args['to'] ) {
		return new WP_Error( 'missing_parameter' );
	}

	if ( ! in_array( $args['cardinality'], array( 'one-to-many', 'many-to-one', 'many-to-many', ), true ) ) {
		return new WP_Error( 'invalid_cardinality');
	}

	$sides = array();
	foreach ( array( 'from', 'to' ) as $direction ) {
		$sides[ $direction ] = bp_relations_create_side_object( $args, $direction );
	}

//djpaultodo: this next -> BP_Relations_Connection_Type and/or self::get_direction_strategy!
	$ctype           = new BP_Relations_Connection_Type( $args, $sides );
	$ctype->strategy = self::get_direction_strategy( $sides, _p2p_pluck( $args, 'reciprocal' ) );
	$ctype           = apply_filters( 'bp_relations_register_connection_type', $ctype, $args, $sides );

	buddypress()->relations->types[ $ctype->name ] = $ctype;
	return $ctype;
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
 * Get details about a registered relations connection type.
 *
 * @param string $type A unique identifier for the connection type to retrieve.
 * @return BP_Relations_Connection_Type|bool Registered connection type, else false if not found.
 * @since BuddyPress (2.3.0)
 */
function bp_relations_get_connection_type( $type ) {
	$connection_type = false;

	if ( isset( buddypress()->relations->types[ $type ] ) ) {
		$connection_type = buddypress()->relations->types[ $type ];
	}

	return apply_filters( 'bp_relations_get_connection_type', $connection_type, $type );
}

/**
 * When items have been deleted (Activity, Posts, Users, and so on), tidy up any relationships.
 *
 * @param int|array $object_ids IDs of the items (of the appropriate type) that have been deleted.
 * @param string $object_type Optional. The type of item deleted. If unset, uses `current_filter()`
 *               to try to find a valid type from the action that invoked this function.
 * @since BuddyPress (2.3.0)
 */
function bp_relations_delete_connections_for_type( $object_ids, $object_type = '' ) {
	if ( ! $object_type ) {
		/**
		 * This function is, by default, hooked to actions such as "deleted_user" and "deleted_post",
		 * so parse the action name to try to get the object type from it.
		 */
		$object_type = preg_replace( '/.*deleted_/i', '', current_filter() );
	}

	if ( ! is_array( $object_ids ) ) {
		$object_ids = (array) $object_ids;
	}

	foreach ( $object_ids as $object_id ) {
		foreach ( buddypress()->relations->types as $type_name => $type ) {
			foreach ( array( 'from', 'to' ) as $direction ) {
				if ( $object_type !== $type->side[ $direction ]->get_object_type() ) {
					continue;
				}

				bp_relations_delete_connections( $type_name, array( $direction => $object_id ) );
			}
		}
	}
}

/**
 * Creates an object representing the side of a connection.
 *
 * The Relations API defines objects for each side of a connection in order to partition the type-
 * specific query/fetching implementation details. It makes them re-usable for other developers,
 * and also helps to avoid a long, hardcoded IF statement of callbacks for each type.
 *
 * For example, for a relationship between Users and Posts, one side will be created for the
 * User (BP_Relations_Side_User) and one for the Post (BP_Relations_Side_Post). Internally,
 * BP_Relations_Side_User uses BP_User_Query, and BP_Relations_Side_Post uses WP_Query to fetch data.
 *
 * @param array $args See bp_relations_register_connection_type() for description.
 * @param string $direction An end of the connection. Either "from" or "to".
 * @return P2P_Side Returns a deverative of the P2P_Side class for this side.
 * @since BuddyPress (2.3.0)
 */
function bp_relations_create_side_object( array $args, $direction ) {
	$object_type = wp_list_pluck( $args, $direction );                  // from, to
	$query_vars  = wp_list_pluck( $args, $direction . '_query_vars' );  // from_query_vars, to_query_vars

	// Custom post types use the BP_Relations_Side_Post class.
	$post_types = get_post_types( array( 'public' => true ), 'names' );
	if ( in_array( $object_type, $post_type, true ) ) {
		$object_type = 'post';
	}

	$class = 'BP_Relations_Side_' . ucfirst( $object_type ); // e.g. BP_Relations_Side_Post
	return new $class( $query_vars );
}



/**
 * Classes (temp, pending core re-org of core-classes.php)
 */
