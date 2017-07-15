<?php

class MailsterSubscriberQuery {

	private $last_result;
	private $last_error;
	private $last_query;

	private $defaults = array(
		'select' => null,
		'status' => null,
		'having' => null,
		'orderby' => null,
		'order' => null,
		'limit' => null,
		'offset' => null,

		'operator' => 'OR',
		'conditions' => null,

		'return_ids' => false,
		'return_count' => false,
		'return_sql' => false,

		'include' => null,
		'exclude' => null,

		'fields' => null,
		'meta' => null,

		'lists' => false,
		'lists__not_in' => null,

		'sent' => null,
		'sent__not_in' => null,
		'sent_since' => null,
		'sent__not_since' => null,

		'open' => null,
		'open__not_in' => null,
		'open_since' => null,
		'open__not_since' => null,

		'click' => null,
		'click__not_in' => null,
		'click_since' => null,
		'click__not_since' => null,
		'click_link' => null,
		'click__not_link' => null,

		'unsubscribe' => null,
		'unsubscribe__not_in' => null,

		'queue' => false,
		'queue__not_in' => false,

		's' => null,
		'search_fields' => false,
		'strict' => false,
		'sentence' => false,
	);

	private static $_instance = null;

	private function __construct( $args = null ) {

		if ( ! is_null( $args ) ) {
			return $this->run( $args );
		}

	}
	public function __destruct() {}

