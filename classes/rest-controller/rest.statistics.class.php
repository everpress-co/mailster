<?php

/**
 * Class Mailster_REST_Statistics_Controller
 */
class Mailster_REST_Statistics_Controller extends WP_REST_Controller {
	/**
	 * The namespace.
	 *
	 * @var string
	 */
	protected $namespace;

	/**
	 * Rest base for the current object.
	 *
	 * @var string
	 */
	protected $rest_base;


	public function __construct() {

		$this->namespace = 'mailster/v1';
		$this->rest_base = 'statistics';
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<metric>[0-9a-z_]+)/',
			array(
				'args'   => array(
					'metric' => array(
						'description' => __( 'Kind of statistics to fetch', 'mailster' ),
						'type'        => 'string',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_statistics' ),
					'permission_callback' => function() {
						return current_user_can( 'mailster_statistics' );
					},
				),
				'schema' => null,

			)
		);

	}

	public function get_statistics( $request ) {

		$params = $request->get_params();

		$metric = (string) $params['metric'];
		$from   = (string) $params['from'];
		$to     = (string) $params['to'];

		$data = mailster( 'statistics' )->get(
			$metric,
			array(
				'from' => $from,
				'to'   => $to,
			)
		);

		// Return all of our comment response data.
		return rest_ensure_response( $data );
	}

}
