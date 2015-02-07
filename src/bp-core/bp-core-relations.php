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
 * Get a piece of message metadata.
 *
 * @since BuddyPress (2.3.0)
 */
function bp_messages_get_meta( $message_id, $meta_key = '', $single = true ) {
	return get_metadata( 'message', $message_id, $meta_key, $single );
}

/**
 * Update a piece of message metadata.
 *
 * @since BuddyPress (2.3.0)
 */
function bp_messages_update_meta( $message_id, $meta_key, $meta_value, $prev_value = '' ) {
	return update_metadata( 'message', $message_id, $meta_key, $meta_value, $prev_value );
}

/**
 * Add a piece of message metadata.
 *
 * @since BuddyPress (2.3.0)
 */
function bp_message_add_meta( $message_id, $meta_key, $meta_value, $unique = false ) {
	return add_metadata( 'message', $message_id, $meta_key, $meta_value, $unique );
}