	public static function get_instance( $args = null ) {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __get( $name ) {

		if ( ! isset( $this->$name ) ) {
			$this->{$name} = $this->{'get_' . $name}();
		}

		return $this->{$name};

	}

	public function run( $args = array() ) {

		global $wpdb;

		if ( is_string( $args ) ) {
			$args = str_replace( '+', '%2B', $args );
		}

		$args = wp_parse_args( $args, $this->defaults );

		$name_order = mailster_option( 'name_order' );

		if ( 'all' == $args['fields'] ) {
			$args['fields'] = $this->custom_fields;
			array_unshift( $args['fields'], 'fullname' );
			$args['select'] = array( 'subscribers.*' );
		}
		if ( 'all' == $args['meta'] ) {
			$args['meta'] = $this->meta_fields;
		}

		if ( $args['return_ids'] ) {
			$args['select'] = array( 'subscribers.ID' );
		} elseif ( $args['return_count'] ) {
			$args['select'] = array( 'COUNT(DISTINCT subscribers.ID)' );
		} elseif ( empty( $args['fields'] ) && empty( $args['select'] ) ) {
			$args['select'] = array( 'subscribers.*' );
		} elseif ( is_null( $args['select'] ) ) {
			$args['select'] = array();
		}

		if ( is_null( $args['status'] ) ) {
			if ( ! $args['s'] ) {
				$args['status'] = array( 1 );
			}
		}

		if ( $args['status'] && ! is_array( $args['status'] ) ) {
			$args['status'] = explode( ',', $args['status'] );
		}
		if ( $args['include'] && ! is_array( $args['include'] ) ) {
			$args['include'] = explode( ',', $args['include'] );
		}
			$args['include'] = $this->id_parse( $args['include'] );

		if ( $args['exclude'] && ! is_array( $args['exclude'] ) ) {
			$args['exclude'] = explode( ',', $args['exclude'] );
		}
			$args['exclude'] = $this->id_parse( $args['exclude'] );

		if ( $args['select'] && ! is_array( $args['select'] ) ) {
			$args['select'] = explode( ',', $args['select'] );
		}
		if ( $args['fields'] && ! is_array( $args['fields'] ) ) {
			$args['fields'] = explode( ',', $args['fields'] );
		}
		if ( $args['meta'] && ! is_array( $args['meta'] ) ) {
			$args['meta'] = explode( ',', $args['meta'] );
		}
		if ( 'OR' != $args['operator'] ) {
			$args['operator'] = 'AND' === strtoupper( $args['operator'] ) ? 'AND' : 'OR' ;
		}
		if ( $args['orderby'] && ! is_array( $args['orderby'] ) ) {
			$args['orderby'] = explode( ',', $args['orderby'] );
		}
		if ( $args['order'] && ! is_array( $args['order'] ) ) {
			$args['order'] = explode( ',', $args['order'] );
		}
		if ( $args['queue'] && ! is_array( $args['queue'] ) ) {
			$args['queue'] = explode( ',', $args['queue'] );
		}
		if ( $args['queue__not_in'] && ! is_array( $args['queue__not_in'] ) ) {
			$args['queue__not_in'] = explode( ',', $args['queue__not_in'] );
		}
		if ( $args['lists'] && ! is_array( $args['lists'] ) ) {
			$args['lists'] = explode( ',', $args['lists'] );
		}
		if ( $args['lists__not_in'] && ! is_array( $args['lists__not_in'] ) ) {
			$args['lists__not_in'] = explode( ',', $args['lists__not_in'] );
		}
		if ( $args['sent'] && ! is_array( $args['sent'] ) ) {
			$args['sent'] = explode( ',', $args['sent'] );
		}
		if ( $args['sent__not_in'] && ! is_array( $args['sent__not_in'] ) ) {
			$args['sent__not_in'] = explode( ',', $args['sent__not_in'] );
		}
		if ( $args['open'] && ! is_array( $args['open'] ) ) {
			$args['open'] = explode( ',', $args['open'] );
		}
		if ( $args['open__not_in'] && ! is_array( $args['open__not_in'] ) ) {
			$args['open__not_in'] = explode( ',', $args['open__not_in'] );
		}
		if ( $args['click'] && ! is_array( $args['click'] ) ) {
			$args['click'] = explode( ',', $args['click'] );
		}
		if ( $args['click__not_in'] && ! is_array( $args['click__not_in'] ) ) {
			$args['click__not_in'] = explode( ',', $args['click__not_in'] );
		}
		if ( $args['unsubscribe'] && ! is_array( $args['unsubscribe'] ) ) {
			$args['unsubscribe'] = explode( ',', $args['unsubscribe'] );
		}
		if ( $args['unsubscribe__not_in'] && ! is_array( $args['unsubscribe__not_in'] ) ) {
			$args['unsubscribe__not_in'] = explode( ',', $args['unsubscribe__not_in'] );
		}
		if ( $args['search_fields'] && ! is_array( $args['search_fields'] ) ) {
			$args['search_fields'] = explode( ',', $args['search_fields'] );
		}
		if ( $args['sent_since'] ) {
			$args['sent_since'] = $this->get_timestamp( $args['sent_since'] );
		}
		if ( $args['open_since'] ) {
			$args['open_since'] = $this->get_timestamp( $args['open_since'] );
		}
		if ( $args['click_since'] ) {
			$args['click_since'] = $this->get_timestamp( $args['click_since'] );
		}
		if ( $args['sent__not_since'] ) {
			$args['sent__not_since'] = $this->get_timestamp( $args['sent__not_since'] );
		}
		if ( $args['open__not_since'] ) {
			$args['open__not_since'] = $this->get_timestamp( $args['open__not_since'] );
		}
		if ( $args['click__not_since'] ) {
			$args['click__not_since'] = $this->get_timestamp( $args['click__not_since'] );
		}
		if ( $args['click_link'] && ! is_array( $args['click_link'] ) ) {
			$args['click_link'] = explode( ',', $args['click_link'] );
		}
		if ( $args['click__not_link'] && ! is_array( $args['click__not_link'] ) ) {
			$args['click__not_link'] = explode( ',', $args['click__not_link'] );
		}

		if ( ! $args['return_count'] ) {
			if ( ! empty( $args['fields'] ) ) {
				foreach ( $args['fields'] as $field ) {
					if ( 'fullname' == $field ) {
						$args['fields'][] = 'firstname';
						$args['fields'][] = 'lastname';
						$args['select'][] = ( ! $name_order ? "CONCAT(`field_firstname`.meta_value, ' ', `field_lastname`.meta_value)" : "CONCAT(`field_lastname`.meta_value, ' ', `field_firstname`.meta_value)" ) . ' AS fullname';
					} elseif ( in_array( strtolower( $field ), $this->fields ) ) {
						$args['select'][] = "subscribers.$field";
					} else {
						$args['select'][] = "`field_$field`.meta_value AS `$field`";
					}
				}
				$args['fields'] = array_unique( $args['fields'] );
				// sort($args['fields']);
			}
			if ( ! empty( $args['meta'] ) ) {
				foreach ( $args['meta'] as $field ) {
					if ( 'lat' == $field ) {
						$args['select'][] = "CAST(SUBSTRING_INDEX(`meta_lat`.meta_value, ',', 1) AS DECIMAL(10,2)) AS `lat`";
					} elseif ( 'lng' == $field ) {
						$args['select'][] = "CAST(SUBSTRING_INDEX(`meta_lng`.meta_value, ',', -1) AS DECIMAL(10,2)) AS `lng`";
					} else {
						$args['select'][] = "`meta_$field`.meta_value AS `$field`";
					}
				}
				// sort($args['meta']);
			}
		}

		$cache_key = 'subscriber_query_' . md5( serialize( $args ) );

		if ( $result = mailster_cache_get( $cache_key ) ) {
			return $result;
		}

		$select = 'SELECT ' . implode( ', ', $args['select'] );

		$from = " FROM {$wpdb->prefix}mailster_subscribers AS subscribers";

		$join = '';
		if ( $args['lists'] !== false || $args['lists__not_in'] ) {
			$join .= " LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS lists_subscribers ON subscribers.ID = lists_subscribers.subscriber_id";
			if ( $args['lists__not_in'] && $args['lists__not_in'][0] != -1 ) {
				 $join .= ' AND lists_subscribers.list_id IN (' . implode( ',', array_filter( $args['lists__not_in'], 'is_numeric' ) ) . ')';
			}
		}

		if ( $args['queue'] || $args['queue__not_in'] ) {
			$join .= " LEFT JOIN {$wpdb->prefix}mailster_queue AS queue ON subscribers.ID = queue.subscriber_id";
			if ( $args['sent'] && $args['sent'][0] != -1 ) {
				$join .= ' AND queue.campaign_id IN (' . implode( ',', array_filter( $args['queue'], 'is_numeric' ) ) . ')';
			}
			if ( $args['sent__not_in'] && $args['sent__not_in'][0] != -1 ) {
				$join .= ' AND queue.campaign_id IN (' . implode( ',', array_filter( $args['queue__not_in'], 'is_numeric' ) ) . ')';
			}
		}

		if ( $args['sent'] || $args['sent__not_in'] || $args['sent_since'] || $args['sent__not_since'] ) {
			$join .= " LEFT JOIN {$wpdb->prefix}mailster_actions AS actions_sent ON actions_sent.type = 1 AND subscribers.ID = actions_sent.subscriber_id";
			if ( $args['sent'] && $args['sent'][0] != -1 ) {
				$join .= ' AND actions_sent.campaign_id IN (' . implode( ',', array_filter( $args['sent'], 'is_numeric' ) ) . ')';
			}
			if ( $args['sent__not_in'] && $args['sent__not_in'][0] != -1 ) {
				$join .= ' AND actions_sent.campaign_id IN (' . implode( ',', array_filter( $args['sent__not_in'], 'is_numeric' ) ) . ')';
			}
		}

		if ( $args['open'] || $args['open__not_in'] || $args['open_since'] || $args['open__not_since'] ) {
			$join .= " LEFT JOIN {$wpdb->prefix}mailster_actions AS actions_open ON actions_open.type = 2 AND subscribers.ID = actions_open.subscriber_id";
			if ( $args['open'] && $args['open'][0] != -1 ) {
				$join .= ' AND actions_open.campaign_id IN (' . implode( ',', array_filter( $args['open'], 'is_numeric' ) ) . ')';
			}
			if ( $args['open__not_in'] && $args['open__not_in'][0] != -1 ) {
				$join .= ' AND actions_open.campaign_id IN (' . implode( ',', array_filter( $args['open__not_in'], 'is_numeric' ) ) . ')';
			}
		}

		if ( $args['click'] || $args['click__not_in'] || $args['click_since'] || $args['click__not_since'] || $args['click_link'] || $args['click__not_link'] ) {
			$join .= " LEFT JOIN {$wpdb->prefix}mailster_actions AS actions_click ON actions_click.type = 3 AND subscribers.ID = actions_click.subscriber_id";
			if ( $args['click'] && $args['click'][0] != -1 ) {
				$join .= ' AND actions_click.campaign_id IN (' . implode( ',', array_filter( $args['click'], 'is_numeric' ) ) . ')';
			}
			if ( $args['click__not_in'] && $args['click__not_in'][0] != -1 ) {
				$join .= ' AND actions_click.campaign_id IN (' . implode( ',', array_filter( $args['click__not_in'], 'is_numeric' ) ) . ')';
			}
			if ( $args['click_link'] || $args['click__not_link'] ) {
				$join .= " LEFT JOIN {$wpdb->prefix}mailster_links AS links ON actions_click.link_id = links.ID";
			}
		}

		if ( $args['unsubscribe'] || $args['unsubscribe__not_in'] ) {
			$join .= " LEFT JOIN {$wpdb->prefix}mailster_actions AS actions_unsubscribe ON actions_unsubscribe.type = 4 AND subscribers.ID = actions_unsubscribe.subscriber_id";
			if ( $args['unsubscribe'] && $args['unsubscribe'][0] != -1 ) {
				$join .= ' AND actions_unsubscribe.campaign_id IN (' . implode( ',', array_filter( $args['unsubscribe'], 'is_numeric' ) ) . ')';
			}
			if ( $args['unsubscribe__not_in'] && $args['unsubscribe__not_in'][0] != -1 ) {
				$join .= ' AND actions_unsubscribe.campaign_id IN (' . implode( ',', array_filter( $args['unsubscribe__not_in'], 'is_numeric' ) ) . ')';
			}
		}

		$joins = array();

		$meta_and_fields = wp_parse_args( $args['fields'], $args['meta'] );

		if ( $args['s'] ) {
			$search_fields = $args['search_fields'] ? $args['search_fields'] : array_merge( array( 'email', 'hash', 'fullname' ), $this->custom_fields );
			$meta_and_fields = array_merge( $search_fields, $meta_and_fields );
		} else {
		}

		if ( in_array( 'fullname', $meta_and_fields ) ) {
			$meta_and_fields[] = 'firstname';
			$meta_and_fields[] = 'lastname';
		}

		if ( ! empty( $meta_and_fields ) ) {

			foreach ( $meta_and_fields as $field ) {

				$field = esc_sql( $field );

				if ( in_array( $field, array( 'lat', 'lng' ) ) ) {

					$joins[] = "LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS `meta_$field` ON `meta_$field`.subscriber_id = subscribers.ID AND `meta_$field`.meta_key = 'coords'";

				} elseif ( in_array( $field, $this->custom_fields ) ) {

					$joins[] = "LEFT JOIN {$wpdb->prefix}mailster_subscriber_fields AS `field_$field` ON `field_$field`.subscriber_id = subscribers.ID AND `field_$field`.meta_key = '$field'";

				} elseif ( in_array( $field, $this->wp_user_meta ) ) {
					$joins[] = "LEFT JOIN {$wpdb->usermeta} AS `meta_wp_$field` ON `meta_wp_$field`.user_id = subscribers.wp_id AND `meta_wp_$field`.meta_key = '" . str_replace( 'wp_', $wpdb->prefix, $field ) . "'";

				} elseif ( in_array( $field, $this->meta_fields ) ) {

					$joins[] = "LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS `meta_$field` ON `meta_$field`.subscriber_id = subscribers.ID AND `meta_$field`.meta_key = '$field'";
				}
			}
		}

		if ( $args['conditions'] ) {

			if ( ! isset( $args['conditions'][0]['conditions'] ) ) {
				$args['conditions'] = array( array( 'operator' => $args['operator'], 'conditions' => $args['conditions'] ) );
			}

			foreach ( $args['conditions'] as $i => $conditions ) {

				foreach ( $conditions['conditions'] as $options ) {

					$field = esc_sql( $options['field'] );

					if ( in_array( $field, array( 'lat', 'lng' ) ) ) {

						$joins[] = "LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS `meta_coords` ON `meta_coords`.subscriber_id = subscribers.ID AND `meta_coords`.meta_key = 'coords'";

					} elseif ( in_array( $field, $this->custom_fields ) ) {

						$joins[] = "LEFT JOIN {$wpdb->prefix}mailster_subscriber_fields AS `field_$field` ON `field_$field`.subscriber_id = subscribers.ID AND `field_$field`.meta_key = '$field'";

					} elseif ( in_array( $field, $this->wp_user_meta ) ) {
						$joins[] = "LEFT JOIN {$wpdb->usermeta} AS `meta_wp_$field` ON `meta_wp_$field`.user_id = subscribers.wp_id AND `meta_wp_$field`.meta_key = '" . str_replace( 'wp_', $wpdb->prefix, $field ) . "'";

					} elseif ( in_array( $field, $this->meta_fields ) ) {

						$joins[] = "LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS `meta_$field` ON `meta_$field`.subscriber_id = subscribers.ID AND `meta_$field`.meta_key = '$field'";

					}
				}
			}
		}

		if ( ! empty( $joins ) ) {
			$join .= ' ' . implode( "\n", array_unique( $joins ) );
		}

		$where = ' WHERE 1=1';
		if ( $args['lists'] !== false ) {
			// unassigned members if NULL
			if ( is_array( $args['lists'] ) ) {
				$args['lists'] = array_filter( $args['lists'], 'is_numeric' );
			}

			$where .= ( is_null( $args['lists'] ) ) ? ' AND lists_subscribers.list_id IS NULL' : ( empty( $args['lists'] ) ? ' AND lists_subscribers.list_id = 0' : ' AND lists_subscribers.list_id IN(' . implode( ',', $args['lists'] ) . ')' );
		}

		if ( $args['status'] ) {
			$where .= ' AND subscribers.status IN (' . implode( ',', array_filter( $args['status'], 'is_numeric' ) ) . ')';
		}

		if ( $args['include'] ) {
			$where .= ' AND subscribers.ID IN (' . implode( ',', array_filter( $args['include'], 'is_numeric' ) ) . ')';
		}

		if ( $args['exclude'] ) {
			$where .= ' AND subscribers.ID NOT IN (' . implode( ',', array_filter( $args['exclude'], 'is_numeric' ) ) . ')';
		}

		if ( $args['lists__not_in'] ) {
			$where .= ' AND lists_subscribers.list_id IS NULL';
		}

		if ( $args['sent'] ) {
			$where .= ' AND actions_sent.subscriber_id IS NOT NULL';
		}

		if ( $args['sent__not_in'] ) {
			$where .= ' AND actions_sent.subscriber_id IS NULL';
		}

		if ( $args['sent_since'] ) {
			$where .= ' AND actions_sent.timestamp <= ' . $args['sent_since'];
		}

		if ( $args['sent__not_since'] ) {
			$where .= ' AND actions_sent.timestamp >= ' . $args['sent__not_since'];
		}

		if ( $args['open'] ) {
			$where .= ' AND actions_open.subscriber_id IS NOT NULL';
		}

		if ( $args['open__not_in'] ) {
			$where .= ' AND actions_open.subscriber_id IS NULL';
		}

		if ( $args['open_since'] ) {
			$where .= ' AND actions_open.timestamp <= ' . $args['open_since'];
		}

		if ( $args['open__not_since'] ) {
			$where .= ' AND actions_open.timestamp >= ' . $args['open__not_since'];
		}

		if ( $args['click'] ) {
			$where .= ' AND actions_click.subscriber_id IS NOT NULL';
		}

		if ( $args['click__not_in'] ) {
			$where .= ' AND actions_click.subscriber_id IS NULL';
		}

		if ( $args['click_since'] ) {
			$where .= ' AND actions_click.timestamp <= ' . $args['click_since'];
		}

		if ( $args['click__not_since'] ) {
			$where .= ' AND actions_click.timestamp >= ' . $args['click__not_since'];
		}

		if ( $args['click_link'] ) {
			$where .= ' AND links.link IN ( \'' . implode( '\' , \'', $args['click_link'] ) . '\' )';
		}

		if ( $args['click__not_link'] ) {
			$where .= ' AND links.link NOT IN ( \'' . implode( '\' , \'', $args['click__not_link'] ) . '\' )';
		}

		if ( $args['unsubscribe'] ) {
			$where .= ' AND actions_unsubscribe.subscriber_id IS NOT NULL';
		}

		if ( $args['unsubscribe__not_in'] ) {
			$where .= ' AND actions_unsubscribe.subscriber_id IS NULL';
		}

		if ( $args['queue'] ) {
			$where .= ' AND queue.subscriber_id IS NOT NULL';
		}

		if ( $args['queue__not_in'] ) {
			$where .= ' AND queue.subscriber_id IS NULL';
		}

		if ( $args['conditions'] ) {

			$cond = array();
			$operator = $args['operator'];

			foreach ( $args['conditions'] as $i => $conditions ) {

				$sub_cond = array();
				$sub_operator = $conditions['operator'];

				foreach ( $conditions['conditions'] as $options ) {

					$sub_cond[] = $this->get_condition( $options['field'], $options['operator'], $options['value'] );

				}

				$cond[] = '(' . implode( ' ' . $sub_operator . ' ', $sub_cond ) . ')';

			}

			if ( ! empty( $cond ) ) {
				$where .= ' AND ( ' . implode( ' ' . $operator . ' ', $cond ) . ' )';
			}
		}

		if ( $args['s'] ) {

			$raw_search = addcslashes( trim( $args['s'] ), '%[]_' );
			$search_terms = array();
			$search_orders = array();
			$not_search_terms = array();
			$wildcard = $args['strict'] ? '' : '%';

			if ( $args['sentence'] ) {

				$search_terms = array( $raw_search );

			} else {

				if ( preg_match_all( '/("|\')(.+?)(\1)/', $raw_search, $quotes ) ) {
					$search_terms = array_merge( $search_terms, $quotes[2] );
					$raw_search = trim( str_replace( $quotes[0], '', $raw_search ) );
				}

				$search_terms = array_merge( $search_terms, explode( ' ', $raw_search ) );
				$search_terms = array_filter( $search_terms );

				$search_terms = str_replace( array( '*', '?' ), array( '%', '_' ), $search_terms );

				$not_search_terms = array_values( preg_grep( '/^-/', $search_terms ) );
				$search_terms = array_values( array_diff( $search_terms, $not_search_terms ) );
				$not_search_terms = preg_replace( '/^-/', '', $not_search_terms );
			}

			if ( ! empty( $search_terms ) ) {

				$search_terms = array_map( 'trim', $search_terms );
				$concated_search_terms = implode( ' ', $search_terms );

				foreach ( $search_terms as $i => $term ) {

					$searches = array();
					if ( empty( $term ) ) {
						continue;
					}

					$operator = 'OR';
					if ( ! $i || strpos( $term, '+' ) === 0 ) {
						$term = ltrim( $term, '+' );
						$operator = 'AND';
					}

					foreach ( $search_fields as $search_field ) {

						if ( 'fullname' == $search_field ) {
							if ( ! $name_order ) {
								$searches[] = "(CONCAT(`field_firstname`.meta_value, ' ', `field_lastname`.meta_value) LIKE '$wildcard$term$wildcard')";
							} else {
								$searches[] = "(CONCAT(`field_lastname`.meta_value, ' ', `field_firstname`.meta_value) LIKE '$wildcard$term$wildcard')";
							}
						} elseif ( in_array( $search_field, $this->custom_fields ) ) {

							$searches[] = "(`field_$search_field`.meta_value LIKE '$wildcard$term$wildcard')";

						} elseif ( in_array( $search_field, $this->wp_user_meta ) ) {

							$searches[] = "(`meta_wp_$search_field`.meta_value LIKE '$wildcard$term$wildcard')";

						} elseif ( in_array( $search_field, $this->meta_fields ) ) {

							$searches[] = "(`meta_$search_field`.meta_value LIKE '$wildcard$term$wildcard')";

						} else {

							$searches[] = "(subscribers.$search_field LIKE '$wildcard$term$wildcard')";

						}
					}

					$where .= " $operator ( " . implode( "\n" . ' OR ', $searches ) . ' )';
				}

				foreach ( $search_fields as $search_field ) {

					if ( 'fullname' == $search_field ) {
						if ( ! $name_order ) {
							$search_orders[] = "WHEN (CONCAT(`field_firstname`.meta_value, ' ', `field_lastname`.meta_value) LIKE '%$concated_search_terms%') THEN 1";
						} else {
							$search_orders[] = "WHEN (CONCAT(`field_lastname`.meta_value, ' ', `field_firstname`.meta_value) LIKE '%$concated_search_terms%') THEN 1";
						}
					} elseif ( in_array( $search_field, $this->custom_fields ) ) {

						$search_orders[] = "WHEN (`field_$search_field`.meta_value LIKE '%$concated_search_terms%') THEN 3";

					} elseif ( in_array( $search_field, $this->wp_user_meta ) ) {

						$search_orders[] = "WHEN (`meta_wp_$search_field`.meta_value LIKE '%$concated_search_terms%') THEN 4";

					} elseif ( in_array( $search_field, $this->meta_fields ) ) {

						$search_orders[] = "WHEN (`meta_$search_field`.meta_value LIKE '%$concated_search_terms%') THEN 5";

					} else {

						$search_orders[] = "WHEN (subscribers.$search_field LIKE '%$concated_search_terms%') THEN 2";

					}
				}
			}

			if ( ! empty( $not_search_terms ) ) {

				$not_search_terms = array_map( 'trim', $not_search_terms );
				$searches = array();

				foreach ( $not_search_terms as $i => $term ) {
					if ( empty( $term ) ) {
						continue;
					}

					foreach ( $search_fields as $search_field ) {

						if ( 'fullname' == $search_field ) {
							if ( ! $name_order ) {
								$searches[] = "(CONCAT(`field_firstname`.meta_value, ' ', `field_lastname`.meta_value) NOT LIKE '$wildcard$term$wildcard' OR CONCAT(`field_firstname`.meta_value, ' ', `field_lastname`.meta_value) IS NULL)";
							} else {
								$searches[] = "(CONCAT(`field_lastname`.meta_value, ' ', `field_firstname`.meta_value) NOT LIKE '$wildcard$term$wildcard' OR CONCAT(`field_lastname`.meta_value, ' ', `field_firstname`.meta_value) IS NULL)";
							}
						} elseif ( in_array( $search_field, $this->custom_fields ) ) {

							$searches[] = "(`field_$search_field`.meta_value NOT LIKE '$wildcard$term$wildcard' OR `field_$search_field`.meta_value IS NULL)";

						} elseif ( in_array( $search_field, $this->wp_user_meta ) ) {

							$searches[] = "(`meta_wp_$search_field`.meta_value NOT LIKE '$wildcard$term$wildcard' OR `meta_wp_$search_field`.meta_value IS NULL)";

						} elseif ( in_array( $search_field, $this->meta_fields ) ) {

							$searches[] = "(`meta_$search_field`.meta_value NOT LIKE '$wildcard$term$wildcard' OR `meta_$search_field`.meta_value IS NULL)";

						} else {

							$searches[] = "(subscribers.$search_field NOT LIKE '$wildcard$term$wildcard')";

						}
					}
				}

				$where .= ' AND ( ' . implode( "\n" . ' AND ', $searches ) . ' )';

			}
		}

		$group = '';
		if ( ! $args['return_count'] ) {
			$group .= ' GROUP BY subscribers.ID';
		}

		$having = '';
		if ( $args['having'] ) {
			$having .= ' HAVING ' . esc_sql( $args['having'] );
		}

		$order = '';
		if ( $args['orderby'] && ! $args['return_count'] ) {
			$order .= ' ORDER BY';

			$ordering = isset( $args['order'][0] ) ? strtoupper( $args['order'][0] ) : 'ASC';
			$orders = array();
			if ( ! empty( $search_orders ) ) {
				$orders[] = '(CASE ' . implode( ' ', $search_orders ) . ' ELSE 10 END)';
			}
			foreach ( $args['orderby'] as $i => $orderby ) {
				$ordering = isset( $args['order'][ $i ] ) ? strtoupper( $args['order'][ $i ] ) : $ordering;
				if ( in_array( $orderby, $this->custom_fields ) ) {

					$orders[] = " `field_$orderby`.meta_value $ordering";

				} elseif ( in_array( $orderby, $this->wp_user_meta ) ) {

					$orders[] = " `meta_wp_$orderby`.meta_value $ordering";

				} elseif ( in_array( $orderby, $this->meta_fields ) ) {

					$orders[] = " `meta_$orderby`.meta_value $ordering";

				} elseif ( in_array( $orderby, $this->fields ) ) {

					$orders[] = " subscribers.$orderby $ordering";
				} else {

					$orders[] = " $orderby $ordering";
				}
			}
			$order .= implode( ',', $orders );
		}

		$limit = '';
		if ( $args['limit'] ) {
			$limit .= ' LIMIT ' . intval( $args['offset'] ) . ', ' . intval( $args['limit'] );
		}

		$sql = $select . "\n" . $from . "\n" . $join . "\n" . $where . "\n" . $group . "\n" . $having . "\n" . $order . "\n" . $limit;

		// legacy filter
		$sql = apply_filters( 'mailster_campaign_get_subscribers_by_list_sql', $sql );

		if ( $args['return_sql'] ) {
			$result = $this->last_query = $sql;
			$this->last_error = null;
			$this->last_result = null;
		} else {
			if ( $args['return_ids'] ) {
				$result = $wpdb->get_col( $sql );
			} elseif ( $args['return_count'] ) {
				$result = $wpdb->get_var( $sql );
			} else {
				$result = $this->cast( $wpdb->get_results( $sql ) );
			}
			$this->last_query = $wpdb->last_query;
			$this->last_error = $wpdb->last_error;
			$this->last_result = $result;
		}

		if ( $this->last_error && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( $this->last_error );
		}

		global $pagenow;
		if ( in_array( $pagenow, array( 'admin-ajax.php', 'tools.php' ) ) ) {
			echo '<pre>' . print_r( $sql, true ) . '</pre>';
		}

		echo $this->last_error;

		mailster_cache_set( $cache_key, $result );

		return $result;

	}


	private function cast( $result ) {

		$className = 'MailsterSubscriber';
		$return = array();

		foreach ( $result as $key => $value ) {
			$return[] = unserialize(sprintf(
				'O:%d:"%s"%s',
				strlen( $className ),
				$className,
				strstr( strstr( serialize( $value ), '"' ), ':' )
			));
		}

		return $return;

	}


	private function get_condition( $field, $operator, $value ) {

		// sanitation
		$field = esc_sql( $field );
		$value = esc_sql( stripslashes( $value ) );
		$operator = $this->get_field_operator( $operator );

		$is_empty = '' == $value;
		$extra = '';
		$positive = false;
		$f = false;

		// data sanitation
		switch ( $field ) {
			case 'rating':
				$value = str_replace( ',', '.', $value );
				if ( strpos( $value, '%' ) !== false || $value > 5 ) {
					$value = floatval( $value ) / 100;
				} elseif ( $value >= 1 ) {
					$value = floatval( $value ) * 0.2;
				}
				break;
			case 'lat':
				$f = "CAST(SUBSTRING_INDEX(`meta_coords`.meta_value, ',', 1) AS DECIMAL(10,2))";
				break;
			case 'lng':
				$f = "CAST(SUBSTRING_INDEX(`meta_coords`.meta_value, ',', -1) AS DECIMAL(10,2))";
				break;
			case 'geo':
				if ( '=' == $operator ) {
					return " (`meta_$field`.meta_value LIKE '$value|%' OR `meta_$field`.meta_value LIKE '%|$value') ";
				}
				if ( '!=' == $operator ) {
					return " (`meta_$field`.meta_value NOT LIKE '$value|%' AND `meta_$field`.meta_value NOT LIKE '%|$value') ";
				}
		}

		switch ( $operator ) {
			case '=':
			case 'is':
				$positive = true;
			case '!=':
			case 'is_not':

				if ( $f ) {
				} elseif ( in_array( $field, $this->custom_date_fields ) ) {
					$f = "STR_TO_DATE(`field_$field`.meta_value,'%Y-%m-%d')";
				} elseif ( in_array( $field, $this->time_fields ) ) {
					// $f = "STR_TO_DATE(FROM_UNIXTIME(subscribers.$field),'%Y-%m-%d')";
					$f = "subscribers.$field";
					$value = $this->get_timestamp( $value );
				} elseif ( in_array( $field, $this->custom_fields ) ) {
					$f = "`field_$field`.meta_value";
				} elseif ( in_array( $field, $this->meta_fields ) ) {
					$f = "`meta_$field`.meta_value";
				} elseif ( in_array( $field, $this->wp_user_meta ) ) {
					$f = "`meta_wp_$field`.meta_value";
					if ( $field == 'wp_capabilities' ) {
						$value = 's:' . strlen( $value ) . ':"' . strtolower( $value ) . '";';
						return "`meta_wp_$field`.meta_value " . ( in_array( $operator, array( 'is', '=' ) ) ? 'LIKE' : 'NOT LIKE' ) . " '%$value%'";
						break;
					}
				} else {
					$f = "subscribers.$field";
				}

				$c = $f . ' ' . ( $positive ? '=' : '!=' ) . " '$value'";
				if ( $is_empty && $positive ) {
					$c .= ' OR ' . $f . ' IS NULL';
				}

				return $c;
				break;

			case '<>':
			case 'contains':
				$positive = true;
			case '!<>':
			case 'contains_not':
				if ( $field == 'wp_capabilities' ) {
					$value = "'a:%" . strtolower( $value ) . "%'";
				} else {
					$value = "'%$value%'";
				}
				if ( $f ) {
				} elseif ( in_array( $field, $this->custom_fields ) ) {
					$f = "`field_$field`.meta_value";
				} elseif ( in_array( $field, $this->meta_fields ) ) {
					$f = "`meta_$field`.meta_value";
				} elseif ( in_array( $field, $this->wp_user_meta ) ) {
					$f = "`meta_wp_$field`.meta_value";
				} else {
					$f = "subscribers.$field";
				}

				$c = $f . ' ' . ( $positive ? 'LIKE' : 'NOT LIKE' ) . " $value";
				if ( $is_empty && $positive ) {
					$c .= ' OR ' . $f . ' IS NULL';
				}

				return $c;
				break;

			case '^':
			case 'begin_with':
				if ( $field == 'wp_capabilities' ) {
					$value = "'%\"" . strtolower( $value ) . "%'";
				} else {
					$value = "'$value%'";
				}
				if ( $f ) {
				} elseif ( in_array( $field, $this->custom_fields ) ) {
					$f = "`field_$field`.meta_value";
				} elseif ( in_array( $field, $this->meta_fields ) ) {
					$f = "`meta_$field`.meta_value";
				} elseif ( in_array( $field, $this->wp_user_meta ) ) {
					$f = "`meta_wp_$field`.meta_value";
				} else {
					$f = "subscribers.$field";
				}

				$c = $f . " LIKE $value";

				return $c;
				break;

			case '$':
			case 'end_with':
				if ( $field == 'wp_capabilities' ) {
					$value = "'%" . strtolower( $value ) . "\"%'";
				} else {
					$value = "'%$value'";
				}

				if ( $f ) {
				} elseif ( in_array( $field, $this->custom_fields ) ) {
					$f = "`field_$field`.meta_value";
				} elseif ( in_array( $field, $this->meta_fields ) ) {
					$f = "`meta_$field`.meta_value";
				} elseif ( in_array( $field, $this->wp_user_meta ) ) {
					$f = "`meta_wp_$field`.meta_value";
				} else {
					$f = "subscribers.$field";
				}

				$c = $f . " LIKE $value";

				return $c;
				break;

			case '>=':
			case 'is_greater_equal':
			case '<=':
			case 'is_smaller_equal':
				$extra = '=';
			case '>':
			case 'is_greater':
			case '<':
			case 'is_smaller':

				if ( $f ) {
				} elseif ( in_array( $field, $this->custom_date_fields ) ) {
					$f = "STR_TO_DATE(`field_$field`.meta_value,'%Y-%m-%d')";
					$value = "'$value'";
				} elseif ( in_array( $field, $this->time_fields ) ) {
					// $f = "STR_TO_DATE(FROM_UNIXTIME(subscribers.$field),'%Y-%m-%d')";
					$f = "subscribers.$field";
					// $value = "'$value'";
					$value = $this->get_timestamp( $value );
				} elseif ( in_array( $field, $this->custom_fields ) ) {
					$f = "`field_$field`.meta_value";
					$value = is_numeric( $value ) ? floatval( $value ) : "'$value'";
				} elseif ( in_array( $field, $this->meta_fields ) ) {
					$f = "`meta_$field`.meta_value";
					$value = is_numeric( $value ) ? floatval( $value ) : "'$value'";
				} elseif ( in_array( $field, $this->wp_user_meta ) ) {
					$f = "`meta_wp_$field`.meta_value";
					if ( $field == 'wp_capabilities' ) {
						$value = "'NOTPOSSIBLE'";
					}
				} else {
					$f = "subscribers.$field";
					$value = floatval( $value );
				}

				$c = $f . ' ' . ( in_array( $operator, array( 'is_greater', 'is_greater_equal', '>', '>=' ) ) ? '>' . $extra : '<' . $extra ) . " $value";

				return $c;
				break;

			case '%':
			case 'pattern':
				$positive = true;
			case '!%':
			case 'not_pattern':

				if ( $f ) {
				} elseif ( in_array( $field, $this->custom_date_fields ) ) {
					$f = "STR_TO_DATE(`field_$field`.meta_value,'%Y-%m-%d')";
				} elseif ( in_array( $field, $this->time_fields ) ) {
					// $f = "STR_TO_DATE(FROM_UNIXTIME(subscribers.$field),'%Y-%m-%d')";
					$f = "subscribers.$field";
					$value = $this->get_timestamp( $value );
				} elseif ( in_array( $field, $this->custom_fields ) ) {
					$f = "`field_$field`.meta_value";
				} elseif ( in_array( $field, $this->meta_fields ) ) {
					$f = "`meta_$field`.meta_value";
				} elseif ( in_array( $field, $this->wp_user_meta ) ) {
					$f = "`meta_wp_$field`.meta_value";
				} else {
					$f = "subscribers.$field";
					if ( $field == 'wp_capabilities' ) {
						$value = "'NOTPOSSIBLE'";
						break;
					}
				}
				if ( $is_empty ) {
					$value = '.';
				}

				$is_empty = '.' == $value;

				if ( ! $positive ) {
					$extra = 'NOT ';
				}

				$c = $f . ' ' . $extra . "REGEXP '$value'";
				if ( $is_empty && $positive ) {
					$c .= ' OR ' . $f . ' IS NULL';
				}

				return $c;
				break;

		}

	}

	private function get_field_operator( $operator ) {
		$operator = esc_sql( stripslashes( $operator ) );

		switch ( $operator ) {
			case '=':
				return 'is';
			case '!=':
				return 'is_not';
			case '<>':
				return 'contains';
			case '!<>':
				return 'contains_not';
			case '^':
				return 'begin_with';
			case '$':
				return 'end_with';
			case '>=':
				return 'is_greater_equal';
			case '<=':
				return 'is_smaller_equal';
			case '>':
				return 'is_greater';
			case '<':
				return 'is_smaller';
			case '%':
				return 'pattern';
			case '!%':
				return 'not_pattern';
		}

		return $operator;

	}

	private function get_custom_fields() {
		$custom_fields = mailster()->get_custom_fields( true );
		$custom_fields = wp_parse_args( array( 'firstname', 'lastname' ), (array) $custom_fields );

		return $custom_fields;
	}

	private function get_custom_date_fields() {
		$custom_date_fields = mailster()->get_custom_date_fields( true );

		return $custom_date_fields;
	}

	private function get_fields() {
		$fields = array( 'id', 'hash', 'email', 'wp_id', 'status', 'added', 'updated', 'signup', 'confirm', 'ip_signup', 'ip_confirm', 'rating' );

		return $fields;
	}

	private function get_time_fields() {
		$time_fields = array( 'added', 'updated', 'signup', 'confirm' );

		return $time_fields;
	}

	private function get_meta_fields() {
		$meta_fields = array( 'form', 'referer', 'client', 'clienttype', 'coords', 'geo', 'lang', 'timeoffset', 'lat', 'lng' );

		return $meta_fields;
	}

	private function get_wp_user_meta() {
		$wp_user_meta = wp_parse_args( array( 'wp_user_level', 'wp_capabilities' ), mailster( 'helper' )->get_wpuser_meta_fields() );
		// removing custom fields from wp user meta to prevent conflicts
		$wp_user_meta = array_diff( $wp_user_meta, array_merge( array( 'email' ), $this->custom_fields ) );

		return $wp_user_meta;
	}

	private function get_timestamp( $value, $format = null ) {
		$timestamp = is_numeric( $value ) ? strtotime( '@' . $value ) : strtotime( '' . $value );
		if ( false !== $timestamp ) {
		} elseif ( is_numeric( $value ) ) {
			$timestamp = (int) $value;
		} else {
			return false;
		}

		if ( is_null( $format ) ) {
			return $timestamp;
		}

		return date( $format, $timestamp );
	}

	private function id_parse( $ids ) {

		if ( empty( $ids ) ) {
			return $ids;
		}

		$return = array();
		foreach ( $ids as $id ) {
			if ( false !== strpos( $id, '-' ) ) {
				$splitted = explode( '-', $id );
				$min = min( $splitted );
				$max = max( $splitted );
				for ( $i = $min; $i <= $max; $i++ ) {
					$return[] = $i;
				}
			} else {
				$return[] = $id;
			}
		}

		return array_values( array_unique( $return ) );

	}


}

/**
 *
 */
class MailsterSubscriber {

	function __construct() {
	}
}
