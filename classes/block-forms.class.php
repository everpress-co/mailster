<?php

class MailsterBlockForms {


	private $forms = array();
	private $preview_data;

	public function __construct() {

		// since 5.8
		if ( ! function_exists( 'get_allowed_block_types' ) ) {
			return;
		}
		add_action( 'plugins_loaded', array( &$this, 'maybe_preview' ) );

		add_action( 'init', array( &$this, 'register_post_type' ) );
		add_action( 'init', array( &$this, 'register_post_meta' ) );
		add_action( 'init', array( &$this, 'block_init' ) );

		add_action( 'enqueue_block_editor_assets', array( &$this, 'block_script_styles' ), 1 );

		add_filter( 'allowed_block_types_all', array( &$this, 'allowed_block_types' ), 10, 2 );
		add_filter( 'block_categories_all', array( &$this, 'block_categories' ) );

		add_filter( 'manage_newsletter_form_posts_columns', array( &$this, 'columns' ), 1 );
		add_action( 'manage_newsletter_form_posts_custom_column', array( &$this, 'custom_column' ), 10, 2 );

		add_filter( 'template_redirect', array( &$this, 'prepare_forms' ) );

		add_action( 'save_post_newsletter_form', array( &$this, 'clear_cache' ) );
		add_action( 'switch_theme', array( &$this, 'clear_inline_style' ) );

		add_action( 'mailster_block_form_header', array( &$this, 'prepare_forms' ) );
		add_action( 'mailster_block_form_head', array( &$this, 'form_head' ) );
		add_action( 'mailster_block_form_body', array( &$this, 'form_body' ) );
		// add_action( 'mailster_block_form_footer', array( &$this, 'form_footer' ) );

		add_action(
			'__save_post_newsletter_form',
			function( $post_id, $post ) {

				error_log( print_r( $post->post_content, true ) );
			},
			10,
			2
		);
	}


	public function maybe_preview() {

		// enter preview mode
		if ( isset( $_GET['mailster-block-preview'] ) ) {

			$data = json_decode( stripcslashes( $_GET['mailster-block-preview'] ), true );

			if ( ! isset( $data['p'] ) && 'other' != $data['type'] ) {
				$data['p'] = $this->get_preview_page( $data );
				$redirect  = add_query_arg(
					array(
						'mailster-block-preview' => rawurlencode( json_encode( $data ) ),
						'p'                      => $data['p'],
					),
					home_url()
				);

				wp_redirect( $redirect );
				exit;
			}

			if ( json_last_error() === JSON_ERROR_NONE ) {
				$this->preview_data = $data;
				if ( ! $data['user'] ) {
					add_filter( 'determine_current_user', '__return_false', PHP_INT_MAX );
				}
			}
		}
	}


	public function get_preview_page( $data ) {

		global $wpdb;

		$sql = "SELECT wp_posts.ID FROM {$wpdb->posts} AS wp_posts";

		if ( ! empty( $data['options']['all'] ) ) {
			$sql .= ' WHERE wp_posts.post_type IN ("' . implode( '", "', $data['options']['all'] ) . '")';
		} elseif ( ! empty( $data['options']['posts'] ) ) {
			$sql .= ' WHERE wp_posts.ID IN (' . implode( ', ', $data['options']['posts'] ) . ')';
		} elseif ( ! empty( $data['options']['taxonomies'] ) ) {
			$sql .= " LEFT JOIN {$wpdb->term_relationships} AS terms ON terms.object_id = wp_posts.ID WHERE terms.term_taxonomy_id IN (" . implode( ', ', $data['options']['taxonomies'] ) . ')';
		} else {
			$sql .= ' WHERE 1=1';
		}

		$sql .= " AND (wp_posts.post_status = 'publish') ORDER BY wp_posts.post_date DESC LIMIT 0, 1";

		return $wpdb->get_var( $sql );

	}


	public function form_head() {

		$form_id = isset( $_GET['id'] ) ? (int) $_GET['id'] : null;

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_style( 'mailster-form-default-style', MAILSTER_URI . 'assets/css/form-default-style' . $suffix . '.css', array(), MAILSTER_VERSION );
		if ( isset( $_GET['style'] ) ) {
			wp_register_style( 'mailster-theme-style', get_template_directory_uri() . '/style.css', array(), MAILSTER_VERSION );
			wp_print_styles( 'mailster-theme-style' );
		}
		wp_print_styles( 'mailster-form-block' );
		wp_print_scripts( 'mailster-form-block-preview' );
		wp_print_scripts( 'mailster-form-block' );

	}

