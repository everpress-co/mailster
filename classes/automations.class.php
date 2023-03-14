<?php

class MailsterAutomations {

	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ) );
		add_action( 'init', array( &$this, 'register_post_type' ) );

	}


	public function init() {

		// add_action( 'transition_post_status', array( &$this, 'maybe_queue_post_changed' ), 10, 3 );

		// add_action( 'mailster_finish_campaign', array( &$this, 'remove_revisions' ) );

		// add_action( 'mailster_auto_post_thumbnail', array( &$this, 'get_post_thumbnail' ), 10, 2 );

		// add_action( 'admin_menu', array( &$this, 'remove_meta_boxs' ) );
		// add_action( 'admin_menu', array( &$this, 'autoresponder_menu' ), 20 );
		// add_filter( 'display_post_states', array( &$this, 'display_post_states' ), 10, 2 );

		// add_action( 'save_post', array( &$this, 'save_campaign' ), 10, 3 );
		// add_filter( 'wp_insert_post_data', array( &$this, 'wp_insert_post_data' ), 1, 2 );
		// add_filter( 'post_updated_messages', array( &$this, 'updated_messages' ) );

		// add_action( 'before_delete_post', array( &$this, 'maybe_cleanup_after_delete' ) );

		// add_filter( 'pre_post_content', array( &$this, 'remove_kses' ) );

		// add_filter( 'heartbeat_received', array( &$this, 'heartbeat' ), 9, 2 );

		// add_filter( 'admin_post_thumbnail_html', array( &$this, 'add_post_thumbnail_link' ), 10, 2 );
		// add_filter( 'admin_post_thumbnail_size', array( &$this, 'admin_post_thumbnail_size' ), 10, 3 );

		// add_action( 'wp_loaded', array( &$this, 'edit_hook' ) );
		// add_action( 'get_the_excerpt', array( &$this, 'get_the_excerpt' ) );
		// add_action( 'admin_enqueue_scripts', array( &$this, 'assets' ) );
		// add_filter( 'update_post_metadata', array( &$this, 'prevent_edit_lock' ), 10, 5 );

		// add_filter( 'mailster_campaign_action', array( &$this, 'trigger_campaign_action' ), 10, 2 );
	}


	public function register_post_type() {

		$single = esc_html__( 'Automation', 'mailster' );
		$plural = esc_html__( 'Automations', 'mailster' );

		$labels = array(
			'name'                     => _x( 'Automation', 'Post Type General Name', 'mailster' ),
			'singular_name'            => _x( 'Automation', 'Post Type Singular Name', 'mailster' ),
			'menu_name'                => __( 'Automations', 'mailster' ),
			'attributes'               => __( 'Automation Attributes', 'mailster' ),
			'all_items'                => __( 'Automations', 'mailster' ),
			'add_new_item'             => __( 'Add New Automation', 'mailster' ),
			'add_new'                  => __( 'Add New', 'mailster' ),
			'new_item'                 => __( 'New Automation', 'mailster' ),
			'edit_item'                => __( 'Edit Automation', 'mailster' ),
			'update_item'              => __( 'Update Automation', 'mailster' ),
			'view_item'                => __( 'View Automation', 'mailster' ),
			'view_items'               => __( 'View Automations', 'mailster' ),
			'search_items'             => __( 'Search Automation', 'mailster' ),
			'not_found'                => __( 'Not found', 'mailster' ),
			'not_found_in_trash'       => __( 'Not found in Trash', 'mailster' ),
			'uploaded_to_this_item'    => __( 'Uploaded to this automation', 'mailster' ),
			'items_list'               => __( 'Automations list', 'mailster' ),
			'items_list_navigation'    => __( 'Automations list navigation', 'mailster' ),
			'filter_items_list'        => __( 'Filter automations list', 'mailster' ),
			'item_published'           => __( 'Automation published', 'mailster' ),
			'item_published_privately' => __( 'Automation published privately.', 'mailster' ),
			'item_reverted_to_draft'   => __( 'Automation reverted to draft.', 'mailster' ),
			'item_scheduled'           => __( 'Automation scheduled.', 'mailster' ),
			'item_updated'             => __( 'Automation updated.', 'mailster' ),

		);
		$capabilities = array(
			'edit_post'          => 'mailster_edit_automation',
			'read_post'          => 'mailster_read_automation',
			'delete_post'        => 'mailster_delete_automations',
			'edit_posts'         => 'mailster_edit_automations',
			'edit_others_posts'  => 'mailster_edit_others_automations',
			'publish_posts'      => 'mailster_publish_automations',
			'read_private_posts' => 'mailster_read_private_automations',
		);
		$args         = array(
			'label'                => __( 'Automation', 'mailster' ),
			'description'          => __( 'Newsletter Automation', 'mailster' ),
			'labels'               => $labels,
			'supports'             => array( 'title', 'editor', 'revisions' ),
			'hierarchical'         => false,
			'public'               => false,
			'show_ui'              => true,
			'show_in_menu'         => 'edit.php?post_type=newsletter',
			'show_in_admin_bar'    => false,
			'show_in_nav_menus'    => true,
			'can_export'           => false,
			'has_archive'          => false,
			'exclude_from_search'  => true,
			'rewrite'              => false,
			// 'capabilities'        => $capabilities,
			'show_in_rest'        => true,
			'register_meta_box_cb' => array( &$this, 'meta_boxes' ),

		);
		register_post_type( 'newsletter_auto', $args );

	}


	public function meta_boxes() {

		add_meta_box( 'mailster_workflow', esc_html__( 'Workflow', 'mailster' ), array( &$this, 'workflow' ), 'newsletter_auto', 'normal', 'high' );
		add_meta_box( 'mailster_options', esc_html__( 'Options', 'mailster' ), array( &$this, 'options' ), 'newsletter_auto', 'side', 'low' );

	}


	public function workflow() {
		include MAILSTER_DIR . 'views/automation/workflow.php';
	}
	public function options() {
		include MAILSTER_DIR . 'views/automation/options.php';
	}

}
