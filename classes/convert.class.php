<?php

class MailsterConvert {

	public function __construct() {

		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 1 );
		add_action( 'admin_notices', array( &$this, 'notice' ), 1 );

	}


	public function notice() {

		if ( get_option( 'mailster_freemius' ) ) {
			return;
		}

		$msg  = '<h2>' . esc_html__( '[Action Required] Your Mailster license need to be transfered!', 'mailster' ) . '</h2>';
		$msg .= '<p>' . sprintf( esc_html__( ' Please %1$s and read more about this on our %2$s.', 'mailster' ), '<a href="' . admin_url( 'admin.php?page=mailster_convert' ) . '">Start now</a>', '<a href="' . mailster_url( 'https://mailster.co/blog/' ) . '" class="external">' . esc_html__( 'Blog', 'mailster' ) . '</a>' ) . '</p>';

		mailster_notice(
			$msg,
			'error',
			true,
			'mailster_freemius'
		);

	}

	public function admin_menu() {

		$page = add_submenu_page( null, esc_html__( 'Convert', 'mailster' ), esc_html__( 'Convert', 'mailster' ), 'manage_options', 'mailster_convert', array( &$this, 'convert_page' ) );

		add_action( 'load-' . $page, array( &$this, 'maybe_redirect' ) );
		add_action( 'load-' . $page, array( &$this, 'script_styles' ) );

	}

	public function maybe_redirect() {

		if ( ! get_option( 'mailster_freemius' ) ) {
			return;
		}

		mailster_redirect( admin_url( 'edit.php?post_type=newsletter&page=mailster-account' ) );

	}

	public function convert_page() {

		remove_action( 'admin_notices', array( mailster(), 'admin_notices' ) );
		include MAILSTER_DIR . 'views/convert.php';

	}


	public function script_styles() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'mailster-welcome', MAILSTER_URI . 'assets/css/convert-style' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_enqueue_script( 'mailster-convert', MAILSTER_URI . 'assets/js/convert-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );

	}

	public function convert( $email = null, $license = null ) {

		if ( is_null( $email ) ) {
			$user  = wp_get_current_user();
			$email = mailster()->email( $user->user_email );
		}
		if ( is_null( $license ) ) {
			$license = mailster()->license();
		}

		$endpoint = 'https://staging.mailster.co/wp-json/freemius/v1/api/get';
		$endpoint = 'https://mailster.local/wp-json/freemius/v1/api/get';

		$url = add_query_arg(
			array(
				'license'     => $license,
				'email'       => $email,
				'redirect_to' => rawurlencode( admin_url( 'admin.php?page=mailster_dashboard' ) ),
			),
			$endpoint
		);

		$response = wp_remote_get( $url, array( 'timeout' => 30 ) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code     = wp_remote_retrieve_response_code( $response );
		$response = json_decode( wp_remote_retrieve_body( $response ) );

		error_log( print_r( $response, true ) );

		if ( $code !== 200 ) {
			return new WP_Error( $code, $response->message );
		}

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

		if ( ! function_exists( 'mailster_freemius' ) ) {
			add_option( 'mailster_freemius', time() );
			require MAILSTER_DIR . 'includes/freemius.php';
		}

		$is_marketing_allowed = true;

		$migrate = mailster_freemius()->activate_migrated_license( $response->data->secret_key, $is_marketing_allowed );

		error_log( print_r( $response, true ) );

		if ( isset( $migrate['error'] ) && $migrate['error'] ) {
			delete_option( 'mailster_freemius' );
			return new WP_Error( $code, $migrate['error'] );
		}
		if ( isset( $migrate['success'] ) && $migrate['success'] ) {

		}

		return $response;

	}

}
