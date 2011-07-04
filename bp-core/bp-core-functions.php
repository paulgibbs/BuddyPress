<?php

/**
 * Retrieve an option
 *
 * This is a wrapper for get_blog_option(), which in turn stores settings data (such as bp-pages)
 * on the appropriate blog, given your current setup.
 *
 * The 'bp_get_option' filter is primarily for backward-compatibility.
 *
 * @package BuddyPress
 * @since 1.3
 *
 * @uses bp_get_option_blog_id()
 * @param str $option_name The option to be retrieved
 * @param str $default Optional. Default value to be returned if the option isn't set
 * @return mixed The value for the option
 */
function bp_get_option( $option_name, $default = '' ) {
	$value = get_blog_option( bp_get_option_blog_id( $option_name ), $option_name, $default );
	
	return apply_filters( 'bp_get_option', $value );
}

/**
 * Save an option
 *
 * This is a wrapper for update_blog_option(), which in turn stores settings data (such as bp-pages)
 * on the appropriate blog, given your current setup.
 *
 * @package BuddyPress
 * @since 1.3
 *
 * @uses bp_get_option_blog_id()
 * @param str $option_name The option key to be set
 * @param str $value The value to be set
 */
function bp_update_option( $option_name, $value ) {
	// update_blog_option() does not return anything on success/failure, so neither can we
	update_blog_option( bp_get_option_blog_id( $option_name ), $option_name, $value );
}

/**
 * Delete an option
 *
 * This is a wrapper for delete_blog_option(), which in turn deletes settings data (such as
 * bp-pages) on the appropriate blog, given your current setup.
 *
 * @package BuddyPress
 * @since 1.3
 *
 * @uses bp_get_option_blog_id()
 * @param str $option_name The option key to be set
 */
function bp_delete_option( $option_name ) {
	// update_blog_option() does not return anything on success/failure, so neither can we
	delete_blog_option( bp_get_option_blog_id( $option_name ), $option_name );
}

/**
 * Retrieve the filterable blog_id of the blog where the option is question is saved
 *
 * Since BP 1.3, BuddyPress has stored all of its settings in blog options tables, as opposed to
 * sitemeta. This makes it easier for non-standard setups (like BP_ENABLE_MULTIBLOG and
 * multinetwork BP) to save and access options in a consistent and logical way.
 *
 * By default, nearly all settings are stored in the options table of BP_ROOT_BLOG. The one
 * exception is when BP_ENABLE_MULTIBLOG is enabled. In this case, bp-pages - the list of pages that
 * are associated with BP top-level components - must be specific to each blog in the network. If
 * you are building a plugin that requires an option (either a BP-native option, or your own custom
 * option) to be specific to each blog in a network, filter 'bp_blog_specific_options' and add your
 * option's name. This will allow you to use bp_get_option() and bp_update_option() seamlessly.
 *
 * @package BuddyPress
 * @since 1.3
 *
 * @see bp_get_option()
 * @see bp_update_option()
 * @uses apply_filters() Filter bp_get_option_blog_id to change this setting
 * @return int $blog_id
 */
function bp_get_option_blog_id( $option_name ) {
	$blog_specific_options = apply_filters( 'bp_blog_specific_options', array(
		'bp-pages'
	) );
	
	if ( in_array( $option_name, $blog_specific_options ) ) {
		if ( defined( 'BP_ENABLE_MULTIBLOG' ) && BP_ENABLE_MULTIBLOG ) {
			$blog_id = get_current_blog_id();
		} else {
			$blog_id = BP_ROOT_BLOG;
		}
	} else {
		$blog_id = BP_ROOT_BLOG;
	}

	return apply_filters( 'bp_get_option_blog_id', $blog_id );
}

/**
 * Allow filtering of database prefix. Intended for use in multinetwork installations.
 *
 * @global object $wpdb WordPress database object
 * @return string Filtered database prefix
 */
function bp_core_get_table_prefix() {
	global $wpdb;

	return apply_filters( 'bp_core_get_table_prefix', $wpdb->base_prefix );
}

/**
 * Fetches BP pages from the meta table, depending on setup
 *
 * @package BuddyPress Core
 * @since 1.3
 *
 * @todo Remove the "Upgrading from an earlier version of BP pre-1.3" block. Temporary measure for
 *       people running trunk installations. Leave for a version or two, then remove.
 */
function bp_core_get_page_meta() {
	$page_ids = bp_get_option( 'bp-pages' );
  
  	// Upgrading from an earlier version of BP pre-1.3
	if ( !isset( $page_ids['members'] ) && $ms_page_ids = get_site_option( 'bp-pages' ) ) {
		$is_enable_multiblog = is_multisite() && defined( 'BP_ENABLE_MULTIBLOG' ) && BP_ENABLE_MULTIBLOG ? true : false;
  
		$page_blog_id = $is_enable_multiblog ? get_current_blog_id() : BP_ROOT_BLOG;

		if ( isset( $ms_page_ids[$page_blog_id] ) ) {
			$page_ids = $ms_page_ids[$page_blog_id];

			bp_update_option( 'bp-pages', $page_ids );
		}
  	}
  	
	return apply_filters( 'bp_core_get_page_meta', $page_ids );
}

/**
 * Stores BP pages in the meta table, depending on setup
 *
 * bp-pages data is stored in site_options (falls back to options on non-MS), in an array keyed by
 * blog_id. This allows you to change your BP_ROOT_BLOG and go through the setup process again.
 *
 * @package BuddyPress Core
 * @since 1.3
 *
 * @param array $blog_page_ids The IDs of the WP pages corresponding to BP component directories
 */
function bp_core_update_page_meta( $blog_page_ids ) {
	bp_update_option( 'bp-pages', $blog_page_ids );
}