	public function form_body() {

		$form_id = isset( $_GET['id'] ) ? (int) $_GET['id'] : null;

		$options = $this->preview_data['options'];

		$options['classes'] = array( 'mailster-block-form-type-embed' );

		if ( $form_html = $this->render_form_with_options( $form_id, $options, false ) ) {
			echo $form_html;
		}
	}


	public function columns( $columns ) {

		$columns = array(
			'cb'              => '<input type="checkbox" />',
			'title'           => esc_html__( 'Title', 'mailster' ),
			'impressions'     => esc_html__( 'Impressions', 'mailster' ),
			'conversions'     => esc_html__( 'Conversions', 'mailster' ),
			'conversion_rate' => esc_html__( 'Conversion Rate', 'mailster' ),
			'date'            => esc_html__( 'Date', 'mailster' ),
		);
		return $columns;
	}


	public function custom_column( $column, $post_id ) {

		switch ( $column ) {
			case 'impressions':
				echo '0';
				break;
			case 'conversions':
				echo '0';
				break;
			case 'conversion_rate':
				echo '0%';
				break;
			default:
				break;
		}

	}




	public function prepare_forms() {

		// $terms = wp_get_object_terms( get_the_ID(), get_object_taxonomies('post'), array('fields' => 'ids') );

		if ( $this->preview_data ) {

			$options = $this->preview_data['options'];

			$this->forms[ $this->preview_data['type'] ][ $this->preview_data['form_id'] ] = $options;

			$suffix = SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script( 'mailster-form-block-preview', MAILSTER_URI . 'assets/js/form-block-preview' . $suffix . '.js', array( 'wp-api-fetch', 'wp-polyfill', 'jquery' ), MAILSTER_VERSION );
			wp_enqueue_style( 'mailster-form-block-preview', MAILSTER_URI . 'assets/css/form-block-preview' . $suffix . '.css', array(), MAILSTER_VERSION );
			// wp_register_style( 'mailster-form-block', MAILSTER_URI . 'build/style-form.css', array(), MAILSTER_VERSION );

		} elseif ( $forms = $this->query_forms() ) {

			foreach ( $forms as $form_id ) {
				$placements = (array) get_post_meta( $form_id, 'placements', false );
				// TODO check for A/B Test
				foreach ( $placements as $placement ) {
					$placement_options = get_post_meta( $form_id, 'placement_' . $placement, true );
					if ( $placement_options ) {
						$this->forms[ $placement ][ $form_id ] = $placement_options;
					}
				}
			}
		}
		if ( isset( $this->forms['popup'] ) || isset( $this->forms['bar'] ) || isset( $this->forms['side'] ) ) {
			add_filter( 'wp_footer', array( &$this, 'maybe_add_form_to_footer' ) );
		}
		if ( isset( $this->forms['content'] ) ) {
			add_filter( 'the_content', array( &$this, 'maybe_add_form_to_content' ) );
		}

	}


	public function check_validity( $options = array() ) {

		$post_type = get_post_type();

		if ( ! empty( $options['all'] ) && in_array( $post_type, $options['all'] ) ) {
			return true;
		}

		$current_id = get_the_ID();

		if ( isset( $options['posts'] ) && in_array( $current_id, $options['posts'] ) ) {
			return true;
		}

		if ( ! empty( $options['taxonomies'] ) ) {
			// get all assigned term ids of this post
			$terms = wp_get_object_terms( $current_id, get_object_taxonomies( $post_type ), array( 'fields' => 'ids' ) );
			if ( array_intersect( $options['taxonomies'], $terms ) ) {
				return true;
			}
		}

		return false;

	}



	public function maybe_add_form_to_content( $content ) {

		if ( isset( $this->forms['content'] ) && is_singular() ) {

			foreach ( $this->forms['content'] as $form_id => $options ) {
				if ( isset( $displayed[ $form_id ] ) ) {
					continue;
				}
				if ( $form_html = $this->render_form_with_options( $form_id, $options ) ) {
					$display = $options['display'];

					if ( 'start' == $display ) {
						$content = $form_html . $content;
					} elseif ( 'end' == $display ) {
						$content = $content . $form_html;
					} else {
						$tag = $options['tag'];
						$pos = $options['pos'];

						if ( 'more' == $tag ) {
							$split_at = '<span id="more-' . get_the_ID() . '"></span>';
							$pos      = 1;
						} else {
							$split_at = '</' . $tag . '>';
						}

						$chunks = explode( $split_at, $content );
						if ( $pos < 0 ) {
							$pos = max( 0, count( $chunks ) + $pos );
						}

						if ( isset( $chunks[ $pos ] ) ) {
							$chunks[ $pos ] = $form_html . $chunks[ $pos ];
							$content        = implode( $split_at, $chunks );
						} else {
							$content .= $form_html;
						}
					}
				}
			}
		}

		return $content;

	}

