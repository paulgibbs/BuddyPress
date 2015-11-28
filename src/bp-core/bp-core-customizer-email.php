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
 * @param WP_Customize_Manager $wp_customize
 */
function bp_email_init_customizer( WP_Customize_Manager $wp_customize ) {

	/*
	 * Add email items.
	 */
	$wp_customize->add_panel( 'bp_mailtpl', array(
		'description'   => __( 'Customize the look of your BuddyPress emails', 'buddypress' ),
		'title'         => __( 'Email Templates', 'buddypress' ),
	) );

	$sections = bp_email_get_customizer_sections();
	foreach( $sections as $section_id => $args ) {
		$wp_customize->add_section( $section_id, $args );
	}

	$settings = bp_email_get_customizer_settings();
	foreach( $settings as $setting_id => $args ) {
		$wp_customize->add_setting( $setting_id, $args );
	}

	require dirname( __FILE__ ) . '/classes/class-bp-customizer-control-range.php';
	do_action( 'bp_core_customizer_register_sections', $wp_customize, $sections );

	$controls = bp_email_get_customizer_controls();
	foreach( $controls as $control_id => $args ) {
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

	add_filter( 'customize_section_active', 'bp_email_hide_other_customizer_sections', 10, 2 );
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
	return apply_filters( 'bp_email_get_customizer_sections', array(
		'section_bp_mailtpl_template' => array(
			'capability' => 'bp_moderate',
			'title'      => __( 'Template', 'buddypress' ),
			'panel'      => 'bp_mailtpl',
		),
		'section_bp_mailtpl_header' => array(
			'capability' => 'bp_moderate',
			'title'      => __( 'Email Header', 'buddypress' ),
			'panel'      => 'bp_mailtpl',
		),
		'section_bp_mailtpl_body' => array(
			'capability' => 'bp_moderate',
			'title'      => __( 'Email Body', 'buddypress' ),
			'panel'      => 'bp_mailtpl',
		),
		'section_bp_mailtpl_footer' => array(
			'capability' => 'bp_moderate',
			'title'      => __( 'Footer', 'buddypress' ),
			'panel'      => 'bp_mailtpl',
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

	$settings = array(
		'bp_mailtpl_opts[template]' => array(
			'type'                 => 'option',
			'default'              => $defaults['template'],
			'transport'            => 'refresh',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'bp_customizer_sanitize_callback_email_template',
		),
		'bp_mailtpl_opts[body_bg]' => array(
			'type'                 => 'option',
			'default'              => $defaults['body_bg'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'sanitize_hex_color',
		),
		'bp_mailtpl_opts[header_logo]' => array(
			'type'                 => 'option',
			'default'              => '',
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => '',
		),
		'bp_mailtpl_opts[header_logo_text]' => array(
			'type'                 => 'option',
			'default'              => '',
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'sanitize_text_field',
		),
		'bp_mailtpl_opts[header_aligment]' => array(
			'type'                 => 'option',
			'default'              => $defaults['header_aligment'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'bp_sanitize_customizer_alignment',
		),
		'bp_mailtpl_opts[header_bg]' => array(
			'type'                 => 'option',
			'default'              => $defaults['header_bg'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'sanitize_hex_color',
		),
		'bp_mailtpl_opts[header_text_size]' => array(
			'type'                 => 'option',
			'default'              => $defaults['header_text_size'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'sanitize_text_field',
		),
		'bp_mailtpl_opts[header_text_color]' => array(
			'type'                 => 'option',
			'default'              => $defaults['header_text_color'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'sanitize_hex_color',
		),
		'bp_mailtpl_opts[email_body_bg]' => array(
			'type'                 => 'option',
			'default'              => $defaults['email_body_bg'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'sanitize_hex_color',
		),
		'bp_mailtpl_opts[body_text_size]' => array(
			'type'                 => 'option',
			'default'              => $defaults['body_text_size'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'sanitize_text_field',
		),
		'bp_mailtpl_opts[body_text_color]' => array(
			'type'                 => 'option',
			'default'              => $defaults['body_text_color'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'sanitize_hex_color',
		),
		'bp_mailtpl_opts[footer_text]' => array(
			'type'                 => 'option',
			'default'              => $defaults['footer_text'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'sanitize_text_field',
		),
		'bp_mailtpl_opts[footer_aligment]' => array(
			'type'                 => 'option',
			'default'              => $defaults['footer_aligment'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'bp_sanitize_customizer_alignment',
		),
		'bp_mailtpl_opts[footer_bg]' => array(
			'type'                 => 'option',
			'default'              => $defaults['footer_bg'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'sanitize_hex_color',
		),
		'bp_mailtpl_opts[footer_text_size]' => array(
			'type'                 => 'option',
			'default'              => $defaults['footer_text_size'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'sanitize_text_field',
		),
		'bp_mailtpl_opts[footer_text_color]' => array(
			'type'                 => 'option',
			'default'              => $defaults['footer_text_color'],
			'transport'            => 'postMessage',
			'capability'           => 'bp_moderate',
			'sanitize_callback'    => 'sanitize_hex_color',
		),
	);

	return apply_filters( 'bp_email_get_customizer_settings', $settings );
}

/**
 * Get email controls for the Customizer.
 *
 * @since 2.5.0
 *
 * @return array
 */
function bp_email_get_customizer_controls() {
	return apply_filters( 'bp_email_get_customizer_controls', array(
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

	return apply_filters( 'bp_email_get_customizer_settings_defaults', $defaults );
}

/**
 * Sanitize callback for template selection setting.
 *
 * @since 2.5.0
 *
 * @param $input string to sanitize
 * @return string
 */
function bp_customizer_sanitize_callback_email_template( $input ) {
	$valid = apply_filters( 'bp_sanitize_customizer_templates',
		array(
			'boxed'     => __( 'Simple Theme', 'buddypress' ),
			'fullwidth' => __( 'Fullwidth', 'buddypress' )
		)
	);

	return ( array_key_exists( $input, $valid, true ) ) ? $input : 'boxed';
}

/**
 * Sanitization callback for CSS alignment settings.
 *
 * @since 2.5.0
 *
 * @param $input string to sanitize
 * @return string
 */
function bp_customizer_sanitize_callback_alignment( $input ) {
	$valid = array( 'center', 'left', 'right', );
	return ( in_array( $input, $valid, true ) ) ? $input : 'center';
}
