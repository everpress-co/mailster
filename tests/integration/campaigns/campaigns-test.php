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

}
