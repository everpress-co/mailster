<?php

function mailster_freemius() {

	global $mailster_freemius;

	if ( ! isset( $mailster_freemius ) ) {
		// Include Freemius SDK.

		require_once MAILSTER_DIR . 'classes/license.class.php';
		$mailster_freemius = new MailsterLicense();

		if ( get_option( 'mailster_freemius' ) ) {
			$mailster_freemius->sdk();
		}
	}

	return $mailster_freemius;
}

mailster_freemius()->add_filter( 'connect_message_on_update', 'mailster_freemius_custom_connect_message_on_update', 10, 6 );
function mailster_freemius_custom_connect_message_on_update( $message, $user_first_name, $plugin_title, $user_login, $site_link, $freemius_link ) {
	return sprintf(
		__( 'Hey %1$s' ) . ',<br>' .
		__( 'Please help us improve %2$s! If you opt-in, some data about your usage of %2$s will be sent to %5$s. If you skip this, that\'s okay! %2$s will still work just fine.', 'mailster' ),
		$user_first_name,
		'<b>' . $plugin_title . '</b>',
		'<b>' . $user_login . '</b>',
		$site_link,
		$freemius_link
	);
}

mailster_freemius()->add_filter( 'connect/after_license_input', 'mailster_freemius_connect_before' );
function mailster_freemius_connect_before() {

}

mailster_freemius()->add_filter( 'plugin_icon', 'mailster_freemius_custom_icon' );
function mailster_freemius_custom_icon() {
	return MAILSTER_DIR . 'assets/img/opt-in.png';
}

mailster_freemius()->add_action( 'after_uninstall', 'mailster_freemius_uninstall_cleanup' );
function mailster_freemius_uninstall_cleanup() {
	mailster()->uninstall();
}

mailster_freemius()->add_action( 'hide_plan_change', '__return_true' );


mailster_freemius()->add_filter( 'license_key', 'mailster_legacy_license_key' );
function mailster_legacy_license_key( $key ) {

	$key = trim( $key );

	// check for UUIDv4 (Envato License)
	if ( ! preg_match( '/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/', $key ) ) {
		return $key;
	}

	$response = mailster( 'convert' )->convert( null, $key );

	if ( is_wp_error( $response ) ) {
		set_transient( 'mailster_last_legacy_key_error', $response, 10 );
	} else {
		$key = $response->data->secret_key;
	}
	return $key;
}

mailster_freemius()->add_filter( 'permission_list', 'mailster_add_helpscount_permission' );
function mailster_add_helpscount_permission( $permissions ) {

	$permissions[] = array(
		'id'         => 'helpscout',
		'icon-class' => 'dashicons dashicons-sos',
		'label'      => mailster_freemius()->get_text_inline( 'Help Scout', 'helpscout' ),
		'desc'       => mailster_freemius()->get_text_inline( 'Loading Help Scout\'s beacon for easy support access', 'permissions-helpscout' ),
		'optional'   => true,
	);

	return $permissions;
}
mailster_freemius()->add_action( 'connect/after_license_input', 'mailster_add_link_for_envato' );
function mailster_add_link_for_envato() {
	if ( ! MAILSTER_ENVATO ) {
		return;
	}
	?>
	<style>.fs-license-key-container a.show-license-resend-modal{display: none;}</style>
	<div class="fs-license-key-container">
		<a href="https://kb.mailster.co/where-is-my-purchasecode/" target="_blank"><?php esc_html_e( "Can't find your license key?", 'mailster' ); ?></a>
	</div>
	<?php

}


mailster_freemius()->add_filter( 'permission_list', 'mailster_add_diagnostic_permission' );
function mailster_add_diagnostic_permission( $permissions ) {
	foreach ( $permissions as $key => $permission ) {
		if ( $permission['id'] === 'diagnostic' ) {
			$permissions[ $key ]['default'] = true;
		}
	}

	return $permissions;
}

// change length of licenses keys to accept the one from Envato 36 but allow some whitespace
mailster_freemius()->add_filter( 'opt_in_error_message', 'mailster_freemius_opt_in_error_message' );
function mailster_freemius_opt_in_error_message( $error ) {

	$last_error = get_transient( 'mailster_last_legacy_key_error' );
	if ( $last_error ) {
		$error = $last_error->get_error_message();
		delete_transient( 'mailster_last_legacy_key_error' );
	}
	return $error;
}

// change length of licenses keys to accept the one from Envato 36 but allow some whitespace
mailster_freemius()->add_filter( 'license_key_maxlength', 'mailster_license_key_maxlength' );
function mailster_license_key_maxlength( $length ) {
	return 40;
}

