<?php

class MailsterStatistics {

	private $calendar_table = null;

	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ) );

	}

	public function init() {

		if ( is_admin() ) {
			add_action( 'admin_menu', array( &$this, 'add_menu' ), 40 );
		}

	}


	public function add_menu() {

		$page = add_submenu_page( 'edit.php?post_type=newsletter', __( 'Statistics', 'mailster' ), __( 'Statistics', 'mailster' ), 'mailster_statistics', 'mailster_statistics', array( &$this, 'statistics' ) );
		add_action( 'load-' . $page, array( &$this, 'scripts_styles' ) );
		add_action( 'load-' . $page, array( &$this, 'register_meta_boxes' ) );

	}


	/**
	 *
	 *
	 * @param unknown $range (optional)
	 * @return unknown
	 */
	public function statistics() {

		$this->screen = get_current_screen();

		include MAILSTER_DIR . 'views/statistics.php';

	}

	public function sample_metabox() {
		echo 'Test';
	}

	public function campaigns() {
		include MAILSTER_DIR . 'views/statistics/mb-campaigns.php';
	}

	public function scripts_styles() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'mailster-statistics-script', MAILSTER_URI . 'assets/js/statistics-script' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION );
		wp_localize_script( 'mailster-statistics-script', 'mailsterL10n', array() );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-touch-punch' );

		wp_enqueue_style( 'mailster-manage-style', MAILSTER_URI . 'assets/css/statistics-style' . $suffix . '.css', array(), MAILSTER_VERSION );

		wp_enqueue_style( 'jquery-style', MAILSTER_URI . 'assets/css/libs/jquery-ui' . $suffix . '.css' );
		wp_enqueue_style( 'jquery-datepicker', MAILSTER_URI . 'assets/css/datepicker' . $suffix . '.css' );

		wp_enqueue_script( 'jquery-ui-datepicker' );

	}

	public function register_meta_boxes() {

		$this->register_meta_box( 'metabox1', __( 'MetaBox 1', 'mailster' ), array( &$this, 'sample_metabox' ) );
		$this->register_meta_box( 'metabox2', __( 'MetaBox 2', 'mailster' ), array( &$this, 'sample_metabox' ), 'side' );
		$this->register_meta_box( 'campaigns', __( 'Campaigns', 'mailster' ), array( &$this, 'campaigns' ), 'side' );

	}

	/**
	 *
	 *
	 * @param unknown $range (optional)
	 * @return unknown
	 */
	public function get_dashboard( $range = '7 days' ) {

		$rawdata = $this->get_signups( strtotime( '-' . $range ), time() );

		return array(
			'labels' => $this->get_labels( $rawdata ),
			'datasets' => $this->get_datasets( $rawdata ),
		);

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $title
	 * @param unknown $callback
	 * @param unknown $context       (optional)
	 * @param unknown $priority      (optional)
	 * @param unknown $callback_args (optional)
	 */
	public function register_meta_box( $id, $title, $callback, $context = 'normal', $priority = 'default', $callback_args = null ) {

		$id = 'mailster-mb-' . sanitize_key( $id );
		$screen = get_current_screen();

		add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $context (optional)
	 */
	public function unregister_meta_box( $id, $context = 'normal' ) {

		$id = 'mailster-mb-' . sanitize_key( $id );
		$screen = get_current_screen();

		remove_meta_box( $id, $screen, $context );

	}

	/**
	 *
	 *
	 * @param unknown $rawdata
	 * @return unknown
	 */
	private function get_labels( $rawdata ) {

		global $wp_locale;

		$dates = array_keys( $rawdata );

		foreach ( $dates as $i => $date ) {
			$d = strtotime( $date );
			$dates[ $i ] = $wp_locale->weekday_abbrev[ $wp_locale->weekday[ date( 'w', $d ) ] ];
		}

		return $dates;

	}


	/**
	 *
	 *
	 * @param unknown $rawdata
	 * @return unknown
	 */
	private function get_datasets( $rawdata ) {

		return array(
			// array(
			// 'data' => array_values( $rawdata ),
			// 'fillColor' => "rgba(111,191,77,0.2)",
			// 'strokeColor' => "rgba(111,191,77,1)",
			// 'pointColor' => "rgba(111,191,77,1)",
			// 'pointStrokeColor' => "#fff",
			// 'pointHighlightFill' => "#fff",
			// 'pointHighlightStroke' => "rgba(111,191,77,1)",
			// ),
			// array(
			// 'data' => array_values( $rawdata ),
			// 'fillColor' => "rgba(43,179,231,0.2)",
			// 'strokeColor' => "rgba(43,179,231,1)",
			// 'pointColor' => "rgba(43,179,231,1)",
			// 'pointStrokeColor' => "#fff",
			// 'pointHighlightFill' => "#fff",
			// 'pointHighlightStroke' => "rgba(43,179,231,1)",
			// ),
			array(
				'data' => array_values( $rawdata ),
				'backgroundColor' => 'rgba(43,179,231,0.2)',
				'borderColor' => 'rgba(43,179,231,1)',
				'pointColor' => 'rgba(43,179,231,1)',
				'pointBorderColor' => 'rgba(43,179,231,1)',
				'pointBackgroundColor' => '#fff',
				'pointHoverBackgroundColor' => 'rgba(43,179,231,1)',
			),
		);

	}


	/**
	 *
	 *
	 * @param unknown $from (optional)
	 * @param unknown $to   (optional)
	 * @return unknown
	 */
	public function get_signups( $from = null, $to = null ) {

		global $wpdb;

		$from = is_null( $from ) ? time() : $from;
		$to = is_null( $to ) ? time() + 86399 : $to;

		$dates = $this->get_calendar_table( $from, $to );
		$count = count( $dates );

		$count_before = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}mailster_subscribers WHERE status = 1 AND IF(confirm, confirm, signup) < %d", $from ) );

		$sql = "SELECT @n:=@n + IFNULL(total,0) total FROM (SELECT {$this->calendar_table}.date, total FROM {$this->calendar_table} LEFT JOIN (SELECT FROM_UNIXTIME(IF(confirm, confirm, signup), '%Y-%m-%d') date, count(*) total FROM {$wpdb->prefix}mailster_subscribers WHERE status = 1 GROUP BY date ) t2 ON {$this->calendar_table}.date= t2.date ORDER BY date) t3 CROSS JOIN (SELECT @n:=$count_before) n LIMIT 0, $count";

		$sql = apply_filters( 'mailster_get_signups_sql', $sql, $from, $to );

		$data = array_combine( $dates, array_map( 'floatval', $wpdb->get_col( $sql ) ) );

		return $data;

	}


	/**
	 *
	 *
	 * @param unknown $from
	 * @param unknown $to
	 * @param unknown $format (optional)
	 * @return unknown
	 */
	private function get_calendar_table( $from, $to, $format = 'Y-m-d' ) {

		global $wpdb;
		$dates = $this->get_date_range( $from, $to, '+1 day', $format );
		$count = count( $dates );

		if ( ! $this->calendar_table ) {
			$this->calendar_table = "{$wpdb->prefix}mailster_" . uniqid();
			$sql = "CREATE TEMPORARY TABLE {$this->calendar_table} ( date date );";
			if ( false == $wpdb->query( $sql ) ) {
				return false;
			}
		} else {
			$sql = "TRUNCATE {$this->calendar_table};";
			if ( false == $wpdb->query( $sql ) ) {
				return false;
			}
		}

		$sql = "INSERT INTO {$this->calendar_table} (date) VALUES ('" . implode( "'),('", $dates ) . "');";

		if ( false !== $wpdb->query( $sql ) ) {
			return $dates;
		}

		return false;

	}


	/**
	 *
	 *
	 * @param unknown $first
	 * @param unknown $last
	 * @param unknown $step   (optional)
	 * @param unknown $format (optional)
	 * @return unknown
	 */
	private function get_date_range( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {

		$dates = array();
		$current = $first;

		while ( $current <= $last ) {

			$dates[] = date( $format, $current );
			$current = strtotime( $step, $current );
		}

		return $dates;
	}


}