/**
 * Get bp-pages names and slugs
 *
 * @package BuddyPress Core
 * @since 1.3
 *
 * @return obj $pages Page names, IDs, and slugs
 */
function bp_core_get_page_names() {
	global $wpdb, $bp;

	// Set pages as standard class
	$pages = new stdClass;

	// Get pages and IDs
	if ( $page_ids = bp_core_get_page_meta() ) {

		$posts_table_name = is_multisite() && !defined( 'BP_ENABLE_MULTIBLOG' ) ? $wpdb->get_blog_prefix( BP_ROOT_BLOG ) . 'posts' : $wpdb->posts;
		$page_ids_sql     = implode( ',', (array)$page_ids );
		$page_names       = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_name, post_parent, post_title FROM {$posts_table_name} WHERE ID IN ({$page_ids_sql}) AND post_status = 'publish' " ) );

		foreach ( (array)$page_ids as $key => $page_id ) {
			foreach ( (array)$page_names as $page_name ) {
				if ( $page_name->ID == $page_id ) {
					$pages->{$key}->name  = $page_name->post_name;
					$pages->{$key}->id    = $page_name->ID;
					$pages->{$key}->title = $page_name->post_title;
					$slug[]               = $page_name->post_name;

					// Get the slug
					while ( $page_name->post_parent != 0 ) {
						$parent                 = $wpdb->get_results( $wpdb->prepare( "SELECT post_name, post_parent FROM {$posts_table_name} WHERE ID = %d", $page_name->post_parent ) );
						$slug[]                 = $parent[0]->post_name;
						$page_name->post_parent = $parent[0]->post_parent;
					}

					$pages->{$key}->slug = implode( '/', array_reverse( (array)$slug ) );
				}

				unset( $slug );
			}
		}
	}

	return apply_filters( 'bp_core_get_page_names', $pages );
}

/**
 * Creates a default component slug from a WP page root_slug
 *
 * Since 1.3, BP components get their root_slug (the slug used immediately
 * following the root domain) from the slug of a corresponding WP page.
 *
 * E.g. if your BP installation at example.com has its members page at
 * example.com/community/people, $bp->members->root_slug will be 'community/people'.
 *
 * By default, this function creates a shorter version of the root_slug for
 * use elsewhere in the URL, by returning the content after the final '/'
 * in the root_slug ('people' in the example above).
 *
 * Filter on 'bp_core_component_slug_from_root_slug' to override this method
 * in general, or define a specific component slug constant (e.g. BP_MEMBERS_SLUG)
 * to override specific component slugs.
 *
 * @package BuddyPress Core
 * @since 1.3
 *
 * @param str $root_slug The root slug, which comes from $bp->pages->[component]->slug
 * @return str $slug The short slug for use in the middle of URLs
 */
function bp_core_component_slug_from_root_slug( $root_slug ) {
	$slug_chunks = explode( '/', $root_slug );
 	$slug        = array_pop( $slug_chunks );

 	return apply_filters( 'bp_core_component_slug_from_root_slug', $slug, $root_slug );
}

function bp_core_do_network_admin() {
	$do_network_admin = false;

	if ( is_multisite() && ( !defined( 'BP_ENABLE_MULTIBLOG' ) || !BP_ENABLE_MULTIBLOG ) )
		$do_network_admin = true;

	return apply_filters( 'bp_core_do_network_admin', $do_network_admin );
}

function bp_core_admin_hook() {
	$hook = bp_core_do_network_admin() ? 'network_admin_menu' : 'admin_menu';

	return apply_filters( 'bp_core_admin_hook', $hook );
}

/**
 * Initializes the wp-admin area "BuddyPress" menus and sub menus.
 *
 * @package BuddyPress Core
 * @uses is_super_admin() returns true if the current user is a site admin, false if not
 */
function bp_core_admin_menu_init() {
	if ( !is_super_admin() )
		return false;

	add_action( bp_core_admin_hook(), 'bp_core_add_admin_menu', 9 );

	require ( BP_PLUGIN_DIR . '/bp-core/admin/bp-core-admin.php' );
}
add_action( 'bp_init', 'bp_core_admin_menu_init' );

/**
 * Adds the "BuddyPress" admin submenu item to the Site Admin tab.
 *
 * @package BuddyPress Core
 * @global object $bp Global BuddyPress settings object
 * @uses is_super_admin() returns true if the current user is a site admin, false if not
 * @uses add_submenu_page() WP function to add a submenu item
 */
function bp_core_add_admin_menu() {
	if ( !is_super_admin() )
		return false;

	// Don't add this version of the admin menu if a BP upgrade is in progress
 	// See bp_core_update_add_admin_menu()
	if ( defined( 'BP_IS_UPGRADE' ) && BP_IS_UPGRADE )
 		return false;

	$hooks = array();

	// Add the administration tab under the "Site Admin" tab for site administrators
	$hooks[] = add_menu_page( __( 'BuddyPress', 'buddypress' ), __( 'BuddyPress', 'buddypress' ), 'manage_options', 'bp-general-settings', 'bp_core_admin_component_setup', '', 4 );

	$hooks[] = add_submenu_page( 'bp-general-settings', __( 'Components', 'buddypress' ), __( 'Components', 'buddypress' ), 'manage_options', 'bp-general-settings', 'bp_core_admin_component_setup'  );
	$hooks[] = add_submenu_page( 'bp-general-settings', __( 'Settings',   'buddypress' ), __( 'Settings',   'buddypress' ), 'manage_options', 'bp-settings',         'bp_core_admin_settings'         );

	// Add a hook for css/js
	foreach( $hooks as $hook )
		add_action( "admin_print_styles-$hook", 'bp_core_add_admin_menu_styles' );
}

