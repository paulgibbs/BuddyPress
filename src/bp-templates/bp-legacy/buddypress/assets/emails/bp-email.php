<?php
/**
 * BuddyPress email template.
 *
 * @since 2.5.0
 *
 * @package BuddyPress
 * @subpackage Core
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$settings = get_option( 'bp_email_options', bp_email_get_customizer_settings_defaults() );
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>" />
	<title><?php echo get_bloginfo( 'name' ); ?></title>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
	<div id="body">
		<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
			<tr>
				<td align="center" valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="680px" id="template_container">
						<tr>
							<td align="center" valign="top">
								<!-- Header -->
								<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_header">
						<tr>
							<td>
								<h1 id="logo">
									<a id ="logo_a" href="<?php echo esc_url( apply_filters( 'mailtpl/templates/header_logo_url', home_url() ) ); ?>" title="<?php echo esc_attr( apply_filters( 'mailtpl/templates/header_logo_url_title', get_bloginfo( 'name' ) ) ); ?>"><?php
										if ( ! empty( $settings['header_logo'] ) ) {
											echo '<img src="' . esc_url( apply_filters( 'mailtpl/templates/header_logo', $settings['header_logo'] ) ) . '">';
										} elseif ( ! empty( $settings['header_logo_text'] ) ) {
											echo $settings['header_logo_text'];
										} else {
											echo get_bloginfo('name');
										};
										?>
									</a>
								</h1>
							</td>
						</tr>
					</table><!-- End Header -->
				</td>
			</tr>
			<tr>
				<td align="center" valign="top">
					<!-- Body -->
					<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_body">
						<tr>
							<td valign="top" id="bp_mailtpl_template_bg">
								<!-- Content -->
								<table border="0" cellpadding="20" cellspacing="0" width="100%">
									<tr>
										<td valign="top">
											<div id="bp_mailtpl_body">
												{{content}}
											</div>
										</td>
									</tr>
								</table><!-- End Content -->
							</td>
						</tr>
					</table><!-- End Body -->
				</td>
			</tr>
			<tr>
				<td align="center" valign="top">
					<!-- Footer -->
					<table border="0" cellpadding="10" cellspacing="0" width="100%" id="template_footer">
						<tr>
							<td valign="top">
								<table border="0" cellpadding="10" cellspacing="0" width="100%">
									<tr>
										<td colspan="2" valign="middle" id="credit">
											<?php echo esc_html( $settings['footer_text'] ); ?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table><!-- End Footer -->
				</td>
			</tr>
		</table>
	</div><?php if ( is_customize_preview() ) wp_footer(); ?>
</body>
</html>