	public function maybe_add_form_to_footer() {

		if ( isset( $this->forms['popup'] ) ) {
			foreach ( $this->forms['popup'] as $form_id => $options ) {
				$options['classes'] = array( 'mailster-block-form-type-popup' );
				if ( $form_html = $this->render_form_with_options( $form_id, $options ) ) {
					echo $form_html;
				}
			}
		}

		if ( isset( $this->forms['bar'] ) ) {
			foreach ( $this->forms['bar'] as $form_id => $options ) {
				$options['classes'] = array( 'mailster-block-form-type-bar' );
				if ( $form_html = $this->render_form_with_options( $form_id, $options ) ) {
					echo $form_html;
				}
			}
		}

		if ( isset( $this->forms['side'] ) ) {
			foreach ( $this->forms['side'] as $form_id => $options ) {
				$options['classes'] = array( 'mailster-block-form-type-side' );
				if ( $form_html = $this->render_form_with_options( $form_id, $options ) ) {
					echo $form_html;
				}
			}
		}

	}


	public function get_forms_for_content() {

		return $this->query_forms( array( 'content' ) );

	}

	public function get_forms_for_footer() {

		return $this->query_forms( array( 'popup', 'slidein', 'bar' ) );

	}


	private function query_forms( $for = array() ) {

		$args = array(
			'post_type'              => 'newsletter_form',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
		);

		if ( ! empty( $for ) ) {
			$args['meta_key']   = 'placements';
			$args['meta_value'] = (array) $for;
		}

		if ( ! is_user_logged_in() ) {
			$args['post_status'] = 'publish';
		}

		$query = new WP_Query( $args );

		return $query->posts;

	}



