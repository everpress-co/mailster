<?php

class MailsterAutomations {

	private $jobs = array();

	public function __construct() {

		// since 5.8
		if ( ! function_exists( 'get_allowed_block_types' ) ) {
			return;
		}

		add_action( 'init', array( &$this, 'register_post_type' ) );
		add_action( 'init', array( &$this, 'register_post_meta' ) );
		add_action( 'init', array( &$this, 'block_init' ) );
		add_action( 'rest_api_init', array( &$this, 'register_block_patterns' ) );
		add_action( 'rest_api_init', array( &$this, 'rest_api_init' ) );
		add_action( 'rest_api_init', array( &$this, 'register_conditions_block' ) );

		add_action( 'admin_print_scripts-edit.php', array( &$this, 'overview_script_styles' ), 1 );
		add_action( 'admin_enqueue_scripts', array( &$this, 'beta_badge' ) );

		add_action( 'enqueue_block_assets', array( &$this, 'block_script_styles' ), 1 );

		add_filter( 'allowed_block_types_all', array( &$this, 'allowed_block_types' ), PHP_INT_MAX, 2 );
		add_filter( 'block_editor_settings_all', array( &$this, 'block_editor_settings' ), PHP_INT_MAX, 2 );
		add_filter( 'block_categories_all', array( &$this, 'block_categories' ) );
		// add_action( 'register_block_type_args', array( &$this, 'register_variations' ), 10, 2 );
		// add_action( 'register_block_type_args', array( &$this, 'register_conditions_variations' ), 10, 2 );

		add_filter( 'manage_mailster-workflow_posts_columns', array( &$this, 'columns' ), 1 );
		add_action( 'manage_mailster-workflow_posts_custom_column', array( &$this, 'columns_content' ), 10, 2 );
		add_filter( 'wp_list_table_class_name', array( &$this, 'wp_list_table_class_name' ), 10, 2 );

		add_action( 'mailster_cron_workflow', array( &$this, 'wp_schedule' ) );

		add_action( 'mailster_workflow', array( &$this, '_run_delayed_workflow' ), 10, 3 );

		add_action( 'wp_after_insert_post', array( &$this, 'save_workflow' ), 10, 4 );

		add_action( 'publish_mailster-workflow', array( &$this, 'limit_posts' ), 10, 3 );

		add_action( 'classic_editor_plugin_settings', array( &$this, 'enable_on_classic_editor' ) );
		add_filter( 'display_post_states', array( &$this, 'display_post_states' ), 10, 2 );

		add_shortcode( 'newsletter_block_form', array( &$this, 'block_forms_shortcode' ) );
	}


	public function enable_on_classic_editor( $settings ) {
		if ( ! $settings && isset( $_GET['post'] ) && get_post_type( (int) $_GET['post'] ) === 'mailster-workflow' ) {
			$settings = array( 'editor' => 'block' );
		}
		if ( ! $settings && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'mailster-workflow' ) {
			$settings = array( 'editor' => 'block' );
		}

		return $settings;
	}

	public function get_actions() {

		$actions = array(
			array(
				'id'    => 'update_field',
				'icon'  => 'replace',
				'label' => esc_html__( 'Update Custom Field', 'mailster' ),
				'info'  => esc_html__( 'Update subscribers field with custom value.', 'mailster' ),
			),
			array(
				'id'    => 'add_list',
				'icon'  => 'list',
				'label' => esc_html__( 'Add to List(s)', 'mailster' ),
				'info'  => esc_html__( 'Adds subscribers to one or more lists.', 'mailster' ),
			),
			array(
				'id'    => 'remove_list',
				'icon'  => 'list',
				'label' => esc_html__( 'Remove from List(s)', 'mailster' ),
				'info'  => esc_html__( 'Removes subscribers from one or more lists.', 'mailster' ),
			),
			array(
				'id'    => 'add_tag',
				'icon'  => 'tag',
				'label' => esc_html__( 'Add Tag(s)', 'mailster' ),
				'info'  => esc_html__( 'Adds one ore more tags to a subscriber.', 'mailster' ),
			),
			array(
				'id'    => 'remove_tag',
				'icon'  => 'tag',
				'label' => esc_html__( 'Remove Tag(s)', 'mailster' ),
				'info'  => esc_html__( 'Removes one or more tags from a subscriber.', 'mailster' ),
			),
			array(
				'id'    => 'unsubscribe',
				'icon'  => 'external',
				'label' => esc_html__( 'Unsubscribe', 'mailster' ),
				'info'  => esc_html__( 'Unsubscribes a subscriber.', 'mailster' ),
			),
			array(
				'id'    => 'webhook',
				'icon'  => 'cog',
				'label' => esc_html__( 'Webhook', 'mailster' ),
				'info'  => esc_html__( 'Triggers a webhook.', 'mailster' ),
			),
		);

		return apply_filters( 'mailster_workflow_actions', $actions );
	}



