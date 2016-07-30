<?php
/**
 * BuddyPress XProfile Classes.
 *
 * @package BuddyPress
 * @subpackage XProfileClasses
 * @since 2.7.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Base class for xprofile field types that set/get WordPress profile data from usermeta.
 *
 * @since 2.7.0
 */
abstract class BP_XProfile_Field_Type_WordPress extends BP_XProfile_Field_Type {

	/**
	 * Constructor for the URL field type
	 *
	 * @since 2.7.0
	 */
	public function __construct() {
		parent::__construct();

		/**
		 * Fires inside __construct() method for BP_XProfile_Field_Type_WordPress class.
		 *
		 * @since 2.7.0
		 *
		 * @param BP_XProfile_Field_Type_URL $this Instance of the field type object.
		 */
		do_action( 'bp_xprofile_field_type_wordpress', $this );
	}
}
