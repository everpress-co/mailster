<?php

class MailsterStatistics {

	private $calendar_table = null;

	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ) );

	}

	public function init() {

		if ( is_admin() ) {
			add_action( 'admin_menu', array( &$this, 'add_menu' ), 30 );
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


	public function campaigns() {
		include MAILSTER_DIR . 'views/statistics/mb-campaigns.php';
	}
	public function subscribers() {
		include MAILSTER_DIR . 'views/statistics/mb-subscribers.php';
	}
	public function engagements() {
		include MAILSTER_DIR . 'views/statistics/mb-engagements.php';
	}

	public function scripts_styles() {

		global $wp_locale;
		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'mailster-chartjs', MAILSTER_URI . 'assets/js/libs/chart' . $suffix . '.js', array(), MAILSTER_VERSION );

		wp_enqueue_script( 'mailster-statistics-script', MAILSTER_URI . 'assets/js/statistics-script' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION );
		wp_localize_script( 'mailster-statistics-script', 'mailsterL10n', array() );

		$today           = date( 'Y-m-d' );
		$yesterday       = date( 'Y-m-d', strtotime( 'yesterday' ) );
		$last_7_days     = date( 'Y-m-d', strtotime( '-7 days' ) );
		$last_week_from  = date( 'Y-m-d', strtotime( 'sunday 1 week ago +' . get_option( 'start_of_week' ) . ' day' ) );
		$last_week_to    = date( 'Y-m-d', strtotime( 'sunday 1 week ago +' . ( 6 + get_option( 'start_of_week' ) ) . ' day' ) );
		$last_month_from = date( 'Y-m-d', mktime( 0, 0, 0, date( 'n' ) - 1, 1, date( 'Y' ) ) );
		$last_month_to   = date( 'Y-m-d', mktime( 0, 0, -1, date( 'n' ), 1, date( 'Y' ) ) );
		$this_month      = date( 'Y-m-d', mktime( 0, 0, 0, date( 'n' ), 1, date( 'Y' ) ) );
		$last_12_month   = date( 'Y-m-d', strtotime( '-12 month' ) );

		wp_localize_script(
			'mailster-statistics-script',
			'mailsterL10n',
			array(
				'next'          => __( 'next', 'mailster' ),
				'prev'          => __( 'prev', 'mailster' ),
				'start_of_week' => get_option( 'start_of_week' ),
				'day_names'     => $wp_locale->weekday,
				'day_names_min' => array_values( $wp_locale->weekday_abbrev ),
				'month_names'   => array_values( $wp_locale->month ),
				'now'           => $today,
				'today'         => array( $today, $today ),
				'yesterday'     => array( $yesterday, $yesterday ),
				'last_7_days'   => array( $last_7_days, $yesterday ),
				'last_week'     => array( $last_week_from, $last_week_to ),
				'last_month'    => array( $last_month_from, $last_month_to ),
				'this_month'    => array( $this_month, $yesterday ),
				'last_12_month' => array( $last_12_month, $yesterday ),
			)
		);

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-touch-punch' );

		wp_enqueue_style( 'mailster-statistics-style', MAILSTER_URI . 'assets/css/statistics-style' . $suffix . '.css', array(), MAILSTER_VERSION );

		wp_enqueue_style( 'jquery-style', MAILSTER_URI . 'assets/css/libs/jquery-ui' . $suffix . '.css' );
		wp_enqueue_style( 'jquery-datepicker', MAILSTER_URI . 'assets/css/datepicker' . $suffix . '.css' );

		wp_enqueue_style( 'jquery-datepicker', MAILSTER_URI . 'assets/css/datepicker' . $suffix . '.css', array(), MAILSTER_VERSION );

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-draggable' );

		wp_enqueue_script( 'jquery-ui-datepicker' );

	}

	public function register_meta_boxes() {

		$this->register_meta_box( 'engagements', __( 'Engagements', 'mailster' ), array( &$this, 'engagements' ) );
		$this->register_meta_box( 'subscribers', __( 'Subscribers', 'mailster' ), array( &$this, 'subscribers' ), 'side' );
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
			'labels'   => $this->get_labels( $rawdata ),
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

		$id     = 'mailster-mb-' . sanitize_key( $id );
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

		$id     = 'mailster-mb-' . sanitize_key( $id );
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

		$i    = 0;
		$prev = null;

		foreach ( $rawdata as $date => $count ) {
			$d   = strtotime( $date );
			$str = $wp_locale->weekday_abbrev[ $wp_locale->weekday[ date( 'w', $d ) ] ];
			if ( ! is_null( $prev ) ) {
				$grow = $count - $prev;
				if ( $grow > 0 ) {
					$str .= ' ▲+' . $this->format( $grow ) . ' ';
				} elseif ( $grow < 0 ) {
					$str .= ' ▼-' . $this->format( $grow ) . ' ';
				}
			}
			$prev        = $count;
			$dates[ $i ] = $str;
			$i++;
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
			array(
				'data'                      => array_values( $rawdata ),
				'backgroundColor'           => 'rgba(43,179,231,0.2)',
				'borderColor'               => 'rgba(43,179,231,1)',
				'pointColor'                => 'rgba(43,179,231,1)',
				'pointBorderColor'          => 'rgba(43,179,231,1)',
				'pointBackgroundColor'      => '#fff',
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
		$to   = is_null( $to ) ? time() + DAY_IN_SECONDS - 1 : $to;

		$dates = $this->get_date_range( $from, $to );
		$dates = array_fill_keys( $dates, 0 );

		$total = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}mailster_subscribers AS subscribers LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS list_subscribers ON subscribers.ID = list_subscribers.subscriber_id WHERE subscribers.status = 1 AND (list_subscribers.added != 0 OR list_subscribers.added IS NULL) AND IF(subscribers.confirm, subscribers.confirm, subscribers.signup) < %d", $from ) );

		$sql = "SELECT FROM_UNIXTIME(IF(subscribers.confirm, subscribers.confirm, subscribers.signup), '%Y-%m-%d') AS the_date, COUNT(DISTINCT subscribers.ID) AS increase FROM {$wpdb->prefix}mailster_subscribers AS subscribers LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS list_subscribers ON subscribers.ID = list_subscribers.subscriber_id WHERE subscribers.status = 1 AND (list_subscribers.added != 0 OR list_subscribers.added IS NULL) GROUP BY the_date HAVING the_date >= '" . date( 'Y-m-d', $from ) . "' AND the_date <= '" . date( 'Y-m-d', $to ) . "'";

		$increase_data = $wpdb->get_results( $sql );

		if ( ! empty( $increase_data ) ) {
			$increase_data = array_combine( wp_list_pluck( $increase_data, 'the_date' ), wp_list_pluck( $increase_data, 'increase' ) );
		}

		foreach ( $dates as $date => $count ) {

			if ( isset( $increase_data[ $date ] ) ) {
				$total += $increase_data[ $date ];
			}
			$dates[ $date ] = $total;
		}

			return $dates;
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

		$dates   = array();
		$current = $first;

		while ( $current <= $last ) {

			$dates[] = date( $format, $current );
			$current = strtotime( $step, $current );
		}

		return $dates;
	}


	/**
	 *
	 *
	 * @param unknown $value
	 * @return unknown
	 */
	private function format( $value ) {

		$value = (int) $value;

		if ( $value >= 1000000 ) {
			return round( $value / 1000, 1 ) . 'M';
		} elseif ( $value >= 1000 ) {
			return round( $value / 1000, 1 ) . 'K';
		}

		return ! ( $value % 1 ) ? $value : '';
	}

}
