<?php

class MailsterSubscriberQuery {


	public function __construct( $args = null ) {

		if ( ! is_null( $args ) ) {
			return $this->query( $args );
		}

	}

	public function query( $args = array() ) {

		global $wpdb;

		$defaults = array(
			'select' => null,
			'conditions' => null,
			'status' => null,
			'return_ids' => false,
			'return_count' => false,
			'ignore_sent' => false,
			'ignore_queue' => false,
			'limit' => null,
			'offset' => null,
			'orderby' => null,
			'order' => null,
			'return_sql' => false,
			'lists' => false,
			'lists__not_in' => null,
			'fields' => null,
			'meta' => null,
			'sent' => null,
			'sent__not_in' => null,
			'open' => null,
			'open__not_in' => null,
			'click' => null,
			'click__not_in' => null,
		);

		$args = wp_parse_args( $args, $defaults );

		$custom_fields = mailster()->get_custom_fields( true );
		$custom_fields = wp_parse_args( array( 'firstname', 'lastname' ), (array) $custom_fields );
		$custom_date_fields = mailster()->get_custom_date_fields( true );
		$timefields = array( 'added', 'updated', 'signup', 'confirm' );

		$meta_fields = array( 'form', 'referer', 'client', 'clienttype', 'coords', 'geo', 'lang', 'timeoffset' );

		$wp_user_meta = wp_parse_args( array( 'wp_user_level', 'wp_capabilities' ), mailster( 'helper' )->get_wpuser_meta_fields() );
		// removing custom fields from wp user meta to prevent conflicts
		$wp_user_meta = array_diff( $wp_user_meta, array_merge( array( 'email' ), $custom_fields ) );

		if ( 'all' == $args['fields'] ) {
			$args['fields'] = $custom_fields;
		}
		if ( 'all' == $args['meta'] ) {
			$args['meta'] = $meta_fields;
		}

		if ( $args['return_ids'] ) {
			$args['select'] = array( 'subscribers.ID' );
		} elseif ( $args['return_count'] ) {
			$args['select'] = array( 'COUNT(DISTINCT subscribers.ID)' );
		} elseif ( is_null( $args['select'] ) ) {
			$args['select'] = array( 'subscribers.*' );
		}

		if ( is_null( $args['status'] ) ) {
			$args['status'] = array( 1 );
		}

		if ( $args['select'] && ! is_array( $args['select'] ) ) {
			$args['select'] = explode( ',', $args['select'] );
		}
		if ( $args['fields'] && ! is_array( $args['fields'] ) ) {
			$args['fields'] = explode( ',', $args['fields'] );
		}
		if ( $args['meta'] && ! is_array( $args['meta'] ) ) {
			$args['meta'] = explode( ',', $args['meta'] );
		}
		if ( $args['orderby'] && ! is_array( $args['orderby'] ) ) {
			$args['orderby'] = explode( ',', $args['orderby'] );
		}
		if ( $args['order'] && ! is_array( $args['order'] ) ) {
			$args['order'] = explode( ',', $args['order'] );
		}
		if ( $args['ignore_sent'] && ! is_array( $args['ignore_sent'] ) ) {
			$args['ignore_sent'] = explode( ',', $args['ignore_sent'] );
		}
		if ( $args['ignore_queue'] && ! is_array( $args['ignore_queue'] ) ) {
			$args['ignore_queue'] = explode( ',', $args['ignore_queue'] );
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

		if ( ! empty( $args['fields'] ) ) {
			foreach ( $args['fields'] as $field ) {
				if ( 'fullname' == $field ) {
					$args['fields'][] = 'firstname';
					$args['fields'][] = 'lastname';
					$args['select'][] = (mailster_option( 'name_order' ) ? "CONCAT(`field_firstname`.meta_value, ' ', `field_lastname`.meta_value)" : "CONCAT(`field_lastname`.meta_value, ' ', `field_firstname`.meta_value)" ) . ' AS fullname';
				} else {
					$args['select'][] = "`field_$field`.meta_value AS `$field`";
				}
			}
		}

		if ( ! empty( $args['meta'] ) ) {
			foreach ( $args['meta'] as $field ) {
				$args['select'][] = "`meta_$field`.meta_value AS `$field`";
			}
		}

		$select = 'SELECT ' . implode( ', ', $args['select'] );

		$from = " FROM {$wpdb->prefix}mailster_subscribers AS subscribers";

		$join = '';
		if ( $args['lists'] !== false || $args['lists__not_in'] ) {
			$join .= " LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS lists_subscribers ON subscribers.ID = lists_subscribers.subscriber_id";
			if ( $args['lists__not_in'] ) {
				 $join .= ' AND lists_subscribers.list_id IN (' . implode( ',', array_filter( $args['lists__not_in'], 'is_numeric' ) ) . ')';
			}
		}

		if ( $args['ignore_sent'] ) {
			$join .= " LEFT JOIN {$wpdb->prefix}mailster_actions AS actions_sent ON subscribers.ID = actions_sent.subscriber_id AND actions_sent.campaign_id IN (" . implode( ',', array_filter( $args['ignore_sent'], 'is_numeric' ) ) . ') AND actions_sent.type = 1';
		}

		if ( $args['ignore_queue'] ) {
			$join .= " LEFT JOIN {$wpdb->prefix}mailster_queue AS queue ON subscribers.ID = queue.subscriber_id AND queue.campaign_id IN (" . implode( ',', array_filter( $args['ignore_queue'], 'is_numeric' ) ) . ')';
		}

		if ( $args['sent'] || $args['sent__not_in'] ) {
			$join .= " LEFT JOIN {$wpdb->prefix}mailster_actions AS actions_sent ON actions_sent.type = 1 AND subscribers.ID = actions_sent.subscriber_id";
			if ( $args['sent'] ) {
				$join .= ' AND actions_sent.campaign_id IN (' . implode( ',', array_filter( $args['sent'], 'is_numeric' ) ) . ')';
			}
			if ( $args['sent__not_in'] ) {
				$join .= ' AND actions_sent.campaign_id IN (' . implode( ',', array_filter( $args['sent__not_in'], 'is_numeric' ) ) . ')';
			}
		}

		if ( $args['open'] || $args['open__not_in'] ) {
			$join .= " LEFT JOIN {$wpdb->prefix}mailster_actions AS actions_open ON actions_open.type = 2 AND subscribers.ID = actions_open.subscriber_id";
			if ( $args['open'] ) {
				$join .= ' AND actions_open.campaign_id IN (' . implode( ',', array_filter( $args['open'], 'is_numeric' ) ) . ')';
			}
			if ( $args['open__not_in'] ) {
				$join .= ' AND actions_open.campaign_id IN (' . implode( ',', array_filter( $args['open__not_in'], 'is_numeric' ) ) . ')';
			}
		}

		if ( $args['click'] || $args['click__not_in'] ) {
			$join .= " LEFT JOIN {$wpdb->prefix}mailster_actions AS actions_click ON actions_click.type = 3 AND subscribers.ID = actions_click.subscriber_id";
			if ( $args['click'] ) {
				$join .= ' AND actions_click.campaign_id IN (' . implode( ',', array_filter( $args['click'], 'is_numeric' ) ) . ')';
			}
			if ( $args['click__not_in'] ) {
				$join .= ' AND actions_click.campaign_id IN (' . implode( ',', array_filter( $args['click__not_in'], 'is_numeric' ) ) . ')';
			}
		}

		$joins = array();

		$meta_and_fields = wp_parse_args( $args['fields'], $args['meta'] );

		if ( ! empty( $meta_and_fields ) ) {

			foreach ( $meta_and_fields as $field ) {

				$field = esc_sql( $field );

				if ( in_array( $field, $custom_fields ) ) {

					$joins[] = "LEFT JOIN {$wpdb->prefix}mailster_subscriber_fields AS `field_$field` ON `field_$field`.subscriber_id = subscribers.ID AND `field_$field`.meta_key = '$field'";

				} elseif ( in_array( $field, $wp_user_meta ) ) {
					$joins[] = "LEFT JOIN {$wpdb->usermeta} AS `meta_wp_$field` ON `meta_wp_$field`.user_id = subscribers.wp_id AND `meta_wp_$field`.meta_key = '" . str_replace( 'wp_', $wpdb->prefix, $field ) . "'";

				} elseif ( in_array( $field, $meta_fields ) ) {

					$joins[] = "LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS `meta_$field` ON `meta_$field`.subscriber_id = subscribers.ID AND `meta_$field`.meta_key = '$field'";
				}
			}
		}

		if ( ! empty( $args['conditions']['conditions'] ) && is_array( $args['conditions'] ) ) {

			foreach ( $args['conditions']['conditions'] as $options ) {

				$field = esc_sql( $options['field'] );

				if ( in_array( $field, $custom_fields ) ) {

					$joins[] = "LEFT JOIN {$wpdb->prefix}mailster_subscriber_fields AS `field_$field` ON `field_$field`.subscriber_id = subscribers.ID AND `field_$field`.meta_key = '$field'";

				} elseif ( in_array( $field, $wp_user_meta ) ) {
					$joins[] = "LEFT JOIN {$wpdb->usermeta} AS `meta_wp_$field` ON `meta_wp_$field`.user_id = subscribers.wp_id AND `meta_wp_$field`.meta_key = '" . str_replace( 'wp_', $wpdb->prefix, $field ) . "'";

				} elseif ( in_array( $field, $meta_fields ) ) {

					$joins[] = "LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS `meta_$field` ON `meta_$field`.subscriber_id = subscribers.ID AND `meta_$field`.meta_key = '$field'";
				}
			}
		}

		if ( ! empty( $joins ) ) {
			$join .= "\n" . implode( "\n", array_unique( $joins ) );
		}

			$where .= ' WHERE 1';
		if ( $args['lists'] !== false ) {
			// unassigned members if NULL
			if ( is_array( $args['lists'] ) ) {
				$args['lists'] = array_filter( $args['lists'], 'is_numeric' );
			}

			$where .= ( is_null( $args['lists'] ) ) ? ' AND lists_subscribers.list_id IS NULL' : ( empty( $args['lists'] ) ? ' AND lists_subscribers.list_id = 0' : ' AND lists_subscribers.list_id IN(' . implode( ',', $args['lists'] ) . ')' );
		}

		if ( is_array( $args['status'] ) ) {
			$where .= ' AND subscribers.status IN (' . implode( ',', array_filter( $args['status'], 'is_numeric' ) ) . ')';
		}

		if ( $args['lists__not_in'] ) {
			$where .= ' AND lists_subscribers.list_id IS NULL';
		}

		if ( $args['ignore_sent'] ) {
			$where .= ' AND actions_sent.subscriber_id IS NULL';
		}

		if ( $args['sent'] ) {
			$where .= ' AND actions_sent.subscriber_id IS NOT NULL';
		}

		if ( $args['sent__not_in'] ) {
			$where .= ' AND actions_sent.subscriber_id IS NULL';
		}

		if ( $args['open'] ) {
			$where .= ' AND actions_open.subscriber_id IS NOT NULL';
		}

		if ( $args['open__not_in'] ) {
			$where .= ' AND actions_open.subscriber_id IS NULL';
		}

		if ( $args['click'] ) {
			$where .= ' AND actions_click.subscriber_id IS NOT NULL';
		}

		if ( $args['click__not_in'] ) {
			$where .= ' AND actions_click.subscriber_id IS NULL';
		}

		if ( $args['ignore_queue'] ) {
			$where .= ' AND queue.subscriber_id IS NULL';
		}

		if ( ! empty( $args['conditions']['conditions'] ) && is_array( $args['conditions'] ) ) {

			if ( ! isset( $args['conditions']['conditions'][0]['conditions'] ) ) {
				$args['conditions']['conditions'] = array( array( 'operator' => $args['conditions']['operator'], 'conditions' => $args['conditions']['conditions'] ) );
			}

			$cond = array();
			$operator = $args['conditions']['operator'];

			foreach ( $args['conditions']['conditions'] as $i => $conditions ) {

				$sub_cond = array();
				$sub_operator = $conditions['operator'];

				foreach ( $conditions['conditions'] as $options ) {

					$field = esc_sql( $options['field'] );
					$value = esc_sql( stripslashes( $options['value'] ) );
					$field_operator = esc_sql( stripslashes( $options['operator'] ) );
					$is_empty = '' == $value;
					$extra = '';
					$positive = false;

					switch ( $field ) {
						case 'rating':
							$value = str_replace( ',', '.', $value );
							if ( strpos( $value, '%' ) !== false || $value > 5 ) {
								$value = floatval( $value ) / 100;
							} elseif ( $value >= 1 ) {
								$value = floatval( $value ) * 0.2;
							}
							break;
					}

					switch ( $field_operator ) {
						case '=':
						case 'is':
							$positive = true;
						case '!=':
						case 'is_not':

							if ( in_array( $field, $custom_date_fields ) ) {
								$f = "STR_TO_DATE(`field_$field`.meta_value,'%Y-%m-%d')";
							} elseif ( in_array( $field, $timefields ) ) {
								$f = "STR_TO_DATE(FROM_UNIXTIME(subscribers.$field),'%Y-%m-%d')";
							} elseif ( in_array( $field, $custom_fields ) ) {
								$f = "`field_$field`.meta_value";
							} elseif ( in_array( $field, $meta_fields ) ) {
								$f = "`meta_$field`.meta_value";
							} elseif ( in_array( $field, $wp_user_meta ) ) {
								$f = "`meta_wp_$field`.meta_value";
								if ( $field == 'wp_capabilities' ) {
									$value = 's:' . strlen( $value ) . ':"' . strtolower( $value ) . '";';
									$sub_cond[] = "`meta_wp_$field`.meta_value " . ( in_array( $field_operator, array( 'is', '=' ) ) ? 'LIKE' : 'NOT LIKE' ) . " '%$value%'";
									break;
								}
							} else {
								$f = "subscribers.$field";
							}

							$c = $f . ' ' . ( $positive ? '=' : '!=' ) . " '$value'";
							if ( $is_empty && $positive ) {
								$c .= ' OR ' . $f . ' IS NULL';
							}

							$sub_cond[] = $c;
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
							if ( in_array( $field, $custom_fields ) ) {
								$f = "`field_$field`.meta_value";
							} elseif ( in_array( $field, $meta_fields ) ) {
								$f = "`meta_$field`.meta_value";
							} elseif ( in_array( $field, $wp_user_meta ) ) {
								$f = "`meta_wp_$field`.meta_value";
							} else {
								$f = "subscribers.$field";
							}

							$c = $f . ' ' . ( $positive ? 'LIKE' : 'NOT LIKE' ) . " $value";
							if ( $is_empty && $positive ) {
								$c .= ' OR ' . $f . ' IS NULL';
							}

							$sub_cond[] = $c;
							break;

						case '^':
						case 'begin_with':
							if ( $field == 'wp_capabilities' ) {
								$value = "'%\"" . strtolower( $value ) . "%'";
							} else {
								$value = "'$value%'";
							}
							if ( in_array( $field, $custom_fields ) ) {
								$f = "`field_$field`.meta_value";
							} elseif ( in_array( $field, $meta_fields ) ) {
								$f = "`meta_$field`.meta_value";
							} elseif ( in_array( $field, $wp_user_meta ) ) {
								$f = "`meta_wp_$field`.meta_value";
							} else {
								$f = "subscribers.$field";
							}

								$c = $f . " LIKE $value";

								$sub_cond[] = $c;
							break;

						case '$':
						case 'end_with':
							if ( $field == 'wp_capabilities' ) {
								$value = "'%" . strtolower( $value ) . "\"%'";
							} else {
								$value = "'%$value'";
							}

							if ( in_array( $field, $custom_fields ) ) {
								$f = "`field_$field`.meta_value";
							} elseif ( in_array( $field, $meta_fields ) ) {
								$f = "`meta_$field`.meta_value";
							} elseif ( in_array( $field, $wp_user_meta ) ) {
								$f = "`meta_wp_$field`.meta_value";
							} else {
								$f = "subscribers.$field";
							}

							$c = $f . " LIKE $value";

							$sub_cond[] = $c;
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

							if ( in_array( $field, $custom_date_fields ) ) {
								$f = "STR_TO_DATE(`field_$field`.meta_value,'%Y-%m-%d')";
								$value = "'$value'";
							} elseif ( in_array( $field, $timefields ) ) {
								$f = "STR_TO_DATE(FROM_UNIXTIME(subscribers.$field),'%Y-%m-%d')";
								$value = "'$value'";
							} elseif ( in_array( $field, $custom_fields ) ) {
								$f = "`field_$field`.meta_value";
								$value = is_numeric( $value ) ? floatval( $value ) : "'$value'";
							} elseif ( in_array( $field, $meta_fields ) ) {
								$f = "`meta_$field`.meta_value";
								$value = is_numeric( $value ) ? floatval( $value ) : "'$value'";
							} elseif ( in_array( $field, $wp_user_meta ) ) {
								$f = "`meta_wp_$field`.meta_value";
								if ( $field == 'wp_capabilities' ) {
									$value = "'NOTPOSSIBLE'";
								}
							} else {
								$f = "subscribers.$field";
								$value = floatval( $value );
							}

							$c = $f . ' ' . ( in_array( $field_operator, array( 'is_greater', 'is_greater_equal', '>', '>=' ) ) ? '>' . $extra : '<' . $extra ) . " $value";

							$sub_cond[] = $c;
							break;

						case '%':
						case 'pattern':
							$positive = true;
						case '!%':
						case 'not_pattern':
							if ( in_array( $field, $custom_date_fields ) ) {
								$f = "STR_TO_DATE(`field_$field`.meta_value,'%Y-%m-%d')";
							} elseif ( in_array( $field, $timefields ) ) {
								$f = "STR_TO_DATE(FROM_UNIXTIME(subscribers.$field),'%Y-%m-%d')";
							} elseif ( in_array( $field, $custom_fields ) ) {
								$f = "`field_$field`.meta_value";
							} elseif ( in_array( $field, $meta_fields ) ) {
								$f = "`meta_$field`.meta_value";
							} elseif ( in_array( $field, $wp_user_meta ) ) {
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

							$sub_cond[] = $c;
							break;

					}
				}

				$cond[] = '(' . implode( ' ' . $sub_operator . ' ', $sub_cond ) . ')';

			}

			if ( ! empty( $cond ) ) {
				$where .= ' AND ( ' . implode( ' ' . $operator . ' ', $cond ) . ' )';
			}
		}

			$group = '';
		if ( $args['return_ids'] ) {
			$group .= ' GROUP BY subscribers.ID';
		}

			$order = '';
		if ( $args['orderby'] ) {
			$order .= ' ORDER BY';
			$ordering = strtoupper( $args['order'] ? $args['order'] : 'ASC' );
			$orders = array();
			foreach ( $args['orderby'] as $i => $orderby ) {
				$ordering = isset( $args['order'][ $i ] ) ? strtoupper( $args['order'][ $i ] ) : $ordering;
				$orders[] = " $orderby $ordering";
			}
			$order .= implode( ',', $orders );
		}

			$limit = '';
		if ( $args['limit'] ) {
			$limit .= ' LIMIT ' . intval( $args['offset'] ) . ', ' . intval( $args['limit'] );
		}

		$sql = $select . "\n" . $from . "\n" . $join . "\n" . $where . "\n" . $group . "\n" . $order . "\n" . $limit;

		// legacy filter
		$sql = apply_filters( 'mailster_campaign_get_subscribers_by_list_sql', $sql );

		if ( $args['return_sql'] ) {
			return $sql;
		}

		if ( $args['return_ids'] ) {
			$result = $wpdb->get_col( $sql );
		} elseif ( $args['return_count'] ) {
			$result = $wpdb->get_var( $sql );
		} else {
			$result = $wpdb->get_results( $sql );
		}

		return $result;

	}


}
