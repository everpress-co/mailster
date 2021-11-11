<?php

class MailsterBlockForms {

	private $request = null;

	public function __construct() {

		add_action( 'init', array( &$this, 'register_post_type' ) );
		add_action( 'init', array( &$this, 'register_post_meta' ) );
		add_action( 'enqueue_block_editor_assets', array( &$this, 'block_script_styles' ) );

		add_action( 'wp_enqueue_scripts', array( &$this, 'wp_enqueue_scripts' ) );

	}


	public function wp_enqueue_scripts() {
		$suffix = SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'mailster-form-block', MAILSTER_URI . 'assets/js/form-block' . $suffix . '.js', array(), MAILSTER_VERSION );

	}


	public function register_post_type() {

		$labels       = array(
			'name'                  => _x( 'Block Forms', 'Post Type General Name', 'mailster' ),
			'singular_name'         => _x( 'Form', 'Post Type Singular Name', 'mailster' ),
			'menu_name'             => __( 'Block Forms', 'mailster' ),
			'attributes'            => __( 'Form Attributes', 'mailster' ),
			'all_items'             => __( 'Block Forms', 'mailster' ),
			'add_new_item'          => __( 'Add New Form', 'mailster' ),
			'add_new'               => __( 'Add New', 'mailster' ),
			'new_item'              => __( 'New Form', 'mailster' ),
			'edit_item'             => __( 'Edit Form', 'mailster' ),
			'update_item'           => __( 'Update Form', 'mailster' ),
			'view_item'             => __( 'View Form', 'mailster' ),
			'view_items'            => __( 'View Forms', 'mailster' ),
			'search_items'          => __( 'Search Form', 'mailster' ),
			'not_found'             => __( 'Not found', 'mailster' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'mailster' ),
			'uploaded_to_this_item' => __( 'Uploaded to this form', 'mailster' ),
			'items_list'            => __( 'Forms list', 'mailster' ),
			'items_list_navigation' => __( 'Forms list navigation', 'mailster' ),
			'filter_items_list'     => __( 'Filter forms list', 'mailster' ),
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
			'label'               => __( 'Form', 'mailster' ),
			'description'         => __( 'Newsletter Form', 'mailster' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'revisions', 'custom-fields' ),
			'hierarchical'        => true,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=newsletter',
			'menu_position'       => 60,
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => true,
			'can_export'          => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false,
			'capabilities'        => $capabilities,
			'show_in_rest'        => true,
		);
		register_post_type( 'newsletter_form', $args );

	}

	public function register_post_meta() {

		register_post_meta(
			'newsletter_form',
			'doubleoptin',
			array(
				'type'         => 'boolean',
				'show_in_rest' => true,
				'single'       => true,
				'default'      => true,

			)
		);
		register_post_meta(
			'newsletter_form',
			'gdpr',
			array(
				'type'         => 'boolean',
				'show_in_rest' => true,
				'single'       => true,
				'default'      => true,

			)
		);

		register_post_meta(
			'newsletter_form',
			'subject',
			array(
				'type'         => 'string',
				'show_in_rest' => true,
				'single'       => true,
				'default'      => '',
			)
		);
		register_post_meta(
			'newsletter_form',
			'headline',
			array(
				'type'         => 'string',
				'show_in_rest' => true,
				'single'       => true,
				'default'      => '',
			)
		);

		register_post_meta(
			'newsletter_form',
			'content',
			array(
				'type'         => 'string',
				'show_in_rest' => true,
				'single'       => true,
				'default'      => 'asdasd',
			)
		);

		register_post_meta(
			'newsletter_form',
			'input_styles',
			array(
				'type'         => 'string',
				'show_in_rest' => true,
				'single'       => true,
				'default'      => '',
			)
		);

	}



	public function block_script_styles( $hook ) {

		if ( 'newsletter_form' != get_post_type() ) {
			// return;
		}

		// if ( 'post-new.php' == $hook || 'post.php' == $hook ) {

		$suffix = SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'mailster-form-block-editor', MAILSTER_URI . 'assets/css/blocks-editor' . $suffix . '.css', array(), MAILSTER_VERSION );
		// wp_enqueue_style( 'mailster-form-block', MAILSTER_URI . 'assets/css/blocks-style' . $suffix . '.css', array(), MAILSTER_VERSION );

		// }
	}
}
