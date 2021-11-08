<?php

/**
 * An example test case.
 */
// class Tests_Permalinks  {
class Tests_Permalinks extends WP_UnitTestCase {

	protected static $homepage_id;
	protected static $hash;


	function test_dummy() {
		$this->assertTrue( true );
	}

	function setUp() {
		parent::setUp();

		include 'includes/static.php';
		$homepage_id = wp_insert_post( wp_parse_args( array( 'post_status' => 'publish' ), $mailster_homepage ) );

		mailster_update_option( 'homepage', $homepage_id );

		self::$homepage_id = $homepage_id;
		self::$hash        = str_repeat( '0', 32 );
	}

	function tearDown() {
		parent::tearDown();
	}


	function test_create_homepage() {
		$homepage_id = mailster_option( 'homepage' );
		$this->assertRegExp( '#https?:\/\/dev.local\/newsletter-signup\/#', get_permalink( $homepage_id ) );
	}


	function _test_content() {
		$homepage_id = mailster_option( 'homepage' );
		$url         = get_permalink( $homepage_id );
		$this->go_to( $url );

		global $wp_query;
		$post = $wp_query->get_queried_object();
	}


	function _test_unsubscribe_link() {
		$url = mailster()->get_unsubscribe_link( 0 );
		$this->go_to( $url );
	}
	function _test_profile_link() {
		$url = mailster()->get_profile_link( 0 );
		$this->go_to( $url );
	}
	function test_base_link() {
		$homepage_id = mailster_option( 'homepage' );
		$url         = get_permalink( $homepage_id );
		$this->go_to( $url );
		$this->assertTrue( is_mailster_newsletter_homepage() );
	}
	function test_unsubscribe_link_on_frontpage() {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', mailster_option( 'homepage' ) );
		flush_rewrite_rules( false );
		$url = mailster()->get_unsubscribe_link( 0 );
		$this->go_to( $url );
		$this->assertEquals( 'unsubscribe', get_query_var( '_mailster_page' ), 'Unsubscribe page is not working on the homepage' );
	}
	function test_profile_link_on_frontpage() {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', mailster_option( 'homepage' ) );
		flush_rewrite_rules( false );
		$url = mailster()->get_profile_link( 0 );
		$this->go_to( $url );
		$this->assertEquals( 'profile', get_query_var( '_mailster_page' ), 'Profile page is not working on the homepage' );
	}
	function test_base_link_on_frontpage() {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', mailster_option( 'homepage' ) );
		flush_rewrite_rules( false );
		$homepage_id = mailster_option( 'homepage' );
		$url         = get_permalink( $homepage_id );
		$this->go_to( $url );
		$this->assertTrue( is_mailster_newsletter_homepage(), 'Mailster Newsletter homepage is not working on the homepage' );
	}


	function test_check_tracking_redirection() {
		$link         = 'https://google.com';
		$link         = rtrim( strtr( base64_encode( $link ), '+/', '-_' ), '=' );
		$campaigns_id = rand( 1, 100 );
		$hash         = md5( uniqid() );
		$url          = home_url( "mailster/$campaigns_id/$hash/$link" );
		$this->go_to( $url );

		$this->assertEquals( $campaigns_id, get_query_var( '_mailster' ) );
		$this->assertEquals( $link, get_query_var( '_mailster_page' ) );
		$this->assertEquals( $hash, get_query_var( '_mailster_hash' ) );
	}


}
