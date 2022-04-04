<?php

/**
 * Class Mailster_REST_Form_Controller
 */
class Mailster_REST_Form_Controller extends WP_REST_Controller {
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
		$this->rest_base = 'forms';
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)/impression',
			array(
				'args'   => array(
					'id' => array(
						'description' => __( 'Unique identifier for the form.' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'  => WP_REST_Server::CREATABLE,
					'callback' => array( $this, 'create_impression' ),
					// 'permission_callback' => array( $this, 'create_item_permissions_check' ),
				),
				'schema' => null,

			)
		);

	}
	/**
	 * Check permissions for the read.
	 *
	 * @param WP_REST_Request $request get data from request.
	 *
	 * @return bool|WP_Error
	 */
	public function create_item_permissions_check( $request ) {

		if ( false && ! current_user_can( 'read' ) ) {
			return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view this resource.' ), $this->response_data() );
		}

		return true;
	}


	public function create_item( $request ) {

		error_log( print_r( $request, true ) );

		$all = $request->get_param( 'all' );

		error_log( print_r( $all, true ) );

		$response = array(
			'data' => array(
				'status' => 200,
			),
		);

		// Return all of our comment response data.
		return rest_ensure_response( $response );
	}


	public function create_impression( $request ) {

		$url_params = $request->get_url_params();

		$form_id = $url_params['id'];
		$post_id = url_to_postid( wp_get_referer() );

		mailster( 'block-forms' )->impression( $form_id, $post_id );

		$response = array(
			'data' => array(
				'status' => 200,
			),
		);

		// Return all of our comment response data.
		return rest_ensure_response( $response );
	}

	/**
	 * Sets up the proper HTTP status code for authorization.
	 *
	 * @return int
	 */
	public function authorization_status_code() {

		$status = 401;

		if ( is_user_logged_in() ) {
			$status = 403;
		}

		return $status;
	}

	private function response_data( $args = array() ) {

		$data = wp_parse_args( $args, array( 'status' => $this->authorization_status_code() ) );

		return $data;

	}
}
