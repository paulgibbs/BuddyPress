<?php
/**
 * BuddyPress Customizer implementation for email.
 *
 * @since 2.5.0
 *
 * @package BuddyPress
 * @subpackage Core
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Initialize the Customizer for emails.
 *
 * @since 2.5.0
 *
 * @param WP_Customize_Manager $wp_customize The Customizer object.
 */
function bp_email_init_customizer( WP_Customize_Manager $wp_customize ) {

	/*
	 * Add email items.
	 */
	$wp_customize->add_panel( 'bp_mailtpl', array(
		'description' => __( 'Customize the appearance of emails sent by BuddyPress.', 'buddypress' ),
		'title'       => _x( 'Emails', 'email customizer title', 'buddypress' ),
	) );

	$sections = bp_email_get_customizer_sections();
	foreach( $sections as $section_id => $args ) {
		$wp_customize->add_section( $section_id, $args );
	}

	$settings = bp_email_get_customizer_settings();
	foreach( $settings as $setting_id => $args ) {
		$wp_customize->add_setting( $setting_id, $args );
	}

	/**
	 * HTML range customizer control.
	 */
	require_once dirname( __FILE__ ) . '/classes/class-bp-customizer-control-range.php';

	/**
	 * Fires to let plugins register extra Customizer controls for emails.
	 *
	 * @since 2.5.0
	 *
	 * @param WP_Customize_Manager $wp_customize The Customizer object.
	 */
	do_action( 'bp_core_customizer_register_sections', $wp_customize );

	$controls = bp_email_get_customizer_controls();
	foreach ( $controls as $control_id => $args ) {
		$wp_customize->add_control( new $args['class']( $wp_customize, $control_id, $args ) );
	}


	/*
	 * Enqueue scripts/styles.
	 *
	 * Scripts can't be registered in bp_core_register_common_styles() etc because
	 * the Customizer loads very, very early.
	 */

	$bp  = buddypress();
	$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	wp_enqueue_script(
		'bp-customizer-emails',
		"{$bp->plugin_url}/bp-core/admin/js/customizer-emails{$min}.js",
		array( 'customize-preview' ),
		bp_get_version()
	);


	/*
	 * Hook actions/filters for further configuration.
	 */

	add_filter( 'customize_section_active', 'bp_email_hide_other_customizer_sections', 12, 2 );
	add_action( 'template_include', 'bp_email_override_customizer_template', 8 );
	$wp_customize->remove_panel( 'widgets' );  // WP 4.4
}
add_action( 'bp_customize_register_for_email', 'bp_email_init_customizer' );

/**
 * Only show email sections in the Customizer.
 *
 * @since 2.5.0
 *
 * @param $active Whether the Customizer section is active.
 * @param WP_Customize_Section $section {@see WP_Customize_Section} instance.
 * @return bool
 */
function bp_email_hide_other_customizer_sections( $active, $section ) {
	return in_array( $section->id, array_keys( bp_email_get_customizer_sections() ), true );
}

/**
 * Get email sections for the Customizer.
 *
 * @since 2.5.0
 *
 * @return array
 */
function bp_email_get_customizer_sections() {

	/**
	 * Filter Customizer sections for emails.
	 *
	 * @since 2.5.0
	 *
	 * @param array $sections Email Customizer sections to add.
	 */
	return apply_filters( 'bp_email_get_customizer_sections', array(
		'section_bp_mailtpl_template' => array(
			'capability' => 'bp_moderate',
			'panel'      => 'bp_mailtpl',
			'title'      => __( 'Template', 'buddypress' ),
		),
		'section_bp_mailtpl_header' => array(
			'capability' => 'bp_moderate',
			'panel'      => 'bp_mailtpl',
			'title'      => __( 'Email Header', 'buddypress' ),
		),
		'section_bp_mailtpl_body' => array(
			'capability' => 'bp_moderate',
			'panel'      => 'bp_mailtpl',
			'title'      => __( 'Email Body', 'buddypress' ),
		),
		'section_bp_mailtpl_footer' => array(
			'capability' => 'bp_moderate',
			'panel'      => 'bp_mailtpl',
			'title'      => __( 'Footer', 'buddypress' ),
		),
	) );
}

/**
 * Get email settings for the Customizer.
 *
 * @since 2.5.0
 *
 * @return array
 */