/**
 * Print admin messages to admin_notices or network_admin_notices
 *
 * BuddyPress combines all its messages into a single notice, to avoid a preponderance of yellow
 * boxes.
 *
 * @package BuddyPress Core
 * @since 1.3
 *
 * @global object $bp Global BuddyPress settings object
 * @uses is_super_admin() to check current user permissions before showing the notices
 * @uses bp_is_root_blog()
 */
function bp_core_print_admin_notices() {
	global $bp;

	// Only the super admin should see messages
	if ( !is_super_admin() )
		return;

	// On multisite installs, don't show on the Site Admin of a non-root blog, unless
	// do_network_admin is overridden
	if ( is_multisite() && bp_core_do_network_admin() && !bp_is_root_blog() )
		return;

	// Show the messages
	if ( !empty( $bp->admin->notices ) ) {
	?>
		<div id="message" class="updated fade">
			<?php foreach( $bp->admin->notices as $notice ) : ?>
				<p><?php echo $notice ?></p>
			<?php endforeach ?>
		</div>
	<?php
	}
}
add_action( 'admin_notices', 'bp_core_print_admin_notices' );
add_action( 'network_admin_notices', 'bp_core_print_admin_notices' );

/**
 * Add an admin notice to the BP queue
 *
 * Messages added with this function are displayed in BuddyPress's general purpose admin notices
 * box. It is recommended that you hook this function to admin_init, so that your messages are
 * loaded in time.
 *
 * @package BuddyPress Core
 * @since 1.3
 *
 * @global object $bp Global BuddyPress settings object
 * @param string $notice The notice you are adding to the queue
 */
function bp_core_add_admin_notice( $notice ) {
	global $bp;

	if ( empty( $bp->admin->notices ) ) {
		$bp->admin->notices = array();
	}

	$bp->admin->notices[] = $notice;
}

/**
 * Verify that some BP prerequisites are set up properly, and notify the admin if not
 *
 * On every Dashboard page, this function checks the following:
 *   - that pretty permalinks are enabled
 *   - that a BP-compatible theme is activated
 *   - that every BP component that needs a WP page for a directory has one
 *   - that no directory WP pages are published for deactivated components
 * The administrator will be shown a notice for each check that fails.
 *
 * @package BuddyPress Core
 */
function bp_core_activation_notice() {
	global $wp_rewrite, $wpdb, $bp;

	// Only the super admin gets warnings
	if ( !is_super_admin() )
		return;

	// On multisite installs, don't load on a non-root blog, unless do_network_admin is
	// overridden
	if ( is_multisite() && bp_core_do_network_admin() && !bp_is_root_blog() )
		return;

	// Don't show these messages during setup or upgrade
	if ( isset( $bp->maintenance_mode ) )
		return;

	/**
	 * Check to make sure that the blog setup routine has run. This can't happen during the
	 * wizard because of the order which the components are loaded
	 */
	if ( bp_is_active( 'blogs' ) ) {
		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->blogs->table_name}" ) );

		if ( !$count )
			bp_blogs_record_existing_blogs();
	}

	/**
	 * Are pretty permalinks enabled?
	 */
	if ( isset( $_POST['permalink_structure'] ) )
		return false;

	if ( empty( $wp_rewrite->permalink_structure ) ) {
		bp_core_add_admin_notice( sprintf( __( '<strong>BuddyPress is almost ready</strong>. You must <a href="%s">update your permalink structure</a> to something other than the default for it to work.', 'buddypress' ), admin_url( 'options-permalink.php' ) ) );
	}

	/**
	 * Are you using a BP-compatible theme?
	 */

	// Get current theme info
	$ct = current_theme_info();

	// The best way to remove this notice is to add a "buddypress" tag to
	// your active theme's CSS header.
	if ( !defined( 'BP_SILENCE_THEME_NOTICE' ) && !in_array( 'buddypress', (array)$ct->tags ) ) {
		bp_core_add_admin_notice( sprintf( __( "You'll need to <a href='%s'>activate a <strong>BuddyPress-compatible theme</strong></a> to take advantage of all of BuddyPress's features. We've bundled a default theme, but you can always <a href='%s'>install some other compatible themes</a> or <a href='%s'>update your existing WordPress theme</a>.", 'buddypress' ), admin_url( 'themes.php' ), network_admin_url( 'theme-install.php?type=tag&s=buddypress&tab=search' ), network_admin_url( 'plugin-install.php?type=term&tab=search&s=%22bp-template-pack%22' ) ) );
	}

	/**
	 * Check for orphaned directory pages (BP component is disabled, WP page exists)
	 */

	$orphaned_pages = array();
	foreach( $bp->pages as $component_id => $page ) {

		// Some members of $bp->pages will not have corresponding $bp->{component}, so we
		// skip them. Plugins can add themselves here if necessary.
		$exceptions = apply_filters( 'bp_pages_without_components', array( 'register', 'activate' ) );
		if ( in_array( $component_id, $exceptions ) )
			continue;

		if ( !isset( $bp->{$component_id} ) ) {
			// We'll need to get some more information about the page for the notice
			$page_data = get_post( $page->id );

			$orphaned_pages[] = array(
				'id'	=> $page_data->ID,
				'title'	=> $page_data->post_title
			);
		}

	}

	// If orphaned pages are found, post a notice about them.
	if ( !empty( $orphaned_pages ) ) {

		// Create the string of links to the Edit Page screen for the pages
		$edit_pages_links = array();
		foreach( $orphaned_pages as $op ) {
			$edit_pages_links[] = sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'post.php?action=edit&post=' . $op['id'] ), $op['title'] );
		}

		$admin_url = bp_core_do_network_admin() ? network_admin_url( 'admin.php?page=bp-general-settings' ) : admin_url( 'admin.php?page=bp-general-settings' );

		$notice = sprintf( __( 'Some of your WordPress pages are linked to BuddyPress components that have been disabled. These pages may continue to show up in your site navigation. Consider <a href="%1$s">reactivating the components</a>, or unpublishing the pages: <strong>%2$s</strong>', 'buddypress' ), $admin_url, implode( ', ', $edit_pages_links ) );

		bp_core_add_admin_notice( $notice );
	}

	/**
	 * Check for orphaned BP components (BP component is enabled, no WP page exists)
	 */

	$orphaned_components = array();
	$wp_page_components  = array();

	// Only some BP components require a WP page to function - those with a non-empty root_slug
	foreach( $bp->active_components as $component_id => $is_active ) {
		if ( !empty( $bp->{$component_id}->root_slug ) ) {
			$wp_page_components[] = array(
				'id'	=> $component_id,
				'name'	=> $bp->{$component_id}->name
			);
		}
	}

	// Activate and Register are special cases. They are not components but they need WP pages.
	// If user registration is disabled, we can skip this step.
	if ( isset( $bp->site_options['registration'] ) && ( 'user' == $bp->site_options['registration'] || ( 'all' == $bp->site_options['registration'] ) ) ) {
		$wp_page_components[] = array(
			'id'	=> 'activate',
			'name'	=> __( 'Activate', 'buddypress' )
		);

		$wp_page_components[] = array(
			'id'	=> 'register',
			'name'	=> __( 'Register', 'buddypress' )
		);
	}

	foreach( $wp_page_components as $component ) {
		if ( !isset( $bp->pages->{$component['id']} ) ) {
			$orphaned_components[] = $component['name'];
		}
	}

	if ( !empty( $orphaned_components ) ) {
		$admin_url = bp_core_do_network_admin() ? network_admin_url( 'admin.php?page=bp-general-settings' ) : admin_url( 'admin.php?page=bp-general-settings' );

		$notice = sprintf( __( 'Some BuddyPress components must be associated with WordPress pages for your site to work properly. The following components are missing their required WP pages: <strong>%1$s</strong>. Visit the <a href="%2$s">BuddyPress Components</a> panel, where you can either deactivate unused components or complete the page setup.', 'buddypress' ), implode( ', ', $orphaned_components ), $admin_url );

		bp_core_add_admin_notice( $notice );
	}
}
add_action( 'admin_init', 'bp_core_activation_notice' );

