<?php

class MailsterStatisitcsQuery {

	private $last_result;
	private $last_error;
	private $last_query;

	private $from = 0;
	private $to   = 0;

	private static $_instance = null;

	private function __construct( $args = null, $campaign_id = null ) {

		if ( ! is_null( $args ) ) {
			return $this->run( $args, $campaign_id );
		}

	}
	public function __destruct() {}

	public static function get_instance( $args = null, $campaign_id = null ) {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self( $args, $campaign_id );
		}
		return self::$_instance;
	}

	public function __get( $name ) {

		if ( ! isset( $this->$name ) ) {
			$this->{$name} = $this->{'get_' . $name}();
		}

		return $this->{$name};

	}

	public function get( $metric, $args = array() ) {

		if ( method_exists( $this, 'do_' . $metric ) ) {

			if ( $args['from'] ) {
				$this->from = $this->get_timestamp( $args['from'] );
			}
			if ( $args['to'] ) {
				$this->to = $this->get_timestamp( $args['to'] ) + DAY_IN_SECONDS - 1;
			}

			$this->set_previous_timeframe();

			return $this->{'do_' . $metric}();
		}

		return new WP_Error( 'not_found', 'This metric is not available' );

	}

	private function do_subscribers() {

		global $wpdb;

		$sql  = "SELECT FROM_UNIXTIME(IF(subscribers.confirm, subscribers.confirm, subscribers.signup), '%Y-%m-%d') AS x, COUNT(DISTINCT subscribers.ID) AS y";
		$sql .= " FROM {$wpdb->prefix}mailster_subscribers AS subscribers";
		$sql .= " LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS list_subscribers ON subscribers.ID = list_subscribers.subscriber_id";
		$sql .= ' WHERE (list_subscribers.added != 0 OR list_subscribers.added IS NULL)';
		$sql .= $wpdb->prepare( ' AND IF(subscribers.confirm, subscribers.confirm, subscribers.signup) BETWEEN %d AND %d', $this->from, $this->to );
		$sql .= ' GROUP BY x ORDER BY x';

		$calendar_table = $this->calendar_table( $this->from, $this->to );

		$sql = 'SELECT cal.the_date AS x, IFNULL(metric.y,0) AS y FROM (' . $calendar_table . ') AS cal LEFT JOIN (' . $sql . ') AS metric ON cal.the_date = metric.x ORDER BY cal.the_date';

		$result = $wpdb->get_results( $sql );

		$sql  = "SELECT FROM_UNIXTIME(IF(subscribers.confirm, subscribers.confirm, subscribers.signup), '%Y-%m-%d') AS x, COUNT(DISTINCT subscribers.ID) AS y";
		$sql .= " FROM {$wpdb->prefix}mailster_subscribers AS subscribers";
		$sql .= " LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS list_subscribers ON subscribers.ID = list_subscribers.subscriber_id";
		$sql .= ' WHERE (list_subscribers.added != 0 OR list_subscribers.added IS NULL)';
		$sql .= $wpdb->prepare( ' AND IF(subscribers.confirm, subscribers.confirm, subscribers.signup) BETWEEN %d AND %d', $this->from, $this->to );
		$sql .= ' GROUP BY x ORDER BY x';

		$calendar_table = $this->calendar_table( $this->prev_from, $this->prev_to );

		$sql = 'SELECT cal.the_date AS x, IFNULL(metric.y,0) AS y FROM (' . $calendar_table . ') AS cal LEFT JOIN (' . $sql . ') AS metric ON cal.the_date = metric.x ORDER BY cal.the_date';

		$prev = $wpdb->get_results( $sql );

		$total = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT subscribers.ID) FROM {$wpdb->prefix}mailster_subscribers AS subscribers LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS list_subscribers ON subscribers.ID = list_subscribers.subscriber_id WHERE subscribers.status = 1 AND (list_subscribers.added != 0 OR list_subscribers.added IS NULL) AND IF(subscribers.confirm, subscribers.confirm, subscribers.signup) < %d", $this->from ) );

		$start = $total;
		foreach ( $result as $i => $entry ) {
			$total          += $result[ $i ]->y;
			$result[ $i ]->y = $total;
		}
		$delta      = $total - $start;
		$percantage = $delta / $start;
		$increase   = $delta >= 0;

		$return = array(
			'gain'   => sprintf( '%s %s%%', ( $increase ? '+' : '-' ), number_format_i18n( round( $percantage * 100 ) ) ),
			'delta'  => number_format_i18n( $delta ),
			'total'  => number_format_i18n( $total ),
			'series' => array(
				array(
					'name' => __( 'Subscribers', 'mailster' ),
					'data' => $result,
				),
			),
		);

		return $return;

	}


	private function get_action_data( $action, $prev = 0 ) {
		global $wpdb;

		// only allow certain tables
		if ( ! in_array( $action, array( 'bounces', 'sent', 'clicks' ) ) ) {
			return array();
		}

		$sql  = 'SELECT FROM_UNIXTIME(t.timestamp, %s) AS x, COUNT(DISTINCT t.subscriber_id, t.campaign_id) AS y';
		$sql .= " FROM {$wpdb->prefix}mailster_action_{$action} AS t";
		$sql .= ' WHERE 1';
		// $sql .= ' AND t.campaign_id IS NOT NULL';
		$sql .= ' AND FROM_UNIXTIME(t.timestamp, %s) BETWEEN %s AND %s';
		$sql .= ' GROUP BY x ORDER BY x';
		$sql  = $wpdb->prepare( $sql, '%Y-%m-%d', '%Y-%m-%d', date( 'Y-m-d', $this->prev_from ), date( 'Y-m-d', $this->to ) );

		$calendar_table = $this->calendar_table( $this->prev_from, $this->to );

		$sql = 'SELECT cal.the_date AS x, IFNULL(metric.y,0) AS y FROM (' . $calendar_table . ') AS cal LEFT JOIN (' . $sql . ') AS metric ON cal.the_date = metric.x ORDER BY cal.the_date';

		$result = $wpdb->get_results( $sql );

		$days   = count( $result );
		$chunks = array_chunk( $result, $days / 2 );

		$series     = array();
		$data       = array();
		$prev_data  = array();
		$total      = 0;
		$prev_total = 0;
		$percantage = null;

		foreach ( $chunks[1] as $i => $entry ) {

			$data[ $i ]      = array(
				'x' => $entry->x,
				'y' => $entry->y + $prev,
			);
			$total          += $entry->y;
			$prev_data[ $i ] = array(
				'x' => $entry->x,
				'y' => $chunks[0][ $i ]->y + $prev,
			);
			$prev_total     += $chunks[0][ $i ]->y;

			$prev += $entry->y;
		}

		$return = (object) array(
			'data'       => $data,
			'total'      => $total,
			'prev'       => $prev_data,
			'prev_total' => $prev_total,
		);

		return $return;
	}

	private function p_merge( $a, $b ) {

		$list_a = wp_list_pluck( $a, 'y', 'x' );
		$list_b = wp_list_pluck( $b, 'y', 'x' );

		$new_list = array();

		foreach ( $list_b as $date => $value ) {
			$p          = ( $value ) ? ( ( $list_a[ $date ] / $value ) * 100 ) : null;
			$new_list[] = (object) array(
				'x' => $date,
				'y' => $p,
			);
		}

		return $new_list;
	}


	private function do_click_rate() {

		return $this->do_rate( 'clicks', 'sent' );

	}


	private function do_bounce_rate() {

		return $this->do_rate( 'bounces', 'sent' );

	}

	private function do_rate( $a, $b ) {

		$current_a = $this->get_data( $a, $this->to );
		$prev_a    = $this->get_data( $a, $this->prev_to );

		$current_b = $this->get_data( $b, $this->to );
		$prev_b    = $this->get_data( $b, $this->prev_to );

		$current_rate = $current_b ? $current_a / $current_b : 0;
		$prev_rate    = $prev_b ? $prev_a / $prev_b : 0;

		$delta_a = $current_a - $prev_a;
		$delta_b = $current_b - $prev_b;

		$gain = ( $current_rate - $prev_rate );

		$value = $current_rate * 100;

		$a_data = $this->get_action_data( $a, $prev_a );
		$b_data = $this->get_action_data( $b, $prev_b );

		$current_click_rate_data = $this->p_merge( $a_data->data, $b_data->data );
		$prev_click_rate_data    = $this->p_merge( $a_data->prev, $b_data->prev );

		$return = (object) array(
			'gain'      => $gain ? $gain : null,
			'value'     => $value,
			'data'      => $current_click_rate_data,
			'prev_data' => $prev_click_rate_data,
		);

		error_log( ' ' );
		error_log( 'current_' . $a . ': ' . $current_a );
		error_log( 'prev_' . $a . ': ' . $prev_a );

		error_log( 'current_' . $b . ': ' . $current_b );
		error_log( 'prev_' . $b . ': ' . $prev_b );

		error_log( 'current_rate: ' . $current_rate );
		error_log( 'prev_rate: ' . $prev_rate );
		error_log( 'gain: ' . $gain );

		error_log( 'delta_' . $a . ': ' . $delta_a );
		error_log( 'delta_' . $b . ': ' . $delta_b );

		return $this->prepare_data( $return );

	}


	private function get_data( $action, $to ) {

		global $wpdb;

		$data = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT subscriber_id, campaign_id) AS count FROM {$wpdb->prefix}mailster_action_{$action} AS t WHERE FROM_UNIXTIME(t.timestamp, %s) < %s", '%Y-%m-%d', date( 'Y-m-d', $to ) ) );

		return $data;

	}

	private function prepare_data( $data ) {

		$series[] = array(
			'name' => __( 'Current Period', 'mailster' ),
			'data' => $data->data,
		);

		if ( ! empty( $data->prev_data ) ) {
			$series[] = array(
				'name' => __( 'Previous Period', 'mailster' ),
				'data' => $data->prev_data,
			);
		}

		$return = array(
			'gain'   => ! is_null( $data->gain ) ? sprintf( '%s %s%%', ( $data->gain >= 0 ? '+' : '-' ), number_format_i18n( abs( $data->gain * 100 ), 2 ) ) : '',
			'value'  => number_format_i18n( $data->value, 2 ) . '%',
			'series' => $series,
		);

		return $return;

	}


	private function do_engagement() {

		global $wpdb,$wp_locale;

		$sql  = "SELECT CONCAT(FROM_UNIXTIME(click.timestamp, '%w'), FROM_UNIXTIME(click.timestamp, '%H')) AS x, COUNT(DISTINCT click.subscriber_id) AS y";
		$sql .= " FROM {$wpdb->prefix}mailster_action_clicks AS click";
		$sql .= ' WHERE 1';
		$sql .= $wpdb->prepare( ' AND click.timestamp BETWEEN %d AND %d', $this->from, $this->to );
		$sql .= ' GROUP BY x ORDER BY x';

		$week_table = $this->week_table( $this->from, $this->to );

		$sql    = 'SELECT cal.the_date AS x, IFNULL(metric.y,0) AS y FROM (' . $week_table . ') AS cal LEFT JOIN (' . $sql . ') AS metric ON cal.the_date = metric.x ORDER BY cal.the_date';
		$result = $wpdb->get_results( $sql );

		$values = wp_list_pluck( $result, 'y' );
		$max    = max( $values );
		$best   = array_keys( preg_grep( '/' . $max . '/', $values ) );

		$time_format = get_option( 'time_format' );
		$weekdays    = $wp_locale->weekday;

		$data      = array();
		$best_days = array();

		foreach ( $result as $i => $entry ) {
			$week = $entry->x[0];
			$hour = $entry->x[1] . $entry->x[2];
			if ( ! isset( $data[ $week ] ) ) {
				$data[ $week ] = array(
					'name' => $wp_locale->weekday_abbrev[ $wp_locale->weekday[ $week ] ],
					'data' => array(),
				);
			}
			$time                    = date_i18n( $time_format, strtotime( 'midnight +' . $hour . ' hours' ) );
			$data[ $week ]['data'][] = array(
				'x' => str_replace( ':00', '', $time ),
				'y' => intval( $entry->y ),
			);
			if ( $max && in_array( $i, $best ) ) {
				$best_days[] = sprintf( '%s at %s', $wp_locale->weekday[ $week ], $time );
			}
		}

		$return = array(
			// 'gain'   => sprintf( '%s %d%%', ( $increase ? '+' : '-' ), round( $percantage * 100 ) ),
			// 'delta'  => number_format_i18n( $delta ),
			'max'    => $max,
			'total'  => implode( ' and ', $best_days ),
			'series' => $data,
		);

		return $return;

	}


	private function do_locations() {

		global $wpdb,$wp_locale;

		$sql = "SELECT SUBSTRING_INDEX(meta_value, '|', 1) AS code, COUNT(*) AS count FROM `{$wpdb->prefix}mailster_subscriber_meta` WHERE meta_key = 'geo' GROUP BY SUBSTRING_INDEX(meta_value, '|', 1) ORDER BY count DESC";

		$sql  = "SELECT SUBSTRING_INDEX(meta_value, '|', 1) AS code, COUNT(*) AS count FROM `{$wpdb->prefix}mailster_subscriber_meta` AS meta";
		$sql .= ' LEFT JOIN (SELECT subscribers.ID FROM wp_mailster_subscribers AS subscribers LEFT JOIN wp_mailster_lists_subscribers AS list_subscribers ON subscribers.ID = list_subscribers.subscriber_id';
		$sql .= ' WHERE (list_subscribers.added != 0 OR list_subscribers.added IS NULL) ';
		$sql .= $wpdb->prepare( ' AND IF(subscribers.confirm, subscribers.confirm, subscribers.signup) BETWEEN %d AND %d)', $this->from, $this->to );
		$sql .= '  AS subscribers ON subscribers.ID = meta.subscriber_id';
		$sql .= "  WHERE subscribers.ID IS NOT NULL AND meta.meta_key = 'geo' GROUP BY SUBSTRING_INDEX(meta_value, '|', 1) ORDER BY count DESC";

		$result = $wpdb->get_results( $sql );

		$data = array();

		$html = '<table class="wp-list-table widefat striped table-view-list">';

		foreach ( $result as $i => $entry ) {

			$html .= '<tr><td>' . esc_html( mailster( 'geo' )->code2Country( $entry->code ) ) . '</td><td>' . number_format_i18n( $entry->count ) . '</td></tr>';

			$data[] = array(
				'location' => mailster( 'geo' )->code2Country( $entry->code ),
				'code'     => $entry->code,
				'count'    => (int) $entry->count,
			);

		}
		$html .= '</table>';

		$return = array(
			// 'gain'   => sprintf( '%s %d%%', ( $increase ? '+' : '-' ), round( $percantage * 100 ) ),
			// 'delta'  => number_format_i18n( $delta ),
			// 'max'    => $max,
			// 'total'  => implode( ' and ', $best_days ),
			'html' => $html,
		);

		return $return;

	}

	private function do_links() {

		global $wpdb,$wp_locale;

		$sql = 'SELECT COUNT(*) AS count, links.link FROM `wp_mailster_action_clicks` AS clicks LEFT JOIN `wp_mailster_links` AS links ON links.ID = clicks.link_id GROUP BY link ORDER BY count DESC';

		$sql  = 'SELECT links.link, COUNT(*) AS count FROM `wp_mailster_action_clicks` AS clicks';
		$sql .= ' LEFT JOIN `wp_mailster_links` AS links ON links.ID = clicks.link_id';
		$sql .= " LEFT JOIN `wp_postmeta` AS meta ON meta.post_id = clicks.campaign_id AND meta.meta_key = '_mailster_timestamp'";
		$sql .= $wpdb->prepare( ' WHERE meta.meta_value BETWEEN %d AND %d', $this->from, $this->to );
		$sql .= ' GROUP BY links.link ORDER BY count DESC';

		$result = $wpdb->get_results( $sql );

		$data = array();

		$html = '<table class="wp-list-table widefat striped table-view-list">';

		foreach ( $result as $i => $entry ) {

			$html .= '<tr><td>' . esc_html( $entry->link ) . '</td><td>' . number_format_i18n( $entry->count ) . '</td></tr>';

		}
		$html .= '</table>';

		$return = array(
			// 'gain'   => sprintf( '%s %d%%', ( $increase ? '+' : '-' ), round( $percantage * 100 ) ),
			// 'delta'  => number_format_i18n( $delta ),
			// 'max'    => $max,
			// 'total'  => implode( ' and ', $best_days ),
			'html' => $html,
		);

		return $return;

	}


	private function map( $result, $delta = 0 ) {

		foreach ( $result as $i => $entry ) {
			$delta          += $result[ $i ]->y;
			$result[ $i ]->y = $delta;
		}

		return $result;

		$data = wp_list_pluck( $result, 'increase', 'the_date' );
		$data = array_map( 'floatval', $data );

		$start = $this->args['from'];
		$end   = $this->args['to'];

		for ( $i = $start; $i < $end; $i += DAY_IN_SECONDS ) {
			$date = date( 'Y-m-d', $i );
			if ( isset( $data[ $date ] ) ) {
			} else {
				$data[ $date ] = 0;
			}
		}
		ksort( $data );
		return $data;

	}

	private function calendar_table( $from = null, $to = null, $colname = 'the_date' ) {

		global $wpdb;

		if ( is_null( $from ) ) {
			$from = $this->from;
		}
		if ( is_null( $to ) ) {
			$to = $this->to;
		}

		$number_days = floor( ( $to - $from ) / DAY_IN_SECONDS );

		$to_date = date( 'Y-m-d', $to );

		// https://stackoverflow.com/a/24623199
		$sql = 'SELECT (%s - INTERVAL c.number DAY) AS %s FROM (SELECT s + t + h number FROM ( SELECT 0 s UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 ) s JOIN (SELECT 0 t UNION ALL SELECT 10 UNION ALL SELECT 20 UNION ALL SELECT 30 UNION ALL SELECT 40 UNION ALL SELECT 50 UNION ALL SELECT 60 UNION ALL SELECT 70 UNION ALL SELECT 80 UNION ALL SELECT 90 ) t JOIN (SELECT 0 h UNION ALL SELECT 100 UNION ALL SELECT 200 UNION ALL SELECT 300 UNION ALL SELECT 400 UNION ALL SELECT 500 UNION ALL SELECT 600 UNION ALL SELECT 700 UNION ALL SELECT 800 UNION ALL SELECT 900 ) h ORDER BY number DESC) c WHERE c.number BETWEEN 0 and %d';

		return $wpdb->prepare( $sql, $to_date, $colname, $number_days );

	}

	private function week_table( $from = null, $to = null, $colname = 'the_date' ) {

		global $wpdb;

		$sql = 'SELECT LPAD(c.number, 3, 0) AS %s FROM (SELECT s + t number FROM ( SELECT 0 s UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT  10 UNION ALL SELECT  11 UNION ALL SELECT  12  UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15 UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL SELECT 20 UNION ALL SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23 ) s JOIN (SELECT 0 t UNION ALL SELECT 100 UNION ALL SELECT 200 UNION ALL SELECT 300 UNION ALL SELECT 400 UNION ALL SELECT 500 UNION ALL SELECT 600 ) t ORDER BY number ASC) c WHERE c.number BETWEEN 0 and 623';

		return $wpdb->prepare( $sql, $colname );

	}


	private function set_previous_timeframe() {

		$diff = $this->to - $this->from;

		$this->prev_from = $this->from - $diff - 1;
		$this->prev_to   = $this->from - 1;

	}


	private function get_timestamp( $value, $format = null, $relative = null ) {
		$timestamp = is_numeric( $value ) ? strtotime( '@' . $value ) : strtotime( '' . $value );
		if ( false !== $timestamp ) {
		} elseif ( is_numeric( $value ) ) {
			$timestamp = (int) $value;
		} else {
			return false;
		}

		if ( ! is_null( $relative ) ) {
			$timestamp = time() + $timestamp;
		}

		if ( is_null( $format ) ) {
			return $timestamp;
		}

		return date( $format, $timestamp );
	}

}
