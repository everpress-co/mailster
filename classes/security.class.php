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

		$is_valid = $this->verify( $entry['email'] );
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
	private function verify( $email ) {

		list( $user, $domain ) = explode( '@', $email );

		// check for email addresses
		$blacklisted_emails = explode( "\n", mailster_option( 'blacklisted_emails', '' ) );
		if ( in_array( $email, $blacklisted_emails ) ) {
			return new WP_Error( 'sev_emails_error', mailster_text( 'error' ), 'email' );
		}

		// check for white listed
		$whitelisted_domains = explode( "\n", mailster_option( 'whitelisted_emails', '' ) );
		if ( in_array( $domain, $whitelisted_domains ) ) {
			return true;
		}

		// check for domains
		$blacklisted_domains = explode( "\n", mailster_option( 'blacklisted_domains', '' ) );
		if ( in_array( $domain, $blacklisted_domains ) ) {
			return new WP_Error( 'sev_domains_error', mailster_text( 'error' ), 'email' );
		}

		// check DEP
		if ( $dep_domains = $this->get_dep_domains( false ) ) {
			if ( in_array( $domain, $dep_domains ) ) {
				return new WP_Error( 'sev_dep_error', mailster_text( 'error' ), 'email' );
			}
		}

		// check MX record
		if ( mailster_option( 'sev_check_mx' ) && function_exists( 'checkdnsrr' ) ) {
			if ( ! checkdnsrr( $domain, 'MX' ) ) {
				return new WP_Error( 'sev_mx_error', mailster_text( 'error' ), 'email' );
			}
		}

		// check via SMTP server
		if ( mailster_option( 'sev_check_smtp' ) ) {

			require_once MAILSTER_DIR . '/classes/libs/smtp-validate-email.php';

			$from = mailster_option( 'from' );

			$validator = new SMTP_Validate_Email( $email, $from );
			$smtp_results = $validator->validate();
			$valid = (isset( $smtp_results[ $email ] ) && 1 == $smtp_results[ $email ]) || ! ! array_sum( $smtp_results['domains'][ $domain ]['mxs'] );
			if ( ! $valid ) {
				return new WP_Error( 'sev_smtp_error', mailster_text( 'error' ), 'email' );
			}
		}

		return true;

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


}
