<?php

class FooTest extends MailsterTestCase {

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
	function test_sent() {

		$this->assertEquals( 1, 1 );
	}

	/**
	 *
	 *
	 * @covers *
	 */
	function test_send2() {

		$this->assertEquals( 1, 1 );
	}

}