	public function get_triggers() {

		$triggers = array(
			array(
				'id'    => 'list_add',
				'icon'  => 'formatListBullets',
				'label' => esc_html__( 'Subscriber added to list', 'mailster' ),
				'info'  => esc_html__( 'When a subscriber joins a list', 'mailster' ),
			),
			array(
				'id'    => 'tag_added',
				'icon'  => 'tag',
				'label' => esc_html__( 'Tag added', 'mailster' ),
				'info'  => esc_html__( 'When a Tag is added to a subscriber', 'mailster' ),
			),
			array(
				'id'    => 'updated_field',
				'icon'  => 'update',
				'label' => esc_html__( 'Field updated', 'mailster' ),
				'info'  => esc_html__( 'When a Field is added or updated by a subscriber', 'mailster' ),
			),
			array(
				'id'    => 'form_conversion',
				'icon'  => 'commentEditLink',
				'label' => esc_html__( 'Form Conversion', 'mailster' ),
				'info'  => esc_html__( 'When someone fills out and submits a form', 'mailster' ),
			),
			array(
				'id'    => 'date',
				'icon'  => 'calendar',
				'label' => esc_html__( 'Specific date', 'mailster' ),
				'info'  => esc_html__( 'On a specific date', 'mailster' ),
			),
			array(
				'id'    => 'anniversary',
				'icon'  => 'calendar',
				'label' => esc_html__( 'Anniversary', 'mailster' ),
				'info'  => esc_html__( 'On an anniversary of a date', 'mailster' ),
			),
			array(
				'id'    => 'link_click',
				'icon'  => 'link',
				'label' => esc_html__( 'Click a link', 'mailster' ),
				'info'  => esc_html__( 'When a subscriber clicks a link', 'mailster' ),
			),
			array(
				'id'    => 'page_visit',
				'icon'  => 'page',
				'label' => esc_html__( 'Visits a page', 'mailster' ),
				'info'  => esc_html__( 'When a user visits a given page', 'mailster' ),
			),
			array(
				'id'    => 'hook',
				'icon'  => 'shortcode',
				'label' => esc_html__( 'Custom Hook', 'mailster' ),
				'info'  => esc_html__( 'When a custom hook is called', 'mailster' ),
			),
			array(
				'id'       => 'opened_campaign',
				'icon'     => 'key',
				'label'    => esc_html__( 'Open a campaign', 'mailster' ),
				'info'     => esc_html__( 'When a users opens a campaign', 'mailster' ),
				'disabled' => true,
				'reason'   => esc_html__( 'Comming soon!', 'mailster' ),
			),
		);

		return apply_filters( 'mailster_workflow_triggers', $triggers );
	}


	public function get_limit() {

		$limit = 3;
		if ( mailster_freemius()->is_plan( 'plus', true ) ) {
			$limit = 10;
		} elseif ( mailster_freemius()->is_plan( 'pro', true ) ) {
			$limit = false;
		}

		return $limit;
	}



	public function limit_reached() {

		$limit = $this->get_limit();

		if ( ! $limit ) {
			return false;
		}

		global $wpdb;

		$query = "SELECT COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s";

		$count = $wpdb->get_var( $wpdb->prepare( $query, 'mailster-workflow', 'publish' ) );

		if ( $count <= $limit ) {
			return false;
		}

		return true;

	}


	public function limit_posts( $post_id, $post, $old_status ) {

		if ( $this->limit_reached() ) {
			$post = array(
				'ID'          => $post_id,
				'post_status' => 'private',
			);
			wp_update_post( $post );

			$msg  = '<h2>' . sprintf( esc_html__( 'Workflow limit reached!', 'mailster' ) ) . '</h2>';
			$msg .= '<p>' . sprintf( esc_html__( 'You have reached the maximum number of %1$d active workflows! Please upgrade %2$s to enable more workflows.', 'mailster' ), $this->get_limit(), '<a href="' . admin_url( '?page=mailster-pricing' ) . '">' . esc_html__( 'your plan', 'mailster' ) . '</a>' ) . '</p>';
			$msg .= '<p><a href="' . admin_url( '?page=mailster-pricing' ) . '" class="button button-primary">' . esc_html__( 'Upgrade now', 'mailster' ) . '</a> <a href="https://docs.mailster.co/#/automations-overview" class="button button-secondary external">' . esc_html__( 'All Pro Features', 'mailster' ) . '</a></p>';
			mailster_notice( $msg, 'warning', false, 'mailster-workflow-limit-reached', true );

		} else {
			mailster_remove_notice( 'mailster-workflow-limit-reached' );
		}

	}


	public function do_workflows( $workflow_ids, $trigger, $subscriber ) {
		foreach ( (array) $workflow_ids as $workflow_id ) {
			$this->do_workflow( $workflow_id, $trigger, $subscriber );
		}
	}

	public function do_workflow( $workflow_id, $trigger, $subscriber ) {

		return do_action( 'mailster_trigger', $workflow_id, $trigger, $subscriber );

	}

