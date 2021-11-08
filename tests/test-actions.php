<?php
/**
 * An example test case.
 */
class Tests_Actions extends WP_UnitTestCase {



	function test_sent() {
		do_action( 'mailster_send', 1, 1 );

		$actions = mailster( 'actions' )->get_by_subscriber( 1 );

		$this->assertEquals( $actions['sent'], 1 );
	}



	function test_open() {
		do_action( 'mailster_open', 1, 1 );

		$actions = mailster( 'actions' )->get_by_subscriber( 1 );

		$this->assertEquals( $actions['opens'], 1 );
	}



	function test_click() {
		do_action( 'mailster_click', 1, 1, 'http://google.com' );

		$actions = mailster( 'actions' )->get_by_subscriber( 1 );

		$this->assertEquals( $actions['clicks'], 1 );
	}



	function test_unsubscribes() {
		do_action( 'mailster_unsubscribe', 1, 1 );

		$actions = mailster( 'actions' )->get_by_subscriber( 1 );

		$this->assertEquals( $actions['unsubs'], 1 );
	}



	function test_softbounce() {
		do_action( 'mailster_bounce', 1, 1, false );

		$actions = mailster( 'actions' )->get_by_subscriber( 1 );

		$this->assertEquals( $actions['softbounces'], 1 );
	}



	function test_hardbounce() {
		do_action( 'mailster_bounce', 1, 1, true );

		$actions = mailster( 'actions' )->get_by_subscriber( 1 );

		$this->assertEquals( $actions['bounces'], 1 );
	}


}
