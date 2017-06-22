<?php

class MailsterTests {


	private $message;
	private $tests;
	private $current;

	private $errors;

	public function __construct( $test ) {

		$this->tests = $this->get_tests();
		$this->errors = array(
			'error_count' => 0,
			'warning_count' => 0,
			'notice' => new WP_Error(),
			'error' => new WP_Error(),
			'warning' => new WP_Error(),
			'success' => new WP_Error(),
		);

	}

	public function __call( $method, $args ) {

	}

	public function run( $test_id, $args = array() ) {

		if ( isset( $this->tests[ $test_id ] ) ) {

			$this->last_is_error = false;
			$this->last_error_test = null;
			$this->last_error_message = null;
			$this->last_error_type = 'success';

			$this->current_id = $test_id;
			$this->current = $this->tests[ $test_id ];

			call_user_func_array( array( &$this, $this->current ), $args );

			return ! ($this->last_error_test == $test_id);

		}

		return null;
	}

	public function get_tests() {
		$tests = get_class_methods( $this );
		$tests = preg_grep( '/^test_/', $tests );
		return array_values( $tests );

	}

	public function get_message() {

		$time = date( 'Y-m-d H:i:s' );

		return array(
			'test' => $this->current,
			'time' => $time,
			'html' => '<div class="mailster-test-result mailster-test-is-' . $this->last_error_type . '"><h4>' . $this->nicename( $this->current ) . '</h4><p class="mailster-test-result-more">' . $this->last_error_message . '</p></div>',
		);
	}

	public function nicename( $test ) {
		$test = ucwords( str_replace( array( 'test_', '_' ), array( '', ' ' ), $test ) );
		$test = str_replace( array( 'Php', 'Wordpress', 'Wp ' ), array( 'PHP', 'WordPress', 'WP ' ), $test );
		return $test;
	}

	public function get_next() {

		if ( isset( $this->tests[ $this->current_id + 1 ] ) ) {
			return $this->current_id + 1;
		}
		return null;
	}



	private function error( $msg ) {

		$this->failure( 'error', $msg );

	}


	private function warning( $msg ) {

		$this->failure( 'warning', $msg );

	}


	private function notice( $msg ) {

		$this->failure( 'notice', $msg );

	}

	private function success( $msg ) {

		$this->failure( 'success', $msg );

	}


	private function failure( $type, $msg ) {

		$backtrace = debug_backtrace();
		$test_id = $backtrace[2]['function'];

		if ( is_null( $test_id ) ) {
			$test_id = uniqid();
		}

		$failure = array( 'msg' => $msg );

		$this->errors[ $type ]->add( $test_id, $failure );
		$this->last_is_error = true;
		$this->last_error_type = $type;
		$this->last_error_test = $test_id;
		$this->last_error_message = $msg;

	}








	private function _test_error() {
			$this->error( 'This is a error error' );
	}
	private function _test_notice() {
			$this->notice( 'This is a notice error' );
	}
	private function _test_warning() {
			$this->warning( 'This is a warning error' );
	}
	private function _test_success() {
	}

