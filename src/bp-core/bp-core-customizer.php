<?php
/**
 * BuddyPress Email Templates Customizer.
 *
 * @package BuddyPress
 * @subpackage Core
 * @since 2.4.0?
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Set up the actions needed for customizer only if we need it
 */
function bp_core_add_email_customizer_actions() {

	if( !isset( $_GET['bp_email_template'] ) )
		return;

	add_action( 'customize_controls_enqueue_scripts',  'bp_core_customizer_enqueue_scripts' );
	add_action( 'customize_preview_init',  'bp_core_customizer_enqueue_template_scripts', 99 );
	add_action( 'bp_init', 'bp_core_customizer_remove_all_actions', 99 );

}
add_action( 'bp_init', 'bp_core_add_email_customizer_actions', 10 );

/**
 * Admin js needed for customizer
 */
function bp_core_customizer_enqueue_scripts() {
	wp_enqueue_script( 'bp-customizer-admin' );
}
/**
 * Front js needed for customizer
 */
function bp_core_customizer_enqueue_template_scripts(){
	wp_enqueue_script( 'bp-customizer-front' );
	//TODO wp_enqueue_style( 'bp_mailtpl-css', $bp->plugin_url . '/admin/css/bp_mailtpl-admin.css', '', $this->version, false );
}

/**
 * If we are about to display our email template strip everything out and leave it clean
 */
function bp_core_customizer_remove_all_actions() {
	global $wp_scripts, $wp_styles;

	$exceptions = array(
		'bp-customizer-front',
		'jquery',
		'query-monitor',
		'bp-customizer-admin',
		'customize-preview',
		'customize-controls',
	);

	if ( is_object( $wp_scripts ) && isset( $wp_scripts->queue ) && is_array( $wp_scripts->queue ) ) {
		foreach( $wp_scripts->queue as $handle ){
			if( in_array($handle, $exceptions))
				continue;
			wp_dequeue_script($handle);
		}
	}

	if ( is_object( $wp_styles ) && isset( $wp_styles->queue ) && is_array( $wp_styles->queue ) ) {
		foreach( $wp_styles->queue as $handle ){
			if( in_array($handle, $exceptions) )
				continue;
			wp_dequeue_style($handle);
		}
	}

	// Now remove actions
	$action_exceptions = array(
		'wp_print_footer_scripts',
		'wp_admin_bar_render',
	);

	// No core action in header
	remove_all_actions('wp_header');

	global $wp_filter;
	foreach( $wp_filter['wp_footer'] as $priority => $handle ){
		if( in_array( key($handle), $action_exceptions ) )
			continue;
		unset( $wp_filter['wp_footer'][$priority] );
	}
}

/**
 * Register all needed section for our Email Templates
 * @param WP_Customize_Manager $wp_customize  manager
 */
function bp_core_customizer_register_sections( WP_Customize_Manager $wp_customize ) {

	$wp_customize->add_panel( 'bp_mailtpl', array(
		'title'         => __( 'Email Templates', 'buddypress' ),
		'description'   => __( 'Customize the look of your BuddyPress emails', 'buddypress' ),
	) );

	$sections = bp_core_customizer_get_sections();

	// Add sections
	foreach( $sections as $section_id => $args ) {
		$wp_customize->add_section( $section_id, $args );
	}

	do_action('bp_core_customizer_register_sections', $wp_customize, $sections );

	$settings = bp_core_customizer_get_settings();

	// Add settings
	foreach( $settings as $setting_id => $args ) {
		$wp_customize->add_setting( $setting_id, $args );
	}

	$controls = bp_core_customizer_get_controls();

	// Require custom classes
	require dirname( __FILE__ ) . '/classes/class-font-size-customize-control.php';
	require dirname( __FILE__ ) . '/classes/class-send-mail-customize-control.php';
	// Add controls
	foreach( $controls as $control_id => $args ) {
		$wp_customize->add_control( new $args['class']( $wp_customize, $control_id, $args ) );
	}
}
add_action( 'customize_register', 'bp_core_customizer_register_sections' );