function bp_email_get_customizer_settings() {
	$defaults = bp_email_get_customizer_settings_defaults();

	/**
	 * Filter Customizer settings for emails.
	 *
	 * @since 2.5.0
	 *
	 * @param array $settings Email Customizer settings to add.
	 */
	return apply_filters( 'bp_email_get_customizer_settings', array(
		'bp_mailtpl_opts[body_bg]' => array(
			'capability'           => 'bp_moderate',
			'default'              => $defaults['body_bg'],
			'sanitize_callback'    => 'sanitize_hex_color',
			'transport'            => 'postMessage',
			'type'                 => 'option',
		),
		'bp_mailtpl_opts[header_logo]' => array(
			'capability'           => 'bp_moderate',
			'default'              => '',
			'sanitize_callback'    => '',
			'transport'            => 'postMessage',
			'type'                 => 'option',
		),
		'bp_mailtpl_opts[header_logo_text]' => array(
			'capability'           => 'bp_moderate',
			'default'              => '',
			'sanitize_callback'    => 'sanitize_text_field',
			'transport'            => 'postMessage',
			'type'                 => 'option',
		),
		'bp_mailtpl_opts[header_aligment]' => array(
			'capability'           => 'bp_moderate',
			'default'              => $defaults['header_aligment'],
			'sanitize_callback'    => 'bp_sanitize_customizer_alignment',
			'transport'            => 'postMessage',
			'type'                 => 'option',
		),
		'bp_mailtpl_opts[header_bg]' => array(
			'capability'           => 'bp_moderate',
			'default'              => $defaults['header_bg'],
			'sanitize_callback'    => 'sanitize_hex_color',
			'transport'            => 'postMessage',
			'type'                 => 'option',
		),
		'bp_mailtpl_opts[header_text_size]' => array(
			'capability'           => 'bp_moderate',
			'default'              => $defaults['header_text_size'],
			'sanitize_callback'    => 'sanitize_text_field',
			'transport'            => 'postMessage',
			'type'                 => 'option',
		),
		'bp_mailtpl_opts[header_text_color]' => array(
			'capability'           => 'bp_moderate',
			'default'              => $defaults['header_text_color'],
			'sanitize_callback'    => 'sanitize_hex_color',
			'transport'            => 'postMessage',
			'type'                 => 'option',
		),
		'bp_mailtpl_opts[email_body_bg]' => array(
			'capability'           => 'bp_moderate',
			'default'              => $defaults['email_body_bg'],
			'sanitize_callback'    => 'sanitize_hex_color',
			'transport'            => 'postMessage',
			'type'                 => 'option',
		),
		'bp_mailtpl_opts[body_text_size]' => array(
			'capability'           => 'bp_moderate',
			'default'              => $defaults['body_text_size'],
			'sanitize_callback'    => 'sanitize_text_field',
			'transport'            => 'postMessage',
			'type'                 => 'option',
		),
		'bp_mailtpl_opts[body_text_color]' => array(
			'capability'           => 'bp_moderate',
			'default'              => $defaults['body_text_color'],
			'sanitize_callback'    => 'sanitize_hex_color',
			'transport'            => 'postMessage',
			'type'                 => 'option',
		),
		'bp_mailtpl_opts[footer_text]' => array(
			'capability'           => 'bp_moderate',
			'default'              => $defaults['footer_text'],
			'sanitize_callback'    => 'sanitize_text_field',
			'transport'            => 'postMessage',
			'type'                 => 'option',
		),
		'bp_mailtpl_opts[footer_aligment]' => array(
			'capability'           => 'bp_moderate',
			'default'              => $defaults['footer_aligment'],
			'sanitize_callback'    => 'bp_sanitize_customizer_alignment',
			'transport'            => 'postMessage',
			'type'                 => 'option',
		),
		'bp_mailtpl_opts[footer_bg]' => array(
			'capability'           => 'bp_moderate',
			'default'              => $defaults['footer_bg'],
			'sanitize_callback'    => 'sanitize_hex_color',
			'transport'            => 'postMessage',
			'type'                 => 'option',
		),
		'bp_mailtpl_opts[footer_text_size]' => array(
			'capability'           => 'bp_moderate',
			'default'              => $defaults['footer_text_size'],
			'sanitize_callback'    => 'sanitize_text_field',
			'transport'            => 'postMessage',
			'type'                 => 'option',
		),
		'bp_mailtpl_opts[footer_text_color]' => array(
			'capability'           => 'bp_moderate',
			'default'              => $defaults['footer_text_color'],
			'sanitize_callback'    => 'sanitize_hex_color',
			'transport'            => 'postMessage',
			'type'                 => 'option',
		),
	) );
}

