<?php

class CampaignsTest extends Mailster_UnitTestCase {

	static $campaign_id;
	static $unique_id;
	static $message_id;
	static $message;


	public function set_up() {
		parent::set_up();
		// Your own additional setup.

		$unique_id = uniqid();

		$campaign_id = wp_insert_post(
			array(
				'post_title'   => 'Test Newsletter',
				'post_type'    => 'newsletter',
				'post_content' => 'Hi {fullname}! <a href="http://example.com">example link</a>',
			)
		);

		$subject = 'PHP Unit Test Subject (' . $unique_id . ')';

		mailster( 'campaigns' )->update_meta(
			$campaign_id,
			array(
				'subject'    => $subject,
				'from'       => 'PHP Unit',
				'from_email' => 'testdev@everpress.co',
			)
		);

		$success = mailster( 'campaigns' )->send( $campaign_id, 1 );


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
	public function testCampaignCreated() {

		echo '<pre>'.print_r($GLOBALS['phpmailer'], true).'</pre>';
		tests_retrieve_phpmailer_instance()

		echo '<pre>'.print_r(tests_retrieve_phpmailer_instance(), true).'</pre>';


		$this->assertTrue( true );
	}


}
