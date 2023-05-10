<?php

class MailsterTrigger {

	public function __construct() {

		// subscriber added to list
		add_action( 'mailster_list_confirmed', array( &$this, 'list_confirmed' ), 10, 2 );

		add_action( 'mailster_form_conversion', array( &$this, 'form_conversion' ), 10, 3 );

		// subscriber added to list
		add_action( 'mailster_tag_added', array( &$this, 'tag_added' ), 10, 3 );

		// Visited a page
		add_action( 'template_redirect', array( &$this, 'front_page_hooks' ) );

		// campaign is opened
		add_action( 'mailster_open', array( &$this, 'open' ), 10, 3 );

		// link in campaign is clicked
		add_action( 'mailster_click', array( &$this, 'click' ), 10, 5 );

		// custom trigger
		add_action( 'mailster_trigger', array( &$this, 'trigger' ), 10, 4 );

		// check for date trigger hourly
		add_action( 'mailster_cron', array( &$this, 'hourly' ) );

		// post published
		add_action( 'wp_after_insert_post', array( &$this, 'published_post' ), 10, 4 );

		add_action( 'mailster_trigger_date', array( &$this, 'date' ) );

		add_action( 'mailster_trigger_anniversary', array( &$this, 'anniversary' ) );

	}


	public function hourly() {
		$this->date();
		$this->anniversary();
	}

	public function trigger( $workflow_id, $trigger, $subscriber_id, $step = null ) {

		$this->add_job( $workflow_id, $trigger, $subscriber_id, $step );

	}

	public function hook( $hook, $subscriber_id, $workflow_id = null, $step = null ) {

		$workflows = $this->get_workflows_by_trigger( 'hook' );
		foreach ( $workflows as $workflow ) {

			if ( $workflow_id && $workflow_id != $workflow ) {
				continue;
			}
			$options = mailster( 'automations' )->get_trigger_option( $workflow, 'hook' );

			$this->add_job( $workflow, 'hook', $subscriber_id, $step );

		}

	}

	public function open( $subscriber_id, $campaign_id, $campaign_index ) {

		$workflows = $this->get_workflows_by_trigger( 'opened_campaign' );
		foreach ( $workflows as $workflow ) {
			$options = mailster( 'automations' )->get_trigger_option( $workflow, 'opened_campaign' );

		}
	}

	public function click( $subscriber_id, $campaign_id, $target, $index, $campaign_index ) {

		$workflows = $this->get_workflows_by_trigger( 'link_click' );
		foreach ( $workflows as $workflow ) {
			$options = mailster( 'automations' )->get_trigger_option( $workflow, 'link_click' );

			if ( ! isset( $options['links'] ) || empty( $options['links'] ) ) {
				continue;
			}

			$links = $options['links'];

			$matching_links = preg_grep( '|^' . preg_quote( $target ) . '$|', $links );

			if ( ! empty( $matching_links ) ) {
				$this->add_job( $workflow, 'link_click', $subscriber_id );
			}
		}
	}

	public function list_confirmed( $list_id, $subscriber_id ) {

		$workflows = $this->get_workflows_by_trigger( 'list_add' );
		foreach ( $workflows as $workflow ) {
			$options = mailster( 'automations' )->get_trigger_option( $workflow, 'list_add' );

			if ( in_array( $list_id, $options['lists'] ) ) {
				$this->add_job( $workflow, 'list_add', $subscriber_id );

				// any list
			} elseif ( in_array( '-1', $options['lists'] ) ) {
				$this->add_job( $workflow, 'list_add', $subscriber_id );
			}
		}

	}

	public function form_conversion( $form_id, $subscriber_id, $post_id ) {

		$workflows = $this->get_workflows_by_trigger( 'form_conversion' );
		foreach ( $workflows as $workflow ) {
			$options = mailster( 'automations' )->get_trigger_option( $workflow, 'form_conversion' );

			if ( in_array( $form_id, $options['forms'] ) ) {
				$this->add_job( $workflow, 'form_conversion', $subscriber_id );

				// any list
			} elseif ( in_array( '-1', $options['forms'] ) ) {
				$this->add_job( $workflow, 'form_conversion', $subscriber_id );
			}
		}

	}

