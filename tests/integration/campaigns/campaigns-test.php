<?php

class CampaignsTest extends Mailster_UnitTestCase {

	static $campaign_id;
	static $unique_id;
	static $message_id;
	static $message;


	public function set_up() {
		parent::set_up();
		// Your own additional setup.

		self::$unique_id = uniqid();

		self::$campaign_id = wp_insert_post(
			array(
				'post_title'   => 'Test Newsletter',
				'post_type'    => 'newsletter',
				'post_content' => 'Hi {fullname}! <a href="http://example.com">example link</a>',
			)
		);

		$subject = 'PHP Unit Test Subject (' . self::$unique_id . ')';

		mailster( 'campaigns' )->update_meta(
			self::$campaign_id,
			array(
				'subject'    => $subject,
				'from'       => 'PHP Unit',
				'from_email' => 'testdev@everpress.co',
			)
		);

		$success = mailster( 'campaigns' )->send( self::$campaign_id, 1 );

	}

	public function tear_down() {
		// Your own additional tear down.
		parent::tear_down();
	}



	/**
	 *
	 *
	 * @covers *
	 */
	public function test_one() {
		$this->assertTrue( true );

	}

	/**
	 *
	 *
	 * @covers *
	 */
	public function test_two() {

		$this->assertTrue( true );

		$x = wp_mail( 'xaver@everpress.co', 'test 1', 'test' );

		$email = tests_retrieve_phpmailer_instance()->get_sent();

	}

	function testCampaignCreated() {
		$this->assertTrue( ! is_wp_error( self::$campaign_id ) );
	}

	function testCampaignSent() {
		$this->assertNotNull( tests_retrieve_phpmailer_instance()->get_sent() );
	}

	function testCampaignTestHeaders() {
		$message = tests_retrieve_phpmailer_instance()->get_sent();

		echo '<pre>'.print_r($message, true).'</pre>';

		$source = $message->source;

		$this->assertRegExp( '/X-Mailer/', $source, 'X-Mailer Header is missing' );
		$this->assertRegExp( '/X-Mailster: [a-f0-9]{32}/', $source, 'X-Mailster Header is wrong or missing' );
		$this->assertRegExp( '/X-Mailster-Campaign: ' . self::$campaign_id . '/', $source, 'X-Mailster-Campaign is wrong' );
		$this->assertRegExp( '/X-Mailster-ID: ' . mailster_option( 'ID' ) . '/', $source, 'X-Mailster-ID is wrong' );
		$this->assertRegExp( '/List-Unsubscribe:/', $source, 'List-Unsubscribe is missing' );
	}

}