/**
 * Returns the domain for the root blog.
 * eg: http://domain.com/ OR https://domain.com
 *
 * @package BuddyPress Core
 * @uses get_blog_option() WordPress function to fetch blog meta.
 * @return $domain The domain URL for the blog.
 */
function bp_core_get_root_domain() {
	global $wpdb;

	if ( defined( 'BP_ENABLE_MULTIBLOG' ) )
		$domain = get_home_url( $wpdb->blogid );
	else
		$domain = get_home_url( BP_ROOT_BLOG );

	return apply_filters( 'bp_core_get_root_domain', $domain );
}

/**
 * Get the current GMT time to save into the DB
 *
 * @package BuddyPress Core
 * @since 1.2.6
 */
function bp_core_current_time( $gmt = true ) {
	// Get current time in MYSQL format
	$current_time = current_time( 'mysql', $gmt );

	return apply_filters( 'bp_core_current_time', $current_time );
}

/**
 * Adds a feedback (error/success) message to the WP cookie so it can be
 * displayed after the page reloads.
 *
 * @package BuddyPress Core
 *
 * @global obj $bp
 * @param str $message Feedback to give to user
 * @param str $type updated|success|error|warning
 */
function bp_core_add_message( $message, $type = '' ) {
	global $bp;

	// Success is the default
	if ( empty( $type ) )
		$type = 'success';

	// Send the values to the cookie for page reload display
	@setcookie( 'bp-message',      $message, time() + 60 * 60 * 24, COOKIEPATH );
	@setcookie( 'bp-message-type', $type,    time() + 60 * 60 * 24, COOKIEPATH );

	/***
	 * Send the values to the $bp global so we can still output messages
	 * without a page reload
	 */
	$bp->template_message      = $message;
	$bp->template_message_type = $type;
}

/**
 * Checks if there is a feedback message in the WP cookie, if so, adds a
 * "template_notices" action so that the message can be parsed into the template
 * and displayed to the user.
 *
 * After the message is displayed, it removes the message vars from the cookie
 * so that the message is not shown to the user multiple times.
 *
 * @package BuddyPress Core
 * @global $bp_message The message text
 * @global $bp_message_type The type of message (error/success)
 * @uses setcookie() Sets a cookie value for the user.
 */
function bp_core_setup_message() {
	global $bp;

	if ( empty( $bp->template_message ) && isset( $_COOKIE['bp-message'] ) )
		$bp->template_message = $_COOKIE['bp-message'];

	if ( empty( $bp->template_message_type ) && isset( $_COOKIE['bp-message-type'] ) )
		$bp->template_message_type = $_COOKIE['bp-message-type'];

	add_action( 'template_notices', 'bp_core_render_message' );

	@setcookie( 'bp-message',      false, time() - 1000, COOKIEPATH );
	@setcookie( 'bp-message-type', false, time() - 1000, COOKIEPATH );
}
add_action( 'bp_actions', 'bp_core_setup_message' );

/**
 * Renders a feedback message (either error or success message) to the theme template.
 * The hook action 'template_notices' is used to call this function, it is not called directly.
 *
 * @package BuddyPress Core
 * @global object $bp Global BuddyPress settings object
 */
function bp_core_render_message() {
	global $bp;

	if ( isset( $bp->template_message ) && $bp->template_message ) :
		$type = ( 'success' == $bp->template_message_type ) ? 'updated' : 'error'; ?>

		<div id="message" class="<?php echo $type; ?>">
			<p><?php echo stripslashes( esc_attr( $bp->template_message ) ); ?></p>
		</div>

	<?php

		do_action( 'bp_core_render_message' );

	endif;
}

