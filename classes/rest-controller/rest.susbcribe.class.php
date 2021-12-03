<?php

/**
 * Class Mailster_REST_Subscribe_Controller
 */
class Mailster_REST_Subscribe_Controller extends WP_REST_Controller {
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
		$this->rest_base = 'subscribe';
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(

				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
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
			return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view this resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}

		$post_nonce = mailster_option( 'post_nonce' );
		$form_id    = $request->get_param( '_form_id' );

		return true;
	}


	public function create_item( $request ) {
		$data    = $request->get_params();
		$headers = $request->get_headers();
		$referer = $request->get_header( 'referer' );

		$entry = mailster( 'subscribers' )->verify( $data, true );
		if ( is_wp_error( $entry ) ) {
			return $entry;
		}

		error_log( print_r( $entry, true ) );

		error_log( print_r( $data, true ) );
		error_log( print_r( $headers, true ) );
		return new WP_Error( 'rest_forbidden', esc_html__( 'xxYou cannot view this resource.' ), array( 'status' => $this->authorization_status_code() ) );

		// Return all of our comment response data.
		return rest_ensure_response( $data );
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
}
