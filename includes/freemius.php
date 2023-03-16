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


mailster_freemius()->add_filter( 'plugin_icon', 'mailster_freemius_custom_icon' );
function mailster_freemius_custom_icon() {
	return MAILSTER_DIR . 'assets/img/opt-in.png';
}


mailster_freemius()->add_action( 'after_uninstall', 'mailster_freemius_uninstall_cleanup' );
function mailster_freemius_uninstall_cleanup() {
	mailster()->uninstall();
}


mailster_freemius()->add_action( 'hide_account_tabs', '__return_true' );
mailster_freemius()->add_action( 'hide_freemius_powered_by', '__return_true' );


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


mailster_freemius()->add_filter( 'permission_list', 'mailster_update_permission' );
function mailster_update_permission( $permissions ) {

	$permissions[] = array(
		'id'         => 'helpscout',
		'icon-class' => 'dashicons dashicons-sos',
		'tooltip'    => esc_html__( 'If you agree third-party scripts are loaded to provide you with help.', 'mailster' ),
		'label'      => esc_html__( 'Help Scout (optional)', 'mailster' ),
		'desc'       => esc_html__( 'Loading Help Scout\'s beacon for easy support access', 'mailster' ),
		'optional'   => true,
		'priority'   => 20,
	);

	$list = wp_list_pluck( $permissions, 'id' );
	if ( $key = array_search( 'extensions', $list ) ) {
		$permissions[ $key ]['default'] = true;
	}

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


mailster_freemius()->add_filter( 'checkout_url', 'mailster_freemius_checkout_url' );
function mailster_freemius_checkout_url( $url ) {

	if ( mailster_freemius()->is_whitelabeled() ) {
		return mailster_url( 'https://mailster.co/go/buy' );
	}

	return add_query_arg(
		array(
			'page'          => 'mailster-pricing',
			'checkout'      => 'true',
			'plan_id'       => 20734,
			'billing_cycle' => 'annual',
			'pricing_id'    => 23881,
			'post_type'     => 'newsletter',
		),
		admin_url( 'edit.php' )
	);

}

