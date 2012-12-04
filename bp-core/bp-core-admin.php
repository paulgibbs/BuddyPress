<?php

/**
 * Main BuddyPress Admin Class
 *
 * @package BuddyPress
 * @subpackage CoreAdministration
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'BP_Admin' ) ) :
/**
 * Loads BuddyPress plugin admin area
 *
 * @package BuddyPress
 * @subpackage CoreAdministration
 * @since BuddyPress (1.6)
 */
class BP_Admin {

	/** Directory *************************************************************/

	/**
	 * @var string Path to the BuddyPress admin directory
	 */
	public $admin_dir = '';

	/** URLs ******************************************************************/

	/**
	 * @var string URL to the BuddyPress admin directory
	 */
	public $admin_url = '';

	/**
	 * @var string URL to the BuddyPress images directory
	 */
	public $images_url = '';

	/**
	 * @var string URL to the BuddyPress admin CSS directory
	 */
	public $css_url = '';

	/**
	 * @var string URL to the BuddyPress admin JS directory
	 */
	public $js_url = '';


	/** Methods ***************************************************************/

	/**
	 * The main BuddyPress admin loader
	 *
	 * @since BuddyPress (1.6)
	 *
	 * @uses BP_Admin::setup_globals() Setup the globals needed
	 * @uses BP_Admin::includes() Include the required files
	 * @uses BP_Admin::setup_actions() Setup the hooks and actions
	 */
	public function __construct() {
		$this->setup_globals();
		$this->includes();
		$this->setup_actions();
	}

	/**
	 * Admin globals
	 *
	 * @since BuddyPress (1.6)
	 * @access private
	 */
	private function setup_globals() {
		$bp               = buddypress();
		$this->admin_dir  = trailingslashit( $bp->plugin_dir  . 'bp-core/admin' ); // Admin path
		$this->admin_url  = trailingslashit( $bp->plugin_url  . 'bp-core/admin' ); // Admin url
		$this->images_url = trailingslashit( $this->admin_url . 'images'        ); // Admin images URL
		$this->css_url    = trailingslashit( $this->admin_url . 'css'           ); // Admin css URL
		$this->js_url     = trailingslashit( $this->admin_url . 'js'            ); // Admin css URL
	}

	/**
	 * Include required files
	 *
	 * @since BuddyPress (1.6)
	 * @access private
	 */
	private function includes() {
		require( $this->admin_dir . 'bp-core-actions.php'    );
		require( $this->admin_dir . 'bp-core-settings.php'   );
		require( $this->admin_dir . 'bp-core-functions.php'  );
		require( $this->admin_dir . 'bp-core-components.php' );
		require( $this->admin_dir . 'bp-core-slugs.php'      );
	}

