<?php

use Yoast\WPTestUtils\WPIntegration\TestCase;

/**
 * TestCase base class for convenience methods.
 */
abstract class Mailster_UnitTestCase extends TestCase {

	public function set_up() {
		parent::set_up();
		// Your own additional setup.

	}

	public function tear_down() {		
		// Your own additional tear down.
		parent::tear_down();
	}

}
