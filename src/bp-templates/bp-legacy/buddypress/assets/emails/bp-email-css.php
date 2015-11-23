<?php
/**
 * BuddyPress email template CSS rules.
 *
 * @since 2.5.0
 *
 * @package BuddyPress
 * @subpackage Core
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$settings       = get_option('bp_mailtpl_opts', bp_email_get_customizer_defaults() );
$border_radius  = $settings['template'] == 'boxed' ? '6px' : '0px';
?>
#body {
	background-color: <?php echo $settings['body_bg']; ?>;
	width:100%;
	-webkit-text-size-adjust:none !important;
	margin:0;
	padding: 70px 0 70px 0;
}

#template_container {
	-webkit-box-shadow:0 0 0 3px rgba(0,0,0,0.025) !important;
	box-shadow:0 0 0 3px rgba(0,0,0,0.025) !important;
	-webkit-border-radius:$border_radius !important;
	border-radius:$border_radius !important;
	background-color: #fafafa;
	border-radius:6px !important;
}

#template_header {
	background-color: <?php echo $settings['header_bg']; ?>;
	color: #f1f1f1;
	-webkit-border-top-left-radius:$border_radius !important;
	-webkit-border-top-right-radius:$border_radius !important;
	border-top-left-radius:$border_radius !important;
	border-top-right-radius:$border_radius !important;
	border-bottom: 0;
	font-family:Arial;
	font-weight:bold;
	line-height:100%;
	vertical-align:middle;
}

#mailtpl_body_bg {
	background-color: <?php echo $settings['email_body_bg'];?>;
}
#mailtpl_body {
	color: <?php echo $settings['body_text_color'];?>;
	font-family:Arial;
	font-size: <?php echo $settings['body_text_size'];?>px;
	line-height:150%;
	text-align:left;
}
#logo {
	color: <?php echo $settings['header_text_color'];?>;
	margin:0;
	padding: 28px 24px;
	display:block;
	font-family:Arial;
	font-size: <?php echo $settings['header_text_size'];?>px;
	font-weight:bold;
	text-align:<?php echo $settings['header_aligment'];?>;
	line-height: 150%;
}
#logo_a {
	color: <?php echo $settings['header_text_color'];?>;
	text-decoration: none;
}
#template_footer {
	border-top:1px solid #E2E2E2;
	background: <?php echo $settings['footer_bg'];?>;
	-webkit-border-radius:0px 0px $border_radius $border_radius;
	-o-border-radius:0px 0px $border_radius $border_radius;
	-moz-border-radius:0px 0px $border_radius $border_radius;
	border-radius:0px 0px $border_radius $border_radius;
}

#credit {
	border:0;
	color: <?php echo $settings['footer_text_color'];?>;
	font-family: Arial;
	font-size: <?php echo $settings['footer_text_size'];?>px;
	line-height:125%;
	text-align:<?php echo $settings['footer_aligment'];?>;
}
