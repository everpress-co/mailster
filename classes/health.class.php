<?php

class MailsterHealth {

	private $tests;

	public function __construct() {

		add_action( 'site_status_tests', array( &$this, 'add_tests' ) );

	}


	public function add_tests( $site_health_tests ) {

		$tests = mailster()->test();

		$site_health_tests = array_merge_recursive( $site_health_tests, $tests->get_health_tests() );

		return $site_health_tests;

	}

	public function test() {
		$result = array(
			'label'       => __( 'Caching is enabled' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( 'Performance' ),
				'color' => 'orange',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( 'Caching can help load your site more quickly for visitors.' )
			),
			'actions'     => '',
			'test'        => 'caching_plugin',
		);

		if ( true ) {
			$result['status']      = 'recommended';
			$result['label']       = __( 'Caching is not enabled' );
			$result['description'] = sprintf(
				'<p>%s</p>',
				__( 'Caching is not currently enabled on your site. Caching can help load your site more quickly for visitors.' )
			);
			$result['actions']    .= sprintf(
				'<p><a href="%s">%s</a></p>',
				esc_url( admin_url( 'admin.php?page=cachingplugin&action=enable-caching' ) ),
				__( 'Enable Caching' )
			);
		}

		return $result;

	}


}
