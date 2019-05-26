<?php

class MailsterSecurity {


	public function __construct() {

		add_filter( 'plugins_loaded', array( &$this, 'init' ) );
		add_action( 'mailster_verify_subscriber', array( $this, 'verify_subscriber' ) );

	}


	public function init() {
	}

	/**
	 *
	 *
	 * @param unknown $entry
	 * @return unknown
	 */
	public function verify_subscriber( $entry ) {

		if ( is_wp_error( $entry ) ) {
			return $entry;
		}

		if ( ! isset( $entry['email'] ) ) {
			return $entry;
		}
		if ( ! mailster_option( 'sev_import' ) && defined( 'MAILSTER_DO_BULKIMPORT' ) && MAILSTER_DO_BULKIMPORT ) {
			return $entry;
		}

		if ( is_admin() && isset( $entry['ID'] ) && ! isset( $_POST['action'] ) ) {
			$subscriber = mailster( 'subscribers' )->get( $entry['ID'], false );
			if ( $subscriber && $subscriber->email == $entry['email'] ) {
				return $entry;
			}
		}

		$is_valid = $this->verify( $entry );
		if ( is_wp_error( $is_valid ) ) {
			return $is_valid;
		}

		return $entry;

	}

	/**
	 *
	 *
	 * @param unknown $email
	 * @return unknown
	 */
	private function verify( $entry ) {

		$email = $entry['email'];
		$ip = mailster_get_ip();

		list( $user, $domain ) = explode( '@', $email );

		// check for email addresses
		if ( $this->match( $email, mailster_option( 'blacklisted_emails' ) ) ) {
			return new WP_Error( 'email', 'blacklisted_email', 'blacklisted' );
		}

		// check for white listed
		if ( $this->match( $domain, mailster_option( 'whitelisted_emails' ) ) ) {
			return true;
		}

		// check for domains
		if ( $this->match( $domain, mailster_option( 'blacklisted_domains' ) ) ) {
			return new WP_Error( 'blacklisted',  'blacklisted' , 'email' );
		}

		// check DEP
		if ( $this->match( $domain, $this->get_dep_domains( ) ) ) {
			return new WP_Error( 'dep_error', 'dep_domain', 'email' );
		}

		// check MX record
		if ( mailster_option( 'check_mx' ) && function_exists( 'checkdnsrr' ) ) {
			if ( ! checkdnsrr( $domain, 'MX' ) ) {
				return new WP_Error( 'mx_error',  'mx_check', 'email' );
			}
		}

		// check via SMTP server
		if ( mailster_option( 'check_smtp' ) ) {

			$valid = $this->smtp_check( $email );
			if ( ! $valid ) {
				return new WP_Error( 'smtp_error', 'smtpcheck', 'email' );
			}
		}

		// check via Akismet if enabled
		if ( $this->is_akismet_block( $email, $ip, $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_REFERER'] ) ) {
			return new WP_Error( 'aksimet', 'aksimet', 'email' );
		}

		// check Antiflood
		if ( $this->is_flood( $ip ) ) {
			return new WP_Error( 'dep_error', 'flood', 'email' );
		}

		return true;

	}




	/**
	 *
	 *
	 * @param unknown $check (optional)
	 * @return unknown
	 */
	public function match( $string, $haystack ) {
		if ( ! $haystack ) {
			return false;
		}
		$lines = is_array( $haystack ) ? $haystack : explode( "\n", $haystack );
		foreach ( $lines as $line ) {
			$line = trim( $line );
			if ( $line == $string ) {
				return true;
			}
			if ( preg_match( '/^' . preg_quote( $line ) . '$/', $string ) ) {
				return true;
			}
		}

		return false;
	}




	/**
	 *
	 *
	 * @param unknown $check (optional)
	 * @return unknown
	 */
	public function get_dep_domains() {

		if ( ! mailster_option( 'reject_dep' ) ) {
			return array();
		}

		include MAILSTER_DIR . 'includes/dep.php';

		return apply_filters( 'mailster_dep_domains', $dep_domains );

	}


	public function is_flood( $ip ) {

		$time = mailster_option( 'antiflood' );

		if ( ! $time ) {
			return false;
		}

		$key = 'mailster_ip_check_' . md5( ip2long( $ip ) );

		if ( ! ($set = get_transient( $key )) ) {
			set_transient( $key, time(), $time );
			return false;
		}

		return true;

	}


	public function smtp_check( $email, $from = null ) {
		if ( is_null( $from ) ) {
			$from = mailster_option( 'from' );
		}
		list( $user, $domain ) = explode( '@', $email );

		require_once MAILSTER_DIR . 'classes/libs/smtp-validate-email/Validator.php';
		require_once MAILSTER_DIR . 'classes/libs/smtp-validate-email/Exceptions/Exception.php';
		require_once MAILSTER_DIR . 'classes/libs/smtp-validate-email/Exceptions/NoHelo.php';
		require_once MAILSTER_DIR . 'classes/libs/smtp-validate-email/Exceptions/NoResponse.php';
		require_once MAILSTER_DIR . 'classes/libs/smtp-validate-email/Exceptions/NoTimeout.php';
		require_once MAILSTER_DIR . 'classes/libs/smtp-validate-email/Exceptions/Timeout.php';
		require_once MAILSTER_DIR . 'classes/libs/smtp-validate-email/Exceptions/NoConnection.php';
		require_once MAILSTER_DIR . 'classes/libs/smtp-validate-email/Exceptions/NoMailFrom.php';
		require_once MAILSTER_DIR . 'classes/libs/smtp-validate-email/Exceptions/NoTLS.php';
		require_once MAILSTER_DIR . 'classes/libs/smtp-validate-email/Exceptions/SendFailed.php';
		require_once MAILSTER_DIR . 'classes/libs/smtp-validate-email/Exceptions/UnexpectedResponse.php';

		$validator = new SMTPValidateEmail\Validator( $email, $from );
		$smtp_results = $validator->validate();
		$valid = (isset( $smtp_results[ $email ] ) && 1 == $smtp_results[ $email ]) || ! ! array_sum( $smtp_results['domains'][ $domain ]['mxs'] );

		return $valid;

	}

	function is_akismet_block( $email, $ip, $agent, $referrer ) {
		if ( ! class_exists( 'Akismet' ) ) {
			return false;
		}

		$response = Akismet::http_post(Akismet::build_query(array(
			'blog' => home_url(),
			'referrer' => $referrer,
			'user_agent' => $agent,
			'comment_type' => 'signup',
			'comment_author_email' => $email,
			'user_ip' => $ip,
		)), 'comment-check');

		if ( $response && $response[1] == 'true' ) {
			return true;
		}
		return false;
	}


}
