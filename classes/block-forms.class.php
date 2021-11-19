<?php

class MailsterBlockForms {

	private $forms = array();

	public function __construct() {

		// since 5.8
		if ( ! function_exists( 'get_allowed_block_types' ) ) {
			return;
		}
		add_action( 'init', array( &$this, 'register_post_type' ) );
		add_action( 'init', array( &$this, 'register_post_meta' ) );
		add_action( 'init', array( &$this, 'block_init' ) );

		add_action( 'enqueue_block_editor_assets', array( &$this, 'block_script_styles' ), 1 );

		add_action( 'wp_enqueue_scripts', array( &$this, 'wp_enqueue_scripts' ) );

		add_filter( 'allowed_block_types_all', array( &$this, 'allowed_block_types' ), 10, 2 );
		add_filter( 'block_categories_all', array( &$this, 'block_categories' ) );
		add_filter( 'block_categories_all', array( &$this, 'block_categories' ) );

		add_filter( 'template_redirect', array( &$this, 'prepare_forms' ) );
		add_filter( 'the_content', array( &$this, 'maybe_add_form_to_content' ) );
		add_filter( 'wp_footer', array( &$this, 'maybe_add_form_to_footer' ) );

		add_action( 'save_post_newsletter_form', array( &$this, 'clear_cache' ), 10, 2 );

		add_action(
			'__save_post_newsletter_form',
			function( $post_id, $post ) {

				error_log( print_r( $post->post_content, true ) );
			},
			10,
			2
		);
	}


	public function prepare_forms() {

		global $wp_query;

		if ( $forms = $this->query_forms() ) {

			foreach ( $forms as $form ) {
				$placements = get_post_meta( $form, 'placements', false );

				foreach ( $placements as $placement ) {

					if ( $placement ) {
						$this->forms[ $placement ][] = $form;
					}
				}
			}
		}

	}


	public function check_validity( $form_id, $context = null ) {
		$options = get_post_meta( $form_id, 'placement_content', true );

		if ( isset( $options['all'] ) && $options['all'] ) {
			return $options;
		}

		$current_id = get_the_ID();

		if ( in_array( $current_id, $options['posts'] ) ) {
			return $options;
		}

		if ( ! empty( $options['category'] ) && $categories = get_the_terms( $current_id, 'category' ) ) {
			$cats = wp_list_pluck( $categories, 'term_id' );
			if ( array_intersect( $options['category'], $cats ) ) {
				return $options;
			}
		}

		if ( ! empty( $options['post_tag'] ) && $tags = get_the_terms( $current_id, 'post_tag' ) ) {
			$cats = wp_list_pluck( $tags, 'term_id' );
			if ( array_intersect( $options['post_tag'], $cats ) ) {
				return $options;
			}
		}

		return false;

	}


	public function maybe_add_form_to_content( $content ) {

		if ( isset( $this->forms['content'] ) ) {

			if ( is_page() || is_singular() ) {
				foreach ( $this->forms['content'] as $form_id ) {

					if ( ! ( $option = $this->check_validity( $form_id, 'content' ) ) ) {
						return $content;
					}

					$form_html = $this->render_form_byid( $form_id );

					$tag = $option['tag'];
					$pos = $option['pos'];

					$chunks = explode( '</' . $tag . '>', $content );

					if ( $pos < 0 ) {
						$pos = max( 0, count( $chunks ) + $pos );
					}

					if ( isset( $chunks[ $pos ] ) ) {
						$chunks[ $pos ] = $form_html . $chunks[ $pos ];
						$content        = implode( '</' . $tag . '>', $chunks );
					} else {
						$content .= $form_html;
					}
				}
			}
		}

		return $content;

	}

	public function maybe_add_form_to_footer() {

		if ( isset( $this->forms['popup'] ) ) {
			foreach ( $this->forms['popup'] as $form ) {
				echo $this->render_form_byid( $form, array( 'mailster-form-type-popup' ) );
			}
		}
		if ( isset( $this->forms['bar'] ) ) {
			foreach ( $this->forms['bar'] as $form ) {
				echo $this->render_form_byid( $form, array( 'mailster-form-type-bar' ) );
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
			'public'              => true,
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
			'input_styles',
			array(
				'type'         => 'string',
				'show_in_rest' => true,
				'single'       => true,
				'default'      => '',
			)
		);

		register_post_meta(
			'newsletter_form',
			'posts',
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
			'categories',
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
				'default'      => '',
			)
		);

		foreach ( array( 'content', 'bar', 'popup', 'side', 'other' ) as $placement_type ) {
			register_post_meta(
				'newsletter_form',
				'placement_' . $placement_type,
				array(
					'single'       => true,
					'type'         => 'object',
					'default'      => array(
						'all'        => false,
						'post_types' => array(),
						'posts'      => array(),
						'category'   => array(),
						'post_tag'   => array(),
						'tag'        => 'p',
						'pos'        => 0,
					),
					'show_in_rest' => array(
						'schema' => array(
							'type'       => 'object',
							'properties' => array(
								'all'        => array(
									'type' => 'boolean',
								),
								'post_types' => array(
									'type' => 'array',
								),
								'posts'      => array(
									'type' => 'array',
								),
								'category'   => array(
									'type' => 'array',
								),
								'post_tag'   => array(
									'type' => 'array',
								),
								'tag'        => array(
									'type' => 'string',
								),
								'pos'        => array(
									'type' => 'integer',
								),
							),
						),
					),
				),
			);
		}       }



