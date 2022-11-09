<?php

class MailsterLogs {

	/**
	 *
	 *
	 * @param unknown $method
	 * @param unknown $args
	 */
	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ) );

	}


	public function init() {

		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 55 );

	}


	public function admin_menu() {

		$page = add_submenu_page( 'edit.php?post_type=newsletter', esc_html__( 'Logs', 'mailster' ), esc_html__( 'Logs', 'mailster' ), 'mailster_view_logs', 'mailster_logs', array( &$this, 'view_logs' ) );

		add_action( 'load-' . $page, array( &$this, 'script_styles' ) );

		if ( isset( $_GET['ID'] ) ) :

			add_action( 'load-' . $page, array( &$this, 'edit_entry' ), 99 );

		else :

			add_action( 'load-' . $page, array( &$this, 'screen_options' ), 99 );
			add_action( 'load-' . $page, array( &$this, 'bulk_actions' ), 99 );
			add_filter( 'manage_' . $page . '_columns', array( &$this, 'get_columns' ) );

		endif;

	}


	public function script_styles() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		if ( isset( $_GET['ID'] ) ) :

			wp_enqueue_style( 'mailster-log-detail', MAILSTER_URI . 'assets/css/log-style' . $suffix . '.css', array(), MAILSTER_VERSION );
			wp_enqueue_script( 'mailster-log-detail', MAILSTER_URI . 'assets/js/log-script' . $suffix . '.js', array( 'mailster-script', 'mailster-select2' ), MAILSTER_VERSION, true );

			mailster_localize_script(
				'logs',
				array(
					'next' => esc_html__( 'next', 'mailster' ),
				)
			);

		else :

			wp_enqueue_style( 'mailster-logs-table', MAILSTER_URI . 'assets/css/logs-table-style' . $suffix . '.css', array(), MAILSTER_VERSION );
			wp_enqueue_script( 'mailster-logs-table', MAILSTER_URI . 'assets/js/logs-table-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );
			mailster_localize_script(
				'logs',
				array(
					'next' => esc_html__( 'next', 'mailster' ),
				)
			);

		endif;

	}

	public function view_logs() {

		if ( isset( $_GET['ID'] ) ) :

			include MAILSTER_DIR . 'views/logs/detail.php';

		else :

			include MAILSTER_DIR . 'views/logs/overview.php';

		endif;

	}

	public function screen_options() {

		require_once MAILSTER_DIR . 'classes/logs.table.class.php';

		$screen = get_current_screen();

		add_screen_option(
			'per_page',
			array(
				'label'   => esc_html__( 'Logs', 'mailster' ),
				'default' => 50,
				'option'  => 'mailster_logs_per_page',
			)
		);

	}

	public function edit_entry() {
	}

	public function bulk_actions() {

		if ( empty( $_POST ) ) {
			return;
		}

		if ( isset( $_POST['action'] ) && -1 != $_POST['action'] ) {
			$action = $_POST['action'];
		}

		if ( isset( $_POST['action2'] ) && -1 != $_POST['action2'] ) {
			$action = $_POST['action2'];
		}

		if ( isset( $_GET['action'] ) ) {
			$action = $_GET['action'];
		}

	}

	public function get_columns() {
	}

	public function get( $id ) {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}mailster_logs WHERE ID = %d";

		return $wpdb->get_results( $wpdb->prepare( $sql, $id ) );

	}

}
