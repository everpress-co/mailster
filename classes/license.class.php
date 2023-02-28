<?php

class MailsterLicense {

	private $filters = array();
	private $actions = array();

	public function __call( $method, $args ) {

		// always return false to prevent errors
		return false;

	}

	public function sdk() {

		global $mailster_freemius;

		require_once MAILSTER_DIR . 'vendor/freemius/wordpress-sdk/start.php';
		$mailster_freemius = fs_dynamic_init(
			array(
				'id'                  => 11268,
				'slug'                => 'mailster',
				'public_key'          => 'pk_24ea323af7b2d311e3883b4c79db9',
				'is_premium'          => true,
				'is_premium_only'     => true,
				'has_premium_version' => true,
				'has_addons'          => false,
				'has_paid_plans'      => true,
				'is_org_compliant'    => true,
				'has_affiliation'     => true,
				'menu'                => array(
					'slug'        => 'edit.php?post_type=newsletter',
					'contact'     => false,
					'support'     => false,
					'pricing'     => false,
					'affiliation' => false,
					'first-path'  => 'admin.php?page=mailster_dashboard',
					'account'     => false,
				),
			)
		);

		// Signal that SDK was initiated.
		do_action( 'mailster_freemius_loaded' );

		return $mailster_freemius;
	}

	public function activate_migrated_license( $secret_key, $is_marketing_allowed ) {

		if ( $fs_accounts = get_option( 'fs_accounts' ) ) {
			if ( isset( $fs_accounts['plans']['mailster'] ) ) {
				unset( $fs_accounts['plans']['mailster'] );
			}
			if ( isset( $fs_accounts['plugins']['mailster'] ) ) {
				unset( $fs_accounts['plugins']['mailster'] );
			}
			if ( isset( $fs_accounts['sites']['mailster'] ) ) {
				unset( $fs_accounts['sites']['mailster'] );
			}
			if ( isset( $fs_accounts['plugin_data']['mailster'] ) ) {
				unset( $fs_accounts['plugin_data']['mailster'] );
			}
			update_option( 'fs_accounts', $fs_accounts );
		}

		$this->sdk();

		// at this point mailster_freemius is the freemius SDK

		// add collected filters
		foreach ( $this->filters as $filter ) {
			call_user_func_array( array( mailster_freemius(), 'add_filter' ), $filter );
		}

		// add collected actions
		foreach ( $this->actions as $action ) {
			call_user_func_array( array( mailster_freemius(), 'add_action' ), $filter );
		}

		// migrate
		$migrate = mailster_freemius()->activate_migrated_license( $secret_key, $is_marketing_allowed );

		if ( isset( $migrate['error'] ) && $migrate['error'] ) {
			return new WP_Error( 'freemius_error', $migrate['error'] );
		}

		add_option( 'mailster_freemius', time() );

		return $migrate;

	}

	public function add_filter( $hook, $callback, $priority = 10, $arguments = 1 ) {
		$this->filters[] = func_get_args();
	}

	public function add_action( $hook, $callback, $priority = 10, $arguments = 1 ) {
		$this->actions[] = func_get_args();
	}

	public function get_user() {

		$user = array();

		if ( defined( 'MAILSTER_EMAIL' ) && MAILSTER_EMAIL ) {
			$user['email'] = MAILSTER_EMAIL;
		} else {
			$user['email'] = get_option( 'mailster_email' );
		}
		if ( defined( 'MAILSTER_USERNAME' ) && MAILSTER_USERNAME ) {
			$user['first'] = MAILSTER_USERNAME;
		} else {
			$user['first'] = get_option( 'mailster_username' );
		}
		$user['last'] = '';
		if ( defined( 'MAILSTER_LICENSE' ) && MAILSTER_LICENSE ) {
			$user['secret_key'] = MAILSTER_LICENSE;
		} else {
			$user['secret_key'] = get_option( 'mailster_license' );
		}

		return (object) $user;
	}

}