	public function wp_register_scripts() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'mailster-form-block', MAILSTER_URI . 'assets/js/form-block' . $suffix . '.js', array(), MAILSTER_VERSION, true );
		wp_register_style( 'mailster-form-block', MAILSTER_URI . 'build/style-form.css', array(), MAILSTER_VERSION );

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
			'rewrite'             => array(
				'with_front' => false,
				'slug'       => 'newsletter-form',
			),
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
				'default'      => false,

			)
		);

		register_post_meta(
			'newsletter_form',
			'userschoice',
			array(
				'type'         => 'boolean',
				'show_in_rest' => true,
				'single'       => true,
				'default'      => false,

			)
		);

		register_post_meta(
			'newsletter_form',
			'redirect',
			array(
				'type'         => 'string',
				'show_in_rest' => true,
				'single'       => true,
				'default'      => '',

			)
		);

		register_post_meta(
			'newsletter_form',
			'confirmredirect',
			array(
				'type'         => 'string',
				'show_in_rest' => true,
				'single'       => true,
				'default'      => '',

			)
		);

		register_post_meta(
			'newsletter_form',
			'overwrite',
			array(
				'type'         => 'boolean',
				'show_in_rest' => true,
				'single'       => true,
				'default'      => false,

			)
		);

		register_post_meta(
			'newsletter_form',
			'lists',
			array(
				'type'         => 'array',
				'show_in_rest' => array(
					'schema' => array(
						'type'  => 'array',
						'items' => array(
							'type' => 'number',
						),
					),
				),
				'single'       => true,
				'default'      => array(),
			)
		);

		register_post_meta(
			'newsletter_form',
			'subject',
			array(
				'type'         => 'string',
				'show_in_rest' => true,
				'single'       => true,
				'default'      => esc_html__( 'Please confirm', 'mailster' ),
			)
		);
		register_post_meta(
			'newsletter_form',
			'headline',
			array(
				'type'         => 'string',
				'show_in_rest' => true,
				'single'       => true,
				'default'      => esc_html__( 'Please confirm your Email Address', 'mailster' ),
			)
		);

		register_post_meta(
			'newsletter_form',
			'content',
			array(
				'type'         => 'string',
				'show_in_rest' => true,
				'single'       => true,
				'default'      => sprintf( esc_html__( 'You have to confirm your email address. Please click the link below to confirm. %s', 'mailster' ), "\n{link}" ),
			)
		);

		register_post_meta(
			'newsletter_form',
			'link',
			array(
				'type'         => 'string',
				'show_in_rest' => true,
				'single'       => true,
				'default'      => esc_html__( 'Click here to confirm', 'mailster' ),
			)
		);

		register_post_meta(
			'newsletter_form',
			'tags',
			array(
				'type'         => 'array',
				'show_in_rest' => array(
					'schema' => array(
						'type'  => 'array',
						'items' => array(
							'type' => 'number',
						),
					),
				),
				'single'       => true,
				'default'      => array(),
			)
		);

		register_post_meta(
			'newsletter_form',
			'placements',
			array(
				'type'         => 'string',
				'show_in_rest' => true,
				'single'       => false,
			)
		);

		foreach ( array( 'content', 'bar', 'popup', 'side', 'other' ) as $placement_type ) {

			if ( 'content' == $placement_type ) {
				$default = array(
					'all'     => array( 'post' ),
					'tag'     => 'p',
					'pos'     => 0,
					'display' => 'end',
				);
			} elseif ( 'other' == $placement_type ) {
				$default = array();
			} else {
				$default = array(
					'all'              => array( 'post' ),
					'triggers'         => array( 'delay' ),
					'trigger_delay'    => 120,
					'trigger_inactive' => 120,
					'trigger_click'    => '',
					'trigger_scroll'   => 66,
					'width'            => 70,
					'padding'          => array(
						'top'    => '1em',
						'right'  => '1em',
						'bottom' => '1em',
						'left'   => '1em',
					),
				);
			}

			register_post_meta(
				'newsletter_form',
				'placement_' . $placement_type,
				array(
					'single'       => true,
					'type'         => 'object',
					'default'      => $default,
					'show_in_rest' => array(
						'schema' => array(
							'type'       => 'object',
							'properties' => array(
								'all'              => array(
									'type' => 'array',
								),
								'triggers'         => array(
									'type' => 'array',
								),
								'schedule'         => array(
									'type' => 'array',
								),
								'posts'            => array(
									'type' => 'array',
								),
								'taxonomies'       => array(
									'type' => 'array',
								),
								'tag'              => array(
									'type' => 'string',
								),
								'pos'              => array(
									'type' => 'integer',
								),
								'trigger_delay'    => array(
									'type' => 'integer',
								),
								'trigger_inactive' => array(
									'type' => 'integer',
								),
								'trigger_click'    => array(
									'type' => 'string',
								),
								'trigger_scroll'   => array(
									'type' => 'integer',
								),
								'display'          => array(
									'type' => 'string',
								),
								'align'            => array(
									'type' => 'string',
								),
								'width'            => array(
									'type' => 'integer',
								),
								'padding'          => array(
									'type'                 => 'object',
									'additionalProperties' => array(
										'type' => 'string',
									),
								),
								'animation'        => array(
									'type' => 'string',
								),

							),
						),
					),
				),
			);
		}       }



	public function block_init() {

		// add_action( 'wp_enqueue_scripts', array( &$this, 'wp_register_scripts' ) );
		$this->wp_register_scripts();

		register_block_type(
			MAILSTER_DIR . 'blocks/form/',
			array(
				'api_version'     => 2,
				'render_callback' => array( $this, 'render_form' ),
				'textdomain'      => 'mailster',
				'script'          => 'mailster-form-block',
				// 'style'           => 'mailster-form-block',
			)
		);
		register_block_type( MAILSTER_DIR . 'blocks/homepage/', array() );

		if ( ! is_admin() ) {
			return;
		}
		// from https://www.designbombs.com/registering-gutenberg-blocks-for-custom-post-type/
		global $pagenow;
		$typenow = '';

		if ( 'post-new.php' === $pagenow ) {
			if ( isset( $_REQUEST['post_type'] ) && post_type_exists( $_REQUEST['post_type'] ) ) {
				  $typenow = $_REQUEST['post_type'];
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

		if ( $typenow == 'newsletter_form' ) {

			// not in use on the form edit page
			unregister_block_type( 'mailster/form' );
			unregister_block_type( 'mailster/homepage' );
			$this->register_block_pattern();
			$this->register_block_pattern_category();

			wp_enqueue_code_editor(
				array(
					'type'       => 'text/css',
					'codemirror' => array( 'lint' => true ),
				)
			);

			$blocks = $this->get_blocks();

			foreach ( $blocks as $block ) {
				$args = array();

				$block_name = str_replace( '-', '_', basename( dirname( $block ) ) );

				if ( method_exists( $this, 'render_' . $block_name ) ) {
					$args['render_callback'] = array( $this, 'render_' . $block_name );
				}

				register_block_type( $block, $args );
			}
		}

	}

	private function get_blocks() {
		$blocks = glob( MAILSTER_DIR . 'blocks/forms/*/block.json' );
		return $blocks;
	}

	public function block_script_styles( $hook ) {

		if ( 'newsletter_form' != get_post_type() ) {
			return;
		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'mailster-form-block-editor', MAILSTER_URI . 'build/forms/form-inspector/index.js', array( 'mailster-script', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-plugins', 'wp-edit-post' ), MAILSTER_VERSION );
		wp_enqueue_script( 'mailster-form-field-block-editor', MAILSTER_URI . 'build/forms/input/index.js', array( 'mailster-script', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-plugins', 'wp-edit-post' ), MAILSTER_VERSION );

		wp_enqueue_style( 'mailster-form-block-editor', MAILSTER_URI . 'assets/css/blocks-editor' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_add_inline_script( 'mailster-form-block-editor', 'var mailster_fields = ' . json_encode( array_values( $this->get_fields() ) ) . ';' );
		wp_add_inline_script( 'mailster-form-block-editor', 'var mailster_inline_styles = ' . json_encode( get_option( 'mailster_inline_styles' ) ) . ';' );

	}

	public function get_fields() {
		$custom_fields = mailster()->get_custom_fields();

		$fields = array(
			'submit'    => array(
				'name'    => __( 'Submit Button', 'mailster' ),
				'id'      => 'submit',
				'default' => mailster_text( 'submitbutton' ),
				'type'    => 'submit',
			),
			'email'     => array(
				'name' => mailster_text( 'email' ),
				'id'   => 'email',
				'type' => 'email',
			),
			'firstname' => array(
				'name' => mailster_text( 'firstname' ),
				'id'   => 'firstname',
				'type' => 'text',
			),

			'lastname'  => array(
				'name' => mailster_text( 'lastname' ),
				'id'   => 'lastname',
				'type' => 'text',
			),
		);

		return array_merge( $fields, $custom_fields );
	}

	public function allowed_block_types( $allowed_block_types, $block_editor_context ) {

		// just skip if not on our cpt
		if ( 'newsletter_form' != get_post_type() ) {
			return $allowed_block_types;
		}

		$types = array( 'core/paragraph', 'core/image', 'core/heading', 'core/gallery', 'core/list', 'core/quote', 'core/shortcode', 'core/archives', 'core/audio', 'core/button', 'core/buttons', 'core/calendar', 'core/categories', 'core/code', 'core/columns', 'core/column', 'core/cover', 'core/embed', 'core/file', 'core/group', 'core/freeform', 'core/html', 'core/media-text', 'core/latest-comments', 'core/latest-posts', 'core/missing', 'core/more', 'core/nextpage', 'core/page-list', 'core/preformatted', 'core/pullquote', 'core/rss', 'core/search', 'core/separator', 'core/block', 'core/social-links', 'core/social-link', 'core/spacer', 'core/table', 'core/tag-cloud', 'core/text-columns', 'core/verse', 'core/video', 'core/site-logo', 'core/site-tagline', 'core/site-title', 'core/query', 'core/post-template', 'core/query-title', 'core/query-pagination', 'core/query-pagination-next', 'core/query-pagination-numbers', 'core/query-pagination-previous', 'core/post-title', 'core/post-content', 'core/post-date', 'core/post-excerpt', 'core/post-featured-image', 'core/post-terms', 'core/loginout' );

		// $types  = array( 'core/paragraph' );
		$blocks = $this->get_blocks();

		foreach ( $blocks as $block ) {

			$block_name = basename( dirname( $block ) );

			$types[] = 'mailster/' . $block_name;
		}

		$custom_fields = array_keys( $this->get_fields() );
		foreach ( $custom_fields as $block ) {

			$types[] = 'mailster/field-' . $block;
		}

		return apply_filters( 'mailster_forms_allowed_block_types', $types );

	}

	public function block_categories( $categories ) {

		if ( 'newsletter_form' != get_post_type() ) {
			return $categories;
		}

		return array_merge(
			array(
				array(
					'slug'  => 'mailster-form-fields',
					'title' => __( 'Newsletter Form Fields', 'mailster' ),
				),

			),
			$categories,
		);
	}

	public function register_block_pattern_category() {
		register_block_pattern_category( 'mailster-forms', array( 'label' => __( 'Mailster Forms', 'mailster' ) ) );
	}

	public function register_block_pattern() {

		$WP_Block_Patterns_Registry = WP_Block_Patterns_Registry::get_instance();
		$registered                 = wp_list_pluck( $WP_Block_Patterns_Registry->get_all_registered(), 'name' );

		foreach ( $registered as $pattern ) {
			unregister_block_pattern( $pattern );
		}

		include MAILSTER_DIR . 'includes/form-pattern.php';

		foreach ( $patterns as $key => $pattern ) {

			$args = wp_parse_args(
				$pattern,
				array(
					'keywords'      => array( 'mailster-form' ),
					'viewportWidth' => 600,
				)
			);

			$args['categories'] = array( 'featured', 'mailster-forms' );

			register_block_pattern( 'mailster/pattern_' . $key, $args );
		}

	}

	public function render_form_with_options( $form, $options = array(), $check_validity = true ) {

		if ( $check_validity && ! $this->check_validity( $options ) ) {
			return '';
		}

		$form = get_post( $form );

		if ( get_post_type( $form ) != 'newsletter_form' ) {
			return '';
		}

		$options['id'] = $form->ID;
		if ( 'the_content' == current_filter() && isset( $options['triggers'] ) ) {
			unset( $options['triggers'] );
		}

		if ( isset( $this->preview_data ) ) {

			$html = '<div class="wp-block-mailster-form-outside-wrapper-' . $options['id'] . ' wp-block-mailster-form-outside-wrapper-placeholder is-empty">' . esc_html__( 'Loading your form...', 'mailster' ) . '</div>';

			if ( $form->post_status != 'auto-draft' ) {
				$options['classes'][] = 'wp-block-mailster-form-outside-wrapper-placeholder';

				$block = parse_blocks( '<!-- wp:mailster/form ' . json_encode( $options ) . ' /-->' );

				$html = render_block( $block[0] );
			}
		} else {

			$block = parse_blocks( '<!-- wp:mailster/form ' . json_encode( $options ) . ' /-->' );

			$html = render_block( $block[0] );
		}

		return $html;
	}


	public function render_form( $args, $content, WP_Block $block ) {

		if ( empty( $args ) ) {
			return;
		}

		if ( ! ( $form = $original_form = get_post( $args['id'] ) ) ) {
			return;
		}

		// further checks for revisions
		if ( get_post_type( $form ) == 'revision' ) {
			$original_form = get_post( $form->post_parent );
			if ( get_post_type( $original_form ) != 'newsletter_form' ) {
				return;
			}
		}

		$args = wp_parse_args(
			$args,
			array(
				'identifier' => hash( 'crc32', md5( serialize( $args ) ) ),
				'classes'    => array( 'mailster-block-form-type-content' ), // gets overwritten by other types
				'isPreview'  => false,
			)
		);

		// is on a page in the backend and loaded via the REST API
		$is_backend = defined( 'REST_REQUEST' ) && REST_REQUEST;

		if ( ! $is_backend ) {
			if ( $cached = get_post_meta( $form->ID, '_cached', true ) ) {
				return $cached;
			}
		}

		$blockattributes = $block->attributes;
		$uniqid          = substr( uniqid(), 8, 5 );

		// in preview mode check for content here
		$request_body = file_get_contents( 'php://input' );
		if ( ! empty( $request_body ) ) {
			$data = json_decode( $request_body, true );
			if ( isset( $data['block_form_content'] ) ) {
				$blocks     = parse_blocks( $data['block_form_content'] );
				$args       = wp_parse_args( $data['args'], $args );
				$form_block = $blocks[0];
			} else {
				$form_block = $this->get_form_block( $form );
			}
		} else {
			$form_block = $this->get_form_block( $form );
		}

		$output = render_block( $form_block );
		$inject = '';

		$stylesheets = array();
		if ( $is_backend ) {
			$stylesheets = array( 'style-form.css', 'style-input.css' );
		}

		$args['classes'][] = 'wp-block-mailster-form-outside-wrapper';
		$args['classes'][] = 'wp-block-mailster-form-outside-wrapper-' . $uniqid;
		$args['classes'][] = 'wp-block-mailster-form-outside-wrapper-' . $original_form->ID;

		if ( isset( $blockattributes['align'] ) ) {
			$args['classes'][] = 'align' . $blockattributes['align'];
		}
		$embeded_style = '';

		foreach ( $form_block['innerBlocks'] as $block ) {

			if ( 'mailster/messages' == $block['blockName'] ) {
				$embeded_style .= '.wp-block-mailster-form-outside-wrapper-' . $uniqid . '{';
				foreach ( $block['attrs'] as $key => $value ) {
					if ( ! is_array( $value ) ) {
						$embeded_style .= '--mailster--color--' . strtolower( preg_replace( '/([a-z])([A-Z])/', '$1-$2', $key ) ) . ': ' . $value . ';';
					}
				}
				$embeded_style .= '}';
			}
		}

		if ( isset( $form_block['attrs']['className'] ) ) {
			$args['classes'][] = $form_block['attrs']['className'];
		}

		$custom_styles = array();

		if ( isset( $form_block['attrs']['padding'] ) ) {

			$custom_styles[''][] = 'padding:' . $form_block['attrs']['padding'] . 'px';
		}
		if ( isset( $form_block['attrs']['color'] ) ) {
			$custom_styles[''][] = 'color:' . $form_block['attrs']['color'];
		}
		if ( isset( $args['width'] ) ) {
			$custom_styles['.mailster-block-form'][] = 'flex-basis:' . $args['width'] . '%';
		}
		if ( isset( $args['padding'] ) ) {
			foreach ( $args['padding'] as $key => $value ) {
				$custom_styles['.mailster-block-form'][] = 'padding-' . $key . ':' . $value;
			}
		}

		$inject .= '<a class="mailster-block-form-close" href="#"></a>';

		if ( isset( $args['animation'] ) ) {
			$args['classes'][] = 'has-animation animation-' . $args['animation'];
		}

		if ( isset( $form_block['attrs']['background']['image'] ) ) {

			$background = $form_block['attrs']['background'];

			$custom_styles['::before'] = array(
				'content:"";position: absolute;top: 0;left: 0;bottom: 0;right: 0;',
				'background-image:url(' . $background['image'] . ')',
				'opacity:' . $background['opacity'] . '%',
				'background-size:' . ( ! is_numeric( $background['size'] ) ? $background['size'] : $background['size'] . '%' ),
				'background-position:' . ( $background['position']['x'] * 200 - 50 ) . '% ' . ( $background['position']['y'] * 100 ) . '%',
			);
			if ( $background['fixed'] ) {
				$custom_styles['::before'][] = 'background-attachment:fixed';
			}
			if ( $background['repeat'] ) {
				$custom_styles['::before'][] = 'background-repeat:repeat';
			} else {
				$custom_styles['::before'][] = 'background-repeat:no-repeat';
			}
		}
		if ( isset( $form_block['attrs']['borderRadius'] ) ) {
			$custom_styles[''][]         = 'border-radius:' . $form_block['attrs']['borderRadius'];
			$custom_styles['::before'][] = 'border-radius:' . $form_block['attrs']['borderRadius'];
		}

		if ( isset( $form_block['attrs']['style'] ) ) {
			$custom_styles[' .mailster-label'] = array();
			$custom_styles[' .input']          = array();

			// $embeded_style .= '.wp-block-mailster-form-outside-wrapper-' . $uniqid . ' .wp-block-mailster-form-wrapper .input{';
			foreach ( $form_block['attrs']['style'] as $key => $value ) {
				if ( $value ) {
					switch ( $key ) {
						case 'labelColor':
							$custom_styles[' .mailster-label'][] = 'color:' . $value;
							break;
						case 'borderWidth':
						case 'inputColor':
						case 'backgroundColor':
						case 'borderColor':
							$custom_styles[' .input'][] = strtolower( preg_replace( '/([a-z])([A-Z])/', '$1-$2', $key ) ) . ':' . $value;
							break;
					}
				}
			}
		}

		foreach ( $custom_styles as $selector => $property ) {
			$embeded_style .= '.wp-block-mailster-form-outside-wrapper-' . $uniqid . ' .wp-block-mailster-form-wrapper' . $selector . '{';
			$embeded_style .= implode( ';', $property );
			$embeded_style .= '}';
		}

		if ( $is_backend && $input_styles = get_option( 'mailster_inline_styles' ) ) {
			$embeded_style .= $input_styles;
		}

		require MAILSTER_DIR . 'classes/libs/InlineStyle/autoload.php';

		// $i_error = libxml_use_internal_errors( true );
		$htmldoc = new \InlineStyle\InlineStyle();

		if ( isset( $form_block['attrs']['css'] ) ) {
			foreach ( $form_block['attrs']['css'] as $name => $css ) {
				if ( empty( $css ) ) {
					continue;
				}
				$parsed = $htmldoc->parseStylesheet( $css );
				$css    = '';
				foreach ( $parsed as $rule ) {
					$selector = array_shift( $rule );
					if ( ! empty( $rule ) ) {
						// wrapper needs no extra space
						if ( '.wp-block-mailster-form-outside-wrapper' != $selector ) {
							$selector = ' ' . $selector;
						}
						$css .= 'div.wp-block-mailster-form-outside-wrapper-' . $uniqid . '.wp-block-mailster-form-outside-wrapper-' . $form->ID . $selector . '{' . implode( ';', $rule ) . '}';
					}
				}

				switch ( $name ) {
					case 'tablet':
						$embeded_style .= '@media only screen and (max-width: 800px) {' . $css . '}';
						break;
					case 'mobile':
						$embeded_style .= '@media only screen and (max-width: 400px) {' . $css . '}';
						break;
					default:
						$embeded_style .= $css;
						break;
				}
			}
		}

		// $parsed = $htmldoc->parseStylesheet( $stylesheet );
		// foreach ( $parsed as $rule ) {
		// $selector = array_shift( $rule );
		// if ( ! empty( $rule ) && preg_match( '(::?(after|before))', $selector ) ) {
		// $embeded_style .= '.wp-block-mailster-form-outside-wrapper-' . $uniqid . ' ' . $selector . '{' . implode( ';', $rule ) . '}';
		// }
		// }

		if ( ! empty( $embeded_style ) ) {
			$output = '<style>' . $embeded_style . '</style>' . $output;
		}

		$output = '<div class="' . implode( ' ', $args['classes'] ) . '" role="dialog" id="dialog1" aria-labelledby="dialog1_label" aria-modal="true">' . $output . '</div>';

		// $htmldoc->loadHTML( $output );

		// $htmldoc->applyStylesheet( $stylesheet );

		// $html = $htmldoc->getHTML();
		$html = $output;
		// libxml_clear_errors();
		// libxml_use_internal_errors( $i_error );

		// preg_match( '#<body([^>]*)>(.*)<\/body>#is', $html, $matches );
		// if ( ! empty( $matches ) ) {
		// $html = trim( $matches[2] );
		// }

		if ( $is_backend ) {
			$html = do_shortcode( $html );
		}

		if ( ! $is_backend ) {
			// update_post_meta( $original_form->ID, '_cached', $html, true );
		}
		$form_args = array(
			'id'         => $args['id'],
			'identifier' => $args['identifier'],
			'isPreview'  => $args['isPreview'],
		);

		if ( isset( $args['triggers'] ) ) {
			$form_args['triggers'] = $args['triggers'];
			foreach ( $args['triggers'] as $trigger ) {
				if ( isset( $args[ 'trigger_' . $trigger ] ) ) {
					$form_args[ 'trigger_' . $trigger ] = $args[ 'trigger_' . $trigger ];
				}
			}
		}

		if ( ! $this->preview_data ) {
			$inject .= '<script class="mailster-block-form-data" type="application/json">' . json_encode( $form_args ) . '</script>';
		}
		$inject .= '<input name="_formid" type="hidden" value="' . esc_attr( $original_form->ID ) . '">' . "\n";
		$inject .= '<input name="_timestamp" type="hidden" value="' . esc_attr( time() ) . '">' . "\n";
		if ( is_user_logged_in() ) {
			$inject .= '<div style="font-size:10px;opacity:0.5">' . json_encode( $form_args ) . '</div>';
		}

		$html = str_replace( '</form>', $inject . '</form>', $html );

		return ( $html );

	}

	public function get_required_fields( $form ) {

		if ( ! ( $form = get_post( $form ) ) ) {
			return;
		}

		$fields = array();

		$form_block   = $this->get_form_block( $form );
		$inner_blocks = wp_list_pluck( $form_block['innerBlocks'], 'innerHTML', 'blockName' );

		foreach ( $form_block['innerBlocks'] as $block ) {

			if ( false !== strpos( $block['innerHTML'], 'aria-required="true"' ) ) {
				$fields[] = str_replace( 'mailster/field-', '', $block['blockName'] );
			}
		}

		return $fields;

	}

	private function get_form_block( $form ) {

		$form    = get_post( $form );
		$content = $form->post_content;

		$parsed = parse_blocks( $content );
		foreach ( $parsed as $innerblock ) {
			if ( $innerblock['blockName'] == 'mailster/form-wrapper' ) {
				return $innerblock;
			}
		}

		return null;
	}

	public function clear_cache( $post_id ) {

		delete_post_meta( $post_id, '_cached' );

	}

	public function clear_inline_style() {

		update_option( 'mailster_inline_styles', '', 'no' );

	}
}
