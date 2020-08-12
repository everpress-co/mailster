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
			$this->schedule_runner();
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

		if ( $this->get_count() ) {
			$this->run();
			$this->schedule_runner( 10 );
		}
	}

	public function run() {

		global $wp_roles;

		$roles = $wp_roles->get_names();

		foreach ( $roles as $role => $name ) {
			if ( $lists = mailster_option( 'wp_role_' . $role ) ) {
				$userdata = array(
					'_lists'  => $lists,
					'referer' => 'wpuser',
				);
				$this->create_subscribers_from_wprole( $role, $userdata );
			}
		}

	}

	public function get_count( $role = null ) {

		global $wpdb;

		$sql = "SELECT COUNT(DISTINCT users.ID) FROM {$wpdb->users} AS users LEFT JOIN {$wpdb->usermeta} AS usermeta ON usermeta.user_id = users.ID AND usermeta.meta_key = '{$wpdb->prefix}capabilities' LEFT JOIN {$wpdb->prefix}mailster_subscribers AS subscribers ON users.user_email = subscribers.email WHERE 1=1";

		if ( ! is_null( $role ) ) {
			$sql .= " AND usermeta.meta_value LIKE '%s:" . strlen( $role ) . ":\"$role\";b:1;%'";
		}

		$sql  .= ' AND subscribers.ID IS NULL';
		$count = $wpdb->get_var( $sql );

		error_log( print_r( $count, true ) );

		return $count;

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
