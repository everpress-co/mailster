<?php

use Yoast\WPTestUtils\BrainMonkey\YoastTestCase;

/**
 * TestCase base class for convenience methods.
 */
abstract class MailsterTestCase extends YoastTestCase {

	public function set_up() {
		parent::set_up();
		// Your own additional setup.

	}

	public function tear_down() {		
		// Your own additional tear down.
		parent::tear_down();
	}
}