	public function save_workflow( $workflow_id, $post, $update, $post_before ) {

		if ( get_post_type( $post ) !== 'mailster-workflow' ) {
			return;
		}

		// TODO CHECK CHANGES and remoe triggers
		$blocks        = parse_blocks( $post->post_content );
		$blocks_before = $post_before ? parse_blocks( $post_before->post_content ) : null;

		// TODO run trigger for this Workflow only
		do_action( 'mailster_trigger_date' );
		do_action( 'mailster_trigger_anniversary' );

		mailster_remove_notice( 'workflow_error_' . $workflow_id );

		$this->update_trigger_option();
	}


	public function get_trigger_option( $workflow, $trigger ) {

		$workflow = get_post( $workflow );
		$blocks   = parse_blocks( $workflow->post_content );

		foreach ( $blocks as $block ) {
			if ( $block['blockName'] !== 'mailster-workflow/triggers' ) {
				continue;
			}

			foreach ( $block['innerBlocks'] as $innerBlock ) {
				if ( empty( $innerBlock ) ) {
					continue;
				}
				if ( ! isset( $innerBlock['attrs']['trigger'] ) ) {
					continue;
				}
				if ( $innerBlock['attrs']['trigger'] !== $trigger ) {
					continue;
				}

				return $innerBlock['attrs'];

			}
		}

		return false;

	}


	public function update_trigger_option() {

		global $wpdb;

		$sql = "SELECT post_id FROM `{$wpdb->postmeta}` AS postmeta LEFT JOIN {$wpdb->posts} AS posts ON postmeta.post_id = posts.id WHERE posts.post_type = %s AND posts.post_status = 'publish' AND postmeta.meta_key = 'trigger' AND postmeta.meta_value = %s;";

		$sql = $wpdb->prepare( $sql, 'mailster-workflow', 'page_visit' );

		$workflow_ids = $wpdb->get_col( $sql );

		$store = array();

		foreach ( $workflow_ids as $workflow_id ) {

			$options = $this->get_trigger_option( $workflow_id, 'page_visit' );

			if ( isset( $options['pages'] ) ) {
				foreach ( $options['pages'] as $page ) {
					$page = trim( $page, '/' );
					if ( ! isset( $store[ $page ] ) ) {
						$store[ $page ] = array();
					}
					$store[ $page ][] = $workflow_id;
					$store[ $page ]   = array_values( array_unique( $store[ $page ] ) );
				}
			}
		}

		update_option( 'mailster_trigger', $store );

	}

	public function wp_schedule() {
		global $wpdb;

		// time to schedule upfront ins seconds
		$queue_upfront = HOUR_IN_SECONDS;

		// limit to not overload the WP cron
		$limit = 5000;

		$now = time();

		$entries = $wpdb->get_results( $wpdb->prepare( "SELECT workflow_id, `trigger`, step, IFNULL(`timestamp`, %d) AS timestamp FROM {$wpdb->prefix}mailster_workflows WHERE (`timestamp` <= %d OR `timestamp` IS NULL) AND finished = 0 AND subscriber_id IS NOT NULL GROUP BY workflow_id, `timestamp` ORDER BY `timestamp` LIMIT %d", $now, $now + $queue_upfront, $limit ) );

		if ( empty( $entries ) ) {
			return;
		}

		foreach ( $entries as $i => $entry ) {
			$args = array(
				'workflow_id' => (int) $entry->workflow_id,
				'trigger'     => $entry->trigger,
				'step'        => $entry->step,
			);

			// run now if timestamp is now
			if ( $entry->timestamp <= $now ) {
				call_user_func_array( array( $this, 'run_all' ), $args );
			} else {
				// add the timestamp to allow multiple events every minute
				$args['timestamp'] = (int) floor( $entry->timestamp / 60 ) * 60;
				wp_schedule_single_event( $entry->timestamp, 'mailster_workflow', $args, true );
			}
		}

		return true;

	}

	// only used for mailster_workflow hook
	public function _run_delayed_workflow( $workflow_id, $trigger, $step ) {
		$this->run_all( $workflow_id, $trigger, $step );
	}


	private function run_async( $workflow, $trigger, $step = null ) {

		foreach ( (array) $subscribers as $subscriber ) {
			$args = array(
				'id'      => $workflow,
				'trigger' => $trigger,
				'step'    => $step,
			);

			$this->jobs[] = $args;
		}

		add_action( 'shutdown', array( &$this, 'schedule_async_jobs' ) );

	}

	public function schedule_async_jobs() {

		foreach ( $this->jobs as $i => $job ) {
			wp_schedule_single_event( time() + floor( $i / 5 ), 'mailster_workflow', $job );
		}

	}

