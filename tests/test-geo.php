<?php

/**
 * An example test case.
 */
class Tests_Geo extends WP_UnitTestCase {

	protected static $ip;
	protected static $ip6;


	function test_dummy() {
		$this->assertTrue( true );
	}

	function setUp() {
		parent::setUp();

		add_filter( 'mailster_location_db_folder', array( $this, 'location_db_folder' ) );

		mailster_update_option( 'track_location', true );

		// from https://my.pingdom.com/probes/feed
		self::$ip  = '52.63.167.55';
		self::$ip6 = '2a02:5740:24:14::4073';
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_city_db_exist() {
		$folder = trailingslashit( apply_filters( 'mailster_location_db_folder', MAILSTER_UPLOAD_DIR ) );
		$this->assertTrue( file_exists( $folder . 'CountryDB.dat' ), 'City database does not exist in ' . $folder );
	}

	function test_country_db_exist() {
		$folder = trailingslashit( apply_filters( 'mailster_location_db_folder', MAILSTER_UPLOAD_DIR ) );
		$this->assertTrue( file_exists( $folder . 'CityDB.dat' ), 'Country database does not exist in ' . $folder );
	}

	function test_ip_4_2_country_code() {
		$this->assertEquals( 'AU', mailster_ip2Country( self::$ip ), sprintf( 'Not able to test IP address %s.', self::$ip ) );
	}

	function test_ip_4_2_country() {
		$this->assertEquals( 'Australia', mailster_ip2Country( self::$ip, 'name' ), sprintf( 'Not able to test IP address %s.', self::$ip ) );
	}

	function test_ip_4_2_city() {
		$this->assertEquals( 'Sydney', mailster_ip2City( self::$ip, 'city' ), sprintf( 'Not able to test IP address %s.', self::$ip ) );
	}

	function _test_ip_6_2_country_code() {
		$this->assertEquals( 'SE', mailster_ip2Country( self::$ip6 ), sprintf( 'Not able to test IP address %s.', self::$ip6 ) );
	}

	function _test_ip_6_2_country() {
		$this->assertEquals( 'Sweden', mailster_ip2Country( self::$ip6, 'name' ), sprintf( 'Not able to test IP address %s.', self::$ip6 ) );
	}

	function _test_ip_6_2_city() {
		$this->assertEquals( 'XXX', mailster_ip2City( self::$ip6, 'city' ), sprintf( 'Not able to test IP address %s.', self::$ip6 ) );
	}


	function location_db_folder( $ocation ) {
		return '/Users/Xaver/Sites/dev.local/app/public/wp-content/uploads';
	}





}
