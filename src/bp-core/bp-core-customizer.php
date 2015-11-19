<?php
/**
 * BuddyPress Email Templates Customizer.
 *
 * @since 2.5.0
 *
 * @package BuddyPress
 * @subpackage Core
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Initialise Customizer scripts.
 *
 * @since 2.5.0
 */
function bp_core_add_email_customizer_actions() {
	if ( ! is_customize_preview() || ! isset( $_GET['bp_customizer'] ) || $_GET['bp_customizer'] !== 'email' ) ) {
		return;
	}

	add_action( 'customize_preview_init', 'bp_core_customizer_enqueue_template_scripts' );
}
add_action( 'bp_init', 'bp_core_add_email_customizer_actions', 10 );

/**
 * Customizer front-end scripts.
 *
 * @since 2.5.0
 */
function bp_core_customizer_enqueue_template_scripts() {
	// djpaultodo: This is called after wp_loaded (see WP_Customize_Manager->wp_loaded) which is way early before our script registration functions.
	$bp  = buddypress();
	$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$url = $bp->plugin_url . 'bp-core/js/../admin/js/';

	wp_enqueue_script( 'bp-customizer-emails', "{$url}customizer-emails{$min}.js", array( 'customize-preview' ), bp_get_version() );
}

/**
 * Register Customizer settings for Emails.
 *
 * @since 2.5.0
 *
 * @param WP_Customize_Manager $wp_customize
 */
function bp_core_customizer_register_sections( WP_Customize_Manager $wp_customize ) {
	$wp_customize->add_panel( 'bp_mailtpl', array(
		'description'   => __( 'Customize the look of your BuddyPress emails', 'buddypress' ),
		'title'         => __( 'Email Templates', 'buddypress' ),
	) );

	$sections = bp_core_customizer_get_sections();
	foreach( $sections as $section_id => $args ) {
		$wp_customize->add_section( $section_id, $args );
	}

	$settings = bp_core_customizer_get_settings();
	foreach( $settings as $setting_id => $args ) {
		$wp_customize->add_setting( $setting_id, $args );
	}

	require dirname( __FILE__ ) . '/classes/class-bp-customizer-control-range.php';
	do_action( 'bp_core_customizer_register_sections', $wp_customize, $sections );

	// Add controls
	$controls = bp_core_customizer_get_controls();
	foreach( $controls as $control_id => $args ) {
		$wp_customize->add_control( new $args['class']( $wp_customize, $control_id, $args ) );  // djpaultodo is this 5.2 compat?
	}
}
add_action( 'customize_register', 'bp_core_customizer_register_sections' );

/**
 * Tidy up the Customizer by removing all the default WP sections.
 *
 * @since 2.5.0
 *
 * @param $active Whether the Customizer section is active.
 * @param WP_Customize_Section $section {@see WP_Customize_Section} instance.
 * @return bool
 */
function bp_core_customizer_remove_sections( $active, $section ) {
	if ( isset( $_GET['bp_customizer'] ) && $_GET['bp_customizer'] === 'email' ) {
		return in_array( $section->id, array_keys( bp_core_customizer_get_sections() ), true );
	}

	return true;
}
add_action( 'customize_section_active', 'bp_core_customizer_remove_sections', 10, 2 );

/**
 * Define available sections for the Customizer.
 *
 * @since 2.5.0
 *
 * @return array
 */
function bp_core_customizer_get_sections() {
	return apply_filters( 'bp_core_customizer_get_sections', array(
		'section_bp_mailtpl_template' => array(
			'title' => __( 'Template', 'buddypress' ),
			'panel' => 'bp_mailtpl',
		),
		'section_bp_mailtpl_header' => array(
			'title' => __( 'Email Header', 'buddypress' ),
			'panel' => 'bp_mailtpl',
		),
		'section_bp_mailtpl_body' => array(
			'title' => __( 'Email Body', 'buddypress' ),
			'panel' => 'bp_mailtpl',
		),
		'section_bp_mailtpl_footer' => array(
			'title' => __( 'Footer', 'buddypress' ),
			'panel' => 'bp_mailtpl',
		),
	) );
}

/**
 * Get available settings for the Customizer.
 *
 * @since 2.5.0
 *
 * @return array
 */
