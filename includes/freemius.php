<?php
if ( ! function_exists( 'mailster_freemius' ) ) {
	// Create a helper function for easy SDK access.
	function mailster_freemius() {
		global $mailster_freemius;

		if ( ! isset( $mailster_freemius ) ) {
			// Include Freemius SDK.
			require_once MAILSTER_DIR . '/vendor/freemius/wordpress-sdk/start.php';

			$mailster_freemius = fs_dynamic_init(
				array(
					'id'                  => '11268',
					'slug'                => 'mailster',
					'premium_slug'        => 'mailster',
					'type'                => 'plugin',
					'public_key'          => 'pk_24ea323af7b2d311e3883b4c79db9',
					'is_premium'          => true,
					'is_premium_only'     => true,
					// If your plugin is a serviceware, set this option to false.
					'has_premium_version' => true,
					'has_addons'          => false,
					'has_paid_plans'      => true,
					'is_org_compliant'    => false,
					'menu'                => array(
						'slug'    => 'edit.php?post_type=newsletter',
						'contact' => false,
						'support' => false,
					),
					// Set the SDK to work in a sandbox mode (for development & testing).
					// IMPORTANT: MAKE SURE TO REMOVE SECRET KEY BEFORE DEPLOYMENT.
					'secret_key'          => 'sk_C]ABkbx^>84%_K4uH<li>U8bvTMXO',
				)
			);
		}

		return $mailster_freemius;
	}

	// Init Freemius.
	mailster_freemius();
	// Signal that SDK was initiated.
	do_action( 'mailster_freemius_loaded' );
}
