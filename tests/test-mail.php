<?php

/**
 * An example test case.
 */
class Tests_Mail extends WP_UnitTestCase {

	function test_dummy() {
		$this->assertTrue( true );
	}

	function test_wp_mail() {
		$data    = file_get_contents( MAILSTER_UPLOAD_DIR . '/templates/mymail/index.html' );
		$subject = 'Test single email Plain';
		$success = wp_mail( 'xaver@revaxarts.com', $subject, 'this is the message from wp_mail' );
		$this->assertTrue( $success );

		$x = mailcatcher()->get_by_subject( $subject );
	}


	function send_smtp_email( &$phpmailer ) {
		if ( ! is_object( $phpmailer ) ) {
			$phpmailer = (object) $phpmailer;
		}

		$phpmailer->Mailer     = 'smtp';
		$phpmailer->Host       = mailster_option( 'smtp_host' );
		$phpmailer->SMTPAuth   = mailster_option( 'smtp_auth' );
		$phpmailer->Port       = mailster_option( 'smtp_port' );
		$phpmailer->Username   = mailster_option( 'smtp_user' );
		$phpmailer->Password   = mailster_option( 'smtp_pwd' );
		$phpmailer->SMTPSecure = mailster_option( 'smtp_secure' );
		$phpmailer->From       = mailster_option( 'from' );
		$phpmailer->FromName   = mailster_option( 'from_name' );
	}


}
