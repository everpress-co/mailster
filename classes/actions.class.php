<?php

class MailsterActions {

	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ), 1 );

	}


	public function init() {

		add_action( 'mailster_send', array( &$this, 'send' ), 10, 2 );
		add_action( 'mailster_open', array( &$this, 'open' ), 10, 3 );
		add_action( 'mailster_click', array( &$this, 'click' ), 10, 4 );
		add_action( 'mailster_unsubscribe', array( &$this, 'unsubscribe' ), 10, 3 );
		add_action( 'mailster_list_unsubscribe', array( &$this, 'list_unsubscribe' ), 10, 4 );
		add_action( 'mailster_bounce', array( &$this, 'bounce' ), 10, 4 );
		add_action( 'mailster_subscriber_error', array( &$this, 'error' ), 10, 3 );
		add_action( 'mailster_cron_cleanup', array( &$this, 'cleanup' ) );

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @param unknown $campaign_id
	 * @return unknown
	 */
	public function send( $subscriber_id, $campaign_id ) {

		return $this->add_action(
			array(
				'subscriber_id' => $subscriber_id,
				'campaign_id'   => $campaign_id,
				'type'          => 'sent',
			),
			true
		);

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @param unknown $campaign_id
	 * @param unknown $explicit      (optional)
	 * @return unknown
	 */
	public function open( $subscriber_id, $campaign_id, $explicit = true ) {

		return $this->add_subscriber_action(
			array(
				'subscriber_id' => $subscriber_id,
				'campaign_id'   => $campaign_id,
				'type'          => 'opens',
			),
			$explicit
		);

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @param unknown $campaign_id
	 * @param unknown $link
	 * @param unknown $index         (optional)
	 * @param unknown $explicit      (optional)
	 * @return unknown
	 */
	public function click( $subscriber_id, $campaign_id, $link, $index = 0, $explicit = true ) {

		$this->open( $subscriber_id, $campaign_id, false );

		$link_id = $this->get_link_id( $link, $index );

		return $this->add_subscriber_action(
			array(
				'subscriber_id' => $subscriber_id,
				'campaign_id'   => $campaign_id,
				'type'          => 'clicks',
				'link_id'       => $link_id,
			),
			$explicit
		);

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @param unknown $campaign_id
	 * @param unknown $status (optional)
	 * @return unknown
	 */
	public function unsubscribe( $subscriber_id, $campaign_id, $status = null ) {

		return $this->add_action(
			array(
				'subscriber_id' => $subscriber_id,
				'campaign_id'   => $campaign_id,
				'type'          => 'unsubs',
			)
		);

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @param unknown $campaign_id
	 * @param unknown $lists
	 * @param unknown $status (optional)
	 * @return unknown
	 */
	public function list_unsubscribe( $subscriber_id, $campaign_id, $lists, $status = null ) {

		return $this->unsubscribe( $subscriber_id, $campaign_id, $status );

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @param unknown $campaign_id
	 * @param unknown $hard          (optional)
	 * @param unknown $status        (optional)
	 * @return unknown
	 */
	public function bounce( $subscriber_id, $campaign_id, $hard = false, $status = null ) {

		return $this->add_action(
			array(
				'subscriber_id' => $subscriber_id,
				'campaign_id'   => $campaign_id,
				'type'          => 'bounces',
				'hard'          => $hard,
				'text'          => $status,
				'count'         => 1,
			)
		);

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @param unknown $campaign_id
	 * @param unknown $error         (optional)
	 * @return unknown
	 */
	public function error( $subscriber_id, $campaign_id, $error = '' ) {

		mailster( 'subscribers' )->update_meta( $subscriber_id, $campaign_id, 'error', $error );

		return $this->add_action(
			array(
				'subscriber_id' => $subscriber_id,
				'campaign_id'   => $campaign_id,
				'type'          => 'error',
			)
		);

	}


	/**
	 *
	 *
	 * @param unknown $args
	 * @param unknown $explicit (optional)
	 */
	private function add_subscriber_action( $args, $explicit = true ) {

		if ( mailster_option( 'do_not_track' ) && isset( $_SERVER['HTTP_DNT'] ) && $_SERVER['HTTP_DNT'] == 1 ) {
			return;
		}

		$user_meta = array(
			'ip' => mailster_get_ip(),
		);

		if ( 'unknown' !== ( $geo = mailster_ip2City() ) ) {

			$user_meta['geo'] = $geo->country_code . '|' . $geo->city;
			if ( $geo->city ) {
				$user_meta['coords']     = (float) $geo->latitude . ',' . (float) $geo->longitude;
				$user_meta['timeoffset'] = (int) $geo->timeoffset;
			}
		}

		// only explicitly opened
		if ( 'opens' == $args['type'] && $explicit ) {

			if ( $client = mailster_get_user_client() ) {

				// remove meta info if client is Gmail (GoogleImageProxyy)
				if ( 'Gmail' == $client->client ) {
					$user_meta = array();
					// Gmail downloads images as soon as recevied
					if ( 'http://mail.google.com/' == wp_get_raw_referer() ) {
						return;
					}
				}
				if ( 'Yahoo' == $client->client ) {
					$user_meta = array();
				}

				$user_meta['client']        = $client->client;
				$user_meta['clientversion'] = $client->version;
				$user_meta['clienttype']    = $client->type;
			}
		}

		mailster( 'subscribers' )->update_meta( $args['subscriber_id'], $args['campaign_id'], $user_meta );

		$this->add( $args, $explicit );

	}


	/**
	 *
	 *
	 * @param unknown $args
	 * @param unknown $explicit (optional)
	 */
	private function add_action( $args, $explicit = true ) {

		$this->add( $args, $explicit );
	}


	/**
	 *
	 *
	 * @param unknown $args
	 * @param unknown $explicit (optional)
	 * @return unknown
	 */
	private function add( $args, $explicit = true ) {

		global $wpdb;

		$type = $args['type'];
		unset( $args['type'] );

		$args = wp_parse_args(
			$args,
			array(
				'timestamp' => time(),
				'count'     => 1,
			)
		);

		$table = 'action_' . $type;

		$sql = "INSERT INTO {$wpdb->prefix}mailster_$table (" . implode( ', ', array_keys( $args ) ) . ')';

		$sql .= " VALUES ('" . implode( "','", array_values( $args ) ) . "') ON DUPLICATE KEY UPDATE";

		$sql .= ( $explicit ) ? ' timestamp = timestamp, count = count+1' : ' count = count';

		$sql = apply_filters( 'mailster_actions_add_sql', $sql, $args, $explicit );

		$result = $wpdb->query( $sql );

		if ( false !== $result ) {
			// re calculate rating on actions
			if ( $result == 1 && $explicit && $type != 'sent' && isset( $args['subscriber_id'] ) ) {
				wp_schedule_single_event( time() + 120, 'mailster_update_rating', array( $args['subscriber_id'] ) );
			}

			return true;
		}

		return false;

	}


	/**
	 * clear queue with all subscribers in $campaign_id but NOT in subscribers
	 *
	 * @param unknown $campaign_id
	 * @param unknown $subscribers
	 * @return unknown
	 */
	public function clear( $campaign_id, $subscribers ) {

		global $wpdb;

		$campaign_id = (int) $campaign_id;
		$subscribers = array_filter( $subscribers, 'is_numeric' );

		if ( empty( $subscribers ) ) {
			return true;
		}

		$chunks = array_chunk( $subscribers, 200 );

		$success = true;

		foreach ( $chunks as $subscriber_chunk ) {

			$sql = "DELETE a FROM {$wpdb->prefix}mailster_queue AS a WHERE a.campaign_id = %d AND a.sent = 0 AND a.subscriber_id NOT IN (" . implode( ',', $subscriber_chunk ) . ')';

			$success = $success && $wpdb->query( $wpdb->prepare( $sql, $campaign_id ) );

		}

		return $success;

	}


	public function cleanup() {

		global $wpdb;

		// remove actions where's either a subscriber nor a campaign assigned
		$wpdb->query( "DELETE actions FROM {$wpdb->prefix}mailster_action_sent AS actions WHERE actions.subscriber_id IS NULL AND actions.campaign_id IS NULL" );
		$wpdb->query( "DELETE actions FROM {$wpdb->prefix}mailster_action_opens AS actions WHERE actions.subscriber_id IS NULL AND actions.campaign_id IS NULL" );
		$wpdb->query( "DELETE actions FROM {$wpdb->prefix}mailster_action_clicks AS actions WHERE actions.subscriber_id IS NULL AND actions.campaign_id IS NULL" );
		$wpdb->query( "DELETE actions FROM {$wpdb->prefix}mailster_action_unsubs AS actions WHERE actions.subscriber_id IS NULL AND actions.campaign_id IS NULL" );
		$wpdb->query( "DELETE actions FROM {$wpdb->prefix}mailster_action_bounces AS actions WHERE actions.subscriber_id IS NULL AND actions.campaign_id IS NULL" );
		$wpdb->query( "DELETE actions FROM {$wpdb->prefix}mailster_action_errors AS actions WHERE actions.subscriber_id IS NULL AND actions.campaign_id IS NULL" );

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id (optional)
	 * @param unknown $action      (optional)
	 * @return unknown
	 */
	public function get_by_campaign( $campaign_id = null, $action = null ) {

		global $wpdb;

		$cache_key = 'action_counts_by_campaign_' . $action;

		$action_counts = mailster_cache_get( $cache_key );
		if ( ! $action_counts ) {
			$action_counts = array();
		}

		if ( is_numeric( $campaign_id ) ) {

			if ( isset( $action_counts[ $campaign_id ] ) ) {
				if ( is_null( $action ) ) {
					return $action_counts[ $campaign_id ];
				}

				return isset( $action_counts[ $campaign_id ][ $action ] ) ? $action_counts[ $campaign_id ][ $action ] : null;
			}

			$campaign_ids = array( $campaign_id );

		} elseif ( is_array( $campaign_id ) ) {

			$campaign_ids = $campaign_id;

		}

		$default = $this->get_default_action_counts();

		$sql = "SELECT a.post_id AS ID, a.meta_value AS parent_id FROM {$wpdb->postmeta} AS a WHERE a.meta_key = '_mailster_parent_id'";

		if ( isset( $campaign_ids ) ) {
			$sql .= ' AND a.meta_value IN (' . implode( ',', $campaign_ids ) . ')';
		}

		$parent_ids = array();
		$parents    = $wpdb->get_results( $sql );
		foreach ( $parents as $parent ) {
			$parent_ids[ $parent->ID ] = $parent->parent_id;
		}

		$table = $mod_action = str_replace( array( '_total', '_deleted' ), '', $action );
		$table = str_replace( array( 'soft' ), '', $table );

		$sql = "SELECT a.campaign_id AS ID, COUNT( DISTINCT COALESCE( a.subscriber_id, 1) ) AS count, COUNT(DISTINCT a.subscriber_id) AS count_cleard, SUM(a.count) AS total FROM `{$wpdb->prefix}mailster_action_$table` AS a";

		if ( isset( $campaign_ids ) ) {
			$sql .= ' WHERE a.campaign_id IN (' . implode( ',', $campaign_ids ) . ')';
		}

		if ( ! empty( $parent_ids ) ) {
			$sql .= ' OR a.campaign_id IN (' . implode( ',', array_keys( $parent_ids ) ) . ')';
		}

		$sql .= ' GROUP BY a.campaign_id';

		$result = $wpdb->get_results( $sql );

		foreach ( $campaign_ids as $id ) {
			if ( ! isset( $action_counts[ $id ] ) ) {
				$action_counts[ $id ] = $default;
			}
		}

		foreach ( $result as $row ) {

			if ( ! isset( $action_counts[ $row->ID ] ) ) {
				$action_counts[ $row->ID ] = $default;
			}

			if ( ( $hasparent = isset( $parent_ids[ $row->ID ] ) ) && ! isset( $action_counts[ $parent_ids[ $row->ID ] ] ) ) {
				$action_counts[ $parent_ids[ $row->ID ] ] = $default;
			}

			// sent
			if ( 'sent' == $mod_action ) {
				$action_counts[ $row->ID ]['sent']         = (int) $row->count;
				$action_counts[ $row->ID ]['sent_total']   = (int) $row->total;
				$action_counts[ $row->ID ]['sent_deleted'] = (int) $row->count - (int) $row->count_cleard;
				if ( $hasparent ) {
					$action_counts[ $parent_ids[ $row->ID ] ]['sent']         += (int) $row->count;
					$action_counts[ $parent_ids[ $row->ID ] ]['sent_total']   += (int) $row->total;
					$action_counts[ $parent_ids[ $row->ID ] ]['sent_deleted'] += ( (int) $row->count - (int) $row->count_cleard );
				}
			} // opens
			elseif ( 'opens' == $mod_action ) {
				$action_counts[ $row->ID ]['opens']         = (int) $row->count;
				$action_counts[ $row->ID ]['opens_total']   = (int) $row->total;
				$action_counts[ $row->ID ]['opens_deleted'] = (int) $row->count - (int) $row->count_cleard;
				if ( $hasparent ) {
					$action_counts[ $parent_ids[ $row->ID ] ]['opens']         += (int) $row->count;
					$action_counts[ $parent_ids[ $row->ID ] ]['opens_total']   += (int) $row->total;
					$action_counts[ $parent_ids[ $row->ID ] ]['opens_deleted'] += ( (int) $row->count - (int) $row->count_cleard );
				}
			} // clicks
			elseif ( 'clicks' == $mod_action ) {
				$action_counts[ $row->ID ]['clicks']         = (int) $row->count;
				$action_counts[ $row->ID ]['clicks_total']   = (int) $row->total;
				$action_counts[ $row->ID ]['clicks_deleted'] = (int) $row->count - (int) $row->count_cleard;
				if ( $hasparent ) {
					$action_counts[ $parent_ids[ $row->ID ] ]['clicks']         += (int) $row->count;
					$action_counts[ $parent_ids[ $row->ID ] ]['clicks_total']   += (int) $row->total;
					$action_counts[ $parent_ids[ $row->ID ] ]['clicks_deleted'] += ( (int) $row->count - (int) $row->count_cleard );
				}
			} // unsubs
			elseif ( 'unsubs' == $mod_action ) {
				$action_counts[ $row->ID ]['unsubs']         = (int) $row->count;
				$action_counts[ $row->ID ]['unsubs_deleted'] = (int) $row->count - (int) $row->count_cleard;
				if ( $hasparent ) {
					$action_counts[ $parent_ids[ $row->ID ] ]['unsubs']         += (int) $row->count;
					$action_counts[ $parent_ids[ $row->ID ] ]['unsubs_deleted'] += ( (int) $row->count - (int) $row->count_cleard );
				}
			} // softbounces
			elseif ( 'softbounces' == $mod_action ) {
				$action_counts[ $row->ID ]['softbounces']         = (int) $row->count;
				$action_counts[ $row->ID ]['softbounces_deleted'] = (int) $row->count - (int) $row->count_cleard;
				if ( $hasparent ) {
					$action_counts[ $parent_ids[ $row->ID ] ]['softbounces']         += (int) $row->count;
					$action_counts[ $parent_ids[ $row->ID ] ]['softbounces_deleted'] += ( (int) $row->count - (int) $row->count_cleard );
				}
			} // bounces
			elseif ( 'bounces' == $mod_action ) {
				$action_counts[ $row->ID ]['bounces']         = (int) $row->count;
				$action_counts[ $row->ID ]['bounces_deleted'] = (int) $row->count - (int) $row->count_cleard;
				$action_counts[ $row->ID ]['sent']           -= (int) $row->count;
				if ( $hasparent ) {
					$action_counts[ $parent_ids[ $row->ID ] ]['bounces']         += (int) $row->count;
					$action_counts[ $parent_ids[ $row->ID ] ]['bounces_deleted'] += ( (int) $row->count - (int) $row->count_cleard );
					$action_counts[ $parent_ids[ $row->ID ] ]['sent']            -= (int) $row->count;
				}
			} // error
			elseif ( 'error' == $mod_action ) {
				$action_counts[ $row->ID ]['errors']         = (int) $row->count;
				$action_counts[ $row->ID ]['errors_total']   = (int) $row->total;
				$action_counts[ $row->ID ]['errors_deleted'] = (int) $row->count - (int) $row->count_cleard;
				if ( $hasparent ) {
					$action_counts[ $parent_ids[ $row->ID ] ]['errors']         += (int) $row->count;
					$action_counts[ $parent_ids[ $row->ID ] ]['errors_total']   += (int) $row->total;
					$action_counts[ $parent_ids[ $row->ID ] ]['errors_deleted'] += ( (int) $row->count - (int) $row->count_cleard );
				}
			}
		}

		mailster_cache_set( $cache_key, $action_counts );

		if ( is_null( $campaign_id ) && is_null( $action ) ) {
			return $action_counts;
		}

		if ( is_array( $campaign_id ) && is_null( $action ) ) {
			return $action_counts;
		}

		if ( is_null( $action ) ) {
			return isset( $action_counts[ $campaign_id ] ) ? $action_counts[ $campaign_id ] : $default;
		}

		return isset( $action_counts[ $campaign_id ] ) && isset( $action_counts[ $campaign_id ][ $action ] ) ? $action_counts[ $campaign_id ][ $action ] : 0;

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id (optional)
	 * @param unknown $action        (optional)
	 * @param unknown $campaign_id        (optional)
	 * @return unknown
	 */
	public function get_by_subscriber( $subscriber_id = null, $action = null, $campaign_id = null ) {

		global $wpdb;

		if ( ! $action ) {
			$return = array();
			foreach ( array( 'sent', 'opens', 'clicks', 'unsubs', 'bounces', 'errors', 'softbounces' ) as $a ) {
				$return[ $a ] = $this->get_by_subscriber( $subscriber_id, $a, $campaign_id );
			}

			return $return;
		}

		$cache_key      = 'action_counts_by_subscriber_' . $action . $campaign_id;
		$subscriber_ids = array();

		$action_counts = mailster_cache_get( $cache_key );
		if ( ! $action_counts ) {
			$action_counts = array();
		}

		if ( is_numeric( $subscriber_id ) ) {

			if ( isset( $action_counts[ $subscriber_id ] ) ) {
				if ( is_null( $action ) ) {
					return $action_counts[ $subscriber_id ];
				}

				return isset( $action_counts[ $subscriber_id ][ $action ] ) ? $action_counts[ $subscriber_id ][ $action ] : null;
			}

			$subscriber_ids = array( $subscriber_id );

		} elseif ( is_array( $subscriber_id ) ) {

			$subscriber_ids = array_filter( $subscriber_id, 'is_numeric' );

		}

		$table = $mod_action = str_replace( array( '_total', '_deleted' ), '', $action );
		$table = str_replace( array( 'soft' ), '', $table );

		$default = $this->get_default_action_counts();

		$sql = "SELECT action_table.*, COUNT(DISTINCT action_table.subscriber_id) AS count, SUM(action_table.count) AS total FROM `{$wpdb->prefix}mailster_action_$table` AS action_table WHERE 1";

		if ( ! empty( $subscriber_ids ) ) {
			$sql .= ' AND action_table.subscriber_id IN (' . implode( ',', $subscriber_ids ) . ')';
		}
		if ( ! empty( $campaign_ids ) ) {
			$sql .= ' AND action_table.campaign_id = ' . (int) $campaign_id;
		}
		if ( 'softbounces' == $action ) {
			$sql .= ' AND action_table.hard = 0';
		} elseif ( 'bounces' == $action ) {
			$sql .= ' AND action_table.hard = 1';

		}
		$sql .= ' GROUP BY action_table.subscriber_id, action_table.campaign_id';

		$result = $wpdb->get_results( $sql );

		foreach ( $subscriber_ids as $id ) {
			if ( ! isset( $action_counts[ $id ] ) ) {
				$action_counts[ $id ] = $default;
			}
		}

		foreach ( $result as $row ) {

			if ( ! isset( $action_counts[ $row->subscriber_id ] ) ) {
				$action_counts[ $row->subscriber_id ] = $default;
			}

			if ( 'sent' == $mod_action ) {
				$action_counts[ $row->subscriber_id ]['sent']       += (int) $row->count;
				$action_counts[ $row->subscriber_id ]['sent_total'] += (int) $row->total;
			} elseif ( 'opens' == $mod_action ) {
				$action_counts[ $row->subscriber_id ]['opens']       += (int) $row->count;
				$action_counts[ $row->subscriber_id ]['opens_total'] += (int) $row->total;
			} elseif ( 'clicks' == $mod_action ) {
				$action_counts[ $row->subscriber_id ]['clicks']       += (int) $row->count;
				$action_counts[ $row->subscriber_id ]['clicks_total'] += (int) $row->total;
			} elseif ( 'unsubs' == $mod_action ) {
				$action_counts[ $row->subscriber_id ]['unsubs'] += (int) $row->count;
			} elseif ( 'softbounces' == $mod_action ) {
				$action_counts[ $row->subscriber_id ]['softbounces'] += (int) $row->count;
			} elseif ( 'bounces' == $mod_action ) {
				$action_counts[ $row->subscriber_id ]['bounces'] += (int) $row->count;
			} elseif ( 'error' == $mod_action ) {
				$action_counts[ $row->subscriber_id ]['errors']       += floor( $row->count );
				$action_counts[ $row->subscriber_id ]['errors_total'] += floor( $row->total );
			}
		}

		mailster_cache_set( $cache_key, $action_counts );

		if ( is_null( $subscriber_id ) && is_null( $action ) ) {
			return $action_counts;
		}

		if ( is_array( $subscriber_id ) ) {
			return $action_counts;
		}

		if ( is_null( $action ) ) {
			return isset( $action_counts[ $subscriber_id ] ) ? $action_counts[ $subscriber_id ] : $default;
		}

		if ( isset( $action_counts[ $subscriber_id ] ) && isset( $action_counts[ $subscriber_id ][ $action ] ) ) {
			return $action_counts[ $subscriber_id ][ $action ];
		}

		return 0;

	}


	/**
	 *
	 *
	 * @param unknown $list_id (optional)
	 * @param unknown $action  (optional)
	 * @return unknown
	 */
	public function get_by_list( $list_id = null, $action = null ) {

		global $wpdb;

		$cache_key = 'action_counts_by_lists_' . $action;

		$action_counts = mailster_cache_get( $cache_key );
		if ( ! $action_counts ) {
			$action_counts = array();
		}

		if ( is_numeric( $list_id ) ) {

			if ( isset( $action_counts[ $list_id ] ) ) {
				if ( is_null( $action ) ) {
					return $action_counts[ $list_id ];
				}

				return isset( $action_counts[ $list_id ][ $action ] ) ? $action_counts[ $list_id ][ $action ] : null;
			}

			$list_ids = array( $list_id );

		} elseif ( is_array( $list_id ) ) {

			$list_ids = $list_id;

		}

		$table = str_replace( array( '_total', '_deleted' ), '', $action );
		if ( 'total' == $table ) {
			$table = 'sent';
		} elseif ( 'unsubscribes' == $table ) {
			$table = 'unsubs';
		}

		$default = $this->get_default_action_counts();

		$sql = "SELECT b.list_id AS ID, COUNT(DISTINCT a.subscriber_id) AS count, SUM(a.count) AS total FROM {$wpdb->prefix}mailster_action_$table AS a";

		$sql .= " LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS b ON a.subscriber_id = b.subscriber_id WHERE a.campaign_id != 0";

			$sql .= ' AND b.list_id = ' . (int) $list_id;

		$sql .= ' GROUP BY b.list_id, a.campaign_id';

		$result = $wpdb->get_results( $sql );

		foreach ( $list_ids as $id ) {
			if ( ! isset( $action_counts[ $id ] ) ) {
				$action_counts[ $id ] = $default;
			}
		}

		foreach ( $result as $row ) {

			if ( ! isset( $action_counts[ $row->ID ] ) ) {
				$action_counts[ $row->ID ] = $default;
			}

			if ( 'sent' == $action ) {
				$action_counts[ $row->ID ]['sent']       += (int) $row->count;
				$action_counts[ $row->ID ]['sent_total'] += (int) $row->total;
			} elseif ( 'opens' == $action ) {
				$action_counts[ $row->ID ]['opens']       += (int) $row->count;
				$action_counts[ $row->ID ]['opens_total'] += (int) $row->total;
			} elseif ( 'clicks' == $action ) {
				$action_counts[ $row->ID ]['clicks']       += (int) $row->count;
				$action_counts[ $row->ID ]['clicks_total'] += (int) $row->total;
			} elseif ( 'unsubs' == $action ) {
				$action_counts[ $row->ID ]['unsubs'] += (int) $row->count;
			} elseif ( 'softbounces' == $action ) {
				$action_counts[ $row->ID ]['softbounces'] += (int) $row->count;
			} elseif ( 'bounces' == $action ) {
				$action_counts[ $row->ID ]['bounces'] += (int) $row->count;
			} elseif ( 'error' == $action ) {
				$action_counts[ $row->ID ]['errors']       += floor( $row->count );
				$action_counts[ $row->ID ]['errors_total'] += floor( $row->total );
			}
		}

		mailster_cache_set( $cache_key, $action_counts );

		if ( is_null( $list_id ) && is_null( $action ) ) {
			return $action_counts;
		}

		if ( is_null( $action ) ) {
			return isset( $action_counts[ $list_id ] ) ? $action_counts[ $list_id ] : $default;
		}

		return isset( $action_counts[ $list_id ] ) && isset( $action_counts[ $list_id ][ $action ] ) ? $action_counts[ $list_id ][ $action ] : 0;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function get_default_action_counts() {
		return array(
			'sent'                => 0,
			'sent_total'          => 0,
			'sent_deleted'        => 0,
			'opens'               => 0,
			'opens_total'         => 0,
			'opens_deleted'       => 0,
			'clicks'              => 0,
			'clicks_total'        => 0,
			'clicks_deleted'      => 0,
			'unsubs'              => 0,
			'unsubs_deleted'      => 0,
			'softbounces'         => 0,
			'softbounces_deleted' => 0,
			'bounces'             => 0,
			'bounces_deleted'     => 0,
			'errors'              => 0,
			'errors_total'        => 0,
			'errors_deleted'      => 0,
		);
	}



	/**
	 *
	 *
	 * @param unknown $name
	 * @return unknown
	 */
	private function get_color_string( $name ) {

		switch ( $name ) {
			case 'sent':
				return '234,53,86';
			case 'opens':
				return '97,210,214';
			case 'clicks':
				return '255,228,77';
			case 'unsubs':
				return '181,225,86';
			case 'bounces':
				return '130,24,124';
			default:
				return '128,128,128';
		}

	}



	public function get_campaign_actions( $campaign_id, $subscriber_id = null, $action = null, $cache = true ) {

		global $wpdb;

		if ( false === ( $actions = mailster_cache_get( 'campaign_actions' ) ) ) {

			$default = array(
				'sent'              => 0,
				'sent_total'        => 0,
				'opens'             => 0,
				'opens_total'       => 0,
				'clicks'            => array(),
				'clicks_total'      => 0,
				'unsubs'            => 0,
				'softbounces'       => 0,
				'softbounces_total' => 0,
				'bounces'           => 0,
				'errors'            => 0,
				'errors_total'      => 0,
			);

			$actions = array();

			$sql = "SELECT a.subscriber_id AS ID, type, COUNT(DISTINCT a.subscriber_id) AS count, SUM(a.count) AS total, a.timestamp, a.link_id, b.link FROM {$wpdb->prefix}mailster_actions AS a LEFT JOIN {$wpdb->prefix}mailster_links AS b ON b.ID = a.link_id WHERE a.campaign_id = %d";

			// if not cached just get from the current user
			if ( ! $cache && $subscriber_id ) {
				$sql .= ' AND a.subscriber_id = ' . (int) $subscriber_id;
			}

			$sql .= ' GROUP BY a.type, a.link_id, a.subscriber_id, a.campaign_id';

			$result = $wpdb->get_results( $wpdb->prepare( $sql, $campaign_id ) );

			foreach ( $result as $row ) {

				if ( ! isset( $actions[ $row->ID ] ) ) {
					$actions[ $row->ID ] = $default;
				}

				// sent
				if ( 1 == $row->type ) {
					$actions[ $row->ID ]['sent']       = (int) $row->timestamp;
					$actions[ $row->ID ]['sent_total'] = (int) $row->total;
				} // opens
				elseif ( 2 == $row->type ) {
						$actions[ $row->ID ]['opens']       = (int) $row->timestamp;
						$actions[ $row->ID ]['opens_total'] = (int) $row->total;
				} // clicks
				elseif ( 3 == $row->type ) {
						$actions[ $row->ID ]['clicks'][ $row->link ] = (int) $row->total;
						$actions[ $row->ID ]['clicks_total']        += (int) $row->total;
				} // unsubs
				elseif ( 4 == $row->type ) {
						$actions[ $row->ID ]['unsubs'] = (int) $row->timestamp;
				} // softbounces
				elseif ( 5 == $row->type ) {
						$actions[ $row->ID ]['softbounces']        = (int) $row->timestamp;
						$actions[ $row->ID ]['softbounces_total'] += (int) $row->total;
				} // bounces
				elseif ( 6 == $row->type ) {
						$actions[ $row->ID ]['bounces'] = (int) $row->timestamp;
				} // error
				elseif ( 7 == $row->type ) {
						$actions[ $row->ID ]['errors']       = floor( $row->timestamp );
						$actions[ $row->ID ]['errors_total'] = floor( $row->total );
				}
			}

			if ( $cache ) {
				mailster_cache_add( 'campaign_actions', $actions );
			}
		}

		if ( is_null( $subscriber_id ) && is_null( $action ) ) {
			return $actions;
		}

		if ( is_null( $action ) ) {
			return isset( $actions[ $subscriber_id ] ) ? $actions[ $subscriber_id ] : $default;
		}

		return isset( $actions[ $subscriber_id ] ) && isset( $actions[ $subscriber_id ][ $action ] ) ? $actions[ $subscriber_id ][ $action ] : false;

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @return unknown
	 */
	public function get_clicked_links( $campaign_id ) {

		global $wpdb;

		if ( false === ( $clicked_links = mailster_cache_get( 'clicked_links_' . $campaign_id ) ) ) {

			if ( $parent_id = get_post_meta( $campaign_id, '_mailster_parent_id', true ) ) {
				$sql = "SELECT c.link, c.i, COUNT(*) AS clicks, SUM(a.count) AS total FROM {$wpdb->prefix}mailster_action_clicks AS a LEFT JOIN {$wpdb->postmeta} AS b ON b.meta_key = '_mailster_parent_id' AND b.post_id = a.campaign_id LEFT JOIN {$wpdb->prefix}mailster_links AS c ON c.ID = a.link_id WHERE (a.campaign_id = %d OR b.meta_value = %d) GROUP BY a.campaign_id, a.link_id ORDER BY c.i ASC, total DESC, clicks DESC";

				$sql = $wpdb->prepare( $sql, $campaign_id, $campaign_id );

			} else {
				$sql = "SELECT c.link, c.i, COUNT(*) AS clicks, SUM(a.count) AS total FROM {$wpdb->prefix}mailster_action_clicks AS a LEFT JOIN {$wpdb->prefix}mailster_links AS c ON c.ID = a.link_id WHERE a.campaign_id = %d GROUP BY a.campaign_id, a.link_id ORDER BY c.i ASC, total DESC, clicks DESC";

				$sql = $wpdb->prepare( $sql, $campaign_id );

			}

			$result = $wpdb->get_results( $sql );

			$clicked_links = array();

			foreach ( $result as $row ) {
				$clicked_links[ $row->link ][ $row->i ] = array(
					'clicks' => $row->clicks,
					'total'  => $row->total,
				);
			}

			mailster_cache_add( 'clicked_links_' . $campaign_id, $clicked_links );

		}

		return $clicked_links;

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @return unknown
	 */
	public function get_clients( $campaign_id ) {

		global $wpdb;

		if ( false === ( $clients = mailster_cache_get( 'clients_' . $campaign_id ) ) ) {

			$sql = "SELECT COUNT(DISTINCT a.subscriber_id) AS count, a.meta_value AS name, b.meta_value AS type, c.meta_value AS version FROM {$wpdb->prefix}mailster_subscriber_meta AS a LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS b ON a.subscriber_id = b.subscriber_id AND a.campaign_id = b.campaign_id LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS c ON a.subscriber_id = c.subscriber_id AND a.campaign_id = c.campaign_id WHERE a.meta_key = 'client' AND b.meta_key = 'clienttype' AND c.meta_key = 'clientversion' AND a.campaign_id = %d GROUP BY a.meta_value, c.meta_value ORDER BY count DESC";

			$result = $wpdb->get_results( $wpdb->prepare( $sql, $campaign_id ) );

			$total = ! empty( $result ) ? array_sum( wp_list_pluck( $result, 'count' ) ) : 0;

			$clients = array();

			foreach ( $result as $row ) {
				$clients[] = array(
					'name'       => $row->name,
					'type'       => $row->type,
					'version'    => $row->version,
					'count'      => $row->count,
					'percentage' => $row->count / $total,
				);
			}

			mailster_cache_add( 'clients_' . $campaign_id, $clients );

		}

		return $clients;

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @return unknown
	 */
	public function get_environment( $campaign_id ) {

		global $wpdb;

		if ( false === ( $environment = mailster_cache_get( 'environment_' . $campaign_id ) ) ) {

			$sql = "SELECT COUNT(DISTINCT a.subscriber_id) AS count, a.meta_value AS type FROM {$wpdb->prefix}mailster_subscriber_meta AS a LEFT JOIN {$wpdb->prefix}mailster_action_opens AS b ON a.subscriber_id = b.subscriber_id AND a.campaign_id = b.campaign_id WHERE a.meta_key = 'clienttype' AND a.campaign_id = %d GROUP BY a.meta_value ORDER BY count DESC";

			$result = $wpdb->get_results( $wpdb->prepare( $sql, $campaign_id ) );

			$total = ! empty( $result ) ? array_sum( wp_list_pluck( $result, 'count' ) ) : 0;

			$environment = array();

			foreach ( $result as $row ) {
				$environment[ $row->type ] = array(
					'count'      => $row->count,
					'percentage' => $row->count / $total,
				);
			}

			mailster_cache_add( 'environment_' . $campaign_id, $environment );

		}

		return $environment;

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @return unknown
	 */
	public function get_error_list( $campaign_id ) {

		global $wpdb;

		if ( false === ( $error_list = mailster_cache_get( 'error_list_' . $campaign_id ) ) ) {

			$sql = "SELECT s.ID, s.email, a.timestamp, a.count, b.meta_value AS errormsg FROM {$wpdb->prefix}mailster_action_errors AS a LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS b ON a.subscriber_id = b.subscriber_id AND a.campaign_id = b.campaign_id LEFT JOIN {$wpdb->prefix}mailster_subscribers AS s ON s.ID = a.subscriber_id WHERE a.campaign_id = %d AND b.meta_key = 'error' ORDER BY a.timestamp DESC";

			$error_list = $wpdb->get_results( $wpdb->prepare( $sql, $campaign_id ) );

			mailster_cache_add( 'error_list_' . $campaign_id, $error_list );

		}

		return $error_list;

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id   (optional)
	 * @param unknown $subscriber_id (optional)
	 * @param unknown $limit         (optional)
	 * @return unknown
	 */
	public function get_activity( $campaign_id = null, $subscriber_id = null, $limit = null ) {

		global $wpdb;

		$sql = 'SELECT';
		if ( ! is_null( $limit ) ) {
			$sql .= ' SQL_CALC_FOUND_ROWS';
		}
		$sql      .= ' p.post_title AS campaign_title, p.post_status AS campaign_status, a.*, l.link FROM';
		$union_sql = '';

		if ( ! is_null( $campaign_id ) ) {
			$union_sql .= ' AND campaign_id = ' . (int) $campaign_id;
		}

		if ( ! is_null( $subscriber_id ) ) {
			$union_sql .= ' AND subscriber_id = ' . (int) $subscriber_id;
		}

		$sql .= ' (' . implode(
			' UNION ',
			array(

				$wpdb->prepare( "SELECT %s AS type, subscriber_id, campaign_id, timestamp, count, NULL AS link_id, text FROM {$wpdb->prefix}mailster_action_errors WHERE 1=1$union_sql", 'error' ),
				$wpdb->prepare( "SELECT %s AS type, subscriber_id, campaign_id, timestamp, count, NULL AS link_id, text FROM {$wpdb->prefix}mailster_action_bounces WHERE hard = 1$union_sql", 'bounce' ),
				$wpdb->prepare( "SELECT %s AS type, subscriber_id, campaign_id, timestamp, count, NULL AS link_id, text FROM {$wpdb->prefix}mailster_action_bounces WHERE hard = 0$union_sql", 'softbounce' ),
				$wpdb->prepare( "SELECT %s AS type, subscriber_id, campaign_id, timestamp, count, NULL AS link_id, text FROM {$wpdb->prefix}mailster_action_unsubs WHERE 1=1$union_sql", 'unsub' ),
				$wpdb->prepare( "SELECT %s AS type, subscriber_id, campaign_id, timestamp, count, link_id, NULL AS text FROM {$wpdb->prefix}mailster_action_clicks WHERE 1=1$union_sql", 'click' ),
				$wpdb->prepare( "SELECT %s AS type, subscriber_id, campaign_id, timestamp, count, NULL AS link_id, NULL AS text FROM {$wpdb->prefix}mailster_action_opens WHERE 1=1$union_sql", 'open' ),
				$wpdb->prepare( "SELECT %s AS type, subscriber_id, campaign_id, timestamp, count, NULL AS link_id, NULL AS text FROM {$wpdb->prefix}mailster_action_sent WHERE 1=1$union_sql", 'sent' ),

			)
		) . ') AS a';

		$sql .= " LEFT JOIN `{$wpdb->posts}` as p ON p.ID = a.campaign_id";
		$sql .= " LEFT JOIN `{$wpdb->prefix}mailster_links` AS l ON l.ID = a.link_id";
		$sql .= ' WHERE 1=1';

		$sql .= ' ORDER BY a.timestamp DESC';

		if ( ! is_null( $limit ) ) {
			$sql .= ' LIMIT ' . (int) $limit;
		}

		return $wpdb->get_results( $sql );

	}


	public function get_list_activity( $list_id = null, $limit = null ) {

		global $wpdb;

		$sql = 'SELECT p.post_title AS campaign_title, a.*, l.link FROM';

		$sql .= ' (' . implode(
			' UNION ',
			array(

				$wpdb->prepare( "SELECT %s AS type, subscriber_id, campaign_id, timestamp, count, NULL AS link_id, NULL AS text FROM {$wpdb->prefix}mailster_action_sent", 'sent' ),
				$wpdb->prepare( "SELECT %s AS type, subscriber_id, campaign_id, timestamp, count, NULL AS link_id, NULL AS text FROM {$wpdb->prefix}mailster_action_opens", 'open' ),
				$wpdb->prepare( "SELECT %s AS type, subscriber_id, campaign_id, timestamp, count, link_id, NULL AS text FROM {$wpdb->prefix}mailster_action_clicks", 'click' ),
				$wpdb->prepare( "SELECT %s AS type, subscriber_id, campaign_id, timestamp, count, NULL AS link_id, text FROM {$wpdb->prefix}mailster_action_unsubs", 'unsub' ),
				$wpdb->prepare( "SELECT %s AS type, subscriber_id, campaign_id, timestamp, count, NULL AS link_id, text FROM {$wpdb->prefix}mailster_action_bounces WHERE hard = 0", 'softbounce' ),
				$wpdb->prepare( "SELECT %s AS type, subscriber_id, campaign_id, timestamp, count, NULL AS link_id, text FROM {$wpdb->prefix}mailster_action_bounces WHERE hard = 1", 'bounce' ),
				$wpdb->prepare( "SELECT %s AS type, subscriber_id, campaign_id, timestamp, count, NULL AS link_id, text FROM {$wpdb->prefix}mailster_action_errors", 'error' ),

			)
		) . ') AS a';

		$sql .= " LEFT JOIN `{$wpdb->posts}` as p ON p.ID = a.campaign_id";
		$sql .= " LEFT JOIN `{$wpdb->prefix}mailster_links` AS l ON l.ID = a.link_id";
		$sql .= " LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS ab ON a.subscriber_id = ab.subscriber_id";
		$sql .= ' WHERE 1';

		if ( ! is_null( $list_id ) ) {
			$sql .= ' AND ab.list_id = ' . (int) $list_id;
		}
		if ( ! is_null( $limit ) ) {
			$sql .= ' LIMIT ' . (int) $limit;
		}

		$sql .= '  GROUP BY a.type, a.link_id';
		$sql .= ' ORDER BY a.timestamp DESC, a.type DESC';

		return $wpdb->get_results( $sql );

	}


	/**
	 *
	 *
	 * @param unknown $link
	 * @param unknown $index (optional)
	 * @return unknown
	 */
	public function get_link_id( $link, $index = 0 ) {

		global $wpdb;

		if ( $id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}mailster_links WHERE `link` = %s AND `i` = %d LIMIT 1", $link, (int) $index ) ) ) {

			return (int) $id;

		} elseif ( $wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}mailster_links (`link`, `i`) VALUES (%s, %d)", $link, $index ) ) ) {

			return (int) $wpdb->insert_id;

		}

		return null;

	}


}
