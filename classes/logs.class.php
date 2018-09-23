<?php

class MailsterLogs {

	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ) );
		add_action( 'plugins_loaded', array( &$this, 'log_actions' ) );

	}


	public function init() {

		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 35 );

	}


	public function log_actions() {

		add_action( 'mailster_send', array( &$this, 'capture' ), 10, 10 );

		add_action( 'mailster_unsubscribe', array( &$this, 'capture' ), 10, 10 );
		add_action( 'mailster_list_unsubscribe', array( &$this, 'capture' ), 10, 10 );

		add_action( 'mailster_subscriber_error', array( &$this, 'capture' ), 10, 10 );
		add_action( 'mailster_system_error', array( &$this, 'capture' ), 10, 10 );
		add_action( 'mailster_campaign_error', array( &$this, 'capture' ), 10, 10 );

	}


	public function capture() {

		if ( ! mailster_option( 'log' ) ) {
			return;
		}

		$current_filter = current_filter();
		$func_args = func_get_args();
		$type = str_replace( 'mailster_', '', $current_filter );

		$args = array(
			'type' => $type,
		);

		switch ( $current_filter ) {
			case 'mailster_send':
				$args['subscriber_id'] = $func_args[0];
				$args['campaign_id'] = $func_args[1];
				$args['text'] = 'Email was sent to %1';
				break;
			case 'mailster_list_unsubscribe':
			case 'mailster_unsubscribe':
				$args['subscriber_id'] = $func_args[0];
				$args['campaign_id'] = $func_args[1];
				$args['text'] = 'unsub %1';
				break;
			case 'mailster_subscriber_error':
			case 'mailster_system_error':
			case 'mailster_campaign_error':
				$args['subscriber_id'] = $func_args[0];
				$args['campaign_id'] = $func_args[1];
				$args['text'] = $func_args[2];
				break;

			default:
				// code...
				break;
		}

		$this->log_it( $args );
		$this->clear();

	}


	public function clear( $all = false ) {

		global $wpdb;

		if ( $all ) {
			$wpdb->query( "TRUCNATE {$wpdb->prefix}mailster_logs" );
		} else {
			if ( $limit = mailster_option( 'log_items' ) ) {
				$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}mailster_logs" );
				if ( $count > $limit ) {
					$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}mailster_logs WHERE timestamp NOT IN (SELECT timestamp FROM ( SELECT timestamp  FROM {$wpdb->prefix}mailster_logs ORDER BY timestamp DESC LIMIT %d ) foo )", mailster_option( 'log_items' ) ) );
				}
			}
		}

	}


	private function log_it( $args ) {

		$defaults = array(
			'type' => 0,
			'timestamp' => microtime( true ),
			'subscriber_id' => null,
			'campaign_id' => null,
			'text' => '',
		);

		$data = wp_parse_args( $args, $defaults );

		global $wpdb;

		return false !== $wpdb->insert( "{$wpdb->prefix}mailster_logs", $data );

	}


	public function admin_menu() {

		if ( mailster_option( 'log' ) ) {
			$page = add_submenu_page( 'edit.php?post_type=newsletter', __( 'Logs', 'mailster' ), __( 'Logs', 'mailster' ), 'mailster_edit_logs', 'mailster_logs', array( &$this, 'view_logs' ) );
		}

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'timestamp' => __( 'Time', 'mailster' ),
			'text' => __( 'Text', 'mailster' ),
			'type' => __( 'Type', 'mailster' ),
			'campaign_id' => __( 'Campaign', 'mailster' ),
			'subscriber_id' => __( 'Subscriber', 'mailster' ),

		);
		return $columns;
	}


	public function view_logs() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'mailster-logs-table', MAILSTER_URI . 'assets/css/logs-table-style' . $suffix . '.css', array(), MAILSTER_VERSION );

		include MAILSTER_DIR . 'views/logs/overview.php';

	}


	public function bulk_actions() {

		if ( empty( $_POST ) ) {
			return;
		}

		if ( empty( $_POST['logs'] ) ) {
			return;
		}

		if ( isset( $_POST['action'] ) && -1 != $_POST['action'] ) {
			$action = $_POST['action'];
		}

		if ( isset( $_POST['action2'] ) && -1 != $_POST['action2'] ) {
			$action = $_POST['action2'];
		}

		$redirect = add_query_arg( $_GET );

		switch ( $action ) {

			case 'delete':
			break;

		}

	}


	/**
	 *
	 *
	 * @param unknown $ids
	 * @param unknown $subscribers (optional)
	 * @return unknown
	 */
	public function remove( $ids, $subscribers = false ) {

		global $wpdb;

		$ids = is_numeric( $ids ) ? array( $ids ) : $ids;

		if ( $subscribers ) {
			$sql = "DELETE a,b,c,d,e,f FROM {$wpdb->prefix}mailster_subscribers AS a LEFT JOIN {$wpdb->prefix}mailster_logs_subscribers b ON a.ID = b.subscriber_id LEFT JOIN {$wpdb->prefix}mailster_subscriber_fields c ON a.ID = c.subscriber_id LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS d ON a.ID = d.subscriber_id LEFT JOIN {$wpdb->prefix}mailster_actions AS e ON a.ID = e.subscriber_id LEFT JOIN {$wpdb->prefix}mailster_queue AS f ON a.ID = f.subscriber_id WHERE b.log_id IN (" . implode( ', ', array_filter( $ids, 'is_numeric' ) ) . ')';

			$wpdb->query( $sql );
		}

		$sql = "DELETE a,b FROM {$wpdb->prefix}mailster_logs AS a LEFT JOIN {$wpdb->prefix}mailster_logs_subscribers b ON a.ID = b.log_id WHERE a.ID IN (" . implode( ', ', array_filter( $ids, 'is_numeric' ) ) . ')';

		if ( false !== $wpdb->query( $sql ) ) {

			foreach ( $ids as $log_id ) {
				$this->remove_from_forms( $log_id );
			}

			return true;
		}

		return false;

	}

	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_log_count() {

		global $wpdb;

		$sql = "SELECT COUNT( * ) AS count FROM {$wpdb->prefix}mailster_logs";

		return $wpdb->get_var( $sql );
	}


	/**
	 *
	 *
	 * @param unknown $new
	 */
	public function on_activate( $new ) {

	}


}