/**
 * We are not editing the normal front end so we remove other customizer sections.
 * @param $active  Whether the Customizer section is active.
 * @param WP_Customize_Section $section {@see WP_Customize_Section} instance.
 *
 * @return bool
 */
function bp_core_customizer_remove_sections( $active, $section ){
	if ( isset( $_GET['bp_email_template'] ) ) {

		if ( in_array( $section->id, array_keys( bp_core_customizer_get_sections() ) ) )
			return true;

		return false;
	}
	return true;
}
add_action( 'customize_section_active', 'bp_core_customizer_remove_sections', 10, 2 );

/**
 * Define available sections for the customizer
 * @return Array
 */
function bp_core_customizer_get_sections() {
	$sections = array(
			'section_bp_mailtpl_settings'   => array(
				'title' => __( 'Settings', 'buddypress' ),
				'panel' => 'bp_mailtpl',
			),
			'section_bp_mailtpl_template'   => array(
				'title' => __( 'Template', 'buddypress' ),
				'panel' => 'bp_mailtpl',
			),
			'section_bp_mailtpl_header'     => array(
				'title' => __( 'Email Header', 'buddypress' ),
				'panel' => 'bp_mailtpl',
			),
			'section_bp_mailtpl_body'       => array(
				'title' => __( 'Email Body', 'buddypress' ),
				'panel' => 'bp_mailtpl',
			),
			'section_bp_mailtpl_footer'     => array(
				'title' => __( 'Footer', 'buddypress' ),
				'panel' => 'bp_mailtpl',
			),
			'section_bp_mailtpl_test'       =>  array(
				'title' => __( 'Send test email', 'buddypress' ),
				'panel' => 'bp_mailtpl',
			)
	);

	return apply_filters( 'bp_core_customizer_get_sections', $sections );
}

/**
 * Define available settings for the customizer
 * @return Array
 */
