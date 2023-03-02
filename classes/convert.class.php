<?php

class MailsterConvert {

	public function __construct() {

		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 100 );

	}


	public function admin_menu() {

		if ( get_option( 'mailster_freemius' ) ) {
			return;
		}

		$this->notice();

		$page = add_submenu_page( 'edit.php?post_type=newsletter', esc_html__( 'Convert License', 'mailster' ), esc_html__( 'Convert License', 'mailster' ), 'manage_options', 'mailster_convert', array( &$this, 'convert_page' ) );
		add_action( 'load-' . $page, array( &$this, 'script_styles' ) );

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

	public function notice() {

		$msg  = '<h2>' . esc_html__( '[Action Required] Your Mailster license need to be transferred!', 'mailster' ) . '</h2>';
		$msg .= '<p>' . sprintf( esc_html__( ' Please %1$s and read more about this on our %2$s.', 'mailster' ), '<a href="' . admin_url( 'admin.php?page=mailster_convert' ) . '">' . esc_html__( 'start the process now', 'mailster' ) . '</a>', '<a href="' . mailster_url( 'https://kb.mailster.co/63fe029de6d6615225474599' ) . '" class="mailster-help-link" data-article="63fe029de6d6615225474599">' . esc_html__( 'Knowledge Base', 'mailster' ) . '</a>' ) . '</p>';
		$msg .= '<p><a class="button button-primary" href="' . admin_url( 'admin.php?page=mailster_convert' ) . '">' . esc_html__( 'Convert now', 'mailster' ) . '</a> or <a href="' . mailster_url( 'https://kb.mailster.co/63fe029de6d6615225474599' ) . '" class="mailster-help-link" data-article="63fe029de6d6615225474599">' . esc_html__( 'read more about it', 'mailster' ) . '</a></p>';

		mailster_notice( $msg, 'info', true, 'mailster_freemius' );

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

		if ( $code !== 200 ) {
			return new WP_Error( $code, $response->message );
		}

		$is_marketing_allowed = true;

		$migrate = mailster_freemius()->activate_migrated_license( $response->data->secret_key, $is_marketing_allowed );

		if ( is_wp_error( $migrate ) ) {
			return $migrate;
		}

		if ( strtotime( $response->data->support ) > time() ) {
			update_option( 'mailster_support', strtotime( $response->data->support ) );
		}

		$response->migrate = $migrate;

		return $response;

	}

}
