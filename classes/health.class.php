<?php

class MailsterHealth {

	public function __construct() {

		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 150 );
		add_action( 'wp_version_check', array( &$this, 'notice' ) );
	}

	public function admin_menu() {

		$page = add_submenu_page( true, esc_html__( 'Health', 'mailster' ), esc_html__( 'Health', 'mailster' ), 'manage_options', 'mailster_health', array( &$this, 'health_page' ) );
		add_action( 'load-' . $page, array( &$this, 'script_styles' ) );
	}

	public function health_page() {

		remove_action( 'admin_notices', array( mailster(), 'admin_notices' ) );
		include MAILSTER_DIR . 'views/health.php';
	}

	public function script_styles() {

		do_action( 'mailster_admin_header' );

		$suffix = SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'mailster-welcome', MAILSTER_URI . 'assets/css/health-style' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_enqueue_script( 'mailster-health', MAILSTER_URI . 'assets/js/health-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );
	}

	public function notice() {

		if ( get_option( 'mailster_freemius' ) ) {
			return;
		}

		$msg  = '<h2>' . esc_html__( '[Action Required] We need to transfer your Mailster license!', 'mailster' ) . '</h2>';
		$msg .= '<p>' . esc_html__( 'Hey there! Just wanted to give you a heads up that we\'re changing our license provider.', 'mailster' ) . '</p>';
		$msg .= '<p>' . esc_html__( 'As part of the process, we\'ll need your consent for a quick manual step. Thanks for your help in advance!', 'mailster' ) . '</p>';
		$msg .= '<p><a class="button button-primary button-hero" href="' . admin_url( 'admin.php?page=mailster_health' ) . '">' . esc_html__( 'health now', 'mailster' ) . '</a> or <a href="' . mailster_url( 'https://kb.mailster.co/63fe029de6d6615225474599' ) . '" data-article="63fe029de6d6615225474599">' . esc_html__( 'read more about it', 'mailster' ) . '</a></p>';

		mailster_notice( $msg, 'info', false, 'mailster_freemius' );
	}

	public function health( $email = null, $license = null, $is_marketing_allowed = null ) {

		$user = wp_get_current_user();
		if ( is_null( $email ) ) {
			$email = mailster()->get_email( $user->user_email );
		}

		if ( is_null( $license ) ) {
			$license = mailster()->get_license();
		}

		$endpoint = apply_filters( 'mailster_updatecenter_endpoint', 'https://update.mailster.co/' );
		$endpoint = trailingslashit( $endpoint ) . 'wp-json/freemius/v1/api/get';

		$args = array(
			'version'     => MAILSTER_VERSION,
			'license'     => $license,
			'email'       => $email,
			'whitelabel'  => $user->user_email != $email,
			'redirect_to' => rawurlencode( admin_url( 'edit.php?post_type=newsletter&page=mailster-account' ) ),
		);

		if ( ! is_null( $is_marketing_allowed ) ) {
			$args['marketing'] = $is_marketing_allowed;
		}

		$url = add_query_arg( $args, $endpoint );

		$response = wp_remote_get( $url, array( 'timeout' => 30 ) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code     = wp_remote_retrieve_response_code( $response );
		$body     = wp_remote_retrieve_body( $response );
		$response = json_decode( $body );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return new WP_Error( 'no_json', 'Response is invalid: ' . json_last_error_msg() );
		}

		if ( $code !== 200 ) {
			return new WP_Error( $code, $response->message );
		}

		$migrate = mailster_freemius()->activate_migrated_license( $response->data->secret_key, (bool) $response->data->marketing );

		if ( is_wp_error( $migrate ) ) {
			return $migrate;
		}

		$response->migrate = $migrate;

		return $response;
	}


	public function send_test() {

		$precheck_id = hash( 'crc32', uniqid( 1 ) ) . hash( 'crc32', uniqid( 9 ) );
		$receiver    = apply_filters( 'mailster_precheck_mail', 'mailster-' . $precheck_id . '@precheck.email', $precheck_id );

		$n = mailster( 'notification' );
		$n->to( $receiver );
		$n->subject( esc_html__( 'Mailster Health Check', 'mailster' ) );
		$n->template( 'health_check' );
		$n->requeue( false );
		$success = $n->add();
		$mail    = $n->mail;

		if ( $success ) {
			return $precheck_id;
		}

		return new WP_Error( 'send_test', 'Could not send test mail', $mail->get_errors() );
	}
}
