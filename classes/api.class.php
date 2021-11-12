<?php

class MailsterApi {

	public function __construct() {

		add_action( 'rest_api_init', array( &$this, 'init' ) );
	}


	public function init() {

		register_rest_route(
			'mailster/v1',
			'/forms',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_forms' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'mailster/v1',
			'/fields',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_fields' ),
				'permission_callback' => '__return_true',
			)
		);

	}


	public function get_forms( WP_REST_Request $request ) {

		$query = get_posts(
			array(
				'post_type' => 'newsletter_form',

			)
		);

		$return = array();

		foreach ( $query as $form ) {
			$return[] = array(
				'label' => $form->post_title,
				'value' => (int) $form->ID,
			);
		}

		return $return;

	}

	public function get_fields( WP_REST_Request $request ) {

		$custom_fields = array_values( mailster()->get_custom_fields() );

		$fields = array(
			array(
				'name' => 'Email',
				'id'   => 'email',
			),
			array(
				'name' => 'First Name',
				'id'   => 'firstname',
			),
			array(
				'name' => 'Last name',
				'id'   => 'lastname',
			),
		);

		return array_merge( $fields, $custom_fields );

	}


}
