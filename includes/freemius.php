<?php

if ( 1 && ! function_exists( 'mailster_freemius' ) ) {
	// Create a helper function for easy SDK access.
	function mailster_freemius() {
		global $mailster_freemius;

		if ( ! isset( $mailster_freemius ) ) {
			// Include Freemius SDK.
			require_once MAILSTER_DIR . 'vendor/freemius/wordpress-sdk/start.php';

			$mailster_freemius = fs_dynamic_init(
				array(
					'id'               => '11268',
					'slug'             => 'mailster',
					'public_key'       => 'pk_24ea323af7b2d311e3883b4c79db9',
					'is_premium'       => true,
					'is_premium_only'  => true,
					// 'has_premium_version' => true,
					'has_addons'       => false,
					'has_paid_plans'   => true,
					'is_org_compliant' => false,
					'menu'             => array(
						'slug'    => 'admin.php?page=mailster_dashboard',
						'slug'    => 'edit.php?post_type=newsletter',
						'contact' => false,
						'support' => false,
						'pricing' => false,
						// 'account' => false,
					),
					'navigation'       => 'menu',
				)
			);
		}

		return $mailster_freemius;
	}

	// Init Freemius.
	mailster_freemius();
	// Signal that SDK was initiated.
	do_action( 'mailster_freemius_loaded' );


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

	mailster_freemius()->add_action( 'after_uninstall', 'mailster_freemius_uninstall_cleanup' );
	function mailster_freemius_uninstall_cleanup() {
		include_once MAILSTER_DIR . 'includes/uninstall.php';
	}


	mailster_freemius()->add_filter( 'license_key_', 'mailster_legacy_license_key' );
	function mailster_legacy_license_key( $key ) {

		return 'sk_wY%r?ymD4dW?Cn%r6W=!~vF0;3i=2';

		if ( ! preg_match( '/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{8,12}/', $key ) ) {
			return $key;
		}

		$key = 'sk_wY%r?ymD4dW?Cn%r6W=!~vF0;3i=2';

		return $key;
	}

	// mailster_freemius()->add_filter( 'permission_list', 'mailster_add_helpscount_permission' );
	function mailster_add_helpscount_permission( $permissions ) {

		$permissions[] = array(
			'id'         => 'helpscout',
			'icon-class' => 'dashicons dashicons-sos',
			'label'      => mailster_freemius()->get_text_inline( 'Help Scout', 'helpscout' ),
			'desc'       => mailster_freemius()->get_text_inline( 'Rendering Help Scout\'s beacon for easy support access', 'permissions-helpscout' ),
			'optional'   => true,
			'priority'   => 16,
		);

		return $permissions;
	}


	mailster_freemius()->add_filter( 'permission_list', 'mailster_add_diagnostic_permission' );
	function mailster_add_diagnostic_permission( $permissions ) {
		foreach ( $permissions as $key => $permission ) {
			if ( $permission['id'] === 'diagnostic' ) {
				$permissions[ $key ]['default'] = false;
			}
		}

		return $permissions;
	}

	add_action( 'admin_init', 'my_fs_license_key_migration' );
	function my_fs_license_key_migration() {
		if ( ! mailster_freemius()->has_api_connectivity() || mailster_freemius()->is_registered() ) {
			// No connectivity OR the user already opted-in to Freemius.
			return;
		}

		if ( 'pending' != get_option( 'mailster_freemius_migrated2fs', 'pending' ) ) {
			return;
		}

		// Get the license key from the previous eCommerce platform's storage.
		$license_key = mailster()->license();

		if ( empty( $license_key ) ) {
			// No key to migrate.
			return;
		}

		// Get the first 32 characters.
		$license_key = substr( $license_key, 0, 32 );

		error_log( print_r( 'XXX ' . $license_key, true ) );
		try {
			$next_page = mailster_freemius()->activate_migrated_license( $license_key );
			error_log( print_r( $next_page, true ) );
		} catch ( Exception $e ) {
			update_option( 'mailster_freemius_migrated2fs', 'unexpected_error' );
			return;
		}

		if ( mailster_freemius()->can_use_premium_code() ) {
			update_option( 'mailster_freemius_migrated2fs', 'done' );

			if ( is_string( $next_page ) ) {
				fs_redirect( $next_page );
			}
		} else {
			update_option( 'mailster_freemius_migrated2fs', 'failed' );
		}
	}
}