	private function run_all( $workflow_id, $trigger, $step = null ) {

		require_once MAILSTER_DIR . 'classes/workflow.class.php';

		$workflow = get_post( $workflow_id );

		if ( ! $workflow ) {
			return;
		}

		global $wpdb;

		// do not run more than that at a time
		/**
		 * filter the limit of subscribers per workflow run
		 *
		 * @param int $limit default 10000
		 */
		$limit = apply_filters( 'mailster_workflow_limit', 10000 );

		/**
		 * filter the max runtime of a workflow
		 *
		 * @param int $max_execution in seconds default 15
		 */
		$max_execution = apply_filters( 'mailster_workflow_runtime', 15 );

		$sql  = "SELECT subscriber_id FROM {$wpdb->prefix}mailster_workflows";
		$sql .= $wpdb->prepare( ' WHERE workflow_id = %d AND finished = 0', $workflow->ID );

		// prepeare doesn't support NULL https://core.trac.wordpress.org/ticket/12819
		$sql .= ' AND `trigger` ' . ( $trigger ? $wpdb->prepare( '= %s', $trigger ) : 'IS NULL' );
		$sql .= ' AND `step` ' . ( $step ? $wpdb->prepare( '= %s', $step ) : 'IS NULL' );

		// timestamp is either null or max now
		$sql .= ' AND (`timestamp` <= %d OR `timestamp` IS NULL)';
		$sql .= ' LIMIT %d';

		$subscribers = $wpdb->get_col( $wpdb->prepare( $sql, time(), $limit ) );

		// maybe remove notices
		mailster_remove_notice( 'workflow_error_' . $workflow->ID );

		$start = microtime( true );
		foreach ( (array) $subscribers as $subscriber ) {

			$wf = new MailsterWorkflow( $workflow, $trigger, $subscriber, $step );
			// messure time
			$result = $wf->run();

			// stop if it takes to long to prevent timeouts
			$endtime = microtime( true ) - $start;
			if ( $max_execution && $endtime > $max_execution ) {
				break;
			}
		}
		add_action( '_shutdown', array( &$this, 'wp_schedule' ) );

	}

	public function get_numbers( $workflow ) {

		$workflow = get_post( $workflow );

		if ( ! $workflow ) {
			return;
		}

		if ( false === ( $numbers = mailster_cache_get( 'workflow_numbers_' . $workflow->ID ) ) ) {

			global $wpdb;

			$entries = $wpdb->get_results( $wpdb->prepare( "SELECT step, COUNT(*) AS count FROM {$wpdb->prefix}mailster_workflows WHERE workflow_id = %d AND step IS NOT NULL GROUP BY step;", $workflow->ID ) );

			$active = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) AS count FROM {$wpdb->prefix}mailster_workflows WHERE workflow_id = %d AND timestamp IS NOT NULL AND finished = 0 AND step IS NOT NULL;", $workflow->ID ) );

			$finished = $wpdb->get_var( $wpdb->prepare( "SELECT  COUNT(*) AS count FROM {$wpdb->prefix}mailster_workflows WHERE workflow_id = %d AND finished != 0;", $workflow->ID ) );

			$numbers = array(
				'steps'       => array(),
				'active'      => (int) $active,
				'finished'    => (int) $finished,
				'total'       => (int) $finished + $active,
				'sent'        => 0,
				'opens'       => 0,
				'clicks'      => 0,
				'unsubs'      => 0,
				'bounces'     => 0,
				'open_rate'   => 0,
				'click_rate'  => 0,
				'unsub_rate'  => 0,
				'bounce_rate' => 0,
			);

			foreach ( $entries as $entry ) {
				if ( ! isset( $numbers['steps'][ $entry->step ] ) ) {
					$return['steps'][ $entry->step ] = array(
						'count'  => 0,
						'errors' => array(),
					);
				}
				$numbers['steps'][ $entry->step ]['count'] = $entry->count;
			}

			$workflow_campaigns = $this->get_workflow_campaigns( $workflow );

			foreach ( $workflow_campaigns as $workflow_campaign ) {
				$actions = mailster( 'actions' )->get_by_campaign( $workflow_campaign );

				$numbers['sent']    += $actions['sent'];
				$numbers['opens']   += $actions['opens'];
				$numbers['clicks']  += $actions['clicks'];
				$numbers['unsubs']  += $actions['unsubs'];
				$numbers['bounces'] += $actions['bounces'];
			}

			$numbers['open_rate']   = $numbers['sent'] ? $numbers['opens'] / $numbers['sent'] : 0;
			$numbers['click_rate']  = $numbers['opens'] ? $numbers['clicks'] / $numbers['opens'] : 0;
			$numbers['unsub_rate']  = $numbers['sent'] ? $numbers['unsubs'] / $numbers['sent'] : 0;
			$numbers['bounce_rate'] = $numbers['sent'] ? $numbers['bounces'] / $numbers['sent'] : 0;

			mailster_cache_set( 'workflow_numbers_' . $workflow->ID, $numbers );

		}

