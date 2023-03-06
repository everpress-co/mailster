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
				'id'               => 12132,
				'slug'             => 'mailster',
				'public_key'       => 'pk_24ea323af7b2d311e3883b4c79db9',
				'public_key'       => 'pk_73a8ad525d7ab5e8fd03c90abe4b8',
				'is_premium'       => true,
				'is_premium_only'  => true,
				// 'has_premium_version' => true,
				'has_addons'       => false,
				'has_paid_plans'   => true,
				'is_org_compliant' => false,
				// 'has_affiliation'     => true,
				'menu'             => array(
					'slug'        => 'edit.php?post_type=newsletter',
					'contact'     => false,
					'support'     => false,
					'pricing'     => false,
					'affiliation' => false,
					'first-path'  => 'admin.php?page=mailster_dashboard',
					'account'     => true,
				),
			)
		);

		// Signal that SDK was initiated.
		do_action( 'mailster_freemius_loaded' );

		add_action( 'load-newsletter_page_mailster-pricing', array( $this, '_maybe_redirect_to_checkout' ) );

		return $mailster_freemius;
	}

	public function _maybe_redirect_to_checkout() {

		if ( ! isset( $_GET['plan_id'] ) ) {
			mailster_redirect( mailster_freemius_checkout_url() );
		}

	}

	public function activate_migrated_license( $secret_key, $is_marketing_allowed ) {

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

	public function get_update() {
		return (object) array(
			'version' => MAILSTER_VERSION,
			'updated' => false,
		);
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
