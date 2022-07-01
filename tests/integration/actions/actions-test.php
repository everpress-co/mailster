<?php

class BarTest extends Mailster_UnitTestCase {

	public function set_up() {
		parent::set_up();
		// Your own additional setup.
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
	function testSent() {
		do_action( 'mailster_send', 1, 1 );

		$actions = mailster( 'actions' )->get_by_subscriber( 1 );

		$this->assertEquals( $actions['sent'], 1 );
	}

	/**
	 *
	 *
	 * @covers *
	 */
	function testOpen() {
		do_action( 'mailster_open', 1, 1 );

		$actions = mailster( 'actions' )->get_by_subscriber( 1 );

		$this->assertEquals( $actions['opens'], 1 );
	}

	/**
	 *
	 *
	 * @covers *
	 */
	function testClick() {
		do_action( 'mailster_click', 1, 1, 'http://google.com' );

		$actions = mailster( 'actions' )->get_by_subscriber( 1 );

		$this->assertEquals( $actions['clicks'], 1 );
	}

	/**
	 *
	 *
	 * @covers *
	 */
	function testUnsubscribes() {
		do_action( 'mailster_unsubscribe', 1, 1 );

		$actions = mailster( 'actions' )->get_by_subscriber( 1 );

		$this->assertEquals( $actions['unsubs'], 1 );
	}

	/**
	 *
	 *
	 * @covers *
	 */
	function testSoftbounce() {
		do_action( 'mailster_bounce', 1, 1, false );

		$actions = mailster( 'actions' )->get_by_subscriber( 1 );

		$this->assertEquals( $actions['softbounces'], 1 );
	}
	/**
	 *
	 *
	 * @covers *
	 */
	function testHardbounce() {
		do_action( 'mailster_bounce', 1, 1, true );

		$actions = mailster( 'actions' )->get_by_subscriber( 1 );

		$this->assertEquals( $actions['bounces'], 1 );
	}

}