function bp_core_customizer_get_settings() {
	$defaults = bp_core_customizer_get_defaults();

	$settings = array(
		'bp_mailtpl_opts[from_name]' => array(
			'type'                 => 'option',
			'default'              => $defaults['from_name'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'sanitize_text_field',
			'sanitize_js_callback' => '',
		),
		'bp_mailtpl_opts[from_email]' => array(
			'type'                 => 'option',
			'default'              => $defaults['from_email'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'sanitize_text_field',
			'sanitize_js_callback' => '',
		),
		'bp_mailtpl_opts[template]' => array(
			'type'                 => 'option',
			'default'              => $defaults['template'],
			'transport'            => 'refresh',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'bp_sanitize_customizer_templates',
			'sanitize_js_callback' => '',
		),
		'bp_mailtpl_opts[body_bg]' => array(
			'type'                 => 'option',
			'default'              => $defaults['body_bg'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'sanitize_hex_color',
			'sanitize_js_callback' => '',
		),
		'bp_mailtpl_opts[header_logo]' => array(
			'type'                 => 'option',
			'default'              => '',
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => '',
			'sanitize_js_callback' => '',
		),
		'bp_mailtpl_opts[header_logo_text]' => array(
			'type'                 => 'option',
			'default'              => '',
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'bp_sanitize_customizer_text',
			'sanitize_js_callback' => '',
		),
		'bp_mailtpl_opts[header_aligment]' => array(
			'type'                 => 'option',
			'default'              => $defaults['header_aligment'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'bp_sanitize_customizer_alignment',
			'sanitize_js_callback' => '',
		),
		'bp_mailtpl_opts[header_bg]' => array(
			'type'                 => 'option',
			'default'              => $defaults['header_bg'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'sanitize_hex_color',
			'sanitize_js_callback' => '',
		),
		'bp_mailtpl_opts[header_text_size]' => array(
			'type'                 => 'option',
			'default'              => $defaults['header_text_size'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'bp_sanitize_customizer_text',
			'sanitize_js_callback' => '',
		),
		'bp_mailtpl_opts[header_text_color]' => array(
			'type'                 => 'option',
			'default'              => $defaults['header_text_color'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'sanitize_hex_color',
			'sanitize_js_callback' => '',
		),
		'bp_mailtpl_opts[email_body_bg]' => array(
			'type'                 => 'option',
			'default'              => $defaults['email_body_bg'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'sanitize_hex_color',
			'sanitize_js_callback' => '',
		),
		'bp_mailtpl_opts[body_text_size]' => array(
			'type'                 => 'option',
			'default'              => $defaults['body_text_size'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'bp_sanitize_customizer_text',
			'sanitize_js_callback' => '',
		),
		'bp_mailtpl_opts[body_text_color]' => array(
			'type'                 => 'option',
			'default'              => $defaults['body_text_color'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'sanitize_hex_color',
			'sanitize_js_callback' => '',
		),
		'bp_mailtpl_opts[footer_text]' => array(
			'type'                 => 'option',
			'default'              => $defaults['footer_text'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'bp_sanitize_customizer_text',
			'sanitize_js_callback' => '',
		),
		'bp_mailtpl_opts[footer_aligment]' => array(
			'type'                 => 'option',
			'default'              => $defaults['footer_aligment'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'bp_sanitize_customizer_alignment',
			'sanitize_js_callback' => '',
		),
		'bp_mailtpl_opts[footer_bg]' => array(
			'type'                 => 'option',
			'default'              => $defaults['footer_bg'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'sanitize_hex_color',
			'sanitize_js_callback' => '',
		),
		'bp_mailtpl_opts[footer_text_size]' => array(
			'type'                 => 'option',
			'default'              => $defaults['footer_text_size'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'bp_sanitize_customizer_text',
			'sanitize_js_callback' => '',
		),
		'bp_mailtpl_opts[footer_text_color]' => array(
			'type'                 => 'option',
			'default'              => $defaults['footer_text_color'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'sanitize_hex_color',
			'sanitize_js_callback' => '',
		)
	);

	return apply_filters( 'bp_core_customizer_get_settings', $settings );
}

/**
 * Get available controls for the Customizer.
 *
 * @since 2.5.0
 *
 * @return array
 */
function bp_core_customizer_get_controls() {
	$controls = array(
		'bp_mailtpl_template' => array(
			'class'       => 'WP_Customize_Control',
			'label'       => __( 'Choose one', 'buddypress' ),
			'type'        => 'select',
			'section'     => 'section_bp_mailtpl_template',
			'settings'    => 'bp_mailtpl_opts[template]',
			'description' => '',

			'choices'     => apply_filters( 'bp_mailtpl/template_choices', array(
				'boxed'     => 'Boxed',
				'fullwidth' => 'Fullwidth'
			) ),
		),

		'bp_mailtpl_body_bg' => array(
			'class'       => 'WP_Customize_Color_Control',
			'label'       => __( 'Background Color', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_template',
			'settings'    => 'bp_mailtpl_opts[body_bg]',
			'description' => __( 'Choose email background color', 'buddypress' )
		),

		'bp_mailtpl_header' => array(
			'class'       => 'WP_Customize_Image_Control',
			'label'       => __( 'Logo', 'buddypress' ),
			'type'        => 'image',
			'section'     => 'section_bp_mailtpl_header',
			'settings'    => 'bp_mailtpl_opts[header_logo]',
			'description' => __( 'Add an image to use in header. Leave empty to use text instead', 'buddypress' )
		),

		'bp_mailtpl_header_logo_text' => array(
			'class'       => 'WP_Customize_Control',
			'label'       => __( 'Logo', 'buddypress' ),
			'type'        => 'textarea',
			'section'     => 'section_bp_mailtpl_header',
			'settings'    => 'bp_mailtpl_opts[header_logo_text]',
			'description' => __( 'Add text to your mail header', 'buddypress' )
		),

		'bp_mailtpl_aligment' => array(
			'class'       => 'WP_Customize_Control',
			'label'       => __( 'Aligment', 'buddypress' ),
			'type'        => 'select',
			'default'     => 'center',
			'section'     => 'section_bp_mailtpl_header',
			'settings'    => 'bp_mailtpl_opts[header_aligment]',
			'description' => __( 'Choose alignment for header', 'buddypress' ),

			'choices'     => array(
				'left'   => __( 'Left', 'buddypress' ),
				'center' => __( 'Center', 'buddypress' ),
				'right'  => __( 'Right', 'buddypress' ),
			),
		),

		'bp_mailtpl_header_bg' => array(
			'class'       => 'WP_Customize_Color_Control',
			'label'       => __( 'Background Color', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_header',
			'settings'    => 'bp_mailtpl_opts[header_bg]',
			'description' => __( 'Choose header background color', 'buddypress' )
		),

		'bp_mailtpl_header_text_size' => array(
			'class'       => 'BP_Customizer_Control_Range',
			'label'       => __( 'Text size', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_header',
			'settings'    => 'bp_mailtpl_opts[header_text_size]',
			'description' => __( 'Slide to change text size', 'buddypress' ),
			'input_attrs' => array(
				'max'  => 100,
				'min'  => 1,
				'step' => 1,
			),
		),

		'bp_mailtpl_header_text_color' => array(
			'class'       => 'WP_Customize_Color_Control',
			'label'       => __( 'Text Color', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_header',
			'settings'    => 'bp_mailtpl_opts[header_text_color]',
			'description' => __( 'Choose header text color', 'buddypress' )
		),

		'bp_mailtpl_email_body_bg' => array(
			'class'       => 'WP_Customize_Color_Control',
			'label'       => __( 'Background Color', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_body',
			'settings'    => 'bp_mailtpl_opts[email_body_bg]',
			'description' => __( 'Choose email body background color', 'buddypress' )
		),

		'bp_mailtpl_body_text_size' => array(
			'class'       => 'BP_Customizer_Control_Range',
			'label'       => __( 'Text size', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_body',
			'settings'    => 'bp_mailtpl_opts[body_text_size]',
			'description' => __( 'Slide to change text size', 'buddypress' ),
			'input_attrs' => array(
				'max'  => 100,
				'min'  => 1,
				'step' => 1,
			),
		),

		'bp_mailtpl_body_text_color' => array(
			'class'       => 'WP_Customize_Color_Control',
			'label'       => __( 'Text Color', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_body',
			'settings'    => 'bp_mailtpl_opts[body_text_color]',
			'description' => __( 'Choose body text color', 'buddypress' )
		),

		'bp_mailtpl_footer' => array(
			'class'       => 'WP_Customize_Control',
			'label'       => __( 'Footer text', 'buddypress' ),
			'type'        => 'textarea',
			'section'     => 'section_bp_mailtpl_footer',
			'settings'    => 'bp_mailtpl_opts[footer_text]',
			'description' => __('Change the email footer here', 'buddypress' )
		),

		'bp_mailtpl_footer_aligment' => array(
			'class'       => 'WP_Customize_Control',
			'label'       => __( 'Aligment', 'buddypress' ),
			'type'        => 'select',
			'default'     => 'center',
			'section'     => 'section_bp_mailtpl_footer',
			'settings'    => 'bp_mailtpl_opts[footer_aligment]',
			'description' => __( 'Choose alignment for footer', 'buddypress' ),

			'choices' => array(
				'left'   => __( 'Left', 'buddypress' ),
				'center' => __( 'Center', 'buddypress' ),
				'right'  => __( 'Right', 'buddypress' )
			),
		),

		'bp_mailtpl_footer_bg' => array(
			'class'       => 'WP_Customize_Color_Control',
			'label'       => __( 'Background Color', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_footer',
			'settings'    => 'bp_mailtpl_opts[footer_bg]',
			'description' => __( 'Choose footer background color', 'buddypress' )
		),

		'bp_mailtpl_footer_text_size' => array(
			'class'       => 'BP_Customizer_Control_Range',
			'label'       => __( 'Text size', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_footer',
			'settings'    => 'bp_mailtpl_opts[footer_text_size]',
			'description' => __( 'Slide to change text size', 'buddypress' ),
			'input_attrs' => array(
				'max'  => 100,
				'min'  => 1,
				'step' => 1,
			),
		),

		'bp_mailtpl_footer_text_color' => array(
			'class'       => 'WP_Customize_Color_Control',
			'label'       => __( 'Text Color', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_footer',
			'settings'    => 'bp_mailtpl_opts[footer_text_color]',
			'description' => __( 'Choose footer text color', 'buddypress' )
		)
	);

	return apply_filters( 'bp_core_customizer_get_controls', $controls );
}

/**
 * Define defaults settings for templates
 *
 * @since 2.5.0
 *
 * @return array
 */
function bp_core_customizer_get_defaults() {
	$defaults = array(
		'from_name'         => get_bloginfo( 'name' ),
		'from_email'        => get_bloginfo( 'admin_email' ),
		'template'          => 'boxed',
		'body_bg'           => '#ddd',
		'footer_text'       => '&copy;' . date( 'Y' ) .' ' . get_bloginfo( 'name' ),
		'footer_aligment'   => 'center',
		'footer_bg'         => '#eee',
		'footer_text_size'  => '12',
		'footer_text_color' => '#777',
		'header_aligment'   => 'center',
		'header_bg'         => '#be3631',
		'header_text_size'  => '30',
		'header_text_color' => '#fff',
		'email_body_bg'     => '#fbfbfb',
		'body_text_size'    => '14',
		'body_text_color'   => '#222',
	);

	return apply_filters( 'bp_core_customizer_get_defaults', $defaults );
}

/**
 * Sanitize callback for template select.
 *
 * @since 2.5.0
 *
 * @param $input string to sanitize
 * @return string
 */
function bp_sanitize_customizer_templates( $input ) {
	$valid = apply_filters( 'bp_sanitize_customizer_templates',
		array(
			'boxed'     => __( 'Simple Theme', 'buddypress' ),
			'fullwidth' => __( 'Fullwidth', 'buddypress' )
		)
	);

	if ( array_key_exists( $input, $valid, true ) ) {
		return $input;
	}

	return '';  // djpaultodo: Set a sensible default?
}

/**
 * Sanitize callback for template text.
 *
 * @since 2.5.0
 *
 * @param $input string to sanitize
 * @return string
 */
function bp_sanitize_customizer_text( $input ) {
	return wp_kses_post( force_balance_tags( $input ) );
}

/**
 * Sanitize call back for CSS alignments.
 *
 * @since 2.5.0
 *
 * @param $input string to sanitize
 * @return string
 */
function bp_sanitize_customizer_aligment( $input ) {
	$valid = array(
		'center',
		'left',
		'right',
	);

	if ( in_array( $input, $valid, true ) ) {
		return $input;
	} else {
		return '';  // djpaultodo: Set a sensible default?
	}
}

/**
 * Load template in the customizer before WordPress template is included
 *
 * @since 2.5.0
 */
function bp_core_customizer_load_template() {
	if ( ! is_customize_preview() || ! ( isset( $_GET['bp_customizer'] ) && $_GET['bp_customizer'] === 'email' ) ) {
		return;
	}

	$css     = bp_core_customizer_get_styles();
	$content = bp_core_customizer_get_template();

	echo $content;
	exit();
}
add_action( 'template_redirect', 'bp_core_customizer_load_template');

/**
 * Get the CSS styles for the email template
 *
 * @since 2.5.0
 *
 * @return string
 */
function bp_core_customizer_get_styles() {
	ob_start();
	bp_locate_template( array( 'assets/emails/bp-email-css.php', 'bp-email-css.php' ), true );

	return apply_filters( 'bp_core_customizer_get_styles', ob_get_clean() );
}

/**
 * Get the email template html
 *
 * @since 2.5.0
 *
 * @return string
 */
function bp_core_customizer_get_template(){
	ob_start();
	bp_locate_template( array( 'assets/emails/bp-email.php', 'bp-email.php' ), true );

	return apply_filters( 'bp_core_customizer_get_styles', ob_get_clean() );
}