/**
 * Format numbers the BuddyPress way
 *
 * @param str $number
 * @param bool $decimals
 * @return str
 */
function bp_core_number_format( $number, $decimals = false ) {
	// Check we actually have a number first.
	if ( empty( $number ) )
		return $number;

	return apply_filters( 'bp_core_number_format', number_format( $number, $decimals ), $number, $decimals );
}

/**
 * Based on function created by Dunstan Orchard - http://1976design.com
 *
 * This function will return an English representation of the time elapsed
 * since a given date.
 * eg: 2 hours and 50 minutes
 * eg: 4 days
 * eg: 4 weeks and 6 days
 *
 * @package BuddyPress Core
 * @param $older_date int Unix timestamp of date you want to calculate the time since for
 * @param $newer_date int Unix timestamp of date to compare older date to. Default false (current time).
 * @return str The time since.
 */
function bp_core_time_since( $older_date, $newer_date = false ) {

	// array of time period chunks
	$chunks = array(
		array( 60 * 60 * 24 * 365 , __( 'year', 'buddypress' ), __( 'years', 'buddypress' ) ),
		array( 60 * 60 * 24 * 30 , __( 'month', 'buddypress' ), __( 'months', 'buddypress' ) ),
		array( 60 * 60 * 24 * 7, __( 'week', 'buddypress' ), __( 'weeks', 'buddypress' ) ),
		array( 60 * 60 * 24 , __( 'day', 'buddypress' ), __( 'days', 'buddypress' ) ),
		array( 60 * 60 , __( 'hour', 'buddypress' ), __( 'hours', 'buddypress' ) ),
		array( 60 , __( 'minute', 'buddypress' ), __( 'minutes', 'buddypress' ) ),
		array( 1, __( 'second', 'buddypress' ), __( 'seconds', 'buddypress' ) )
	);

	if ( !empty( $older_date ) && !is_numeric( $older_date ) ) {
		$time_chunks = explode( ':', str_replace( ' ', ':', $older_date ) );
		$date_chunks = explode( '-', str_replace( ' ', '-', $older_date ) );
		$older_date  = gmmktime( (int)$time_chunks[1], (int)$time_chunks[2], (int)$time_chunks[3], (int)$date_chunks[1], (int)$date_chunks[2], (int)$date_chunks[0] );
	}

	/**
	 * $newer_date will equal false if we want to know the time elapsed between
	 * a date and the current time. $newer_date will have a value if we want to
	 * work out time elapsed between two known dates.
	 */
	$newer_date = ( !$newer_date ) ? strtotime( bp_core_current_time() ) : $newer_date;

	// Difference in seconds
	$since = $newer_date - $older_date;

	// Something went wrong with date calculation and we ended up with a negative date.
	if ( 0 > $since )
		return __( 'sometime', 'buddypress' );

	/**
	 * We only want to output two chunks of time here, eg:
	 * x years, xx months
	 * x days, xx hours
	 * so there's only two bits of calculation below:
	 */

	// Step one: the first chunk
	for ( $i = 0, $j = count($chunks); $i < $j; $i++) {
		$seconds = $chunks[$i][0];

		// Finding the biggest chunk (if the chunk fits, break)
		if ( ( $count = floor($since / $seconds) ) != 0 )
			break;
	}

	// If $i iterates all the way to $j, then the event happened 0 seconds ago
	if ( !isset( $chunks[$i] ) )
		return '0 ' . __( 'seconds', 'buddypress' );

	// Set output var
	$output = ( 1 == $count ) ? '1 '. $chunks[$i][1] : $count . ' ' . $chunks[$i][2];

	// Step two: the second chunk
	if ( $i + 2 < $j ) {
		$seconds2 = $chunks[$i + 1][0];
		$name2 = $chunks[$i + 1][1];

		if ( ( $count2 = floor( ( $since - ( $seconds * $count ) ) / $seconds2 ) ) != 0 ) {
			// Add to output var
			$output .= ( 1 == $count2 ) ? _x( ',', 'Separator in time since', 'buddypress' ) . ' 1 '. $chunks[$i + 1][1] : _x( ',', 'Separator in time since', 'buddypress' ) . ' ' . $count2 . ' ' . $chunks[$i + 1][2];
		}
	}

	if ( !(int)trim( $output ) )
		$output = '0 ' . __( 'seconds', 'buddypress' );

	return $output;
}

/**
 * Record user activity to the database. Many functions use a "last active" feature to
 * show the length of time since the user was last active.
 * This function will update that time as a usermeta setting for the user every 5 minutes.
 *
 * @package BuddyPress Core
 * @global $userdata WordPress user data for the current logged in user.
 * @uses bp_update_user_meta() BP function to update user metadata in the usermeta table.
 */
function bp_core_record_activity() {
	global $bp;
	
	if ( !is_user_logged_in() )
		return false;
	
	$user_id = $bp->loggedin_user->id;
	
	if ( bp_core_is_user_spammer( $user_id ) || bp_core_is_user_deleted( $user_id ) )
		return false;

	$activity = bp_get_user_meta( $user_id, 'last_activity', true );

	if ( !is_numeric( $activity ) )
		$activity = strtotime( $activity );

	// Get current time
	$current_time = bp_core_current_time();

	if ( empty( $activity ) || strtotime( $current_time ) >= strtotime( '+5 minutes', $activity ) )
		bp_update_user_meta( $user_id, 'last_activity', $current_time );
}
add_action( 'wp_head', 'bp_core_record_activity' );

