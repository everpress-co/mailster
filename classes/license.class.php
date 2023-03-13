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

		if ( false === $mailster_freemius instanceof Freemius ) {

			require_once dirname( dirname( __FILE__ ) ) . '/vendor/freemius/wordpress-sdk/start.php';

			$args = apply_filters(
				'mailster_freemius_args',
				array(
					'id'               => 12184,
					'slug'             => 'mailster',
					'public_key'       => 'pk_1efa30140fc34f21e5b89959bb877',
					'is_premium'       => true,
					'is_premium_only'  => true,
					'has_addons'       => false,
					'has_paid_plans'   => true,
					'is_org_compliant' => false,
					'menu'             => array(
						'slug'        => 'edit.php?post_type=newsletter',
						'first-path'  => 'admin.php?page=mailster_dashboard',
						'contact'     => false,
						'support'     => false,
						'pricing'     => false,
						'affiliation' => false,
						'account'     => true,
					),
				)
			);

			$mailster_freemius = fs_dynamic_init( $args );

		}

		// Signal that SDK was initiated.
		do_action( 'mailster_freemius_loaded' );

		add_action( 'load-newsletter_page_mailster-pricing', array( $this, '_maybe_redirect_to_checkout' ) );
		add_action( 'load-newsletter_page_mailster-account', array( $this, '_add_account_beacon' ) );

		return $mailster_freemius;
	}

	public function _maybe_redirect_to_checkout() {

		if ( ! isset( $_GET['plan_id'] ) ) {
			mailster_redirect( mailster_freemius()->checkout_url() );
		}

		echo mailster()->beacon( array( '64074c66512c5e08fd71ac91' ), true );

	}

	public function _add_account_beacon() {

		echo mailster()->beacon( array( '640898cd16d5327537bcb740', '611bb01bb55c2b04bf6df0ae', '64074c66512c5e08fd71ac91' ), true );

	}

	public function activate_migrated_license( $secret_key, $is_marketing_allowed ) {

		$this->sdk();

		// at this point mailster_freemius is the Freemius SDK
		if ( mailster_option( 'usage_tracking' ) ) {
			FS_Permission_Manager::instance( mailster_freemius() )->update_permissions_tracking_flag(
				array(
					FS_Permission_Manager::PERMISSION_DIAGNOSTIC => true,
					FS_Permission_Manager::PERMISSION_EXTENSIONS => true,
				)
			);
		}

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
			if ( is_object( $migrate['error'] ) ) {
				return new WP_Error( $migrate['error']->code, $migrate['error']->message );
			}
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
