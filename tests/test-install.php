<?php

/**
 * An example test case.
 */
class Tests_Install extends WP_UnitTestCase {

	function test_dummy() {
		$this->assertTrue( true );
	}


	function _test_js_minification() {
		$jsminfiles = glob( MAILSTER_DIR . 'assets/js/*.min.js' );
		if ( empty( $jsminfiles ) ) {
			$this->markTestSkipped( 'No minified JS files!' );
		}

		$jsfiles = glob( MAILSTER_DIR . 'assets/js/*.js' );

		$this->assertGreaterThanOrEqual( 1, count( $jsminfiles ) );
		$this->assertEquals( count( $jsminfiles ) * 2, count( $jsfiles ) );
	}

	function _test_css_minification() {
		$cssminfiles = glob( MAILSTER_DIR . 'assets/css/*.min.css' );
		if ( empty( $cssminfiles ) ) {
			$this->markTestSkipped( 'No minified CSS files!' );
		}

		$cssfiles = glob( MAILSTER_DIR . 'assets/css/*.css' );

		$this->assertGreaterThanOrEqual( 1, count( $cssminfiles ) );
		$this->assertEquals( count( $cssminfiles ) * 2, count( $cssfiles ) );
	}

	function test_db_structure() {
		global $wpdb;

		$sql = "SHOW TABLES WHERE Tables_in_{$wpdb->dbname} LIKE '{$wpdb->prefix}mailster_%'";

		$db_tables = $wpdb->get_col( $sql );
		sort( $db_tables );

		$tables = mailster()->get_tables( true );
		sort( $tables );

		$this->assertEquals( $tables, $db_tables );
	}

	function test_version_number() {
		$version     = MAILSTER_VERSION;
		$plugin_data = get_plugin_data( MAILSTER_DIR . '/' . basename( MAILSTER_SLUG ) );

		$this->assertArrayHasKey( 'Version', $plugin_data, 'Missing Version in plugin header' );

		$plugin_version = $plugin_data['Version'];

		$this->assertEquals( $version, $plugin_version, 'Version in Plugin header doesn\'t match' );

		$readme = file_get_contents( MAILSTER_DIR . '/readme.txt' );

		preg_match( '/^stable tag: (.*)/mi', $readme, $readme_version );
		$readme_version = $readme_version[1];

		$this->assertEquals( $version, $readme_version, 'Version in Readme doesn\'t match' );

		$this->assertTrue( (bool) preg_match( '/^= ' . preg_quote( $version ) . ' =/m', $readme ), 'Missing Changelog in readme' );
	}

	function test_mailster_options_set() {
		$this->assertTrue( get_option( 'mailster' ) - time() < 5 );
	}

	function test_template_copied_correctly() {
		$files = $this->scan_user_uploads();
		// html files
		$this->assertCount( 2, preg_grep( '#\/mailster\/templates\/mymail\/(.*)\.html#', $files ) );
		// images
		$this->assertCount( 82, preg_grep( '#\/mailster\/templates\/mymail\/(.*)\.png#', $files ) );

		// html files
		$this->assertCount( 2, preg_grep( '#\/mailster\/templates\/mailster\/(.*)\.html#', $files ) );
		// images
		$this->assertCount( 1, preg_grep( '#\/mailster\/templates\/mailster\/(.*)\.png#', $files ) );
	}
	function test_capabilities() {
		global $wp_roles;

		require MAILSTER_DIR . 'includes/capability.php';

		foreach ( $mailster_capabilities as $capability => $data ) {
			$data['roles'][] = 'administrator';

			foreach ( $data['roles'] as $role ) {
				$this->assertArrayHasKey( $capability, $wp_roles->roles[ $role ]['capabilities'] );
			}
		}
	}
	function _test_admin_pages() {
		$this->go_to( 'https://wordpress.dev/wp-admin/admin.php?page=mailster_welcome' );
	}
	function test_php_files() {
		$files = list_files( MAILSTER_DIR, 6 );

		foreach ( $files as $file ) {
			$ext = pathinfo( $file, PATHINFO_EXTENSION );
			if ( ! file_exists( $file ) ) {
				continue;
			}
			if ( 'php' == $ext ) {
				// require_once $file;
			}
		}

		$this->assertTrue( true );
	}



}
