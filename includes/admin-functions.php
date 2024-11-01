<?php

/**
 * Initializes the plugin settings page menu items and admin stylesheet
 */
add_action( 'admin_menu', 'wp_stripe_email_receipts_admin_init' );

function wp_stripe_email_receipts_admin_init() {

	$options_page = add_options_page(
		__( 'WP Stripe Email Receipts Settings', 'wp-stripe-email-receipts' ),
		__( 'Stripe Email Receipts', 'wp-stripe-email-receipts' ),
		'manage_options',
		'wp_stripe_email_receipts_settings',
		'wp_stripe_email_receipts_settings_page'
	);

	add_action( 'admin_print_styles-' . $options_page, 'wp_stripe_email_receipts_admin_stylesheet' );
}


/**
 * Enqueues admin stylesheet
 */
function wp_stripe_email_receipts_admin_stylesheet() {

	// Get plugin data so we have the plugin version to use in our stylesheet as a cachebuster.
	$plugin_data = get_plugin_data( WP_STRIPE_EMAIL_RECEIPTS_PLUGIN_DIR_PATH . 'wp-stripe-email-receipts.php' );

	wp_enqueue_style( 'wp_stripe_email_receipts_admin', WP_STRIPE_EMAIL_RECEIPTS_PLUGIN_DIR_URI . 'css/admin.css', array(), $plugin_data['Version'] );
}


/**
 * Callback which displays the message_body_template settings control
 */
function wp_stripe_email_receipts_setting_cb_message_body_template() {
	$message_body_template = wp_stripe_email_receipts_get_setting( 'message_body_template' );
?>
<textarea id="wp_stripe_email_receipts_setting_message_body_template" name="wp_stripe_email_receipts_settings[message_body_template]"><?php echo esc_textarea( $message_body_template ); ?></textarea>
<?php
}


/**
 * Callback which displays the message_from_email settings control
 */
function wp_stripe_email_receipts_setting_cb_message_from_email() {
	$message_from_email = wp_stripe_email_receipts_get_setting( 'message_from_email' );
?>
<input type="text" id="wp_stripe_email_receipts_setting_message_from_email" name="wp_stripe_email_receipts_settings[message_from_email]" value="<?php echo sanitize_text_field( $message_from_email ); ?>"></input>
<?php
}


/**
 * Callback which displays the message_from_name settings control
 */
function wp_stripe_email_receipts_setting_cb_message_from_name() {
	$message_from_name = wp_stripe_email_receipts_get_setting( 'message_from_name' );
?>
<input type="text" id="wp_stripe_email_receipts_setting_message_from_name" name="wp_stripe_email_receipts_settings[message_from_name]" value="<?php echo sanitize_text_field( $message_from_name ); ?>"></input>
<?php
}


/**
 * Callback which displays the message_subject_template settings control
 */
function wp_stripe_email_receipts_setting_cb_message_subject_template() {
	$message_subject_template = wp_stripe_email_receipts_get_setting( 'message_subject_template' );
?>
<input type="text" id="wp_stripe_email_receipts_setting_message_subject_template" name="wp_stripe_email_receipts_settings[message_subject_template]" value="<?php echo sanitize_text_field( $message_subject_template ); ?>"></input>
<?php
}


/**
 * Registers the plugin settings, settings section, and settings fields
 */
add_action( 'admin_init', 'wp_stripe_email_receipts_settings_init' );

function wp_stripe_email_receipts_settings_init() {

	register_setting(
		'wp_stripe_email_receipts_settings_group',
		'wp_stripe_email_receipts_settings',
		'wp_stripe_email_receipts_settings_validation'
	);

	add_settings_section(
		'wp_stripe_email_receipts_message_settings',
		__( 'Message Settings', 'wp-stripe-email-receipts' ),
		'wp_stripe_email_receipts_settings_section_cb_message_settings',
		'wp_stripe_email_receipts_settings'
	);

	add_settings_field(
		'wp_stripe_email_receipts_setting_message_from_email',
		__( 'Message "From" Email', 'wp-stripe-email-receipts' ),
		'wp_stripe_email_receipts_setting_cb_message_from_email',
		'wp_stripe_email_receipts_settings',
		'wp_stripe_email_receipts_message_settings',
		array(
			'label_for' => 'wp_stripe_email_receipts_setting_message_from_email'
		)
	);
	add_settings_field(
		'wp_stripe_email_receipts_setting_message_from_name',
		__( 'Message "From" Name', 'wp-stripe-email-receipts' ),
		'wp_stripe_email_receipts_setting_cb_message_from_name',
		'wp_stripe_email_receipts_settings',
		'wp_stripe_email_receipts_message_settings',
		array(
			'label_for' => 'wp_stripe_email_receipts_setting_message_from_name'
		)
	);
	add_settings_field(
		'wp_stripe_email_receipts_setting_message_subject_template',
		__( 'Message Subject Template', 'wp-stripe-email-receipts' ),
		'wp_stripe_email_receipts_setting_cb_message_subject_template',
		'wp_stripe_email_receipts_settings',
		'wp_stripe_email_receipts_message_settings',
		array(
			'label_for' => 'wp_stripe_email_receipts_setting_message_subject_template'
		)
	);	
	add_settings_field(
		'wp_stripe_email_receipts_setting_message_body_template',
		__( 'Message Body Template', 'wp-stripe-email-receipts' ),
		'wp_stripe_email_receipts_setting_cb_message_body_template',
		'wp_stripe_email_receipts_settings',
		'wp_stripe_email_receipts_message_settings',
		array(
			'label_for' => 'wp_stripe_email_receipts_setting_message_body_template'
		)
	);
}


/**
 * Displays the plugin settings page
 */
function wp_stripe_email_receipts_settings_page() {
?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e( 'WP Stripe Email Receipts Settings', 'wp-stripe-email-receipts' ); ?></h2>
	<form method="post" action="options.php">
		<?php settings_fields( 'wp_stripe_email_receipts_settings_group' ); ?>
		<?php do_settings_sections( 'wp_stripe_email_receipts_settings' ); ?>
		<?php submit_button(); ?>
	</form>
	<div class="wp-stripe-email-receipts-available-placeholders">
		<p>
			<em><?php _e( 'You can use the following placeholders in the message templates, which will be replaced by the actual values when the email is sent:', 'wp-stripe-email-receipts' ); ?></em>
			<br />
			<code>[from_name]</code>
			<code>[from_email]</code>
			<code>[user_name]</code>
			<code>[user_email]</code>
			<code>[date]</code>
			<code>[amount]</code>
			<code>[card_type]</code>
			<code>[card_lastfour]</code>
			<code>[payment_id]</code>
			<code>[comments]</code>
		</p>
		<p><em><?php _e( 'HTML tags are not allowed.', 'wp_stripe_email_receipts' ); ?></em></p>
	</div>
</div>
<?php
}


/**
 * Callback which displays the message_settings settings section
 */
function wp_stripe_email_receipts_settings_section_cb_message_settings() {
?>
<p><?php _e( 'Change the settings for the email message that is sent to users after a successful transaction.', 'wp-stripe-email-receipts' ); ?></p>
<?php
}


/**
 * Validates the plugin settings user-supplied values
 */
function wp_stripe_email_receipts_settings_validation( $input ) {
	$clean = array();

	$clean['message_body_template']    = wp_kses( $input['message_body_template'] );
	$clean['message_from_email']       = sanitize_email( $input['message_from_email'] );
	$clean['message_from_name']        = wp_kses( $input['message_from_name'] );
	$clean['message_subject_template'] = wp_kses( $input['message_subject_template'] );

	return $clean;
}