		return $numbers;

	}

	public function get_workflow_campaigns( $workflow ) {

		$workflow = get_post( $workflow );

		if ( ! $workflow ) {
			return false;
		}

		$raw_content = $workflow->post_content;

		$ids = array();

		if ( preg_match_all( '/mailster-workflow\/email(.*)"campaign":(\d+)/', $raw_content, $matches ) ) {
			$ids = array_unique( $matches[2] );
		}

		return $ids;

	}

	public function register_conditions_block() {

		$block_types = WP_Block_Type_Registry::get_instance()->get_all_registered();

		$types = array_keys( $block_types );

		// TODO load only for automation post type

		if ( ! in_array( 'mailster-workflow/conditions', $types ) ) {
			register_block_type( MAILSTER_DIR . 'build/workflows/conditions', array( 'render_callback' => array( $this, 'render_conditions' ) ) );
		}
	}

	public function rest_api_init() {

		include MAILSTER_DIR . 'classes/rest-controller/rest.automations.class.php';

		$controller = new Mailster_REST_Automations_Controller();
		$controller->register_routes();

	}

	public function beta_notice() {
		$msg  = '<h2>Welcome to the new Automations page.</h2>';
		$msg .= '<p>Creating forms for Mailster gets easier and more flexible. Utilize the WordPress Block Editor (Gutenberg) to create you custom, feature rich forms.</p>';
		$msg .= '<p><strong>Automations are currently in beta version. Some features are subject to change before the stable release.</strong></p>';
		$msg .= '<p><a href="' . admin_url( 'post-new.php?post_type=mailster-workflow' ) . '" class="button button-primary">' . esc_html__( 'Create Automation' ) . '</a> <a href="https://docs.mailster.co/#/automations-overview" class="button button-secondary external">Check out our guide</a> or <a href="https://github.com/everpress-co/mailster-automations/issues" class="button button-link external">Submit feedback on Github</a></p>';
		mailster_notice( $msg, 'info', true, 'mailster-workflow_beta_notice', true, 'edit-mailster-workflow' );
	}

	public function beta_badge() {
		wp_add_inline_style(
			'admin-menu',
			"#menu-posts-newsletter
				a[href='edit.php?post_type=mailster-workflow']::after {
					content: 'Beta';
					display: inline-block;
					vertical-align: top;
					box-sizing: border-box;
					margin: 1px 5px -1px 5px;
					padding: 0 5px;
					min-width: 18px;
					height: 18px;
					border-radius: 9px;
					background-color: #d63638;
					color: #fff;
					font-size: 11px;
					line-height: 1.6;
					text-align: center;
					z-index: 26;
				}"
		);
	}


	public function display_post_states( $post_states, $post ) {

		if ( 'mailster-workflow' != $post->post_type ) {
			return $post_states;
		}

		if ( $post->post_status == 'private' ) {
			$post_states['private'] = esc_html__( 'Inactive', 'mailster' );
		} elseif ( $post->post_status == 'publish' ) {
			$post_states['publish'] = esc_html__( 'Active', 'mailster' );
		}

		return $post_states;
	}


	public function wp_list_table_class_name( $class_name, $args ) {

		if ( $args['screen']->id !== 'edit-mailster-workflow' ) {
			return $class_name;
		}

		require_once MAILSTER_DIR . 'classes/automation.table.class.php';
		$class_name = 'Mailster_Automations_Table';

		return $class_name;

	}


	public function columns( $columns ) {

		$columns = array(
			'cb'      => '<input type="checkbox" />',
			'title'   => esc_html__( 'Title', 'mailster' ),
			'status'  => esc_html__( 'Status', 'mailster' ),
			'total'   => esc_html__( 'Total', 'mailster' ),
			'open'    => esc_html__( 'Open', 'mailster' ),
			'click'   => esc_html__( 'Clicks', 'mailster' ),
			'unsubs'  => esc_html__( 'Unsubscribes', 'mailster' ),
			'bounces' => esc_html__( 'Bounces', 'mailster' ),
			'date'    => esc_html__( 'Date', 'mailster' ),
		);
		return $columns;
	}


	/**
	 *
	 *
	 * @param unknown $column
	 * @return unknown
	 */
	public function get_columns_content( $column ) {

		ob_start();

		$this->columns_content( $column );

		$output = ob_get_contents();

		ob_end_clean();

		return $output;
	}


	/**
	 *
	 *
	 * @param unknown $column
	 */
	public function columns_content( $column, $post_id ) {

		global $post, $wp_post_statuses;

		if ( ! in_array( $column, array( 'status', 'total', 'open', 'click', 'unsubs', 'bounces' ) ) ) {
			// return;
		}

		$is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

		// if ( ! $is_ajax && $column != 'status' && wp_script_is( 'heartbeat', 'registered' ) ) {
		// echo '<span class="skeleton-loading"></span>';
		// if ( in_array( $column, array( 'open', 'click', 'unsubs', 'bounces' ) ) ) {
		// echo '<br><span class="skeleton-loading nonessential"></span>';
		// }
		// return;
		// }

		$error = ini_get( 'error_reporting' );
		error_reporting( E_ERROR );

		$now        = time();
		$timeformat = mailster( 'helper' )->timeformat();

		switch ( $column ) {

			case 'status':
				$numbers = $this->get_numbers( $post_id );

				printf( '<div class="s-status"><span>%s</span> %s</div>', number_format_i18n( $numbers['active'] ), esc_html__( 'active', 'mailster' ) );
				printf( '<div class="s-status"><span>%s</span> %s</div>', number_format_i18n( $numbers['finished'] ), esc_html__( 'finished', 'mailster' ) );
				printf( '<div class="s-status"><span>%s</span> %s</div>', number_format_i18n( $numbers['total'] ), esc_html__( 'total', 'mailster' ) );

				break;

			case 'total':
				$numbers = $this->get_numbers( $post_id );
				if ( ! $numbers['sent'] ) {
					return;
				}
				$total = $numbers['sent'];
				echo number_format_i18n( $total );

				break;

			case 'open':
				$numbers = $this->get_numbers( $post_id );
				if ( ! $numbers['sent'] ) {
					return;
				}

				echo '<span class="s-opens">' . number_format_i18n( $numbers['opens'] ) . '</span>/<span class="tiny s-sent">' . number_format_i18n( $numbers['sent'] ) . '</span>';
				$rate = round( $numbers['open_rate'] * 100, 2 );
				echo "<br><span title='" . sprintf( esc_attr__( '%s of sent', 'mailster' ), $rate . '%' ) . "' class='nonessential'>";
				echo ' (' . $rate . '%)';
				echo '</span>';
				echo '<br>';

				break;

			case 'click':
				$numbers = $this->get_numbers( $post_id );
				if ( ! $numbers['sent'] ) {
					return;
				}
				$clicks = $numbers['clicks'];
				$rate   = round( $numbers['click_rate'] * 100, 2 );
				echo number_format_i18n( $clicks );
				if ( $rate ) {
					echo "<br><span class='nonessential'>(<span title='" . sprintf( esc_attr__( '%s of sent', 'mailster' ), $rate . '%' ) . "'>";
					echo '' . $rate . '%';
					echo '</span>)</span>';
				} else {
					echo "<br><span title='" . sprintf( esc_attr__( '%s of sent', 'mailster' ), $rate . '%' ) . "' class='nonessential'>";
					echo ' (' . $rate . '%)';
					echo '</span>';
				}
				echo '<br>';

				break;

			case 'unsubs':
				$numbers = $this->get_numbers( $post_id );
				if ( ! $numbers['sent'] ) {
					return;
				}
				$unsubscribes = $numbers['unsubs'];
				$rate         = round( $numbers['unsub_rate'] * 100, 2 );

				echo number_format_i18n( $unsubscribes );
				if ( $rate ) {
					echo "<br><span class='nonessential'>(<span title='" . sprintf( esc_attr__( '%s of sent', 'mailster' ), $rate . '%' ) . "'>";
					echo '' . $rate . '%';
					echo '</span>)</span>';
				} else {
					echo "<br><span title='" . sprintf( esc_attr__( '%s of sent', 'mailster' ), $rate . '%' ) . "' class='nonessential'>";
					echo ' (' . $rate . '%)';
					echo '</span>';
				}
				echo '<br>';

				break;

			case 'bounces':
				$numbers = $this->get_numbers( $post_id );
				if ( ! $numbers['sent'] ) {
					return;
				}
				$bounces = $numbers['bounces'];
				$rate    = round( $numbers['bounce_rate'] * 100, 2 );
				echo number_format_i18n( $bounces );
				echo "<br><span title='" . sprintf( esc_attr__( '%s of totals', 'mailster' ), $rate . '%' ) . "' class='nonessential'>";
				echo ' (' . $rate . '%)';
				echo '</span>';
				echo '<br>';

				break;

		}
		error_reporting( $error );
	}


	public function ___custom_column( $column, $post_id ) {

		switch ( $column ) {
			case 'active':
				$numbers = $this->get_numbers( $post_id );
				echo number_format_i18n( $numbers['active'] );
				break;
			case 'finished':
				$numbers = $this->get_numbers( $post_id );
				echo number_format_i18n( $numbers['finished'] );
				break;
			case 'total':
				$numbers = $this->get_numbers( $post_id );
				echo number_format_i18n( $numbers['total'] );
				echo '<pre>' . print_r( $numbers, true ) . '</pre>';
				break;
			default:
				break;
		}

	}

	public function register_post_type() {

		$labels = array(
			'name'                     => _x( 'Workflows', 'Post Type General Name', 'mailster' ),
			'singular_name'            => _x( 'Workflow', 'Post Type Singular Name', 'mailster' ),
			'menu_name'                => __( 'Automations', 'mailster' ),
			'attributes'               => __( 'Workflow Attributes', 'mailster' ),
			'all_items'                => __( 'Automations', 'mailster' ),
			'add_new_item'             => __( 'Add New Workflow', 'mailster' ),
			'add_new'                  => __( 'Add New Workflow', 'mailster' ),
			'new_item'                 => __( 'New Workflow', 'mailster' ),
			'edit_item'                => __( 'Edit Workflow', 'mailster' ),
			'update_item'              => __( 'Update Workflow', 'mailster' ),
			'view_item'                => __( 'View Workflow', 'mailster' ),
			'view_items'               => __( 'View Workflows', 'mailster' ),
			'search_items'             => __( 'Search Workflow', 'mailster' ),
			'not_found'                => __( 'Not found', 'mailster' ),
			'not_found_in_trash'       => __( 'Not found in Trash', 'mailster' ),
			'items_list'               => __( 'Workflows list', 'mailster' ),
			'items_list_navigation'    => __( 'Workflows list navigation', 'mailster' ),
			'filter_items_list'        => __( 'Filter forms list', 'mailster' ),
			'item_published'           => __( 'Workflow published', 'mailster' ),
			'item_published_privately' => __( 'Workflow published privately.', 'mailster' ),
			'item_reverted_to_draft'   => __( 'Workflow reverted to draft.', 'mailster' ),
			'item_scheduled'           => __( 'Workflow scheduled.', 'mailster' ),
			'item_updated'             => __( 'Workflow updated.', 'mailster' ),

		);
		$capabilities = array(
			'edit_post'          => 'mailster_edit_form',
			'read_post'          => 'mailster_read_form',
			'delete_post'        => 'mailster_delete_forms',
			'edit_posts'         => 'mailster_edit_forms',
			'edit_others_posts'  => 'mailster_edit_others_forms',
			'publish_posts'      => 'mailster_publish_forms',
			'read_private_posts' => 'mailster_read_private_forms',
		);
		$args         = array(
			'label'               => __( 'Automation', 'mailster' ),
			'description'         => __( 'Newsletter Automation', 'mailster' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'custom-fields' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=newsletter',
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => true,
			'can_export'          => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'rewrite'             => false,
			'show_in_rest'        => true,

		);
		register_post_type( 'mailster-workflow', $args );

	}

	public function register_post_meta() {

		register_post_meta(
			'mailster-workflow',
			'trigger',
			array(
				'type'         => 'string',
				'show_in_rest' => true,
				'single'       => false,

			)
		);

		register_post_meta(
			'mailster-workflow',
			'enddate',
			array(
				'type'         => 'string',
				'show_in_rest' => true,
				'single'       => true,

			)
		);

	}

	public function get( $id ) {
		$post = get_post( $id );
		if ( 'mailster-workflow' !== $post->post_type ) {
			return false;
		}
		return $post;
	}

	public function block_init() {

		if ( ! is_admin() ) {
			return;
		}
		global $pagenow;
		$typenow = '';

		// from https://www.designbombs.com/registering-gutenberg-blocks-for-custom-post-type/
		if ( 'post-new.php' === $pagenow ) {
			if ( isset( $_REQUEST['post_type'] ) && post_type_exists( $_REQUEST['post_type'] ) ) {
				$typenow = sanitize_key( $_REQUEST['post_type'] );
			};
		} elseif ( 'post.php' === $pagenow ) {
			if ( isset( $_GET['post'] ) && isset( $_POST['post_ID'] ) && (int) $_GET['post'] !== (int) $_POST['post_ID'] ) {
				// Do nothing
			} elseif ( isset( $_GET['post'] ) ) {
				$post_id = (int) $_GET['post'];
			} elseif ( isset( $_POST['post_ID'] ) ) {
				$post_id = (int) $_POST['post_ID'];
			}
			if ( $post_id ) {
				if ( $post = get_post( $post_id ) ) {
					$typenow = $post->post_type;
				}
			}
		}

		if ( $typenow === 'mailster-workflow' ) {

			$blocks = $this->get_blocks();

			foreach ( $blocks as $block ) {
				$args = array();

				register_block_type( $block );
			}
		}

	}

	public function render_conditions( $args, $content, WP_Block $block ) {

		$conditions = array();
		if ( isset( $block->attributes['conditions'] ) ) {
			$conditions = $block->attributes['conditions'];

			wp_parse_str( $conditions, $params );
			$conditions = isset( $params['conditions'] ) ? $params['conditions'] : array();

		}
		$render = isset( $block->attributes['render'] ) && $block->attributes['render'];
		$plain  = isset( $block->attributes['plain'] ) && $block->attributes['plain'];

		$mailster_conditions = mailster( 'conditions' );

		// set the campaings for the current workflow
		if ( isset( $block->attributes['emails'] ) ) {
			$mailster_conditions->set_workflow_campaigns( $block->attributes['emails'] );
		}

		ob_start();
		if ( $render ) {
			$mailster_conditions->render( $conditions, true, $plain );
		} else {
			$mailster_conditions->view( $conditions, false );
		}
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	private function get_blocks() {
		$blocks = glob( MAILSTER_DIR . 'build/workflows/*/block.json' );
		return $blocks;
	}

	public function overview_script_styles() {

		$post_type = get_post_type();
		if ( ! $post_type ) {
			$post_type = get_current_screen()->post_type;
		}

		if ( 'mailster-workflow' != $post_type ) {
			return;
		}

		$suffix = '';

		do_action( 'mailster_admin_header' );

		wp_enqueue_style( 'mailster-automations-overview', MAILSTER_URI . 'assets/css/automations-overview' . $suffix . '.css', array(), MAILSTER_VERSION );

	}

	public function block_script_styles() {

		if ( 'mailster-workflow' != get_post_type() ) {
			return;
		}

		$suffix = '';

		do_action( 'mailster_admin_header' );

		wp_enqueue_style( 'mailster-automations-block-editor', MAILSTER_URI . 'assets/css/automations-blocks-editor' . $suffix . '.css', array(), MAILSTER_VERSION );

		wp_enqueue_style( 'mailster-conditions', MAILSTER_URI . 'assets/css/conditions-style' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_enqueue_script( 'mailster-conditions', MAILSTER_URI . 'assets/js/conditions-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );

	}

	public function block_editor_settings( $editor_settings, $block_editor_context ) {

		if ( get_post_type( $block_editor_context->post ) !== 'mailster-workflow' ) {
			return $editor_settings;
		}

		// remove all third party styles (as mutch as possible)
		$editor_settings['defaultEditorStyles'] = array();
		$editor_settings['styles']              = array();

		// disable code editor
		$editor_settings['codeEditingEnabled'] = defined( 'WP_DEBUG' ) && WP_DEBUG;

		return $editor_settings;

	}

	public function allowed_block_types( $allowed_block_types, $context ) {

		// just skip if not on our cpt
		if ( 'mailster-workflow' != get_post_type() || $context->name !== 'core/edit-post' ) {
			return $allowed_block_types;
		}

		$block_types = WP_Block_Type_Registry::get_instance()->get_all_registered();
		$types       = array_keys( $block_types );

		// only mailster workflow blocks
		$types = preg_grep( '/^(mailster-workflow)\//', $types );

		return apply_filters( 'mailster_automations_allowed_block_types', array_values( $types ) );

	}

	public function block_categories( $categories ) {

		if ( 'mailster-workflow' != get_post_type() ) {
			return $categories;
		}

		return array_merge(
			array(
				array(
					'slug'  => 'mailster-automation-fields',
					'title' => __( 'Newsletter Automation Fields', 'mailster' ),
				),
				array(
					'slug'  => 'mailster-automation-actions',
					'title' => __( 'Newsletter Action Fields', 'mailster' ),
				),
			),
			$categories
		);
	}


	public function register_conditions_variations( $args, $block_type ) {
		if ( $block_type !== 'mailster-workflow/condition' ) {
			return $args;
		}

		$args['variations'] = array(
			array(
				'name'       => 'fullfilled',
				'title'      => 'FULLFILLED',
				'icon'       => 'update',
				// 'scope'      => array( 'block' ),
				'attributes' => array(
					'fulfilled' => true,
				),
			),
			array(
				'name'       => 'not_fullfilled',
				'title'      => 'NOT FULLFILED',
				'icon'       => 'archive',
				// 'scope'      => array( 'block' ),
				'attributes' => array(
					'fulfilled' => false,
				),
			),

		);

		return $args;
	}


	public function register_variations( $args, $block_type ) {

		if ( $block_type !== 'mailster-workflow/action' ) {
			return $args;
		}

		$actions = mailster( 'automations' )->get_actions();

		$args['variations'] = array();

		foreach ( $actions as $action ) {

			$args['variations'][] = array(
				'name'       => $action['id'],
				'title'      => $action['label'],
				'icon'       => $action['icon'],
				// 'scope'      => array( 'block' ),
				'attributes' => array(
					'action' => $action['id'],
				),
			);
		}

		return $args;
	}


	public function register_block_patterns() {

		$query = wp_parse_url( wp_get_referer(), PHP_URL_QUERY );

		if ( ! $query || false === strpos( $query, 'post_type=mailster-workflow' ) ) {
			return;
		}

		register_block_pattern_category( 'mailster-automations', array( 'label' => __( 'Mailster Automations', 'mailster' ) ) );
		register_block_pattern_category( 'mailster-custom-category', array( 'label' => __( 'Mailster Automations', 'mailster' ) ) );

		include_once MAILSTER_DIR . 'patterns/workflows.php';

	}

	private function update_metas( $subscriber_ids, $campaign_id = 0, $key = null, $value = null ) {
		if ( ! is_array( $subscriber_ids ) ) {
			$subscriber_ids = array( $subscriber_ids );
		}

		$subscriber_ids = array_filter( $subscriber_ids, 'is_numeric' );

		$success = true;

		foreach ( $subscriber_ids as $subscriber_id ) {
			$success = $success && $this->update_meta( $subscriber_id, $campaign_id, $key, $value );
		}

		return $success;
	}

	public function on_install( $new ) {

		if ( $new ) {
			update_option( 'mailster_trigger', '' );
		}

	}

	public function block_forms_shortcode( $atts, $content ) {

		return $this->render_form_with_options( $atts['id'], array(), false );

	}

}
