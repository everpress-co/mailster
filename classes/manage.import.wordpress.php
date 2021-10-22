<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MailsterImportWordPress extends MailsterImport {

	protected $slug = 'WordPress';
	protected $name = 'WordPress';

	private $api;

	function init() {}


	public function get_import_part( $import_data ) {

		$limit  = $import_data['performance'] ? 10 : 500;
		$offset = $import_data['part'];

		$result = $this->query(
			array(
				'roles'       => isset( $import_data['extra']['roles'] ) ? (array) $import_data['extra']['roles'] : array(),
				'no_role'     => isset( $import_data['extra']['no_role'] ),
				'meta_values' => isset( $import_data['extra']['meta_values'] ) ? (array) $import_data['extra']['meta_values'] : array(),
				'offset'      => $offset,
				'limit'       => $limit,
			)
		);

		return $result['data'];

	}

	public function get_import_data() {

		parse_str( $_POST['data'], $data );

		$meta_values = isset( $data['meta_values'] ) ? (array) $data['meta_values'] : array();

		$result = $this->query(
			array(
				'roles'       => isset( $data['roles'] ) ? (array) $data['roles'] : array(),
				'no_role'     => isset( $data['no_role'] ),
				'meta_values' => $meta_values,
				'offset'      => isset( $_POST['offset'] ) ? (int) $_POST['offset'] : 0,
				'limit'       => 10,
			)
		);

		$header = array_merge(
			array(
				mailster_text( 'email' ),
				mailster_text( 'firstname' ),
				mailster_text( 'lastname' ),
				esc_html__( 'login', 'mailster' ),
				esc_html__( 'nickname', 'mailster' ),
				esc_html__( 'display name', 'mailster' ),
				esc_html__( 'registered', 'mailster' ),
				esc_html__( 'roles', 'mailster' ),
			),
			$meta_values
		);

		return array(
			'total'   => $result['total'],
			'removed' => 0,
			'header'  => $header,
			'sample'  => $result['data'],
			'extra'   => $data,
			'insert'  => array(
				'referer' => 'wpuser',
			),
		);

	}


	public function import_options( $data = null ) {
		include MAILSTER_DIR . '/views/manage/method-wordpress.php';
	}

	private function query( $args ) {

		$args = wp_parse_args(
			$args,
			array(
				'roles'       => array(),
				'no_role'     => false,
				'meta_values' => array(),
				'offset'      => 0,
				'limit'       => 5,
			)
		);

		if ( ! empty( $args['roles'] ) ) {
			$user_query = new WP_User_Query(
				array(
					'role__in' => $args['roles'],
					'fields'   => 'ID',
					'number'   => $args['limit'],
					'offset'   => $args['offset'] * $args['limit'],
					'orderby'  => 'ID',
				)
			);

			$user_ids = $user_query->get_results();
			$total    = $user_query->get_total();
		} else {
			$user_ids = array();
		}

		// add users without a role only on the first run
		if ( ! $args['offset'] && $args['no_role'] ) {
			$no_roles = wp_get_users_with_no_role();
			$user_ids = array_merge( $user_ids, $no_roles );
			$total   += count( $no_roles );
		}

		$data = array();

		$wp_roles = wp_list_pluck( wp_roles()->roles, 'name' );

		foreach ( $user_ids as $i => $user_id ) {
			$user = get_user_by( 'ID', $user_id );
			if ( ! $user ) {
				$total--;
				continue;
			}
			if ( $i >= $args['limit'] ) {
				break;
			}

			$roles = array_intersect_key( $wp_roles, array_flip( $user->roles ) );
			$entry = array(
				$user->user_email,
				get_user_meta( $user->ID, 'first_name', true ),
				get_user_meta( $user->ID, 'last_name', true ),
				$user->user_login,
				$user->user_nicename,
				$user->user_name,
				$user->user_registered,
				implode( ',', $roles ),
			);
			foreach ( $args['meta_values'] as $meta_value ) {
				$entry[] = get_user_meta( $user_id, $meta_value, true );
			}

			$data[] = $entry;
		}

		return array(
			'total' => $total,
			'data'  => $data,
		);
	}

}
