<?php
/**
 * Deprecated functions.
 *
 * @deprecated 2.4.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Set "From" name in outgoing email to the site name.
 *
 * @uses bp_get_option() fetches the value for a meta_key in the wp_X_options table.
 *
 * @return string The blog name for the root blog.
 */
function bp_core_email_from_name_filter() {

	/**
	 * Filters the "From" name in outgoing email to the site name.
	 *
	 * @since 1.2.0
	 * @deprecated 2.4.0 Not used any more in BuddyPress core, but left intact for old plugins.
	 *                   This used to be hooked to WordPress' "wp_mail_from_name" action.
	 *
	 * @param string $value Value to set the "From" name to.
	 */
 	return apply_filters( 'bp_core_email_from_name_filter', bp_get_option( 'blogname', 'WordPress' ) );
}
