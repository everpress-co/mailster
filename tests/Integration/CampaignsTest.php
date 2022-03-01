<?php


namespace Mailster\Tests\Unit;

use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

class CampaignsTest extends TestCase {

	static $campaign_id;
	static $unique_id;
	static $message_id;
	static $message;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();
		_setup();

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

		self::$message = sendria()->get_by_subject( $subject );

	}

	function testCampaignCreated() {
		$this->assertTrue( ! is_wp_error( self::$campaign_id ) );
	}

	function testCampaignSent() {
		$this->assertNotNull( self::$message );
	}

	function testCampaignTestHeaders() {
		$message = self::$message;

		$source = $message->source;

		$this->assertRegExp( '/X-Mailer/', $source, 'X-Mailer Header is missing' );
		$this->assertRegExp( '/X-Mailster: [a-f0-9]{32}/', $source, 'X-Mailster Header is wrong or missing' );
		$this->assertRegExp( '/X-Mailster-Campaign: ' . self::$campaign_id . '/', $source, 'X-Mailster-Campaign is wrong' );
		$this->assertRegExp( '/X-Mailster-ID: ' . mailster_option( 'ID' ) . '/', $source, 'X-Mailster-ID is wrong' );
		$this->assertRegExp( '/List-Unsubscribe:/', $source, 'List-Unsubscribe is missing' );
	}

	function _testCampaignTestLinks() {
		$baselink = mailster()->get_base_link( self::$campaign_id );

		$message = self::$message;

		$source = $message->source;

		echo '<pre>' . print_r( wp_extract_urls( $source ), true ) . '</pre>';

		if ( preg_match_all( '#' . preg_quote( trailingslashit( $baselink ) ) . '[a-f0-9]{32}\/[A-Za-z0-9]+#', $source, $matches ) ) {
		}

		echo '<pre>' . print_r( $matches, true ) . '</pre>';
	}

}
