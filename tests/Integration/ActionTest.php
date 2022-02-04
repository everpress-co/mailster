<?php


namespace Mailster\Tests\Unit;

use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * These tests prove test setup works.
 *
 * They are useful for debugging.
 */
class ActionTest extends TestCase {

	function testSent() {
		do_action( 'mailster_send', 1, 1 );

		$actions = mailster( 'actions' )->get_by_subscriber( 1 );

		$this->assertEquals( $actions['sent'], 1 );
	}

}
