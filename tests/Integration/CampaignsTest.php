<?php


namespace Mailster\Tests\Unit;

use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * These tests prove test setup works.
 *
 * They are useful for debugging.
 */
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

		// self::$message = mailcatcher()->get_by_subject( $subject );
	}

	function testCampaignCreated() {
		$this->assertTrue( ! is_wp_error( self::$campaign_id ) );
	}

}
