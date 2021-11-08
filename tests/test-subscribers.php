<?php

/**
 * An example test case.
 */
class Tests_Subscribers extends WP_UnitTestCase {


	function test_dummy() {
		$this->assertTrue( true );
	}

	function _test_first_user() {
		$user_id = mailster( 'subscribers' )->get( 1 );

		$this->assertNotFalse( $user_id, 'User not created' );
		$this->assertEquals( $user_id->ID, 1 );
	}

	function test_create_user_by_email() {
		$user_id = mailster( 'subscribers' )->add( 'testuser@revaxarts.com' );

		$user = mailster( 'subscribers' )->get( $user_id );

		$this->assertEquals( $user->email, 'testuser@revaxarts.com' );
	}

	function test_create_user() {
		$user_id = mailster( 'subscribers' )->add(
			array(
				'email'     => 'testuser2@revaxarts.com',
				'firstname' => 'Test',
				'lastname'  => 'User',
				'form'      => 1,
			)
		);

		$user = mailster( 'subscribers' )->get( $user_id );

		$this->assertEquals( $user->email, 'testuser2@revaxarts.com' );

		$lists = mailster( 'subscribers' )->get_lists( $user_id );
		$this->assertEquals( $lists[0]->confirmed, time(), 'Confirmation date of list is not set' );
	}

	function test_create_user_pending() {
		$user_id = mailster( 'subscribers' )->add(
			array(
				'email'     => 'pendingsubscriber@revaxarts.com',
				'firstname' => 'Test',
				'lastname'  => 'User',
				'status'    => 0,
			)
		);

		$user = mailster( 'subscribers' )->get( $user_id );

		$this->assertEquals( $user->status, 0 );
		$this->assertEquals( $user->email, 'pendingsubscriber@revaxarts.com' );

		$message = mailcatcher()->get_by_subject( 'Please confirm' );

		$lists = mailster( 'subscribers' )->get_lists( $user_id );
		$this->assertEquals( $lists[0]->confirmed, 0, 'Confirmation date of list not 0' );
	}

	function test_create_user_exists() {
		$user_1 = mailster( 'subscribers' )->add( 'testuser@revaxarts.com' );
		$user_2 = mailster( 'subscribers' )->add( 'testuser@revaxarts.com' );

		$this->assertTrue( is_wp_error( $user_2 ) );
	}


	function test_unsubscribe() {
		$user_id = mailster( 'subscribers' )->add(
			array(
				'email'     => 'unsubscribe@revaxarts.com',
				'firstname' => 'Test',
				'lastname'  => 'User',
				'status'    => 1,
			)
		);

		$subscriber = mailster( 'subscribers' )->get( $user_id );

		$this->assertTrue( mailster( 'subscribers' )->unsubscribe( $subscriber->ID ) );

		$subscriber = mailster( 'subscribers' )->get( $user_id );

		$this->assertEquals( $subscriber->status, 2 );
	}

	function test_bounce() {
		$user_id = mailster( 'subscribers' )->add(
			array(
				'email'     => 'unsubscribe@revaxarts.com',
				'firstname' => 'Test',
				'lastname'  => 'User',
				'status'    => 1,
			)
		);

		$subscriber = mailster( 'subscribers' )->get( $user_id );

		$this->assertTrue( mailster( 'subscribers' )->bounce( $subscriber->ID, 1, true ) );

		$subscriber = mailster( 'subscribers' )->get( $user_id );

		$this->assertEquals( $subscriber->status, 3 );
	}

}
