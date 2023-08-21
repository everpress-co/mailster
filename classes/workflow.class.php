<?php

class MailsterWorkflow {

	private $entry;
	private $is_search;
	private $workflow;
	private $trigger;
	private $subscriber;
	private $step;
	private $steps;
	private $args;
	private $current_step;
	private $steps_map = array();

	public function __construct( $workflow, $trigger, $subscriber = null, $step = null ) {

		$this->set_workflow( $workflow );
		$this->set_trigger( $trigger );
		$this->set_subscriber( $subscriber );
		$this->set_step( $step );

	}

	public function set_workflow( $workflow ) {

		$this->workflow = get_post( $workflow );
		$this->steps    = $this->get_steps();

	}

	public function set_trigger( $trigger ) {

		$this->trigger = $trigger;

	}

	public function set_subscriber( $subscriber ) {

		$this->subscriber = $subscriber;

	}

	public function set_step( $step ) {

		$this->step = $step;

	}

	public function get_steps() {

		if ( $this->steps ) {
			return $this->steps;
		}

		$blocks      = parse_blocks( $this->workflow->post_content );
		$this->steps = $this->parse( $blocks );

		return $this->steps;
	}

	private function parse( $blocks, $parent = null ) {
		$parsed = array();
		foreach ( $blocks as $block ) {
			if ( ! $block['blockName'] ) {
				continue;
			}

			$id   = isset( $block['attrs']['id'] ) ? $block['attrs']['id'] : null;
			$type = str_replace( 'mailster-workflow/', '', $block['blockName'] );
			$arg  = array(
				'type' => $type,
				'attr' => $block['attrs'],
				'id'   => $id,
			);

			if ( $parent ) {
				$arg['parent'] = $parent;
			}

			if ( $type === 'conditions' ) {
				$arg['yes'] = $this->parse( $block['innerBlocks'][0]['innerBlocks'], $id );
				$arg['no']  = $this->parse( $block['innerBlocks'][1]['innerBlocks'], $id );
			} elseif ( $type === 'triggers' ) {
				$arg['trigger'] = $this->parse( $block['innerBlocks'], $id );
			}

			if ( $id ) {
				$this->steps_map[ $id ] = $block['attrs'];
			}

			$parsed[] = $arg;

		}

		return $parsed;

	}

	public function run() {

		if ( ! $this->workflow ) {
			return new WP_Error( 'error', 'Workflow does not exist.', $this->step );
		}

		$this->entry = $this->exists();

		if ( ! $this->entry ) {
			$this->log( 'NOT IN DATABSE!' );
			return false;
		}

		if ( get_post_type( $this->workflow ) !== 'mailster-workflow' ) {
			$this->delete();
			return new WP_Error( 'info', 'This is not a correct workflow.', $this->step );
		}
		if ( get_post_status( $this->workflow ) !== 'publish' ) {
			return new WP_Error( 'info', 'This is workflow is not published.', $this->step );
		}

		$this->args = array(
			'trigger'    => $this->trigger,
			'id'         => $this->workflow->ID,
			'subscriber' => $this->subscriber,
			'step'       => $this->step,
		);

		// if a step is defined we have to find it first
		$this->is_search = ! is_null( $this->step );
		if ( ! $this->is_search ) {

			$this->log( 'RUN JOB ' . $this->trigger . ' for ' . $this->subscriber . ' on ' . $this->trigger );

			$enddate = get_post_meta( $this->workflow->ID, 'enddate', true );

			// if enddate is set and in the past
			if ( $enddate && time() > strtotime( $enddate ) ) {

				$this->delete();
				$this->log( 'END DATE REACHED' );
				return false;
			}
		}

		// check if subscriber exists if it's not 0 ( 'date', 'anniversary', 'published_post' )
		if ( $this->subscriber !== 0 ) {
			if ( ! in_array( $this->trigger, array( 'date', 'anniversary', 'published_post' ) ) ) {
				if ( ! mailster( 'subscribers' )->get( $this->subscriber ) ) {
					$this->delete();
					$this->log( 'SUBSCRIBER DOES NOT EXIST' );
					return false;
				}
			}
		}

		// start
		$result = $this->do_steps( $this->steps );

		// all good => finish
		if ( $result === true ) {
			$this->finish();

			// more info here
		} elseif ( is_wp_error( $result ) ) {

			$this->error_notice( $result );

		}

		return $result;

	}

