<?php
/**
 * Plugin Name: WP Change Email From
 * Plugin URI: https://wordpress.org/plugins/wp-change-email-from/
 * Description: Allows you to change the default email address and sender name for emails sent by WordPress.
 * Version: 1
 * Author: obenland
 * Author URI: https://konstantin.obenland.it
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package wp-change-email-from
 */

/**
 * Plugin settings page.
 */
function wpcef_register_settings() {
	register_setting(
		'general',
		'wpcef_email_from_name',
		array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	register_setting(
		'general',
		'wpcef_email_from_address',
		array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	add_settings_section(
		'wpcef_email_from_settings_section',
		__( 'Site Email From Address', 'wp-change-email-from' ),
		'__return_false',
		'general'
	);

	add_settings_field(
		'wpcef_email_from_name',
		__( 'From Name', 'wp-change-email-from' ),
		'wpcef_from_name_field_callback',
		'general',
		'wpcef_email_from_settings_section',
		array(
			'label_for' => 'wpcef_email_from_name',
		)
	);

	add_settings_field(
		'wpcef_from_email_address',
		__( 'From Email Address', 'wp-change-email-from' ),
		'wpcef_from_email_address_field_callback',
		'general',
		'wpcef_email_from_settings_section',
		array(
			'label_for' => 'wpcef_from_email_address',
		)
	);
}
add_action( 'admin_init', 'wpcef_register_settings' );

/**
 * From Name field content.
 */
function wpcef_from_name_field_callback() {
	printf(
		'<input name="wpcef_email_from_name" type="text" class="regular-text" value="%s" />',
		esc_attr( get_option( 'wpcef_email_from_name' ) )
	);
}

/**
 * From Email field content.
 */
function wpcef_from_email_address_field_callback() {
	$site_name  = wp_parse_url( network_home_url(), PHP_URL_HOST );
	$from_email = 'wordpress@';

	if ( null !== $site_name ) {
		if ( 'www.' === substr( $site_name, 0, 4 ) ) {
			$site_name = substr( $site_name, 4 );
		}

		$from_email .= $site_name;
	}

	printf(
		'<input name="wpcef_from_email_address" type="email" class="regular-text" value="%1$s" placeholder="%2$s" />',
		esc_attr( get_option( 'wpcef_email_from_address' ) ),
		esc_attr( $from_email )
	);
}

/**
 * Add settings page link with plugin.
 *
 * @param array $links An array of plugin action links.
 * @return array
 */
function wpcef_mail_from_action_links( array $links ): array {
	$links[] = sprintf(
		'<a href="%1$s">%2$s</a>',
		esc_url( admin_url( 'options-general.php' ) ),
		esc_html__( 'Settings', 'wp-change-email-from' )
	);

	return $links;
}
add_filter( 'plugin_action_links_wp-change-email-from/wp-change-email-from.php', 'wpcef_mail_from_action_links' );

/**
 * Returns chosen from address.
 *
 * @param string $from_email Email address to send from.
 * @return string
 */
function wpcef_mail_from_address( string $from_email ): string {
	$from_address = get_option( 'wpcef_email_from_address' );

	if ( ! empty( $from_address ) ) {
		$from_email = $from_address;
	}

	return $from_email;
}
add_filter( 'wp_mail_from', 'wpcef_mail_from_address' );

/**
 * Returns chosen from name.
 *
 * @param string $from_name Name associated with the "from" email address.
 * @return string
 */
function wpcef_mail_from_name( string $from_name ): string {
	$name = get_option( 'wpcef_email_from_name' );

	if ( ! empty( $name ) ) {
		$from_name = $name;
	}

	return $from_name;
}
add_filter( 'wp_mail_from_name', 'wpcef_mail_from_name' );
