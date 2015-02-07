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
