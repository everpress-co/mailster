<?php

class MailsterWordPressUsers {

	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ) );

	}


	public function init() {

		add_filter( 'mailster_verify_options', array( &$this, 'verify_options' ) );
		add_action( 'mailster_wordpress_users', array( &$this, 'maybe_run' ) );
		add_action( 'mailster_cron', array( &$this, 'maybe_run' ) );

	}

	public function verify_options( $options ) {

		if ( $options['sync'] ) {
			$this->schedule_runner( 1 );
		}
		return $options;
	}


	public function schedule_runner( $delay = 60 ) {
		if ( wp_next_scheduled( 'mailster_wordpress_users' ) ) {
			wp_clear_scheduled_hook( 'mailster_wordpress_users' );
		}
		wp_schedule_single_event( time() + $delay, 'mailster_wordpress_users' );
	}

	public function maybe_run() {

		if ( ! mailster_option( 'sync' ) || ! mailster_option( 'wp_roles' ) ) {
			return;
		}

		if ( $this->run() ) {
			$this->schedule_runner( 10 );
		}
	}

	public function run() {

		global $wpdb, $wp_roles;

		$affected_roles = mailster_option( 'wp_roles', array() );

		if ( empty( $affected_roles ) ) {
			return 0;
		}

		$roles = $wp_roles->get_names();

		$sql = 'SELECT SQL_CALC_FOUND_ROWS users.ID';

		foreach ( $affected_roles as $role ) {
			if ( '_none_' == $role ) {
				$sql .= ", NULLIF(usermeta.meta_value = 'a:0:{}', false) AS _none_";
			} else {
				$sql .= ", NULLIF(usermeta.meta_value LIKE '%\"$role\"%', false) AS $role";
			}
		}

		$sql .= " FROM {$wpdb->users} AS users LEFT JOIN {$wpdb->usermeta} AS usermeta ON usermeta.user_id = users.ID AND usermeta.meta_key = '{$wpdb->prefix}capabilities' LEFT JOIN {$wpdb->prefix}mailster_subscribers AS subscribers ON users.user_email = subscribers.email WHERE 1=1";

		$sql .= ' AND (1=0';
		foreach ( $affected_roles as $role ) {
			if ( '_none_' == $role ) {
				$sql .= " OR usermeta.meta_value = 'a:0:{}'";
			} else {
				$sql .= " OR usermeta.meta_value LIKE '%\"$role\"%'";
			}
		}
		$sql .= ' )';

		$sql  .= ' AND subscribers.ID IS NULL ORDER BY users.ID LIMIT 1000';
		$users = $wpdb->get_results( $sql );
		$total = $wpdb->get_var( 'SELECT FOUND_ROWS();' );
		$count = count( $users );

		error_log( print_r( $count, true ) );
		error_log( print_r( $total, true ) );

		$subscriber_ids = array();

		foreach ( $users as $user ) {

			$userdata = array(
				'referer' => 'wpuser',
			);
			foreach ( $affected_roles as $role ) {

				if ( $user->{$role} ) {
					$userdata['_lists'] = mailster_option( 'wp_role_' . $role );
					$userdata['status'] = mailster_option( 'wp_role_' . $role . '_optin' ) ? 0 : 1;
					$subscriber_ids[]   = mailster( 'subscribers' )->add_from_wp_user( $user->ID, $userdata, true, false );
				}
			}
		}

		return $total;

	}

	public function create_subscribers_from_wprole( $role, $userdata = array(), $limit = 1000 ) {

		global $wpdb;

		$sql = "SELECT SQL_CALC_FOUND_ROWS users.ID FROM {$wpdb->users} AS users LEFT JOIN {$wpdb->usermeta} AS usermeta ON usermeta.user_id = users.ID AND usermeta.meta_key = '{$wpdb->prefix}capabilities' LEFT JOIN {$wpdb->prefix}mailster_subscribers AS subscribers ON users.user_email = subscribers.email WHERE 1=1 AND usermeta.meta_value LIKE '%s:" . strlen( $role ) . ":\"$role\";b:1;%' AND subscribers.ID IS NULL GROUP BY users.user_email ORDER BY users.ID LIMIT " . absint( $limit );

		$user_ids = $wpdb->get_col( $sql );
		$total    = $wpdb->get_var( 'SELECT FOUND_ROWS();' );

		error_log( print_r( $role, true ) );
		error_log( print_r( $total, true ) );
		error_log( print_r( '===', true ) );

		$subscriber_ids = array();

		foreach ( $user_ids as $user_id ) {
			$subscriber_ids[] = mailster( 'subscribers' )->add_from_wp_user( $user_id, $userdata, false, false );
		}

		return $subscriber_ids;

	}


}
