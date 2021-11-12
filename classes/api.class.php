<?php

class MailsterApi {

	private $base    = 'mailster';
	private $version = 'v1';

	public function __construct() {

		add_action( 'rest_api_init', array( &$this, 'init' ) );
	}


	public function init() {

		$routes = $this->get_routes();

		foreach ( $routes as $route => $args ) {
			register_rest_route( $this->base . '/' . $this->version, $route, $args );
		}

	}


	public function get_routes() {
		$routes = array(
			'/fields' => array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_fields' ),
				'permission_callback' => '__return_true',
			),
		);

		return apply_filters( 'mailster_api_routes', $routes );
	}


	public function get_fields( WP_REST_Request $request ) {

		$custom_fields = array_values( mailster()->get_custom_fields() );

		$fields = array(
			array(
				'name' => mailster_text( 'email' ),
				'id'   => 'email',
				'type' => 'email',
			),
			array(
				'name' => mailster_text( 'firstname' ),
				'id'   => 'firstname',
			),
			array(
				'name' => mailster_text( 'lastname' ),
				'id'   => 'lastname',
			),
		);

		return array_merge( $fields, $custom_fields );

	}


}