/**
 * Formats last activity based on time since date given.
 *
 * @package BuddyPress Core
 * @param last_activity_date The date of last activity.
 * @param $before The text to prepend to the activity time since figure.
 * @param $after The text to append to the activity time since figure.
 * @uses bp_core_time_since() This function will return an English representation of the time elapsed.
 */
function bp_core_get_last_activity( $last_activity_date, $string ) {
	if ( !$last_activity_date || empty( $last_activity_date ) )
		$last_active = __( 'not recently active', 'buddypress' );
	else
		$last_active = sprintf( $string, bp_core_time_since( $last_activity_date ) );

	return apply_filters( 'bp_core_get_last_activity', $last_active, $last_activity_date, $string );
}

/**
 * Get the path of of the current site.
 *
 * @package BuddyPress Core
 *
 * @global $bp $bp
 * @global object $current_site
 * @return string
 */
function bp_core_get_site_path() {
	global $bp, $current_site;

	if ( is_multisite() )
		$site_path = $current_site->path;
	else {
		$site_path = (array) explode( '/', home_url() );

		if ( count( $site_path ) < 2 )
			$site_path = '/';
		else {
			// Unset the first three segments (http(s)://domain.com part)
			unset( $site_path[0] );
			unset( $site_path[1] );
			unset( $site_path[2] );

			if ( !count( $site_path ) )
				$site_path = '/';
			else
				$site_path = '/' . implode( '/', $site_path ) . '/';
		}
	}

	return apply_filters( 'bp_core_get_site_path', $site_path );
}

/**
 * Performs a status safe wp_redirect() that is compatible with bp_catch_uri()
 *
 * @package BuddyPress Core
 * @global $bp_no_status_set Makes sure that there are no conflicts with status_header() called in bp_core_do_catch_uri()
 * @uses get_themes()
 * @return An array containing all of the themes.
 */
function bp_core_redirect( $location, $status = 302 ) {
	global $bp_no_status_set;

	// Make sure we don't call status_header() in bp_core_do_catch_uri()
	// as this conflicts with wp_redirect()
	$bp_no_status_set = true;

	wp_redirect( $location, $status );
	die;
}

/**
 * Returns the referrer URL without the http(s)://
 *
 * @package BuddyPress Core
 * @return The referrer URL
 */
function bp_core_referrer() {
	$referer = explode( '/', wp_get_referer() );
	unset( $referer[0], $referer[1], $referer[2] );
	return implode( '/', $referer );
}

/**
 * Adds illegal names to WP so that root components will not conflict with
 * blog names on a subdirectory installation.
 *
 * For example, it would stop someone creating a blog with the slug "groups".
 */
function bp_core_add_illegal_names() {
	update_site_option( 'illegal_names', get_site_option( 'illegal_names' ), array() );
}

/**
 * A javascript free implementation of the search functions in BuddyPress
 *
 * @package BuddyPress Core
 * @global object $bp Global BuddyPress settings object
 * @param string $slug The slug to redirect to for searching.
 */
function bp_core_action_search_site( $slug = '' ) {
	global $bp;

	if ( BP_SEARCH_SLUG != $bp->current_component )
		return;

	if ( empty( $_POST['search-terms'] ) ) {
		bp_core_redirect( bp_get_root_domain() );
		return;
	}

	$search_terms = stripslashes( $_POST['search-terms'] );
	$search_which = !empty( $_POST['search-which'] ) ? $_POST['search-which'] : '';
	$query_string = '/?s=';

	if ( empty( $slug ) ) {
		switch ( $search_which ) {
			case 'posts':
				$slug = '';
				$var  = '/?s=';
				break;

			case 'blogs':
				$slug = bp_is_active( 'blogs' )  ? $bp->blogs->root_slug  : '';
				break;

			case 'forums':
				$slug = bp_is_active( 'forums' ) ? $bp->forums->root_slug : '';
				$query_string = '/?fs=';
				break;

			case 'groups':
				$slug = bp_is_active( 'groups' ) ? $bp->groups->root_slug : '';
				break;

			case 'members':
			default:
				$slug = $bp->members->root_slug;
				break;
		}

		if ( empty( $slug ) && 'posts' != $search_which ) {
			bp_core_redirect( bp_get_root_domain() );
			return;
		}
	}

	bp_core_redirect( apply_filters( 'bp_core_search_site', home_url( $slug . $query_string . urlencode( $search_terms ) ), $search_terms ) );
}
add_action( 'bp_init', 'bp_core_action_search_site', 7 );

/**
 * Prints the generation time in the footer of the site.
 *
 * @package BuddyPress Core
 */
function bp_core_print_generation_time() {
?>

<!-- Generated in <?php timer_stop(1); ?> seconds. (<?php echo get_num_queries(); ?> q) -->

	<?php
}
add_action( 'wp_footer', 'bp_core_print_generation_time' );

/**
 * Load the buddypress translation file for current language
 *
 * @package BuddyPress Core
 */
function bp_core_load_buddypress_textdomain() {
	$locale        = apply_filters( 'buddypress_locale', get_locale() );
	$mofile        = sprintf( 'buddypress-%s.mo', $locale );
	$mofile_global = WP_LANG_DIR . '/' . $mofile;
	$mofile_local  = BP_PLUGIN_DIR . '/bp-languages/' . $mofile;

	if ( file_exists( $mofile_global ) )
		return load_textdomain( 'buddypress', $mofile_global );
	elseif ( file_exists( $mofile_local ) )
		return load_textdomain( 'buddypress', $mofile_local );
	else
		return false;
}
add_action ( 'bp_init', 'bp_core_load_buddypress_textdomain', 2 );