	public function block_init() {

		register_block_type( MAILSTER_DIR . 'blocks/form/', array( 'render_callback' => array( $this, 'render_form' ) ) );
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
			$this->register_block_pattern();
			$this->register_block_pattern_category();

			wp_enqueue_code_editor( array( 'type' => 'text/css' ) );

			$blocks = $this->get_blocks();

			foreach ( $blocks as $block ) {
				$args = array();

				$block_name = str_replace( '-', '_', basename( dirname( $block ) ) );

				if ( 'form' == $block_name ) {
					continue;
				}

				if ( method_exists( $this, 'render_' . $block_name ) ) {
					$args['render_callback'] = array( $this, 'render_' . $block_name );
				}

				register_block_type( $block, $args );
			}
		}

	}

	private function get_blocks() {
		return glob( MAILSTER_DIR . 'blocks/*/block.json' );
	}

	public function block_script_styles( $hook ) {

		if ( 'newsletter_form' != get_post_type() ) {
			return;
		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'mailster-form-block-editor', MAILSTER_URI . 'build/form-inspector.js', array( 'mailster-script', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-plugins', 'wp-edit-post' ), MAILSTER_VERSION );
		wp_enqueue_style( 'mailster-form-block-editor', MAILSTER_URI . 'assets/css/blocks-editor' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_add_inline_script( 'mailster-form-block-editor', 'var mailster_fields = ' . json_encode( array_values( $this->get_custom_fields() ) ) . ';' );
		wp_add_inline_script( 'mailster-form-block-editor', 'var mailster_inline_styles = ' . json_encode( get_option( 'mailster_inline_styles' ) ) . ';' );

	}

	public function get_custom_fields() {
		$custom_fields = mailster()->get_custom_fields();

		$fields = array(
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

		$custom_fields = array_keys( $this->get_custom_fields() );
		foreach ( $custom_fields as $block ) {

			$types[] = 'mailster/field-' . $block;
		}

		return $types;

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
		register_block_pattern_category( 'forms', array( 'label' => __( 'Forms', 'mailster' ) ) );
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

			$args['categories'] = array( 'mailster-forms' );
			register_block_pattern( 'mailster/' . $key, $args );
		}

	}

	public function render_form_byid( $id, $classes = array() ) {

		if ( get_post_type( $id ) != 'newsletter_form' ) {
			return '';
		}
		$block = parse_blocks( '<!-- wp:mailster/form {"id":"' . $id . '"} /-->' );

		$html = render_block( $block[0] );

		if ( ! empty( $classes ) ) {
			$html = str_replace( 'class="wp-block-mailster-form-outer-wrapper', 'class="wp-block-mailster-form-outer-wrapper ' . implode( ' ', (array) $classes ), $html );
		}

		return $html;
	}


	public function render_form( $args, $content, WP_Block $block ) {

		if ( empty( $args ) ) {
			return;
		}

		if ( ! ( $form = get_post( $args['id'] ) ) ) {
			return;
		}

		// is on a page in the backend and loaded via the REST API
		$is_backend = defined( 'REST_REQUEST' ) && REST_REQUEST;

		if ( ! $is_backend ) {
			wp_enqueue_script( 'mailster-form-block' );
			if ( $cached = get_post_meta( $form->ID, '_cached', true ) ) {
				return $cached;
			}
		}

		$blockattributes = $block->attributes;
		$uniqid          = uniqid();

		$innerblocks = parse_blocks( $form->post_content );
		$output      = '';
		foreach ( $innerblocks as $innerblock ) {
			if ( $innerblock['blockName'] == 'mailster/form-wrapper' ) {
				$output .= render_block( $innerblock );
			}
		}

		$inject  = '';
		$inject .= '<input name="_nonce" type="hidden" value="' . esc_attr( wp_create_nonce( 'mailster-form-nonce' ) ) . '">' . "\n";

		$output = str_replace( '</form>', $inject . '</form>', $output );

		$stylesheets = array();
		if ( $is_backend ) {
			$stylesheets = array( 'style-form.css', 'style-input.css' );
		}

		$classes = array( 'wp-block-mailster-form-outer-wrapper wp-block-mailster-form-outside-wrapper-' . $uniqid . ' wp-block-mailster-form-outside-wrapper-' . $form->ID );
		if ( isset( $blockattributes['align'] ) ) {
			$classes[] = 'align' . $blockattributes['align'];
		}
		if ( isset( $innerblock->attributes['className'] ) ) {
			$classes[] = $innerblock->attributes['className'];
		}

		$stylesheet = '';

		foreach ( $stylesheets as $s ) {
			if ( file_exists( MAILSTER_DIR . 'build/' . $s ) ) {
				$stylesheet .= file_get_contents( MAILSTER_DIR . 'build/' . $s );
			}
		}

		$embeded_style = '';

		if ( isset( $innerblock['attrs']['background'] ) ) {
			$embeded_style .= '.wp-block-mailster-form-outside-wrapper-' . $uniqid . ' .wp-block-mailster-form-wrapper::before{';
			$embeded_style .= 'content:"";background-image:url(' . $innerblock['attrs']['background']['image'] . ');';
			$embeded_style .= 'opacity:' . $innerblock['attrs']['background']['opacity'] . '%;';
			$embeded_style .= 'background-size:' . $innerblock['attrs']['background']['size'] . ';';
			if ( $innerblock['attrs']['background']['fixed'] ) {
				$embeded_style .= 'background-attachment:fixed;';
			}
			if ( $innerblock['attrs']['background']['repeat'] ) {
				$embeded_style .= 'background-repeat:repeat;';
			} else {
				$embeded_style .= 'background-repeat:no-repeat;';
			}
			$embeded_style .= 'background-position:' . ( $innerblock['attrs']['background']['position']['x'] * 100 ) . '% ' . ( $innerblock['attrs']['background']['position']['y'] * 100 ) . '%;';
			$embeded_style .= 'position: absolute;background-repeat: no-repeat;top: 0;left: 0;bottom: 0;right: 0;';
			$embeded_style .= '}';

		}

		if ( $is_backend && $input_styles = get_option( 'mailster_inline_styles' ) ) {
			$stylesheet .= ' .wp-block-mailster-form-outside-wrapper-' . $uniqid . ' .input{';
			$stylesheet .= $input_styles;
			$stylesheet .= '}';
		}

		require MAILSTER_DIR . 'classes/libs/InlineStyle/autoload.php';

		$i_error = libxml_use_internal_errors( true );
		$htmldoc = new \InlineStyle\InlineStyle();

		if ( isset( $innerblock['attrs']['css'] ) ) {
			foreach ( $innerblock['attrs']['css'] as $name => $css ) {
				if ( empty( $css ) ) {
					continue;
				}
				$parsed = $htmldoc->parseStylesheet( $css );
				$css    = '';
				foreach ( $parsed as $rule ) {
					$selector = array_shift( $rule );
					if ( ! empty( $rule ) ) {
						$css .= '.wp-block-mailster-form-outside-wrapper-' . $uniqid . ' ' . $selector . '{' . implode( ';', $rule ) . '}';
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

		$parsed = $htmldoc->parseStylesheet( $stylesheet );
		foreach ( $parsed as $rule ) {
			$selector = array_shift( $rule );
			if ( ! empty( $rule ) && preg_match( '(::?(after|before))', $selector ) ) {
				$embeded_style .= '.wp-block-mailster-form-outside-wrapper-' . $uniqid . ' ' . $selector . '{' . implode( ';', $rule ) . '}';
			}
		}

		if ( ! empty( $embeded_style ) ) {
			$output = '<style>' . $embeded_style . '</style>' . $output;
		}

		$output = $uniqid . '<div class="' . implode( ' ', $classes ) . '">' . $output . '</div>';

		$htmldoc->loadHTML( $output );

		$htmldoc->applyStylesheet( $stylesheet );

		$html = $htmldoc->getHTML();
		libxml_clear_errors();
		libxml_use_internal_errors( $i_error );

		preg_match( '#<body([^>]*)>(.*)<\/body>#is', $html, $matches );
		if ( ! empty( $matches ) ) {
			$html = trim( $matches[2] );
		}

		if ( $is_backend ) {
			$html = do_shortcode( $html );
		}

		if ( ! $is_backend ) {
			update_post_meta( $form->ID, '_cached', $html, true );
		}

		return ( $html );

	}

	public function clear_cache( $post_id, $post ) {

		delete_post_meta( $post_id, '_cached' );

	}
}
