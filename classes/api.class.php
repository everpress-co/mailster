<?php

class MailsterApi {

	private $version = 'v1';
	private $namespace;

	public function __construct() {

		add_action( 'rest_api_init', array( &$this, 'routes' ) );

	}


	public function routes() {

		$this->namespace = 'mailster/' . $this->version;

		require_once MAILSTER_DIR . 'classes/api/base.api.class.php';
		require_once MAILSTER_DIR . 'classes/api/subscribers.api.class.php';

		$controller = new Mailster_REST_Subscribers_Controller();

		return;

		$this->register_rest_routes(
			'GET',
			array(
				// 'lists'       => array( &$this, 'my_awesome_func' ),
				'subscribers' => array( &$this, 'my_awesome_func' ),
			),
		);

		$this->register_rest_routes(
			'POST',
			array(
				// 'lists'       => array( &$this, 'my_awesome_func' ),
				'subscribers' => array( &$this, 'my_awesome_func' ),
			),
		);

	}


	private function register_rest_routes( $method, $routes ) {
		foreach ( $routes as $route => $callback ) {
			register_rest_route(
				$this->namespace,
				$route,
				array(
					'methods'  => $method,
					'callback' => $callback,
				)
			);
		}
	}


	public function my_awesome_func( WP_REST_Request $request ) {
		// You can access parameters via direct array access on the object:
		$param = $request['some_param'];

		// Or via the helper method:
		$param = $request->get_param( 'some_param' );

		// You can get the combined, merged set of parameters:
		$parameters = $request->get_params();

		// The individual sets of parameters are also available, if needed:
		$parameters = $request->get_url_params();
		$parameters = $request->get_query_params();
		$parameters = $request->get_body_params();
		$parameters = $request->get_json_params();
		$parameters = $request->get_default_params();

		// Uploads aren't merged in, but can be accessed separately:
		$parameters = $request->get_file_params();

		echo '<pre>' . print_r( $request, true ) . '</pre>';
	}

}