function bp_core_add_ajax_hook() {
	// Theme only, we already have the wp_ajax_ hook firing in wp-admin
	if ( !defined( 'WP_ADMIN' ) && isset( $_REQUEST['action'] ) )
		do_action( 'wp_ajax_' . $_REQUEST['action'] );
}
add_action( 'bp_init', 'bp_core_add_ajax_hook' );

/**
 * When switching from single to multisite we need to copy blog options to
 * site options.
 *
 * @package BuddyPress Core
 * @todo Does this need to be here anymore after the introduction of bp_get_option etc?
 */
function bp_core_activate_site_options( $keys = array() ) {
	global $bp;

	if ( !empty( $keys ) && is_array( $keys ) ) {
		$errors = false;

		foreach ( $keys as $key => $default ) {
			if ( empty( $bp->site_options[ $key ] ) ) {
				$bp->site_options[ $key ] = bp_get_option( $key, $default );

				if ( !bp_update_option( $key, $bp->site_options[ $key ] ) )
					$errors = true;
			}
		}

		if ( empty( $errors ) )
			return true;
	}

	return false;
}

/**
 * BuddyPress uses common options to store configuration settings. Many of these
 * settings are needed at run time. Instead of fetching them all and adding many
 * initial queries to each page load, let's fetch them all in one go.
 *
 * @package BuddyPress Core
 * @todo Use settings API and audit these methods
 */
function bp_core_get_root_options() {
    global $wpdb;

    // These options come from the root blog options table
    $root_blog_options = apply_filters( 'bp_core_site_options', array(

        // BuddyPress core settings
        'bp-deactivated-components'       => serialize( array( ) ),
        'bp-blogs-first-install'          => '0',
        'bp-disable-blog-forum-comments'  => '0',
        'bp-xprofile-base-group-name'     => 'Base',
        'bp-xprofile-fullname-field-name' => 'Name',
        'bp-disable-profile-sync'         => '0',
        'bp-disable-avatar-uploads'       => '0',
        'bp-disable-account-deletion'     => '0',
        'bp-disable-forum-directory'      => '0',
        'bp-disable-blogforum-comments'   => '0',
        'bb-config-location'              => ABSPATH,
        'hide-loggedout-adminbar'         => '0',

        // Useful WordPress settings
        'tags_blog_id'                    => '0',
        'registration'                    => '0',
        'fileupload_maxk'                 => '1500',
        'avatar_default'                  => 'mysteryman'
    ) );
    $root_blog_option_keys  = array_keys( $root_blog_options );
    $blog_options_keys      = implode( "', '", (array) $root_blog_option_keys );
    $blog_options_query     = sprintf( "SELECT option_name AS name, option_value AS value FROM {$wpdb->options} WHERE option_name IN ('%s')", $blog_options_keys );
    $root_blog_options_meta = $wpdb->get_results( $blog_options_query );

    // Missing some options, so do some one-time fixing
    if ( empty( $root_blog_options_meta ) || ( count( $root_blog_options_meta ) < count( $root_blog_option_keys ) ) ) {

        // Unset the query - We'll be resetting it soon
        unset( $root_blog_options_meta );

        // Loop through options
        foreach ( $root_blog_options as $old_meta_key => $old_meta_default ) {

            // Clear out the value from the last time around
            unset( $old_meta_value );

            // Get old site option
            if ( is_multisite() )
                $old_meta_value = get_site_option( $old_meta_key );

            // No site option so look in root blog
            if ( empty( $old_meta_value ) )
                $old_meta_value = bp_get_option( $old_meta_key, $old_meta_default );

            // Update the root blog option
            bp_update_option( $old_meta_key, $old_meta_value );

            // Update the global array
            $root_blog_options_meta[$old_meta_key] = $old_meta_value;
        }

    // We're all matched up
    } else {

        // Loop through our results and make them usable
        foreach ( $root_blog_options_meta as $root_blog_option )
            $root_blog_options[$root_blog_option->name] = $root_blog_option->value;

        // Copy the options no the return val
        $root_blog_options_meta = $root_blog_options;

        // Clean up our temporary copy
        unset( $root_blog_options );
    }

    return apply_filters( 'bp_core_get_root_options', $root_blog_options_meta );
}

/**
 * This function originally let plugins add support for pages in the root of the install.
 * These root level pages are now handled by actual WordPress pages and this function is now
 * a convenience for compatibility with the new method.
 *
 * @global $bp BuddyPress global settings
 * @param $slug str The slug of the component
 */
function bp_core_add_root_component( $slug ) {
	global $bp;

	if ( empty( $bp->pages ) )
		$bp->pages = bp_core_get_page_names();

	$match = false;

	// Check if the slug is registered in the $bp->pages global
	foreach ( (array)$bp->pages as $key => $page ) {
		if ( $key == $slug || $page->slug == $slug )
			$match = true;
	}

	// If there was no match, add a page for this root component
	if ( empty( $match ) ) {
		$bp->add_root[] = $slug;
		add_action( 'bp_init', 'bp_core_create_root_component_page' );
	}
}

function bp_core_create_root_component_page() {
	global $bp;

	$new_page_ids = array();

	foreach ( (array)$bp->add_root as $slug )
		$new_page_ids[$slug] = wp_insert_post( array( 'comment_status' => 'closed', 'ping_status' => 'closed', 'post_title' => ucwords( $slug ), 'post_status' => 'publish', 'post_type' => 'page' ) );

	$page_ids = array_merge( (array) $new_page_ids, (array) bp_core_get_page_meta() );
	bp_core_update_page_meta( $page_ids );
}

/**
 * Is this BP_ROOT_BLOG?
 *
 * @package BuddyPress
 * @since 1.3
 *
 * @param int $blog_id Optional. Defaults to the current blog id.
 * @return bool $is_root_blog Returns true if this is BP_ROOT_BLOG.
 */