function bp_core_customizer_get_settings() {

	$defaults = bp_core_customizer_get_defaults();

	$settings = array(
		'bp_mailtpl_opts[from_name]'   => array(
			'type'                  => 'option',
			'default'               => $defaults['from_name'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'sanitize_text_field',
			'sanitize_js_callback'  => '',
		),
		'bp_mailtpl_opts[from_email]'  => array(
			'type'                  => 'option',
			'default'               => $defaults['from_email'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'sanitize_text_field',
			'sanitize_js_callback'  => '',
		),
		'bp_mailtpl_opts[template]'    => array(
			'type'                  => 'option',
			'default'               => $defaults['template'],
			'transport'             => 'refresh',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'bp_sanitize_customizer_templates',
			'sanitize_js_callback'  => '',
		),
		'bp_mailtpl_opts[body_bg]'     => array(
			'type'                  => 'option',
			'default'               => $defaults['body_bg'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'sanitize_hex_color',
			'sanitize_js_callback'  => '',
		),
		'bp_mailtpl_opts[header_logo]' =>  array(
			'type'                  => 'option',
			'default'               => '',
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => '',
			'sanitize_js_callback'  => '',
		),
		'bp_mailtpl_opts[header_logo_text]'    => array(
			'type'                  => 'option',
			'default'               => '',
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'bp_sanitize_customizer_text',
			'sanitize_js_callback'  => '',
		),
		'bp_mailtpl_opts[header_aligment]'     => array(
			'type'                  => 'option',
			'default'               => $defaults['header_aligment'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'bp_sanitize_customizer_alignment',
			'sanitize_js_callback'  => '',
		),
		'bp_mailtpl_opts[header_bg]'           => array(
			'type'                  => 'option',
			'default'               => $defaults['header_bg'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'sanitize_hex_color',
			'sanitize_js_callback'  => '',
		),
		'bp_mailtpl_opts[header_text_size]'    => array(
			'type'                  => 'option',
			'default'               => $defaults['header_text_size'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'bp_sanitize_customizer_text',
			'sanitize_js_callback'  => '',
		),
		'bp_mailtpl_opts[header_text_color]'   =>  array(
			'type'                  => 'option',
			'default'               => $defaults['header_text_color'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'sanitize_hex_color',
			'sanitize_js_callback'  => '',
		),
		'bp_mailtpl_opts[email_body_bg]'       => array(
			'type'                  => 'option',
			'default'               => $defaults['email_body_bg'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'sanitize_hex_color',
			'sanitize_js_callback'  => '',
		),
		'bp_mailtpl_opts[body_text_size]'      => array(
			'type'                  => 'option',
			'default'               => $defaults['body_text_size'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'bp_sanitize_customizer_text',
			'sanitize_js_callback'  => '',
		),
		'bp_mailtpl_opts[body_text_color]'     => array(
			'type'                  => 'option',
			'default'               => $defaults['body_text_color'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'sanitize_hex_color',
			'sanitize_js_callback'  => '',
		),
		'bp_mailtpl_opts[footer_text]'         =>  array(
			'type'                  => 'option',
			'default'               => $defaults['footer_text'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'bp_sanitize_customizer_text',
			'sanitize_js_callback'  => '',
		),
		'bp_mailtpl_opts[footer_aligment]'     => array(
			'type'                  => 'option',
			'default'               => $defaults['footer_aligment'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'bp_sanitize_customizer_alignment',
			'sanitize_js_callback'  => '',
		),
		'bp_mailtpl_opts[footer_bg]'           => array(
			'type'                  => 'option',
			'default'               => $defaults['footer_bg'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'sanitize_hex_color',
			'sanitize_js_callback'  => '',
		),
		'bp_mailtpl_opts[footer_text_size]'    => array(
			'type'                  => 'option',
			'default'               => $defaults['footer_text_size'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'bp_sanitize_customizer_text',
			'sanitize_js_callback'  => '',
		),
		'bp_mailtpl_opts[footer_text_color]'   => array(
			'type'                  => 'option',
			'default'               => $defaults['footer_text_color'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'sanitize_hex_color',
			'sanitize_js_callback'  => '',
		)
	);

	return apply_filters( 'bp_core_customizer_get_settings', $settings );
}


function bp_core_customizer_get_controls() {
	$controls = array(
		'bp_mailtpl_template'          => array(
			'class'         => 'WP_Customize_Control',
			'label'         => __( 'Choose one', 'buddypress' ),
			'type'          => 'select',
			'section'       => 'section_bp_mailtpl_template',
			'settings'      => 'bp_mailtpl_opts[template]',
			'choices'       => apply_filters( 'bp_mailtpl/template_choices', array(
				'boxed'    => 'Boxed',
				'fullwidth' => 'Fullwidth'
			)),
			'description'   => ''
		),
		'bp_mailtpl_body_bg'           => array(
			'class'         => 'WP_Customize_Color_Control',
			'label'         => __( 'Background Color', 'buddypress' ),
			'section'       => 'section_bp_mailtpl_template',
			'settings'      => 'bp_mailtpl_opts[body_bg]',
			'description'   => __( 'Choose email background color', 'buddypress' )
		),
		'bp_mailtpl_header'            => array(
			'class'         => 'WP_Customize_Image_Control',
			'label'         => __( 'Logo', 'buddypress' ),
			'type'          => 'image',
			'section'       => 'section_bp_mailtpl_header',
			'settings'      => 'bp_mailtpl_opts[header_logo]',
			'description'   => __( 'Add an image to use in header. Leave empty to use text instead', 'buddypress' )
		),
		'bp_mailtpl_header_logo_text'  => array(
			'class'         => 'WP_Customize_Control',
			'label'         => __( 'Logo', 'buddypress' ),
			'type'          => 'textarea',
			'section'       => 'section_bp_mailtpl_header',
			'settings'      => 'bp_mailtpl_opts[header_logo_text]',
			'description'   => __( 'Add text to your mail header', 'buddypress' )
		),
		'bp_mailtpl_aligment'          => array(
			'class'         => 'WP_Customize_Control',
			'label'         => __( 'Aligment', 'buddypress' ),
			'type'          => 'select',
			'default'       => 'center',
			'choices'       => array(
				'left'  => __( 'Left', 'buddypress' ),
				'center'=> __( 'Center', 'buddypress' ),
				'right' => __( 'Right', 'buddypress' )
			),
			'section'       => 'section_bp_mailtpl_header',
			'settings'      => 'bp_mailtpl_opts[header_aligment]',
			'description'   => __( 'Choose alignment for header', 'buddypress' )
		),
		'bp_mailtpl_header_bg'         => array(
			'class'         => 'WP_Customize_Image_Control',
			'label'         => __( 'Background Color', 'buddypress' ),
			'section'       => 'section_bp_mailtpl_header',
			'settings'      => 'bp_mailtpl_opts[header_bg]',
			'description'   => __( 'Choose header background color', 'buddypress' )
		),
		'bp_mailtpl_header_text_size'  => array(
			'class'         => 'WP_Font_Size_Customize_Control',
			'label'         => __( 'Text size', 'buddypress' ),
			'type'          => 'bp_mailtpl_send_mail',
			'section'       => 'section_bp_mailtpl_header',
			'settings'      => 'bp_mailtpl_opts[header_text_size]',
			'description'   => __( 'Slide to change text size', 'buddypress' )
		),
		'bp_mailtpl_header_text_color' => array(
			'class'         => 'WP_Customize_Color_Control',
			'label'         => __( 'Text Color', 'buddypress' ),
			'section'       => 'section_bp_mailtpl_header',
			'settings'      => 'bp_mailtpl_opts[header_text_color]',
			'description'   => __( 'Choose header text color', 'buddypress' )
		),
		'bp_mailtpl_email_body_bg'     => array(
			'class'         => 'WP_Customize_Color_Control',
			'label'         => __( 'Background Color', 'buddypress' ),
			'section'       => 'section_bp_mailtpl_body',
			'settings'      => 'bp_mailtpl_opts[email_body_bg]',
			'description'   => __( 'Choose email body background color', 'buddypress' )
		),
		'bp_mailtpl_body_text_size'    => array(
			'class'         => 'WP_Font_Size_Customize_Control',
			'label'         => __( 'Text size', 'buddypress' ),
			'type'          => 'bp_mailtpl_send_mail',
			'section'       => 'section_bp_mailtpl_body',
			'settings'      => 'bp_mailtpl_opts[body_text_size]',
			'description'   => __( 'Slide to change text size', 'buddypress' )
		),
		'bp_mailtpl_body_text_color'   => array(
			'class'         => 'WP_Customize_Color_Control',
			'label'         => __( 'Text Color', 'buddypress' ),
			'section'       => 'section_bp_mailtpl_body',
			'settings'      => 'bp_mailtpl_opts[body_text_color]',
			'description'   => __( 'Choose body text color', 'buddypress' )
		),
		'bp_mailtpl_footer'            => array(
			'class'         => 'WP_Customize_Control',
			'label'     => __( 'Footer text', 'buddypress' ),
			'type'      => 'textarea',
			'section'   => 'section_bp_mailtpl_footer',
			'settings'  => 'bp_mailtpl_opts[footer_text]',
			'description'   => __('Change the email footer here', 'buddypress' )
		),
		'bp_mailtpl_footer_aligment'   =>   array(
			'class'         => 'WP_Customize_Control',
			'label'         => __( 'Aligment', 'buddypress' ),
			'type'          => 'select',
			'default'       => 'center',
			'choices'       => array(
				'left'  => __( 'Left', 'buddypress' ),
				'center'=> __( 'Center', 'buddypress' ),
				'right' => __( 'Right', 'buddypress' )
			),
			'section'       => 'section_bp_mailtpl_footer',
			'settings'      => 'bp_mailtpl_opts[footer_aligment]',
			'description'   => __( 'Choose alignment for footer', 'buddypress' )
		),
		'bp_mailtpl_footer_bg'         => array(
			'class'         => 'WP_Customize_Color_Control',
			'label'         => __( 'Background Color', 'buddypress' ),
			'section'       => 'section_bp_mailtpl_footer',
			'settings'      => 'bp_mailtpl_opts[footer_bg]',
			'description'   => __( 'Choose footer background color', 'buddypress' )
		),
		'bp_mailtpl_footer_text_size'  => array(
			'class'         => 'WP_Font_Size_Customize_Control',
			'label'         => __( 'Text size', 'buddypress' ),
			'type'          => 'bp_mailtpl_send_mail',
			'section'       => 'section_bp_mailtpl_footer',
			'settings'      => 'bp_mailtpl_opts[footer_text_size]',
			'description'   => __( 'Slide to change text size', 'buddypress' )
		),
		'bp_mailtpl_footer_text_color' => array(
			'class'         => 'WP_Customize_Color_Control',
			'label'         => __( 'Text Color', 'buddypress' ),
			'section'       => 'section_bp_mailtpl_footer',
			'settings'      => 'bp_mailtpl_opts[footer_text_color]',
			'description'   => __( 'Choose footer text color', 'buddypress' )
		)
	);

	return apply_filters( 'bp_core_customizer_get_controls', $controls );
}

/**
 * Define defaults settings for templates
 * @return array
 */
function bp_core_customizer_get_defaults() {
	$defaults = array(
		'from_name'         => get_bloginfo('name'),
		'from_email'        => get_bloginfo('admin_email'),
		'template'          => 'boxed',
		'body_bg'           => '#e3e3e3',
		'footer_text'       => '&copy;'.date('Y').' ' .get_bloginfo('name'),
		'footer_aligment'   => 'center',
		'footer_bg'         => '#eee',
		'footer_text_size'  => '12',
		'footer_text_color' => '#777',
		'header_aligment'   => 'center',
		'header_bg'         => '#454545',
		'header_text_size'  => '30',
		'header_text_color' => '#f1f1f1',
		'email_body_bg'     => '#fafafa',
		'body_text_size'    => '14',
		'body_text_color'   => '#888',
	);

	return apply_filters( 'bp_core_customizer_get_defaults', $defaults );
}

/**
 * Sanitize template select
 * @param $input string to sanitize
 *
 * @return string
 */
function bp_sanitize_customizer_templates( $input ) {
	$valid = apply_filters( 'bp_sanitize_customizer_templates',
		array(
			'boxed'     => __( 'Simple Theme', 'buddypress' ),
			'fullwidth' => __( 'Fullwidth', 'buddypress' )
		)
	);

	if ( array_key_exists( $input, $valid ) ) {
		return $input;
	}

	return '';
}

/**
 * We let them use some safe html
 * @param $input string to sanitize
 *
 * @return string
 */
function bp_sanitize_customizer_text( $input ) {
	return wp_kses_post( force_balance_tags( $input ) );
}

/**
 * Sanitize aligment selects
 * @param $input string to sanitize
 *
 * @return string
 */
function bp_sanitize_customizer_aligment( $input ) {
	$valid = array(
		'left',
		'right',
		'center',
	);

	if ( in_array( $input, $valid ) ) {
		return $input;
	} else {
		return '';
	}
}


function bp_core_customizer_get_template( $template ){

	if( is_customize_preview() && isset( $_GET['bp_email_template'] ) && 'true' == $_GET['bp_email_template'] ){
		return bp_locate_template( array( 'emails/bp-email.php', 'bp-email.php' ), false );
	}
	return $template;
}
add_action( 'template_include' , 'bp_core_customizer_get_template');