	/**
	 * Setup the admin hooks, actions and filters
	 *
	 * @since BuddyPress (1.6)
	 * @access private
	 *
	 * @uses add_action() To add various actions
	 * @uses add_filter() To add various filters
	 */
	private function setup_actions() {

		/** General Actions ***************************************************/

		// Add some page specific output to the <head>
		add_action( 'bp_admin_head',            array( $this, 'admin_head'  ), 999 );

		// Add menu item to settings menu
		add_action( bp_core_admin_hook(),       array( $this, 'admin_menus' ), 5 );

		// Enqueue all admin JS and CSS
		add_action( 'bp_admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/** BuddyPress Actions ************************************************/

		// Add settings
		add_action( 'bp_register_admin_settings', array( $this, 'register_admin_settings' ) );

		/** Filters ***********************************************************/

		// Add link to settings page
		add_filter( 'plugin_action_links', array( $this, 'modify_plugin_action_links' ), 10, 2 );
	}

	/**
	 * Add the navigational menu elements
	 *
	 * @since BuddyPress (1.6)
	 *
	 * @uses add_management_page() To add the Recount page in Tools section
	 * @uses add_options_page() To add the Forums settings page in Settings
	 *                           section
	 */
	public function admin_menus() {

		// Bail if user cannot moderate
		if ( ! bp_current_user_can( 'manage_options' ) )
			return;

		// About
		add_dashboard_page(
			__( 'Welcome to BuddyPress',  'buddypress' ),
			__( 'Welcome to BuddyPress',  'buddypress' ),
			'manage_options',
			'bp-about',
			array( $this, 'about_screen' )
		);

		// Credits
		add_dashboard_page(
			__( 'Welcome to BuddyPress',  'buddypress' ),
			__( 'Welcome to BuddyPress',  'buddypress' ),
			'manage_options',
			'bp-credits',
			array( $this, 'credits_screen' )
		);

		$hooks = array();
		$page  = bp_core_do_network_admin()  ? 'settings.php' : 'options-general.php';

		// Changed in BP 1.6 . See bp_core_admin_backpat_menu()
		$hooks[] = add_menu_page(
			__( 'BuddyPress', 'buddypress' ),
			__( 'BuddyPress', 'buddypress' ),
			'manage_options',
			'bp-general-settings',
			'bp_core_admin_backpat_menu',
			'div'
		);

		$hooks[] = add_submenu_page(
			'bp-general-settings',
			__( 'BuddyPress Help', 'buddypress' ),
			__( 'Help', 'buddypress' ),
			'manage_options',
			'bp-general-settings',
			'bp_core_admin_backpat_page'
		);

		// Add the option pages
		$hooks[] = add_submenu_page(
			$page,
			__( 'BuddyPress Components', 'buddypress' ),
			__( 'BuddyPress', 'buddypress' ),
			'manage_options',
			'bp-components',
			'bp_core_admin_components_settings'
		);

		$hooks[] = add_submenu_page(
			$page,
			__( 'BuddyPress Pages', 'buddypress' ),
			__( 'BuddyPress Pages', 'buddypress' ),
			'manage_options',
			'bp-page-settings',
			'bp_core_admin_slugs_settings'
		);

		$hooks[] = add_submenu_page(
			$page,
			__( 'BuddyPress Settings', 'buddypress' ),
			__( 'BuddyPress Settings', 'buddypress' ),
			'manage_options',
			'bp-settings',
			'bp_core_admin_settings'
		);

		// Fudge the highlighted subnav item when on a BuddyPress admin page
		foreach( $hooks as $hook ) {
			add_action( "admin_head-$hook", 'bp_core_modify_admin_menu_highlight' );
		}
	}

	/**
	 * Register the settings
	 *
	 * @since BuddyPress (1.6)
	 *
	 * @uses add_settings_section() To add our own settings section
	 * @uses add_settings_field() To add various settings fields
	 * @uses register_setting() To register various settings
	 */
	public function register_admin_settings() {

		/** Main Section ******************************************************/

		// Add the main section
		add_settings_section( 'bp_main',            __( 'Main Settings',    'buddypress' ), 'bp_admin_setting_callback_main_section',     'buddypress'            );

		// Hide toolbar for logged out users setting
		add_settings_field( 'hide-loggedout-adminbar',        __( 'Toolbar',        'buddypress' ), 'bp_admin_setting_callback_admin_bar',        'buddypress', 'bp_main' );
	 	register_setting  ( 'buddypress',           'hide-loggedout-adminbar',        'intval'                                                                              );

		// Only show 'switch to Toolbar' option if the user chose to retain the BuddyBar during the 1.6 upgrade
		if ( (bool) bp_get_option( '_bp_force_buddybar', false ) ) {
			add_settings_field( '_bp_force_buddybar', __( 'Toolbar', 'buddypress' ), 'bp_admin_setting_callback_force_buddybar', 'buddypress', 'bp_main' );
		 	register_setting( 'buddypress', '_bp_force_buddybar', 'bp_admin_sanitize_callback_force_buddybar' );
		}

		// Allow account deletion
		add_settings_field( 'bp-disable-account-deletion', __( 'Account Deletion', 'buddypress' ), 'bp_admin_setting_callback_account_deletion', 'buddypress', 'bp_main' );
	 	register_setting  ( 'buddypress',           'bp-disable-account-deletion', 'intval'                                                                              );

		/** XProfile Section **************************************************/

		if ( bp_is_active( 'xprofile' ) ) {

			// Add the main section
			add_settings_section( 'bp_xprofile',      __( 'Profile Settings', 'buddypress' ), 'bp_admin_setting_callback_xprofile_section', 'buddypress'                );

			// Allow avatar uploads
			add_settings_field( 'bp-disable-avatar-uploads', __( 'Avatar Uploads',   'buddypress' ), 'bp_admin_setting_callback_avatar_uploads',   'buddypress', 'bp_xprofile' );
			register_setting  ( 'buddypress',         'bp-disable-avatar-uploads',   'intval'                                                                                  );

			// Profile sync setting
			add_settings_field( 'bp-disable-profile-sync',   __( 'Profile Syncing',  'buddypress' ), 'bp_admin_setting_callback_profile_sync',     'buddypress', 'bp_xprofile' );
			register_setting  ( 'buddypress',         'bp-disable-profile-sync',     'intval'                                                                                  );
		}

		/** Groups Section ****************************************************/

		if ( bp_is_active( 'groups' ) ) {

			// Add the main section
			add_settings_section( 'bp_groups',        __( 'Groups Settings',  'buddypress' ), 'bp_admin_setting_callback_groups_section',   'buddypress'              );

			// Allow subscriptions setting
			add_settings_field( 'bp_restrict_group_creation', __( 'Group Creation',   'buddypress' ), 'bp_admin_setting_callback_group_creation',   'buddypress', 'bp_groups' );
			register_setting  ( 'buddypress',         'bp_restrict_group_creation',   'intval'                                                                                );
		}

		/** Forums ************************************************************/

		if ( bp_is_active( 'forums' ) && bp_forums_is_installed_correctly() ) {

			// Add the main section
			add_settings_section( 'bp_forums',        __( 'Forums Settings',       'buddypress' ), 'bp_admin_setting_callback_bbpress_section',       'buddypress'              );

			// Allow subscriptions setting
			add_settings_field( 'bb-config-location', __( 'bbPress Configuration', 'buddypress' ), 'bp_admin_setting_callback_bbpress_configuration', 'buddypress', 'bp_forums' );
			register_setting  ( 'buddypress',         'bb-config-location',        ''                                                                                           );
		}

		/** Activity Section **************************************************/

		if ( bp_is_active( 'activity' ) ) {

			// Add the main section
			add_settings_section( 'bp_activity',      __( 'Activity Settings', 'buddypress' ), 'bp_admin_setting_callback_activity_section', 'buddypress'                );

			// Activity commenting on blog and forum posts
			add_settings_field( 'bp-disable-blogforum-comments', __( 'Blog &amp; Forum Comments', 'buddypress' ), 'bp_admin_setting_callback_blogforum_comments', 'buddypress', 'bp_activity' );
			register_setting( 'buddypress', 'bp-disable-blogforum-comments', 'bp_admin_sanitize_callback_blogforum_comments' );

			// Allow activity akismet
			if ( is_plugin_active( 'akismet/akismet.php' ) && defined( 'AKISMET_VERSION' ) ) {
				add_settings_field( '_bp_enable_akismet', __( 'Akismet',          'buddypress' ), 'bp_admin_setting_callback_activity_akismet', 'buddypress', 'bp_activity' );
				register_setting  ( 'buddypress',         '_bp_enable_akismet',   'intval'                                                                                  );
			}
		}
	}

	/**
	 * Add Settings link to plugins area
	 *
	 * @since BuddyPress (1.6)
	 *
	 * @param array $links Links array in which we would prepend our link
	 * @param string $file Current plugin basename
	 * @return array Processed links
	 */
	public function modify_plugin_action_links( $links, $file ) {

		// Return normal links if not BuddyPress
		if ( plugin_basename( buddypress()->file ) != $file )
			return $links;

		$page = 'bp-components';
		$text = __( 'Settings', 'buddypress' );
		$url  = bp_core_do_network_admin() ? network_admin_url( 'settings.php' ) : admin_url( 'options-general.php' );

		// Add a few links to the existing links array
		return array_merge( $links, array(
			'settings' => '<a href="' . add_query_arg( array( 'page' => $page      ), $url                      ) . '">' . $text                               . '</a>',
			'about'    => '<a href="' . add_query_arg( array( 'page' => 'bp-about' ), admin_url( 'index.php'  ) ) . '">' . esc_html__( 'About', 'buddypress' ) . '</a>'
		) );
	}

	/**
	 * Add some general styling to the admin area
	 *
	 * @since BuddyPress (1.6)
	 */
	public function admin_head() {
		$settings_page  = bp_core_do_network_admin()  ? 'settings.php' : 'options-general.php';

		// Settings pages
		remove_submenu_page( $settings_page, 'bb-forums-setup'  );
		remove_submenu_page( $settings_page, 'bp-page-settings' );
		remove_submenu_page( $settings_page, 'bp-settings'      );

		// About and Credits pages
		remove_submenu_page( 'index.php', 'bp-about'   );
		remove_submenu_page( 'index.php', 'bp-credits' );
	}

	/**
	 * Add some general styling to the admin area
	 *
	 * @since BuddyPress (1.6)
	 */
	public function enqueue_scripts() {

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$file = $this->css_url . "common{$min}.css";
		$file = apply_filters( 'bp_core_admin_common_css', $file );
		wp_enqueue_style( 'bp-admin-common-css', $file, array(), bp_get_version() );
	}
	
	/** About *****************************************************************/

	/**
	 * Output the about screen
	 *
	 * @since BuddyPress (1.7)
	 */
	public function about_screen() {

		list( $display_version ) = explode( '-', bp_get_version() ); ?>

		<div class="wrap about-wrap">
			<h1><?php printf( __( 'Welcome to BuddyPress %s' ), $display_version ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! BuddyPress %s is ready to make your community a safer, faster, and better looking place to hang out!' ), $display_version ); ?></div>
			<div class="bp-badge"><?php printf( __( 'Version %s' ), $display_version ); ?></div>

			<h2 class="nav-tab-wrapper">
				<a class="nav-tab nav-tab-active" href="<?php echo esc_url( bp_get_admin_url( add_query_arg( array( 'page' => 'bp-about' ), 'index.php' ) ) ); ?>">
					<?php _e( 'What&#8217;s New' ); ?>
				</a><a class="nav-tab" href="<?php echo esc_url( bp_get_admin_url( add_query_arg( array( 'page' => 'bp-credits' ), 'index.php' ) ) ); ?>">
					<?php _e( 'Credits' ); ?>
				</a>
			</h2>

			<div class="changelog">
				<h3><?php _e( 'Some neat thing', 'buddypress' ); ?></h3>

				<div class="feature-section">
					<h4><?php _e( 'Whoa', 'buddypress' ); ?></h4>
					<p><?php _e( 'Nice!', 'buddypress' ); ?></p>

					<h4><?php _e( 'Wow', 'buddypress' ); ?></h4>
					<p><?php _e( 'Amazing!', 'buddypress' ); ?></p>
				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Another neat thing', 'buddypress' ); ?></h3>

				<div class="feature-section">
					<h4><?php _e( 'No way', 'buddypress' ); ?></h4>
					<p><?php _e( 'Yes way.', 'buddypress' ); ?></p>
				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Under the Hood', 'buddypress' ); ?></h3>

				<div class="feature-section three-col">
					<div>
						<h4><?php _e( 'One', 'buddypress' ); ?></h4>
						<p><?php _e( 'Uh huh.', 'buddypress' ); ?></p> 

						<h4><?php _e( 'Two', 'buddypress' ); ?></h4>
						<p><?php _e( 'Yeah.', 'buddypress' ); ?></p>
					</div>

					<div>
						<h4><?php _e( 'Three', 'buddypress' ); ?></h4>
						<p><?php _e( 'I hear ya.', 'buddypress' ); ?></p>

						<h4><?php _e( 'Four', 'buddypress' ); ?></h4>
						<p><?php _e( 'Tell me more.', 'buddypress' ); ?></p>
					</div>

					<div class="last-feature">
						<h4><?php _e( 'Five', 'buddypress' ); ?></h4>
						<p><?php _e( 'Well, shucks.', 'buddypress' ); ?></p>

						<h4><?php _e( 'Six', 'buddypress' ); ?></h4>
						<p><?php _e( ' Whoopie!', 'buddypress' ); ?></p>
					</div>
				</div>
			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( bp_get_admin_url( add_query_arg( array( 'page' => 'buddypress' ), 'options-general.php' ) ) ); ?>"><?php _e( 'Go to Community Settings' ); ?></a>
			</div>

		</div>

		<?php
	}

	/**
	 * Output the credits screen
	 *
	 * Hardcoding this in here is pretty janky. It's fine for 2.2, but we'll
	 * want to leverage api.wordpress.org eventually.
	 *
	 * @since BuddyPress (1.7)
	 */
	public function credits_screen() {

		list( $display_version ) = explode( '-', bp_get_version() ); ?>

		<div class="wrap about-wrap">
			<h1><?php printf( __( 'Welcome to BuddyPress %s' ), $display_version ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! BuddyPress %s is ready to make your community a safer, faster, and better looking place to hang out!' ), $display_version ); ?></div>
			<div class="bp-badge"><?php printf( __( 'Version %s' ), $display_version ); ?></div>

			<h2 class="nav-tab-wrapper">
				<a href="<?php echo esc_url( bp_get_admin_url( add_query_arg( array( 'page' => 'bp-about' ), 'index.php' ) ) ); ?>" class="nav-tab">
					<?php _e( 'What&#8217;s New' ); ?>
				</a><a href="<?php echo esc_url( bp_get_admin_url( add_query_arg( array( 'page' => 'bp-credits' ), 'index.php' ) ) ); ?>" class="nav-tab nav-tab-active">
					<?php _e( 'Credits' ); ?>
				</a>
			</h2>

			<p class="about-description"><?php _e( 'BuddyPress is created by a worldwide network of friendly folks.', 'buddypress' ); ?></p>

			<h4 class="wp-people-group"><?php _e( 'Project Leaders', 'buddypress' ); ?></h4>
			<ul class="wp-people-group " id="wp-people-group-project-leaders">
				<li class="wp-person" id="wp-person-apeatling">
					<a href="http://profiles.wordpress.org/apeatling"><img src="http://0.gravatar.com/avatar/767fc9c115a1b989744c755db47feb60?s=60" class="gravatar" alt="Andy Peatling" /></a>
					<a class="web" href="http://profiles.wordpress.org/apeatling">Andy Peatling</a>
					<span class="title"><?php _e( 'Founding Developer', 'buddypress' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-johnjamesjacoby">
					<a href="http://profiles.wordpress.org/johnjamesjacoby"><img src="http://0.gravatar.com/avatar/81ec16063d89b162d55efe72165c105f?s=60" class="gravatar" alt="John James Jacoby" /></a>
					<a class="web" href="http://profiles.wordpress.org/johnjamesjacoby">John James Jacoby</a>
					<span class="title"><?php _e( 'Project Lead', 'buddypress' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-boonebgorges">
					<a href="http://profiles.wordpress.org/boonebgorges"><img src="http://0.gravatar.com/avatar/6a7c997edea340616bcc6d0fe03f65dd?s=60" class="gravatar" alt="Boone B. Gorges" /></a>
					<a class="web" href="http://profiles.wordpress.org/boonebgorges">Boone B. Gorges</a>
					<span class="title"><?php _e( 'Lead Developer', 'buddypress' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-djpaul">
					<a href="http://profiles.wordpress.org/djpaul"><img src="http://0.gravatar.com/avatar/e341eca9e1a85dcae7127044301b4363?s=60" class="gravatar" alt="Paul Gibbs" /></a>
					<a class="web" href="http://profiles.wordpress.org/djpaul">Paul Gibbs</a>
					<span class="title"><?php _e( 'Lead Developer', 'buddypress' ); ?></span>
				</li>
			</ul>

			<h4 class="wp-people-group"><?php _e( 'Contributing Developers', 'buddypress' ); ?></h4>
			<ul class="wp-people-group " id="wp-people-group-contributing-developers">
				<li class="wp-person" id="wp-person-jmdodd">
					<a href="http://profiles.wordpress.org/jmdodd"><img src="http://0.gravatar.com/avatar/6a7c997edea340616bcc6d0fe03f65dd?s=60" class="gravatar" alt="Jennifer M. Dodd" /></a>
					<a class="web" href="http://profiles.wordpress.org/jmdodd">Jennifer M. Dodd</a>
					<span class="title"></span>
				</li>
				<li class="wp-person" id="wp-person-jaredatch">
					<a href="http://profiles.wordpress.org/jaredatch"><img src="http://0.gravatar.com/avatar/e341eca9e1a85dcae7127044301b4363?s=60" class="gravatar" alt="Jared Atchison" /></a>
					<a class="web" href="http://profiles.wordpress.org/jaredatch">Jared Atchison</a>
					<span class="title"></span>
				</li>
				<li class="wp-person" id="wp-person-gautamgupta">
					<a href="http://profiles.wordpress.org/gautamgupta"><img src="http://0.gravatar.com/avatar/b0810422cbe6e4eead4def5ae7a90b34?s=60" class="gravatar" alt="Gautam Gupta" /></a>
					<a class="web" href="http://profiles.wordpress.org/gautamgupta">Gautam Gupta</a>
					<span class="title"></span>
				</li>
			</ul>

			<h4 class="wp-people-group"><?php _e( 'Codex Rockstars', 'buddypress' ); ?></h4>
			<ul class="wp-people-group " id="wp-people-group-codex-rockstars">
				<li class="wp-person" id="wp-person-masonjames">
					<a href="http://profiles.wordpress.org/masonjames"><img src="http://0.gravatar.com/avatar/99dee4d5287d0f9e26ff72e7228d97ac?s=60" class="gravatar" alt="Mason James" /></a>
					<a class="web" href="http://profiles.wordpress.org/masonjames">Mason James</a>
					<span class="title"></span>
				</li>
				<li class="wp-person" id="wp-person-wordsforwp">
					<a href="http://profiles.wordpress.org/wordsforwp"><img src="http://0.gravatar.com/avatar/5437119b446adad1af813c44944e6c9c?s=60" class="gravatar" alt="Siobhan McKeown" /></a>
					<a class="web" href="http://profiles.wordpress.org/wordsforwp">Siobhan McKeown</a>
					<span class="title"></span>
				</li>
				<li class="wp-person" id="wp-person-JarretC">
					<a href="http://profiles.wordpress.org/JarretC"><img src="http://0.gravatar.com/avatar/e00501bf782b42d5db19ff75fca14f6a?s=60" class="gravatar" alt="Jarret Cade" /></a>
					<a class="web" href="http://profiles.wordpress.org/JarretC">Jarret Cade</a>
					<span class="title"></span>
				</li>
			</ul>

			<h4 class="wp-people-group"><?php _e( 'Core Contributors to BuddyPress 1.7', 'buddypress' ); ?></h4>
			<p class="wp-credits-list">
				<a href="http://profiles.wordpress.org/alexvorn2">alexvorn2</a>,
				<a href="http://profiles.wordpress.org/anointed">anointed</a>,
				<a href="http://profiles.wordpress.org/chexee">chexee</a>,
				<a href="http://profiles.wordpress.org/cnorris23">cnorris23</a>,
				<a href="http://profiles.wordpress.org/DanielJuhl">DanielJuhl</a>,
				<a href="http://profiles.wordpress.org/daveshine">daveshine</a>,
				<a href="http://profiles.wordpress.org/dimadin">dimadin</a>,
				<a href="http://profiles.wordpress.org/gawain">gawain</a>,
				<a href="http://profiles.wordpress.org/iamzippy">iamzippy</a>,
				<a href="http://profiles.wordpress.org/isaacchapman">isaacchapman</a>,
				<a href="http://profiles.wordpress.org/jane">jane</a>,
				<a href="http://profiles.wordpress.org/jkudish">jkudish</a>,
				<a href="http://profiles.wordpress.org/mamaduka">mamaduka</a>,
				<a href="http://profiles.wordpress.org/mercime">mercime</a>,
				<a href="http://profiles.wordpress.org/mesayre">mesayre</a>,
				<a href="http://profiles.wordpress.org/mordauk">mordauk</a>,
				<a href="http://profiles.wordpress.org/MZAWeb">MZAWeb</a>,
				<a href="http://profiles.wordpress.org/netweb">netweb</a>,
				<a href="http://profiles.wordpress.org/nexia">nexia</a>,
				<a href="http://profiles.wordpress.org/Omicron7">Omicron7</a>,
				<a href="http://profiles.wordpress.org/otto42">otto42</a>,
				<a href="http://profiles.wordpress.org/pavelevap">pavelevap</a>,
				<a href="http://profiles.wordpress.org/plescheff">plescheff</a>,
				<a href="http://profiles.wordpress.org/scribu">scribu</a>,
				<a href="http://profiles.wordpress.org/sorich87">sorich87</a>,
				<a href="http://profiles.wordpress.org/SteveAtty">SteveAtty</a>,
				<a href="http://profiles.wordpress.org/tmoorewp">tmoorewp</a>,
				<a href="http://profiles.wordpress.org/tott">tott</a>,
				<a href="http://profiles.wordpress.org/tungdo">tungdo</a>,
				<a href="http://profiles.wordpress.org/vibol">vibol</a>,
				<a href="http://profiles.wordpress.org/wonderboymusic">wonderboymusic</a>,
				<a href="http://profiles.wordpress.org/westi">westi</a>,
				<a href="http://profiles.wordpress.org/xiosen">xiosen</a>,
			</p>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( bp_get_admin_url( add_query_arg( array( 'page' => 'buddypress' ), 'options-general.php' ) ) ); ?>"><?php _e( 'Go to Community Settings' ); ?></a>
			</div>

		</div>

		<?php
	}
}
endif; // class_exists check

/**
 * Setup BuddyPress Admin
 *
 * @since BuddyPress (1.6)
 *
 * @uses BP_Admin
 */
function bp_admin() {
	buddypress()->admin = new BP_Admin();
}