	public function tag_added( $tag_id, $subscriber_id, $tag_name ) {

		$workflows = $this->get_workflows_by_trigger( 'tag_added' );
		foreach ( $workflows as $workflow ) {
			$options = mailster( 'automations' )->get_trigger_option( $workflow, 'tag_added' );

			if ( in_array( $tag_name, $options['tags'] ) ) {
				$this->add_job( $workflow, 'tag_added', $subscriber_id );
			}
		}

	}

	public function published_post( $post_id, $post, $update, $post_before ) {

		$new_status = $post instanceof WP_Post ? $post->post_status : false;
		$old_status = $post_before instanceof WP_Post ? $post_before->post_status : false;

		if ( $new_status == $old_status ) {
			return;
		}

		if ( 'newsletter' == $post->post_type ) {
			return;
		}

		$accepted_status = apply_filters( 'mailster_check_for_autoresponder_accepted_status', 'publish', $post );

		if ( ! is_array( $accepted_status ) ) {
			$accepted_status = array( $accepted_status );
		}

		if ( ! in_array( $new_status, $accepted_status ) ) {
			return;
		}

		$workflows = $this->get_workflows_by_trigger( 'published_post' );
		foreach ( $workflows as $workflow ) {
			$options = mailster( 'automations' )->get_trigger_option( $workflow, 'published_post' );

			$query = isset( $options['query'] ) ? $options['query'] : null;

			// if query is there we need to check it
			if ( $query ) {

				// check correct post type
				if ( $query['postType'] != $post->post_type ) {
					continue;
				}

				// check for authors
				if ( $query['author'] ) {
					$authors = explode( ',', $query['author'] );
					if ( ! in_array( $post->post_author, $authors ) ) {
						continue;
					}
				}

				foreach ( $query['taxQuery'] as $taxonomy => $ids ) {
					$post_terms = get_the_terms( $post->ID, $taxonomy );
					$post_terms = wp_list_pluck( $post_terms, 'term_id' );

					// no post_terms set but required => give up (not passed)
					if ( ! count( array_intersect( $post_terms, $ids ) ) ) {
						break 2; // break out of both loops
					}
				}
			}

			// TODO check for the right step if posts should be skipped

			return $this->add_job( $workflow, 'published_post', null );

		}

	}

	public function date() {

		$workflows = $this->get_workflows_by_trigger( 'date' );

		foreach ( $workflows as $workflow ) {
			$this->run_date( $workflow );
		}

	}

	public function run_date( $workflow ) {

		$options = mailster( 'automations' )->get_trigger_option( $workflow, 'date' );

		$date  = isset( $options['date'] ) ? strtotime( $options['date'] ) : null;
		$field = isset( $options['field'] ) ? $options['field'] : null;

		// get timestamp from today at the time of $date if a usefield is set
		if ( $field ) {
			$date = strtotime( 'today ' . date( 'H:i', $date ) );
		}

		// if date is within one hour
		if ( $date && time() < $date && time() + HOUR_IN_SECONDS > $date ) {
			return $this->add_job( $workflow, 'date', null );
		}

		return false;

	}

	public function anniversary() {

		$workflows = $this->get_workflows_by_trigger( 'anniversary' );

		foreach ( $workflows as $workflow ) {
			$this->run_anniversary( $workflow );
		}

	}

	public function run_anniversary( $workflow ) {

		$options = mailster( 'automations' )->get_trigger_option( $workflow, 'anniversary' );

		$date  = isset( $options['date'] ) ? strtotime( $options['date'] ) : null;
		$field = isset( $options['field'] ) ? $options['field'] : null;

		// get timestamp from today at the time of $date if a userfield is set
		if ( $field ) {
			$date = strtotime( 'today ' . date( 'H:i', $date ) );
		}

		// if date is within one hour
		if ( $date && time() < $date && time() + HOUR_IN_SECONDS > $date ) {
			return $this->add_job( $workflow, 'anniversary', null );
		}

		return false;

	}