function bp_is_root_blog( $blog_id = false ) {
	$is_root_blog = true;

	if ( !$blog_id )
		$blog_id = get_current_blog_id();

	if ( $blog_id != BP_ROOT_BLOG )
		$is_root_blog = false;

	return apply_filters( 'bp_is_root_blog', $is_root_blog );
}

/**
 * Get the meta_key for a given piece of user metadata
 *
 * BuddyPress stores a number of pieces of userdata in the WordPress central usermeta table. In
 * order to allow plugins to enable multiple instances of BuddyPress on a single WP installation,
 * BP's usermeta keys are filtered with this function, so that they can be altered on the fly.
 *
 * Plugin authors should use BP's _user_meta() functions, which bakes in bp_get_user_meta_key().
 *    $last_active = bp_get_user_meta( $user_id, 'last_activity', true );
 * If you have to use WP's _user_meta() functions for some reason, you should use this function, eg
 *    $last_active = get_user_meta( $user_id, bp_get_user_meta_key( 'last_activity' ), true );
 * If using the WP functions, do not not hardcode your meta keys.
 *
 * @package BuddyPress
 * @since 1.3
 *
 * @uses apply_filters() Filter bp_get_user_meta_key to modify keys individually
 * @param str $key
 * @return str $key
 */
function bp_get_user_meta_key( $key = false ) {
	return apply_filters( 'bp_get_user_meta_key', $key );
}

/**
 * Get a piece of usermeta
 *
 * This is a wrapper for get_user_meta() that allows for easy use of bp_get_user_meta_key(), thereby
 * increasing compatibility with non-standard BP setups.
 *
 * @package BuddyPress
 * @since 1.3
 *
 * @uses bp_get_user_meta_key() For a filterable version of the meta key
 * @uses get_user_meta() See get_user_meta() docs for more details on parameters
 * @param int $user_id The id of the user whose meta you're fetching
 * @param string $key The meta key to retrieve.
 * @param bool $single Whether to return a single value.
 * @return mixed Will be an array if $single is false. Will be value of meta data field if $single
 *  is true.
 */
function bp_get_user_meta( $user_id, $key, $single = false ) {
	return get_user_meta( $user_id, bp_get_user_meta_key( $key ), $single );
}

/**
 * Update a piece of usermeta
 *
 * This is a wrapper for update_user_meta() that allows for easy use of bp_get_user_meta_key(),
 * thereby increasing compatibility with non-standard BP setups.
 *
 * @package BuddyPress
 * @since 1.3
 *
 * @uses bp_get_user_meta_key() For a filterable version of the meta key
 * @uses update_user_meta() See update_user_meta() docs for more details on parameters
 * @param int $user_id The id of the user whose meta you're setting
 * @param string $key The meta key to set.
 * @param mixed $value Metadata value.
 * @param mixed $prev_value Optional. Previous value to check before removing.
 * @return bool False on failure, true if success.
 */
function bp_update_user_meta( $user_id, $key, $value, $prev_value = '' ) {
	return update_user_meta( $user_id, bp_get_user_meta_key( $key ), $value, $prev_value );
}

/**
 * Delete a piece of usermeta
 *
 * This is a wrapper for delete_user_meta() that allows for easy use of bp_get_user_meta_key(),
 * thereby increasing compatibility with non-standard BP setups.
 *
 * @package BuddyPress
 * @since 1.3
 *
 * @uses bp_get_user_meta_key() For a filterable version of the meta key
 * @uses delete_user_meta() See delete_user_meta() docs for more details on parameters
 * @param int $user_id The id of the user whose meta you're deleting
 * @param string $key The meta key to delete.
 * @param mixed $value Optional. Metadata value.
 * @return bool False for failure. True for success.
 */
function bp_delete_user_meta( $user_id, $key, $value = '' ) {
	return delete_user_meta( $user_id, bp_get_user_meta_key( $key ), $value );
}


/** Global Manipulators *******************************************************/

/**
 * Set the $bp->is_directory global
 *
 * @global obj $bp
 * @param bool $is_directory
 * @param str $component
 */
function bp_update_is_directory( $is_directory = false, $component = '' ) {
	global $bp;

	if ( empty( $component ) )
		$component = bp_current_component();

	$bp->is_directory = apply_filters( 'bp_update_is_directory', $is_directory, $component );
}

/**
 * Set the $bp->is_item_admin global
 *
 * @global obj $bp
 * @param bool $is_item_admin
 * @param str $component
 */
function bp_update_is_item_admin( $is_item_admin = false, $component = '' ) {
	global $bp;

	if ( empty( $component ) )
		$component = bp_current_component();

	$bp->is_item_admin = apply_filters( 'bp_update_is_item_admin', $is_item_admin, $component );
}

/**
 * Set the $bp->is_item_mod global
 *
 * @global obj $bp
 * @param bool $is_item_mod
 * @param str $component
 */
function bp_update_is_item_mod( $is_item_mod = false, $component = '' ) {
	global $bp;

	if ( empty( $component ) )
		$component = bp_current_component();

	$bp->is_item_mod = apply_filters( 'bp_update_is_item_mod', $is_item_mod, $component );
}

/**
 * Trigger a 404
 *
 * @global object $bp Global BuddyPress settings object
 * @global WP_Query $wp_query WordPress query object
 * @param string $redirect If 'remove_canonical_direct', remove WordPress' "helpful" redirect_canonical action.
 * @since 1.3
 */
function bp_do_404( $redirect = 'remove_canonical_direct' ) {
	global $bp, $wp_query;

	do_action( 'bp_do_404', $redirect );

	$wp_query->set_404(); 
	status_header( 404 ); 
	nocache_headers();

	if ( 'remove_canonical_direct' == $redirect )
		remove_action( 'template_redirect', 'redirect_canonical' );
}
?>
