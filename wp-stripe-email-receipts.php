<?php
/**
 * Plugin Name: WP Stripe Email Receipts
 * Plugin URI:  http://philipnewcomer.net/wordpress-plugins/wp-stripe-email-receipts/
 * Description: Add-on for the WP Stripe plugin: Sends an email receipt to the user after a successful Stripe transaction.
 * Author:      Philip Newcomer
 * Author URI:  http://philipnewcomer.net
 * Text Domain: wp-stripe-email-receipts
 * Domain Path: /languages
 * Version:     1.0
 * License:     GPLv2 or later
 * License URI: <a href="http://www.gnu.org/licenses/gpl-2.0.html" rel="nofollow">http://www.gnu.org/licenses/gpl-2.0.html</a>
 *
 * Copyright (C) 2013  Philip Newcomer (email : contact@philipnewcomer.net)
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


/**
 * Set the plugin directory path and URL
 */
define( 'WP_STRIPE_EMAIL_RECEIPTS_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP_STRIPE_EMAIL_RECEIPTS_PLUGIN_DIR_URI', plugin_dir_url( __FILE__ ) );


/**
 * Load the admin functions file
 */
require_once( WP_STRIPE_EMAIL_RECEIPTS_PLUGIN_DIR_PATH . 'includes/admin-functions.php' );


/**
 * Gets a specific setting value, either from the saved settings, or the setting defaults
 */
function wp_stripe_email_receipts_get_setting( $setting_id ) {

	// Get default settings
	$default_settings = wp_stripe_email_receipts_get_settings_defaults();

	// Get saved settings, if they exist
	$saved_settings = (array) get_option( 'wp_stripe_email_receipts_settings' );

	// Merge default settings with saved settings
	$merged_settings = array_merge( $default_settings, $saved_settings );

	// Return the specified value from the merged settings array.
	return $merged_settings[ $setting_id ];
}


/**
 * Gets plugin settings defaults
 */
function wp_stripe_email_receipts_get_settings_defaults() {

	// Message body template
	$message_body_template  = '';
 	$message_body_template .=          'Payment Receipt from [from_name] ([from_email])';
	$message_body_template .= "\n";
	$message_body_template .= "\n" .  'Name: [user_name] ([user_email])';
	$message_body_template .= "\n" .  'Date: [date]';
	$message_body_template .= "\n" .  'Amount: $[amount]';
	$message_body_template .= "\n" .  'Card: [card_type] XXXX-XXXX-XXXX-[card_lastfour]';
	$message_body_template .= "\n" .  'Payment ID: [payment_id]';
	$message_body_template .= "\n";
	$message_body_template .= "\n" .  'Comments:';
	$message_body_template .= "\n" .  '[user_comments]';

	// Message "From" email
	$message_from_email = get_bloginfo( 'admin_email' );

	// Message "From" name
	$message_from_name = get_bloginfo( 'name' );

	// Message subject template
	$message_subject_template = 'Your Receipt from [from_name]';

	// Assemble defaults into an array
	$defaults = array(
		'message_body_template'    => $message_body_template,
		'message_from_email'       => $message_from_email,
		'message_from_name'        => $message_from_name,
		'message_subject_template' => $message_subject_template
	);

	// Return the assembled array of defaults
	return $defaults;
}


/**
 * Loads the translation files
 */
add_action( 'plugins_loaded', 'wp_stripe_email_receipts_load_textdomain' );

function wp_stripe_email_receipts_load_textdomain() {
	load_plugin_textdomain( 'wp-stripe-email-receipts', false, WP_STRIPE_EMAIL_RECEIPTS_PLUGIN_DIR_PATH . '/languages/' );
}


/**
 * Replaces placeholders in a template with the placeholder values
 */
function wp_stripe_email_receipts_replace_placeholders( $template, $placeholder_data ) {

	// For each item in the $placeholder_data array, replace any ocurrence of the key in the template with the key's value
	foreach( $placeholder_data as $placeholder => $replacement_value ) {
		$template = str_replace( '[' . $placeholder . ']', $replacement_value, $template);
	}

	// Return the processed template
	return $template;
}


/**
 * Generates and sends the user an email receipt after a successful transaction.
 */
add_action( 'wp_stripe_post_successful_charge', 'wp_stripe_email_receipts_successful_charge', 10, 2 );

function wp_stripe_email_receipts_successful_charge( $stripe_response, $user_email ) {

	// If the user has not provided an email address, don't go any further.
	if ( empty( $user_email ) ) {
		return;
	}

	// Setup an array of message data
	$message_data = array(

		// Email "from" info
		'from_email' => wp_stripe_email_receipts_get_setting( 'message_from_email' ),
		'from_name' => wp_stripe_email_receipts_get_setting( 'message_from_name' ),

		// Transaction info
		'amount' => number_format( $stripe_response->amount / 100, 2 ),
		'card_lastfour' => $stripe_response->card['last4'],
		'card_type' => $stripe_response->card['type'],
		'date' => date( get_option( 'date_format' ), $stripe_response->created ),
		'payment_id' => $stripe_response->id,

		// User comments: We don't use the comments value that WP Stripe provides to the wp_stripe_post_successful_charge hook, because WP Stripe prepends the user email to the comments text, which is ugly and redundant.
		'user_comments' => $_POST['wp_stripe_comment'],

		// User info
		'user_email' => $user_email, // already been provided in the second action hook parameter by WP Stripe
		'user_name' => $stripe_response->card['name'],

	);

	// If there are no user comments, set "[none]" as the user comments, so that the "Comments:" string is not displayed alone, which could be confusing to the user if there are no comments.
	if ( empty( $message_data['user_comments'] ) ) {
		$message_data['user_comments'] = __( '[none]', 'wp-stripe-email-receipts' );
	}

	// Get message body template
	$message_body_template = wp_stripe_email_receipts_get_setting( 'message_body_template' );

	// Replace the placeholders in the message body template with the actual values
	$message_body = wp_stripe_email_receipts_replace_placeholders( $message_body_template, $message_data );

	// Get message subject template
	$message_subject_template = wp_stripe_email_receipts_get_setting( 'message_subject_template' );

	// Replace the placeholders in the message subject template with the actual values
	$message_subject = wp_stripe_email_receipts_replace_placeholders( $message_subject_template, $message_data );

	// Set additional email headers
	$message_additional_headers = __( 'From:', 'wp-stripe-email-receipts' ) . sprintf( '"%1$s" <%2$s>', $message_data['from_name'], $message_data['from_email'] );

	// Finally, send the email
	wp_mail( $user_email, $message_subject, $message_body, $message_additional_headers );
}


/**
 * Deletes the WP Stripe Email Receipts settings array from the options table when plugin is uninstalled
 */
register_uninstall_hook( __FILE__, 'wp_stripe_email_receipts_uninstall' );

function wp_stripe_email_receipts_uninstall() {
	delete_option( 'wp_stripe_email_receipts_settings' );
}
