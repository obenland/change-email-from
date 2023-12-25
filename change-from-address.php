<?php
/**
 * Plugin Name: Change From Address
 * Plugin URI: https://wordpress.org/plugins/change-from-address/
 * Description: Allows you to change the default email address and sender name for emails sent by WordPress.
 * Version: 2
 * Author: obenland
 * Author URI: https://konstantin.obenland.it
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package change-from-address
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin settings page.
 */
function cefko_register_settings() {
	register_setting(
		'general',
		'cefko_email_from_name',
		array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	register_setting(
		'general',
		'cefko_email_from_address',
		array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	add_settings_section(
		'cefko_email_from_settings_section',
		'<span id="email-from-address">' . __( 'Site Email From Address', 'change-from-address' ) . '</span>',
		'__return_false',
		'general'
	);

	add_settings_field(
		'cefko_email_from_name',
		__( 'From Name', 'change-from-address' ),
		'cefko_from_name_field_callback',
		'general',
		'cefko_email_from_settings_section',
		array(
			'label_for' => 'cefko_email_from_name',
		)
	);

	add_settings_field(
		'cefko_from_email_address',
		__( 'From Email Address', 'change-from-address' ),
		'cefko_from_email_address_field_callback',
		'general',
		'cefko_email_from_settings_section',
		array(
			'label_for' => 'cefko_from_email_address',
		)
	);
}
add_action( 'admin_init', 'cefko_register_settings' );

/**
 * From Name field content.
 */
function cefko_from_name_field_callback() {
	printf(
		'<input name="cefko_email_from_name" type="text" class="regular-text" value="%s" />',
		esc_attr( get_option( 'cefko_email_from_name' ) )
	);
}

/**
 * From Email field content.
 */
function cefko_from_email_address_field_callback() {
	$site_name  = wp_parse_url( network_home_url(), PHP_URL_HOST );
	$from_email = 'wordpress@';

	if ( null !== $site_name ) {
		if ( 'www.' === substr( $site_name, 0, 4 ) ) {
			$site_name = substr( $site_name, 4 );
		}

		$from_email .= $site_name;
	}

	printf(
		'<input name="cefko_from_email_address" type="email" class="regular-text" value="%1$s" placeholder="%2$s" />',
		esc_attr( get_option( 'cefko_email_from_address' ) ),
		esc_attr( $from_email )
	);
}

/**
 * Add settings page link with plugin.
 *
 * @param array $links An array of plugin action links.
 * @return array
 */
function cefko_mail_from_action_links( array $links ): array {
	$links[] = sprintf(
		'<a href="%1$s">%2$s</a>',
		esc_url( admin_url( 'options-general.php#email-from-address' ) ),
		esc_html__( 'Settings', 'change-from-address' )
	);

	return $links;
}
add_filter( 'plugin_action_links_change-from-address/change-from-address.php', 'cefko_mail_from_action_links' );

/**
 * Returns chosen from address.
 *
 * @param string $from_email Email address to send from.
 * @return string
 */
function cefko_mail_from_address( string $from_email ): string {
	$from_address = get_option( 'cefko_email_from_address' );

	if ( ! empty( $from_address ) ) {
		$from_email = $from_address;
	}

	return $from_email;
}
add_filter( 'wp_mail_from', 'cefko_mail_from_address' );

/**
 * Returns chosen from name.
 *
 * @param string $from_name Name associated with the "from" email address.
 * @return string
 */
function cefko_mail_from_name( string $from_name ): string {
	$name = get_option( 'cefko_email_from_name' );

	if ( ! empty( $name ) ) {
		$from_name = $name;
	}

	return $from_name;
}
add_filter( 'wp_mail_from_name', 'cefko_mail_from_name' );