	private function test_php_version() {
		if ( version_compare( PHP_VERSION, '5.3' ) < 0 ) {
			$this->error( sprintf( 'Mailster requires PHP version 5.3 or higher. Your current version is %s. Please update or ask your hosting provider to help you updating.', PHP_VERSION ) );
		} else {
			$this->success( 'You have version ' . PHP_VERSION );
		}
	}
	private function test_wordpress_version() {
		if ( version_compare( get_bloginfo( 'version' ), '3.8' ) < 0 ) {
			$this->error( sprintf( 'Mailster requires WordPress version 3.8 or higher. Your current version is %s.', get_bloginfo( 'version' ) ) );
		} else {
			$this->success( 'You have version ' . get_bloginfo( 'version' ) );

		}
	}
	private function test_verfied_installation() {

		if ( mailster()->is_verified() ) {
			$this->success( 'Thank you!' );
		} else {
			$this->error( 'Your Mailster installation is not verified! Please register via your <a href="' . admin_url( 'admin.php?page=mailster_dashboard' ) . '">dashboard</a>.' );
		}

	}
	private function test_dom_document_extension() {
		if ( ! class_exists( 'DOMDocument' ) ) {
			$this->error( 'Mailster requires the <a href="https://php.net/manual/en/class.domdocument.php" target="_blank">DOMDocument</a> library.' );
		}
	}
	private function test_wp_debug() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$this->warning( 'WP_DEBUG is enabled and should be disabled on a production site. Read more about it <a href="https://codex.wordpress.org/WP_DEBUG" target="_blank">here</a>.' );
		}
	}


	private function test_fsockopen_extension() {
		if ( ! function_exists( 'fsockopen' ) ) {
			$this->warning( 'Your server does not support <a href="https://php.net/manual/en/function.fsockopen.php" target="_blank">fsockopen</a>.' );
		}
	}
	private function test_content_directory() {
		$content_dir = dirname( MAILSTER_UPLOAD_DIR );
		if ( ! is_dir( $content_dir ) || ! wp_is_writable( $content_dir ) ) {
			$this->warning( sprintf( 'Your content folder in %s is not writable.', '"' . $content_dir . '"' ) );
		} else {
			$this->success( sprintf( 'Your content folder in %s is writable.', '"' . $content_dir . '"' ) );

		}
	}
	private function test_memory_limit() {
		if ( max( intval( @ini_get( 'memory_limit' ) ), intval( WP_MAX_MEMORY_LIMIT ) ) < 128 ) {
			$this->warning( 'Your Memory Limit is ' . size_format( WP_MEMORY_LIMIT * 1048576 ) . ', Mailster recommends at least 128 MB' );
		} else {
			$this->success( 'Your Memory Limit is ' . size_format( WP_MEMORY_LIMIT * 1048576 ) );
		}
	}

	private function test_plugin_location() {
		if ( MAILSTER_SLUG != 'mailster/mailster.php' ) {
			$this->warning( 'You have changed the plugin location of Mailster. This can cause problems while updating the plugin.' );
		} else {
		}
	}

	private function test_no_mailster_folder_in_root() {
		if ( is_dir( ABSPATH . 'mailster' ) ) {
			$this->error( 'There\'s a folder called \'mailster\' in ' . ABSPATH . ' which causes a conflict with campaign links! Please remove or rename this folder.' );
		}
	}
	private function test_working_cron() {

		$last_hit = get_option( 'mailster_cron_lasthit' );
		if ( ! $last_hit ) {
			$this->warning( 'Your cron is maybe not working. Please check <a href="https://kb.mailster.co/how-do-i-know-if-my-cron-is-working-correctly/" target="_blank">this article</a> on our knowledge base.' );
		} else {
			$this->success( sprintf( __( 'Last hit was %s ago', 'mailster' ), human_time_diff( $last_hit['timestamp'] ) ) );
		}
	}
	private function test_cron_lock() {

		mailster( 'cron' )->lock();

		if ( ! mailster( 'cron' )->is_locked() ) {
			$this->warning( 'Cron Lock mechanism is not working with the current method. Read more about this <a href="https://kb.mailster.co/what-is-a-cron-lock/" target="_blank">here</a>.' );
		} else {
			$this->success( ' No Cron Lock in place!' );
		}
		mailster( 'cron' )->unlock();

	}

	private function test_update_server_connection() {

		$response = wp_remote_post( 'https://update.mailster.co/' );
		$code = wp_remote_retrieve_response_code( $response );

		if ( is_wp_error( $response ) ) {
			$this->error( $response->get_error_message() . ' - Please allow connection to update.mailster.co!' );
		} elseif ( $code >= 200 && $code < 300 ) {
		} else {
			$this->error( 'does not work: ' . $code );
		}
	}



	private function test_wp_remote_post() {
		$response = wp_remote_post( 'https://www.paypal.com/cgi-bin/webscr', array(
			'sslverify' => true,
			'timeout' => 5,
			'body' => array( 'cmd' => '_notify-validate' ),
		) );

		$code = wp_remote_retrieve_response_code( $response );

		if ( is_wp_error( $response ) ) {
			$this->error( 'does not work: ' . $response->get_error_message() );
		} elseif ( $code >= 200 && $code < 300 ) {
		} else {
			$this->error( 'does not work: ' . $code );
		}

	}


	private function test_mailfunction() {
		$mail = mailster( 'mail' );
		$mail->to = 'deadend@newsletter-plugin.com';
		$mail->subject = 'test';
		$mail->debug();

		if ( ! $mail->send_notification( 'Sendtest', 'this test message can get deleted', array( 'notification' => '' ), false ) ) {
			$error_message = strip_tags( $mail->get_errors() );
			$msg = 'You are not able to send mails with the current delivery settings!';

			if ( false !== stripos( $error_message, 'smtp connect()' ) ) {
				$this->error( $msg . '<br>' . $error_message . '<br>Get more info <a href="https://kb.mailster.co/smtp-error-could-not-connect-to-smtp-host/" target="_blank">here</a>.' );
			} elseif ( false !== stripos( $error_message, 'data not accepted' ) ) {
				$this->error( $msg . '<br>' . $error_message . '<br>Get more info <a href="https://kb.mailster.co/smtp-error-data-not-accepted/" target="_blank">here</a>.' );
			} else {
				$this->error( $msg . '<br>' . $error_message );
			}

			// $this->error( strip_tags( $mail->get_errors() ));
		}

	}

	private function test_db_version() {

		if ( get_option( 'mailster_dbversion' ) != MAILSTER_DBVERSION ) {
			$this->error( 'Your current DB version is ' . get_option( 'mailster_dbversion' ) . ' and should be ' . MAILSTER_DBVERSION . '. Please visit the <a href="' . admin_url( 'admin.php?page=mailster_update' ) . '">update page</a> to run necessary updates.' );

		}

	}

	private function test_port_110() {

		$this->port_test( 110, 'pop.gmx.net' );

	}
	private function test_port_995() {

		$this->port_test( 995, 'pop.gmail.com' );

	}
	private function test_port_993() {

		$this->port_test( 993, 'smtp.gmail.com' );

	}
	private function test_port_25() {

		$this->port_test( 25, 'smtp.gmail.com' );

	}
	private function test_port_2525() {

		$this->port_test( 2525, 'smtp.sparkpostmail.com' );

	}
	private function test_port_465() {

		$this->port_test( 465, 'smtp.gmail.com' );

	}
	private function test_port_587() {

		$this->port_test( 587, 'smtp.gmail.com' );

	}
	private function port_test( $port, $domain ) {

		$result = mailster( 'settings' )->check_port( $domain, $port );
		if ( strpos( $result, 'open' ) !== false ) {
			$this->success( sprintf( 'Port %d is open an can be used! <code>' . $result . '</code>', $port ) );
		} else {
			$this->warning( $result );
		}

	}

	private function test_permalink_structure() {

		if ( ! mailster( 'helper' )->using_permalinks() ) {
			$this->notice( 'You are not using a permalink structure. Define one <a href="' . admin_url( 'options-permalink.php' ) . '">here</a>.' );
		} elseif ( ! mailster()->check_link_structure() ) {
			$this->error( 'A problem with you permalink structure. Please check the slugs on the <a href="' . admin_url( 'admin.php?page=mailster_settings#frontend' ) . '">frontend tab</a>.' );
		}

	}


}
