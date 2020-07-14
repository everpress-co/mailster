<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Mailster_REST_Subscribers_Controller extends Mailster_REST_Controller {

	protected $meta;

	public $rest_base = 'subscribers';

	public function register_routes() {

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_items' ),
					'permission_callback' => array( $this, 'update_items_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_items' ),
					'permission_callback' => array( $this, 'delete_items_permissions_check' ),
					'args'                => array(
						'force' => array(
							'type'        => 'boolean',
							'default'     => false,
							'description' => __( 'Whether to bypass Trash and force deletion.' ),
						),
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				'args' => array(
					'id' => array(
						'description' => __( 'Unique identifier for the subscriber.' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
					'args'                => array(
						'force' => array(
							'type'        => 'boolean',
							'default'     => false,
							'description' => __( 'Whether to bypass Trash and force deletion.' ),
						),
					),
				),
			)
		);
	}

	public function get_items_permissions_check( $request ) {

		return current_user_can( 'mailster_edit_subscribers' );

	}

	public function get_items( $request ) {

		$registered = $this->get_collection_params();

		$params = $request->get_params();
		$args   = $request->get_query_params();

		// for totals
		$args['calc_found_rows'] = true;

		if ( isset( $request['limit'] ) ) {
			$args['limit'] = $request['limit'];
		}

		$items = mailster( 'subscribers' )->query( $args );

		global $wpdb;
		$total_items = $wpdb->get_var( 'SELECT FOUND_ROWS();' );

		$data = $this->prepare_item_for_response( $items, $request );

		$response = rest_ensure_response( $data );

		return $response;

	}

	public function update_items_permissions_check( $request ) {

		return current_user_can( 'mailster_edit_subscribers' );

	}

	public function update_items( $request ) {

		$registered = $this->get_collection_params();

		$params = $request->get_params();
		$args   = $request->get_query_params();

		if ( ! isset( $args['status'] ) ) {
			$args['status'] = false;
		}
		$args['return_ids'] = true;

		$items = mailster( 'subscribers' )->query( $args );

		$entry = $request->get_body_params();

		$overwrite               = true;
		$merge                   = true;
		$subscriber_notification = false;

		foreach ( $items as $item ) {
			$entry['ID']   = $item;
			$subscriber_id = mailster( 'subscribers' )->update( $entry, $overwrite, $merge, $subscriber_notification );
			if ( is_wp_error( $subscriber_id ) ) {
				return $subscriber_id;
			}
		}

		$request->set_param( 'context', 'edit' );

		$response = new WP_REST_Response();
		$response->set_data(
			array(
				'updated' => true,
				'count'   => count( $items ),
			)
		);

		return rest_ensure_response( $response );

	}

	public function delete_items_permissions_check( $request ) {

		return current_user_can( 'mailster_delete_subscribers' );

	}

	public function delete_items( $request ) {

		$registered = $this->get_collection_params();

		$params = $request->get_params();
		$args   = $request->get_query_params();

		if ( ! isset( $args['status'] ) ) {
			$args['status'] = false;
		}
		if ( isset( $request['limit'] ) ) {
			$args['limit'] = $request['limit'];
		}

		$items = mailster( 'subscribers' )->query( $args );

		$status         = null;
		$remove_actions = false;
		$remove_meta    = true;

		$subscriber_id = mailster( 'subscribers' )->remove( wp_list_pluck( $items, 'ID' ), $status, $remove_actions, $remove_meta );
		if ( is_wp_error( $subscriber_id ) ) {
			return $subscriber_id;
		}

		$request->set_param( 'context', 'edit' );

		$response = new WP_REST_Response();
		$response->set_data(
			array(
				'deleted'  => true,
				'count'    => count( $items ),
				'previous' => $items,
			)
		);

		return rest_ensure_response( $response );

	}


	public function get_item_permissions_check( $request ) {

		return current_user_can( 'mailster_edit_subscribers' );

	}

	public function get_item( $request ) {
		$subscriber = mailster( 'subscribers' )->get( $request['id'], true );

		if ( is_wp_error( $subscriber ) ) {
			return $subscriber;
		}

		$data     = $this->prepare_item_for_response( $subscriber, $request );
		$response = rest_ensure_response( $data );

		return $response;
	}


	public function create_item_permissions_check( $request ) {

		return current_user_can( 'mailster_add_subscribers' );

	}


	public function create_item( $request ) {

		$entry = $request->get_query_params();

		$overwrite               = false;
		$merge                   = false;
		$subscriber_notification = true;

		$subscriber_id = mailster( 'subscribers' )->add( $entry, $overwrite, $merge, $subscriber_notification );
		if ( is_wp_error( $subscriber_id ) ) {
			return $subscriber_id;
		}

		$subscriber = mailster( 'subscribers' )->get( $subscriber_id );
		if ( is_wp_error( $subscriber ) ) {
			return $subscriber;
		}

		if ( ! $subscriber ) {
			return new \WP_Error( 'rest_not_found', 'User not found.', array( 'status' => 404 ) );
		}

		$request->set_param( 'context', 'edit' );

		$response = $this->prepare_item_for_response( $subscriber, $request );

		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $subscriber->ID ) ) );

		return rest_ensure_response( $response );

	}


	public function update_item_permissions_check( $request ) {

		return current_user_can( 'mailster_edit_subscribers' );

	}


	public function update_item( $request ) {

		$subscriber = mailster( 'subscribers' )->get( $request['id'], true );
		if ( is_wp_error( $subscriber ) ) {
			return $subscriber;
		}

		if ( ! $subscriber ) {
			return new \WP_Error( 'rest_not_found', 'User not found.', array( 'status' => 404 ) );
		}

		$subscriber = $this->prepare_item_for_database( $request );

		$entry = $request->get_query_params();

		$overwrite               = true;
		$merge                   = true;
		$subscriber_notification = false;

		$subscriber_id = mailster( 'subscribers' )->update( $entry, $overwrite, $merge, $subscriber_notification );
		if ( is_wp_error( $subscriber_id ) ) {
			return $subscriber_id;
		}

		$subscriber = mailster( 'subscribers' )->get( $subscriber_id );
		if ( is_wp_error( $subscriber ) ) {
			return $subscriber;
		}

		$request->set_param( 'context', 'edit' );

		$response = $this->prepare_item_for_response( $subscriber, $request );

		return rest_ensure_response( $response );
	}


	public function delete_item_permissions_check( $request ) {

		return current_user_can( 'mailster_delete_subscribers' );
	}

	public function delete_item( $request ) {

		$subscriber = mailster( 'subscribers' )->get( $request['id'], true );
		if ( is_wp_error( $subscriber ) ) {
			return $subscriber;
		}

		if ( ! $subscriber ) {
			return new \WP_Error( 'rest_not_found', 'User not found.', array( 'status' => 404 ) );
		}

		$status         = null;
		$remove_actions = false;
		$remove_meta    = true;

		$subscriber_id = mailster( 'subscribers' )->remove( $subscriber->ID, $status, $remove_actions, $remove_meta );
		if ( is_wp_error( $subscriber_id ) ) {
			return $subscriber_id;
		}

		$request->set_param( 'context', 'edit' );

		$response = new WP_REST_Response();
		$response->set_data(
			array(
				'deleted'  => true,
				'previous' => $subscriber,
			)
		);

		return rest_ensure_response( $response );
	}


	protected function prepare_item_for_database( $request ) {

		$prepared = new stdClass();

		$prepared = (object) $request;

		return apply_filters( 'mailster_rest_pre_insert_subscriber', $prepared, $request );

	}

	public function check_read_permission( $subscriber ) {

		return current_user_can( 'mailster_edit_subscribers' );
	}

	protected function check_update_permission( $subscriber ) {

		return current_user_can( 'mailster_edit_subscribers' );
	}

	protected function check_create_permission( $subscriber ) {

		return current_user_can( 'mailster_add_subscribers' );
	}

	protected function check_delete_permission( $subscriber ) {

		return current_user_can( 'mailster_delete_subscribers' );

	}

	public function prepare_item_for_response( $subscriber, $request ) {

		$fields = $this->get_fields_for_response( $request );

		$data = array();

		$data = $subscriber;

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );

		return apply_filters( 'mailster_rest_prepare_subsriber', $response, $subscriber, $request );
	}


	public function get_collection_params() {

		$query_params = parent::get_collection_params();

		$query_params['context']['default'] = 'view';

		$query_params['after'] = array(
			'description' => __( 'Limit response to subscribers published after a given ISO8601 compliant date.' ),
			'type'        => 'string',
			'format'      => 'date-time',
		);

		$query_params['before'] = array(
			'description' => __( 'Limit response to subscribers published before a given ISO8601 compliant date.' ),
			'type'        => 'string',
			'format'      => 'date-time',
		);

		$query_params['exclude'] = array(
			'description' => __( 'Ensure result set excludes specific IDs.' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'integer',
			),
			'default'     => array(),
		);

		$query_params['include'] = array(
			'description' => __( 'Limit result set to specific IDs.' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'integer',
			),
			'default'     => array(),
		);

		$query_params['offset'] = array(
			'description' => __( 'Offset the result set by a specific number of items.' ),
			'type'        => 'integer',
		);

		$query_params['limit'] = array(
			'description' => __( 'Limits the result.' ),
			'type'        => 'integer',
		);

		$query_params['order'] = array(
			'description' => __( 'Order sort attribute ascending or descending.' ),
			'type'        => 'string',
			'default'     => 'desc',
			'enum'        => array( 'asc', 'desc' ),
		);

		return $query_params;
	}

}
