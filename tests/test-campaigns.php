<?php

/**
 * An example test case.
 */
class Tests_Campaigns extends WP_UnitTestCase {

	static $campaign_id;
	static $unique_id;
	static $message_id;
	static $message;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		self::$unique_id = uniqid();

		self::$campaign_id = self::factory()->post->create(
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

		mailster( 'campaigns' )->send( self::$campaign_id, 1 );
		self::$message = mailcatcher()->get_by_subject( $subject );
	}


	function test_dummy() {
		$this->assertTrue( true );
	}

	function test_campaign_created() {
		$this->assertTrue( ! is_wp_error( self::$campaign_id ) );
	}

	function test_campaign_send() {
		$this->assertNotNull( self::$message );
	}

	function test_campaign_test_headers() {
		$message = self::$message;

		$source = $message->source;

		$this->assertRegExp( '/X-Mailer/', $source, 'X-Mailer Header is missing' );
		$this->assertRegExp( '/X-Mailster: [a-f0-9]{32}/', $source, 'X-Mailster Header is wrong or missing' );
		$this->assertRegExp( '/X-Mailster-Campaign: ' . self::$campaign_id . '/', $source, 'X-Mailster-Campaign is wrong' );
		$this->assertRegExp( '/X-Mailster-ID: ' . mailster_option( 'ID' ) . '/', $source, 'X-Mailster-ID is wrong' );
		$this->assertRegExp( '/List-Unsubscribe:/', $source, 'List-Unsubscribe is missing' );
	}

	function _test_campaign_test_links() {
		$baselink = mailster()->get_base_link( self::$campaign_id );

		$message = self::$message;

		$source = $message->source;

		echo '<pre>' . print_r( wp_extract_urls( $source ), true ) . '</pre>';

		if ( preg_match_all( '#' . preg_quote( trailingslashit( $baselink ) ) . '[a-f0-9]{32}\/[A-Za-z0-9]+#', $source, $matches ) ) {
		}

		echo '<pre>' . print_r( $matches, true ) . '</pre>';
	}

}
