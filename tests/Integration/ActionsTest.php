<?php


namespace Mailster\Tests\Unit;

use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * These tests prove test setup works.
 *
 * They are useful for debugging.
 */
class ActionTests extends TestCase {

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();
		_setup();
	}

	function testSent() {
		do_action( 'mailster_send', 1, 1 );

		$actions = mailster( 'actions' )->get_by_subscriber( 1 );

		$this->assertEquals( $actions['sent'], 1 );
	}


	function testOpen() {
		do_action( 'mailster_open', 1, 1 );

		$actions = mailster( 'actions' )->get_by_subscriber( 1 );

		$this->assertEquals( $actions['opens'], 1 );
	}


	function testClick() {
		do_action( 'mailster_click', 1, 1, 'http://google.com' );

		$actions = mailster( 'actions' )->get_by_subscriber( 1 );

		$this->assertEquals( $actions['clicks'], 1 );
	}


	function testUnsubscribes() {
		do_action( 'mailster_unsubscribe', 1, 1 );

		$actions = mailster( 'actions' )->get_by_subscriber( 1 );

		$this->assertEquals( $actions['unsubs'], 1 );
	}


	function testSoftbounce() {
		do_action( 'mailster_bounce', 1, 1, false );

		$actions = mailster( 'actions' )->get_by_subscriber( 1 );

		$this->assertEquals( $actions['softbounces'], 1 );
	}

	function testHardbounce() {
		do_action( 'mailster_bounce', 1, 1, true );

		$actions = mailster( 'actions' )->get_by_subscriber( 1 );

		$this->assertEquals( $actions['bounces'], 1 );
	}

}