/**
 * Get email controls for the Customizer.
 *
 * @since 2.5.0
 *
 * @return array
 */
function bp_email_get_customizer_controls() {

	/**
	 * Filter Customizer controls for emails.
	 *
	 * @since 2.5.0
	 *
	 * @param array $controls Email Customizer controls to add.
	 */
	return apply_filters( 'bp_email_get_customizer_controls', array(
		'bp_mailtpl_body_bg' => array(
			'class'       => 'WP_Customize_Color_Control',
			'description' => __( 'Choose email background color', 'buddypress' ),
			'label'       => __( 'Background Color', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_template',
			'settings'    => 'bp_mailtpl_opts[body_bg]',
		),

		'bp_mailtpl_header' => array(
			'class'       => 'WP_Customize_Image_Control',
			'description' => __( 'Add an image to use in header. Leave empty to use text instead', 'buddypress' ),
			'label'       => __( 'Logo', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_header',
			'settings'    => 'bp_mailtpl_opts[header_logo]',
			'type'        => 'image',
		),

		'bp_mailtpl_header_logo_text' => array(
			'class'       => 'WP_Customize_Control',
			'description' => __( 'Add text to your mail header', 'buddypress' ),
			'label'       => __( 'Logo', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_header',
			'settings'    => 'bp_mailtpl_opts[header_logo_text]',
			'type'        => 'textarea',
		),

		'bp_mailtpl_aligment' => array(
			'class'       => 'WP_Customize_Control',
			'default'     => 'center',
			'description' => __( 'Choose alignment for header', 'buddypress' ),
			'label'       => __( 'Aligment', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_header',
			'settings'    => 'bp_mailtpl_opts[header_aligment]',
			'type'        => 'select',

			'choices'     => array(
				'left'   => __( 'Left', 'buddypress' ),
				'center' => __( 'Center', 'buddypress' ),
				'right'  => __( 'Right', 'buddypress' ),
			),
		),

		'bp_mailtpl_header_bg' => array(
			'class'       => 'WP_Customize_Color_Control',
			'description' => __( 'Choose header background color', 'buddypress' ),
			'label'       => __( 'Background Color', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_header',
			'settings'    => 'bp_mailtpl_opts[header_bg]',
		),

		'bp_mailtpl_header_text_size' => array(
			'class'       => 'BP_Customizer_Control_Range',
			'description' => __( 'Slide to change text size', 'buddypress' ),
			'label'       => __( 'Text size', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_header',
			'settings'    => 'bp_mailtpl_opts[header_text_size]',

			'input_attrs' => array(
				'max'  => 100,
				'min'  => 1,
				'step' => 1,
			),
		),

		'bp_mailtpl_header_text_color' => array(
			'class'       => 'WP_Customize_Color_Control',
			'description' => __( 'Choose header text color', 'buddypress' ),
			'label'       => __( 'Text Color', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_header',
			'settings'    => 'bp_mailtpl_opts[header_text_color]',
		),

		'bp_mailtpl_email_body_bg' => array(
			'class'       => 'WP_Customize_Color_Control',
			'description' => __( 'Choose email body background color', 'buddypress' ),
			'label'       => __( 'Background Color', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_body',
			'settings'    => 'bp_mailtpl_opts[email_body_bg]',
		),

		'bp_mailtpl_body_text_size' => array(
			'class'       => 'BP_Customizer_Control_Range',
			'description' => __( 'Slide to change text size', 'buddypress' ),
			'label'       => __( 'Text size', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_body',
			'settings'    => 'bp_mailtpl_opts[body_text_size]',

			'input_attrs' => array(
				'max'  => 100,
				'min'  => 1,
				'step' => 1,
			),
		),

		'bp_mailtpl_body_text_color' => array(
			'class'       => 'WP_Customize_Color_Control',
			'description' => __( 'Choose body text color', 'buddypress' ),
			'label'       => __( 'Text Color', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_body',
			'settings'    => 'bp_mailtpl_opts[body_text_color]',
		),

		'bp_mailtpl_footer' => array(
			'class'       => 'WP_Customize_Control',
			'description' => __('Change the email footer here', 'buddypress' ),
			'label'       => __( 'Footer text', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_footer',
			'settings'    => 'bp_mailtpl_opts[footer_text]',
			'type'        => 'textarea',
		),

		'bp_mailtpl_footer_aligment' => array(
			'class'       => 'WP_Customize_Control',
			'default'     => 'center',
			'description' => __( 'Choose alignment for footer', 'buddypress' ),
			'label'       => __( 'Aligment', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_footer',
			'settings'    => 'bp_mailtpl_opts[footer_aligment]',
			'type'        => 'select',

			'choices' => array(
				'left'   => __( 'Left', 'buddypress' ),
				'center' => __( 'Center', 'buddypress' ),
				'right'  => __( 'Right', 'buddypress' )
			),
		),

		'bp_mailtpl_footer_bg' => array(
			'class'       => 'WP_Customize_Color_Control',
			'description' => __( 'Choose footer background color', 'buddypress' ),
			'label'       => __( 'Background Color', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_footer',
			'settings'    => 'bp_mailtpl_opts[footer_bg]',
		),

		'bp_mailtpl_footer_text_size' => array(
			'class'       => 'BP_Customizer_Control_Range',
			'description' => __( 'Slide to change text size', 'buddypress' ),
			'label'       => __( 'Text size', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_footer',
			'settings'    => 'bp_mailtpl_opts[footer_text_size]',

			'input_attrs' => array(
				'max'  => 100,
				'min'  => 1,
				'step' => 1,
			),
		),

		'bp_mailtpl_footer_text_color' => array(
			'class'       => 'WP_Customize_Color_Control',
			'description' => __( 'Choose footer text color', 'buddypress' ),
			'label'       => __( 'Text Color', 'buddypress' ),
			'section'     => 'section_bp_mailtpl_footer',
			'settings'    => 'bp_mailtpl_opts[footer_text_color]',
		)
	) );
}

/**
 * Get defaults settings for the email Customizer templates.
 *
 * @since 2.5.0
 *
 * @return array
 */
function bp_email_get_customizer_settings_defaults() {
	$defaults = array(
		'body_bg'           => '#222222',
		'body_text_color'   => '#222',
		'body_text_size'    => '14',
		'email_body_bg'     => '#fbfbfb',
		'footer_aligment'   => 'center',
		'footer_bg'         => '#eee',
		'footer_text'       => '&copy;' . date( 'Y' ) . ' ' . get_bloginfo( 'name' ),
		'footer_text_color' => '#777',
		'footer_text_size'  => '12',
		'header_aligment'   => 'center',
		'header_bg'         => '#be3631',
		'header_text_color' => '#fff',
		'header_text_size'  => '30',
	);

	/**
	 * Filter email Customizer settings' default values.
	 *
	 * @since 2.5.0
	 *
	 * @param array $defaults Settings default values.
	 */
	return apply_filters( 'bp_email_get_customizer_settings_defaults', $defaults );
}

/**
 * Sanitization callback for CSS alignment settings.
 *
 * @since 2.5.0
 *
 * @param string $input string to sanitize.
 * @return string
 */
function bp_email_sanitize_customizer_alignment( $input ) {
	$valid = array( 'center', 'left', 'right', );
	return ( in_array( $input, $valid, true ) ) ? $input : 'center';
}

/**
 * When previewing an email in the Customizer, change the template used to display it.
 *
 * @since 2.5.0
 *
 * @param string $template Path to current template (probably single.php).
 * @return string New template path.
 */
function bp_email_override_customizer_template( $template ) {
	$object = get_queried_object();
	if ( empty( $object->post_type ) || $object->post_type !== bp_get_email_post_type() || ! is_customize_preview() ) {
		return $template;
	}

	/**
	 * Filter template used to display email in the Customizer.
	 *
	 * @since 2.5.0
	 *
	 * @param string $template Path to current template (probably single.php).
	 */
	return apply_filters( 'bp_email_override_customizer_template',
		bp_locate_template( bp_email_get_template( $object ), false ),
		$template
	);
}