	// this runs running on every pageload so make it as fast as possible
	public function front_page_hooks() {

		$triggers = get_option( 'mailster_trigger' );

		// nothing to do
		if ( empty( $triggers ) ) {
			return;
		}

		$links = array_keys( $triggers );

		$matching_links = preg_grep( '|^' . preg_quote( rtrim( $_SERVER['REQUEST_URI'], '/' ) ) . '$|', $links );

		// no matching links
		if ( empty( $matching_links ) ) {
			return;
		}

		$subscriber_id = mailster_get_current_user_id();

		// no current subscriber
		if ( ! $subscriber_id ) {
			return;
		}

		$links = array_values( $matching_links );

		$workflows = $this->get_workflows_by_trigger( 'page_visit' );

		foreach ( $workflows as $workflow ) {
			$options = mailster( 'automations' )->get_trigger_option( $workflow, 'page_visit' );

			// if ( in_array( $list_id, $options['lists'] ) ) {
			$this->add_job( $workflow, 'page_visit', $subscriber_id );
			// }
		}

	}

	private function add_job( $workflow, $trigger, $subscriber_id, $step = null, $timestamp = null ) {

		// only some triggers allow all susbscribers
		if ( ! $subscriber_id ) {
			if ( ! in_array( $trigger, array( 'date', 'anniversary', 'published_post' ) ) ) {
				return false;
			}
		}

		$job = array(
			'subscriber_id' => (int) $subscriber_id,
			'workflow_id'   => (int) $workflow,
			'trigger'       => $trigger,
			'step'          => $step,
			'timestamp'     => $timestamp,
		);

		$success = $this->queue_job( $job );

		if ( $success && ! $subscriber_id ) {
			// run this to get the actual subscribers for the workflow
			// TODO check if working
			do_action( 'mailster_trigger', $workflow, $trigger, $subscriber_id );
		}

		return $success;

	}

	public function queue_jobs__DELETE() {

		foreach ( $this->jobs as $job ) {
			$this->queue_job( $job );
		}

		mailster( 'automations' )->wp_schedule();

	}

	public function bulk_add( $workflow, $trigger, $subscriber_ids, $step, $timestamp = null ) {

		foreach ( (array) $subscriber_ids as $subscriber_id ) {
			$this->add_job( $workflow, $trigger, $subscriber_id, $step, $timestamp );
		}

	}

	private function queue_job( $job ) {

		global $wpdb;

		$job['added'] = time();

		$fields = array_keys( $job );
		$data   = array_values( $job );

		$sql = "INSERT INTO {$wpdb->prefix}mailster_workflows (`" . implode( '`, `', $fields ) . "`) VALUES ('" . implode( "', '", $data ) . "') ON DUPLICATE KEY UPDATE timestamp = values(timestamp)";
		$sql = str_replace( "''", 'NULL', $sql );

		$suppress_errors = $wpdb->suppress_errors( true );

		// if ( $wpdb->insert( "{$wpdb->prefix}mailster_workflows", $job ) ) {
		if ( $wpdb->query( $sql ) ) {
			$success = true;
		} else {
			$success = false;
		}

		$wpdb->suppress_errors( $suppress_errors );

		return $success;

	}

	private function get_workflows_by_trigger( $trigger ) {

		if ( ! ( $workflow_ids = mailster_cache_get( 'workflow_by_trigger_' . $trigger ) ) ) {

			// TODO DO NOT USE get_posts

			$workflow_ids = get_posts(
				array(
					'fields'     => 'ids',
					'post_type'  => 'mailster-workflow',
					'meta_key'   => 'trigger',
					'meta_query' => array(
						array(
							'key'     => 'trigger',
							'value'   => $trigger,
							'compare' => '=',
						),
					),
				)
			);

			mailster_cache_set( 'workflow_by_trigger_' . $trigger, $workflow_ids );

		}

		return $workflow_ids;

	}

}