	private function error_notice( WP_Error $error, $notice_id = null ) {

		if ( is_null( $notice_id ) ) {
			$notice_id = 'workflow_error_' . $this->workflow->ID;
		}

		$error_code = $error->get_error_code();
		$error_data = $error->get_error_data();
		$error_msg  = $error->get_error_message();
		$link       = admin_url( 'post.php?post=' . $this->workflow->ID . '&action=edit' );
		$steplink   = $link;
		if ( isset( $error_data['id'] ) ) {
			$steplink .= '#step-' . $error_data['id'];
		}
		mailster_notice( sprintf( 'Workflow %s had a problem: %s', '"<a href="' . esc_url( $steplink ) . '">' . get_the_title( $this->workflow ) . '</a>"', '<strong>' . $error_msg . '</strong>' ), $error_code, false, $notice_id );

	}

	/**
	 * Returns the id of the current Workflow from the database
	 *
	 * @return string|null
	 */
	private function exists() {

		global $wpdb;

		$workflow_id   = $this->workflow->ID;
		$trigger       = $this->trigger;
		$subscriber_id = $this->subscriber;

		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}mailster_workflows WHERE `workflow_id` = %d AND `trigger` = %d AND `subscriber_id` = %d", $workflow_id, $trigger, $subscriber_id ) );
	}

	/**
	 * Checks if the count of the workflow has been reached
	 *
	 * @param mixed $count
	 * @return bool
	 */
	private function check_count( $count ) {

		global $wpdb;

		$workflow_id   = $this->workflow->ID;
		$trigger       = $this->trigger;
		$subscriber_id = $this->subscriber;

		$entries = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}mailster_workflows WHERE `workflow_id` = %d AND `trigger` = %s AND `subscriber_id` = %d", $workflow_id, $trigger, $subscriber_id ) );

		// enough entries in the database
		if ( $count >= $entries ) {
			return true;
		}

		return false;
	}

	/**
	 * Deletes the current Workflow from the database
	 *
	 * @return bool
	 */
	private function delete() {

		global $wpdb;

		if ( ! $this->entry ) {
			return false;
		}

		return false !== $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}mailster_workflows WHERE ID = %d", $this->entry->ID ) );

	}


	/**
	 * Updates the current Workflow in the database
	 *
	 * @return bool
	 */
	private function update( array $args = array() ) {

		global $wpdb;

		$success = true;

		$workflow_id   = $this->workflow->ID;
		$trigger       = $this->trigger;
		$subscriber_id = $this->subscriber;
		$step          = $this->step;

		$suppress_errors = $wpdb->suppress_errors( true );

		$args = wp_parse_args( $args, array( 'step' => $this->current_step ) );

		$where = array(
			'workflow_id'   => $workflow_id,
			'trigger'       => $trigger,
			'subscriber_id' => $subscriber_id,
			'finished'      => 0,
		);

		if ( $wpdb->update( "{$wpdb->prefix}mailster_workflows", $args, $where ) ) {

		} else {

			$success = false;
		}

		$wpdb->suppress_errors( $suppress_errors );

		return $success;

	}

	private function do_steps( $steps ) {

		foreach ( $steps as $i => $step ) {

			$result = $this->do_step( $step );

			if ( $result === true ) {
				continue;
			}

			return $result;

		}

		return true;

	}

	private function do_step( $step ) {

		$this->current_step = $step['id'];

		// no more subscriber TODO maybe not needed at that point
		if ( empty( $this->args['subscriber'] ) ) {
			// return false;
		}

		// we are in search mode, let's find our step
		if ( $this->is_search ) {

			// not our step
			if ( $step['id'] !== $this->args['step'] ) {

				// we need to search condtions as well
				if ( $step['type'] == 'conditions' ) {
					$result = $this->do_steps( $step['yes'] );
					if ( $this->is_search ) {
						$result = $this->do_steps( $step['no'] );
					}
					return $result;
				}

				// return true so we can search in the next step
				return true;
			}

			// got it => continue
			$this->is_search = false;
			$this->log( 'FOUND  ' . $step['id'] . ' for ' . $this->subscriber );

		}

		switch ( $step['type'] ) {
			case 'triggers':
				return $this->triggers( $step );
			break;

			case 'action':
				$result = $this->action( $step );

				// try again wuth logic of retry action
				if ( is_wp_error( $result ) ) {

					$tries = (int) $this->entry->try;
					$tries++;
					$error_msg = $result->get_error_message();
					$max_tries = 10;

					// Stop after more tries
					if ( $tries > $max_tries ) {

						$error = new WP_Error( 'error', sprintf( __( 'Action failed with %1$s after %2$d tries. Workflow has been finished.', 'mailster' ), '"' . $error_msg . '"', $tries ), $step );
						// finish with error
						$this->finish( array( 'error' => $error_msg ) );

						return $error;
					}

					$try_again_after = 60 * $tries + 60;
					$try_again_after = 6;

					$error = new WP_Error( 'warning', sprintf( __( 'Action failed with %1$s', 'mailster' ), '"' . $error_msg . '"', $tries ), $step );
					$this->error_notice( $error, 'workflow_error_action_' . $step['id'] . '_' . $this->subscriber );

					$this->update(
						array(
							'timestamp' => time() + $try_again_after,
							'error'     => $error_msg,
							'try'       => $tries,
						)
					);

					// return false to not go to the next step
					return false;

				}

				return $result;
			break;

			case 'email':
				$result = $this->email( $step );

				// try again
				if ( is_wp_error( $result ) ) {
					$this->update(
						array(
							'timestamp' => time() + 60,
							'error'     => $result->get_error_message(),
						)
					);
				}

				return $result;
			break;

			case 'stop':
				return $this->stop( $step );
			break;

			case 'delay':
				return $this->delay( $step );
			break;

			case 'conditions':
				return $this->conditions( $step );
			break;
		}

		return true;

	}

	private function action( $step ) {

		$attr = isset( $step['attr'] ) ? $step['attr'] : array();

		$action = isset( $step['attr']['action'] ) ? $step['attr']['action'] : null;

		if ( ! $action ) {
			return new WP_Error( 'info', 'No Action for this step . ', $step );
		}

		$this->log( 'ACTION ' . $step['attr']['action'] . ' ' . $step['id'] . ' for ' . $this->subscriber );

		switch ( $action ) {
			case 'nothing':
				$this->log( 'nothing' );
				break;

			case 'update_field':
				$this->log( 'update_field' );
				$remove_old = false;
				$field      = isset( $attr['field'] ) ? $attr['field'] : null;
				$value      = isset( $attr['value'] ) ? $attr['value'] : '';
				if ( $field ) {

					// special case for date fields
					$datefields = mailster()->get_custom_date_fields( true );

					if ( in_array( $field, $datefields ) ) {
						if ( is_numeric( $value ) ) {
							if ( $value == 0 ) {
								$value = date( 'Y-m-d' );
							} else {

								// relative date so we ned the current one
								$fields = mailster( 'subscribers' )->get_custom_fields( $this->subscriber );

								if ( ! isset( $fields[ $field ] ) ) {
									return true;
								}
								// stop if no initial value is set
								if ( empty( $fields[ $field ] ) ) {
									return true;
								}

								// to the current add the offset (maybe negative)
								$value = date( 'Y-m-d', strtotime( $fields[ $field ] ) + ( $value * DAY_IN_SECONDS ) );

							}
						} else {
							// some sanitizations
							$value = date( 'Y-m-d', strtotime( $value ) );
						}
					}

					if ( $value !== '' ) {
						mailster( 'subscribers' )->add_custom_field( $this->subscriber, $field, $value );

					} else {
						mailster( 'subscribers' )->remove_custom_field( $this->subscriber, $field );
					}
				}
				break;

			case 'add_list':
				$this->log( 'add_list' );
				if ( isset( $attr['lists'] ) ) {
					$remove_old  = false;
					$doubleoptin = isset( $attr['doubleoptin'] ) && $attr['doubleoptin'];
					mailster( 'lists' )->assign_subscribers( $attr['lists'], $this->subscriber, $remove_old, ! $doubleoptin );
				}
				break;

			case 'remove_list':
				$this->log( 'remove_list' );
				if ( isset( $attr['lists'] ) ) {
					mailster( 'lists' )->unassign_subscribers( $attr['lists'], $this->subscriber );
				}
				break;

			case 'add_tag':
				$this->log( 'add_tag' );
				$this->log( $attr['tags'] );
				$this->log( $this->subscriber );
				if ( isset( $attr['tags'] ) ) {
					mailster( 'tags' )->assign_subscribers( $attr['tags'], $this->subscriber );
				}

				break;

			case 'remove_tag':
				$this->log( 'remove_tag' );
				if ( isset( $attr['tags'] ) ) {
					mailster( 'tags' )->unassign_subscribers( $attr['tags'], $this->subscriber );
				}
				break;

			case 'unsubscribe':
				$this->log( 'unsubscribe' );

				mailster( 'subscribers' )->unsubscribe( $this->subscriber, $this->workflow->ID, 'UNSUBSCRIBED FROM WORKFLOW' );
				break;

			case 'webhook':
				return $this->webhook( $step );
				break;

			default:
				return new WP_Error( 'info', 'Invalid action', $step );
				break;
		}

		return true;

	}




	private function webhook( $step ) {

		$url = isset( $step['attr']['webhook'] ) ? $step['attr']['webhook'] : null;

		if ( ! $url ) {
			return new WP_Error( 'error', 'No Webhook defined', $step );
		}
		$subscriber = mailster( 'subscribers' )->get( $this->subscriber, true );

		$data = array(
			'workflow'   => array(
				'id'        => $this->workflow->ID,
				'step'      => $step['attr']['id'],
				'name'      => $this->workflow->post_title,
				'trigger'   => $this->entry->trigger,
				'added'     => $this->entry->added,
				'timestamp' => $this->entry->timestamp,
				'try'       => $this->entry->try,
			),
			'subscriber' => $subscriber,
		);

		$args = array(
			'timeout'    => 5,
			'headers'    => array(
				'content-type' => 'application/json',
			),
			'user-agent' => 'Mailster/' . MAILSTER_VERSION,
			'method'     => 'POST',
			'body'       => json_encode( $data ),
		);

		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		// $body     = wp_remote_retrieve_body( $response );

		// if the webhook failed try again after 5 minutes and stop the workflow after 3 tries
		if ( $code !== 200 ) {

			$error = get_status_header_desc( $code );
			return new WP_Error( 'error', $error, $step );

		}

		return true;
	}




	private function triggers( $step ) {

		// check if the current trigger is the right one
		foreach ( $step['trigger'] as $trigger ) {

			// no => try next
			if ( $trigger['attr']['trigger'] !== $this->trigger ) {
				continue;
			}

			// check how often we can run this trigger
			$repeat = isset( $trigger['attr']['repeat'] ) ? $trigger['attr']['repeat'] : 1;

			if ( $repeat !== -1 ) {
				if ( ! $this->check_count( $repeat ) ) {
					$this->log( 'LIMIT REACHED' );
					$this->delete();
					return false;

				}
			}

			// check for conditions
			$conditions = isset( $trigger['attr']['conditions'] ) ? $trigger['attr']['conditions'] : array();

			if ( $conditions ) {
				$conditions = $this->sanitize_conditions( $conditions );

				if ( $this->subscriber && ! mailster( 'conditions' )->check( $conditions, $this->subscriber ) ) {
					$this->log( 'CONDITION NOT PASSED ! ! ' );
					$this->delete();
					return false;
				}
			}

			$this->log( 'use TRIGGER ' . $this->trigger );

			switch ( $this->trigger ) {
				case 'date':
				case 'anniversary':
					$timestamp = isset( $trigger['attr']['date'] ) ? strtotime( $trigger['attr']['date'] ) : null;

					// if this is not defined we get all based on the condtion
					if ( ! $this->subscriber ) {
						if ( ! $timestamp ) {
							$this->delete();
							return false;
						}

						$query_args = array(
							'return_ids' => true,
							'conditions' => $conditions,
						);

						$field = isset( $trigger['attr']['field'] ) ? $trigger['attr']['field'] : null;

						// handle custom field options
						if ( $field ) {
							// $query_args['return_sql'] = true;

							// get timestamp for the defined time of today
							$timestamp = strtotime( 'today ' . date( 'H:i', $timestamp ) );

							if ( $this->trigger === 'anniversary' ) {
								$cond = array(
									'field'    => $field,
									'operator' => 'end_with',
									'value'    => date( '-m-d' ),
								);
							} else {
								$cond = array(
									'field'    => $field,
									'operator' => 'is',
									'value'    => date( 'Y-m-d' ),
								);
							}

							// for anniversary get all with the field on today, otherwise exactly today
							$value = $this->trigger == 'anniversary' ? '-m-d' : 'Y-m-d';

							// add the date field as AND condition
							$query_args['conditions'][] = array( $cond );

							// not in the future
							$query_args['conditions'][] = array(
								array(
									'field'    => $field,
									'operator' => 'is_smaller_equal',
									'value'    => date( 'Y-m-d' ),
								),
							);

							// $query_args['return_sql'] = true;

						}

						$subscriber_ids = mailster( 'subscribers' )->query( $query_args );

						// $step = isset( $trigger['attr']['id'] ) ? $trigger['attr']['id'] : null;
						if ( ! empty( $subscriber_ids ) ) {
							mailster( 'trigger' )->bulk_add( $this->workflow->ID, $this->trigger, $subscriber_ids, null, $timestamp );
						}
						$this->delete();
						return false;
					}

					// round it down to second 00
					$timestamp = strtotime( date( 'Y-m-d H:i', $timestamp ) );

					if ( time() < $timestamp ) {
						$this->log( 'TIMESTAMP NOT REACHED' );
						return false;
					}

					break;
				case 'published_post':
					// if this is not defined we get all based on the condtion
					if ( ! $this->subscriber ) {

						$query_args = array(
							'include '   => $this->subscriber,
							'return_ids' => true,
							'conditions' => $conditions,
						);

						$timestamp = time();

						$subscriber_ids = mailster( 'subscribers' )->query( $query_args );

						$context = $this->entry->context;

						// $step = isset( $trigger['attr']['id'] ) ? $trigger['attr']['id'] : null;
						if ( ! empty( $subscriber_ids ) ) {
							mailster( 'trigger' )->bulk_add( $this->workflow->ID, $this->trigger, $subscriber_ids, null, $timestamp, $context );
						}
						$this->delete();
						return false;

					}

					break;

				default:
					break;
			}

			// everything is prepared and we can move on
			return true;

		}

		// no such trigger found in this workflow
		$this->delete();
		return new WP_Error( 'error', 'No matching trigger found ! ', $step );

	}


	private function email( $step ) {

		// TODO invalid step can cause email to get stuck
		if ( ! isset( $step['attr']['campaign'] ) ) {
			return new WP_Error( 'error', 'Step is incomplete', $step );
		}

		if ( ! $campaign = mailster( 'campaigns' )->get( $step['attr']['campaign'] ) ) {
			return new WP_Error( 'error', 'Step is incomplete', $step );
		}

		// skip that as it's the current step
		if ( $step['id'] !== $this->args['step'] ) {
			$this->args['step'] = $step['id'];
			$this->log( 'EMAIL ' . $step['id'] . ' for ' . $this->subscriber );

			// use the timestamp from the step for correct queueing
			$timestamp = $this->entry->timestamp ? $this->entry->timestamp : time();

			$tags = array();
			if ( isset( $step['attr']['subject'] ) ) {
				$tags['subject'] = $step['attr']['subject'];
			}
			if ( isset( $step['attr']['preheader'] ) ) {
				$tags['preheader'] = $step['attr']['preheader'];
			}
			if ( isset( $step['attr']['from_name'] ) ) {
				$tags['from_name'] = $step['attr']['from_name'];
			}
			if ( isset( $step['attr']['from_email'] ) ) {
				$tags['from_email'] = $step['attr']['from_email'];
			}

			$args = array(
				'campaign_id'   => $campaign->ID,
				'subscriber_id' => $this->subscriber,
				'priority'      => 15,
				'timestamp'     => $timestamp,
				'ignore_status' => false,
				'options'       => false,
				'tags'          => $tags,
			);

			if ( mailster( 'queue' )->add( $args ) ) {
				$this->update( array( 'timestamp' => $timestamp ) );
			}

			return false;
		}

		return true;

		// TODO check when step is inclomplete and the campaigns hasn't been sent already

		// only continue with the next step if campaign has been sent
		$has_been_sent = mailster( 'actions' )->get_by_subscriber( $this->subscriber, 'sent', $campaign->ID );

		return (bool) $has_been_sent;
	}

	private function stop( $step ) {

		$this->log( 'STOP ' . $step['id'] . ' for ' . $this->subscriber );
		$this->finish();

		// return false to not execute the next step
		return false;

	}

	private function delay( $step ) {

		// skip that if it's the current step
		if ( $step['id'] === $this->args['step'] ) {
			return true;
		}

		$this->args['step'] = $step['id'];

		$use_timezone = isset( $step['attr']['timezone'] ) ? $step['attr']['timezone'] : false;
		$amount       = $step['attr']['amount'];
		$unit         = $step['attr']['unit'];
		$timeoffset   = 0;
		$date         = 0;

		if ( isset( $step['attr']['date'] ) ) {
			if ( $use_timezone ) {
				$timeoffset = mailster( 'subscribers' )->meta( $this->subscriber, 'timeoffset' );
			}
			$date = strtotime( $step['attr']['date'] ) + ( $timeoffset * HOUR_IN_SECONDS );
		}

		switch ( $unit ) {
			case 'minutes':
			case 'hours':
			case 'days':
			case 'weeks':
			case 'months':
				$timestamp = strtotime( '+' . $amount . ' ' . $unit );
				break;

			case 'day':
				if ( $date < time() ) {
					$this->log( 'WE ARE IN THE FUTURE' );
					$timestamp = strtotime( 'tomorrow ' . date( 'H:i', $date ) );

				} else {
					$this->log( 'WE ARE IN THE PAST' );
					$timestamp = strtotime( 'today ' . date( 'H:i', $date ) );
				}
				break;

			case 'week':
				if ( ! isset( $step['attr']['weekdays'] ) ) {
					return new WP_Error( 'error', 'No weekdays defined!', $step );
				}

				$weekdays = $step['attr']['weekdays'];
				// get a fictional date with the same day (respecting timezone)
				$date = strtotime( date( 'Y-m-d ' . date( 'H:i', $date ) ) );

				if ( $date < time() ) {
					$timestamp = mailster( 'helper' )->get_next_date_in_future( $date, 1, 'day', $weekdays, true );

				} else {
					// today in in the list of weekdays
					if ( empty( $weekdays ) || in_array( date( 'w' ), $weekdays ) ) {
						$timestamp = $date;
					} else {
						$timestamp = mailster( 'helper' )->get_next_date_in_future( $date, 1, 'day', $weekdays, false );
					}
				}

				break;

			case 'month':
				error_log( print_r( $step, true ) );

				if ( ! isset( $step['attr']['month'] ) ) {
					return new WP_Error( 'error', 'No month defined!', $step );
				}

				$day = $step['attr']['month'];

				// get a fictional date with the same day
				if ( $day === -1 ) { // last day of the month
					// t returns the number of days in the month of a given date
					$date = strtotime( date( 'Y-m-t ' . date( 'H:i', $date ) ) );

				} else {

					$date = strtotime( date( 'Y-m-' . $day . ' ' . date( 'H:i', $date ) ) );

					// check if the current month has this day
					if ( $day > 28 ) {

						// get last day of the month
						$last = strtotime( date( 'Y-m-t ' . date( 'H:i', $date ) ) );

						if ( $date != $last ) {
							// the last day of the current month + our days
							$date = $last + ( $day * DAY_IN_SECONDS );
						}
					}
				}

				$timestamp = $date;

				// timestamp is in the past
				if ( $timestamp < time() ) {
					$weekdays  = array(); // no support for that
					$timestamp = mailster( 'helper' )->get_next_date_in_future( $timestamp, 1, 'month', $weekdays, false );
				}
				break;

			case 'year':
				// remove seconds from our date
				$timestamp = strtotime( date( 'Y-m-d H:i', $date ) );

				// timestamp is in the past
				if ( $timestamp < time() ) {
					return new WP_Error( 'error', 'Date of step is in the past.', $step );
				}
				break;

			default:
				return new WP_Error( 'error', 'No matching delay option found.', $step );
			break;
		}

		// no need to schedule if in the past
		if ( $timestamp <= time() ) {
			$this->log( 'SKIP DELAY' );
			return true;
		}
		$this->update( array( 'timestamp' => $timestamp ) );

		$this->log( 'SCHEDULE DELAY ' . $step['id'] . ' for ' . human_time_diff( $timestamp ) );

		// return false to stop the queue from processing
		return false;

	}

	private function conditions( $step ) {

		if ( ! isset( $step['attr']['conditions'] ) ) {
			return new WP_Error( 'missing_arg', 'Condition missing', $step );
		}

		$conditions = $this->sanitize_conditions( $step['attr']['conditions'] );

		if ( mailster( 'conditions' )->check( $conditions, $this->subscriber ) ) {
			$use = $step['yes'];
			$this->log( 'CONDITION PASSED ' . $step['id'] . ' for ' . $this->subscriber );
		} else {
			$use = $step['no'];
			$this->log( 'CONDITION NOT PASSED ' . $step['id'] . ' for ' . $this->subscriber );
		}

		return $this->do_steps( $use );

	}

	private function sanitize_conditions( $conditions ) {

		wp_parse_str( $conditions, $params );
		$conditions = $params['conditions'];

		// replace the step id with the actual campaing id to get the correct condition
		// TOTO optimze this
		foreach ( $conditions as $i => $condition_group ) {
			foreach ( $condition_group as $j => $condition ) {
				if ( isset( $this->steps_map[ $condition['value'] ] ) ) {
					$from_map                        = $this->steps_map[ $condition['value'] ];
					$conditions[ $i ][ $j ]['value'] = $from_map['campaign'] ? $from_map['campaign'] : null;
				}
			}
		}

		return $conditions;

	}

	private function finish( array $args = array() ) {
		$this->log( 'FINISHED' );

		$args = wp_parse_args(
			$args,
			array(
				'finished'  => time(),
				'step'      => null,
				'timestamp' => null,
				'error'     => '',
			)
		);

		$this->update( $args );
	}


	private function log( $str ) {
		if ( WP_DEBUG ) {
			error_log( print_r( $str, true ) );
		}
	}
}
