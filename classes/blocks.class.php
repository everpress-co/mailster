<?php

class MailsterBlocks {

	private $blocks__ = array( 'form-wrapper', 'input', 'email', 'button' );
	private $blocks   = array( 'form-wrapper', 'input', 'button', 'gdpr' );

	public function __construct() {

		// since 5.8
		if ( ! function_exists( 'get_allowed_block_types' ) ) {
			return;
		}

		add_action( 'enqueue_block_editor_assets', array( &$this, 'register_sidebar_script' ) );
		add_action( 'init', array( &$this, 'block_init' ) );
		add_action( 'rest_api_init', array( &$this, 'api_init' ) );
		// add_action( 'init', array( &$this, 'register_sidebar_script' ) );

		add_action(
			'save_post',
			function( $post_id, $post ) {

				error_log( print_r( $post->post_content, true ) );
			},
			10,
			2
		);

		add_filter( 'allowed_block_types_all', array( &$this, 'allowed_block_types' ), 10, 2 );
		add_filter( 'block_categories_all', array( &$this, 'block_categories' ) );

		add_action( 'init', array( &$this, 'post_type_template' ) );

	}

	public function block_init() {

		// from https://www.designbombs.com/registering-gutenberg-blocks-for-custom-post-type/
		if ( is_admin() ) {
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
				$this->register_block_pattern();
				$this->register_block_pattern_category();

				if ( function_exists( 'wp_enqueue_code_editor' ) ) {
					wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
				} else {
					wp_enqueue_script( 'mailster-codemirror', MAILSTER_URI . 'assets/js/libs/codemirror' . $suffix . '.js', array(), MAILSTER_VERSION );
					wp_enqueue_style( 'mailster-codemirror', MAILSTER_URI . 'assets/css/libs/codemirror' . $suffix . '.css', array(), MAILSTER_VERSION );
				}
				foreach ( $this->blocks as $block ) {

					$args = array();

					if ( method_exists( $this, 'render_' . $block ) ) {
						$args['render_callback'] = array( $this, 'render_' . $block );
					}
					register_block_type( MAILSTER_DIR . 'blocks/' . $block . '/', $args );
				}

				return;
			}
		}

		register_block_type( MAILSTER_DIR . 'blocks/form/', array( 'render_callback' => array( $this, 'render_form' ) ) );

	}

	public function api_init() {
		register_rest_route(
			'mailster/v1',
			'/forms',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_forms' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'mailster/v1',
			'/fields',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_fields' ),
				'permission_callback' => '__return_true',
			)
		);

	}


	public function post_type_template() {

		return;
		if ( $page_type_object = get_post_type_object( 'newsletter_form' ) ) {
			// $page_type_object->template_lock = 'removal';
			$page_type_object->template = array(
				array(
					'mailster/form-wrapper',
					array(),
					array(
						array( 'mailster/input' ),
						array( 'mailster/button' ),
					),
				),
			);
		}

		return;

		if ( $page_type_object = get_post_type_object( 'newsletter_form' ) ) {
			// $page_type_object->template_lock = 'removal';
			$page_type_object->template = array(
				array(
					'mailster/form-wrapper',
					array(),
					array(),
				),
			);
		}
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


	public function render_form( $args, $content, WP_Block $block ) {

		if ( empty( $args ) ) {
			return;
		}

		if ( ! ( $form = get_post( $args['id'] ) ) ) {
			return;
		}

		$blockattributes = $block->attributes;
		// is on a page in the backend and loaded via the REST API
		$is_backend = defined( 'REST_REQUEST' ) && REST_REQUEST;

		if ( ! $is_backend ) {
			wp_enqueue_script( 'mailster-form-block' );
		}

		$uniqid = uniqid();

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
		if ( is_admin() ) {
			$stylesheets = array( 'style-form.css', 'style-input.css' );
		}

		$classes = array( 'wp-block-mailster-form-outer-wrapper wp-block-mailster-form-outside-wrapper-' . $uniqid );
		if ( isset( $blockattributes['align'] ) ) {
			$classes[] = 'align' . $blockattributes['align'];
		}
		if ( isset( $innerblock->attributes['className'] ) ) {
			$classes[] = $innerblock->attributes['className'];
		}

		$output     = '<div class="' . implode( ' ', $classes ) . '">' . $output . '</div>';
		$stylesheet = '';

		foreach ( $stylesheets as $s ) {
			if ( file_exists( MAILSTER_DIR . 'build/' . $s ) ) {
				$stylesheet .= file_get_contents( MAILSTER_DIR . 'build/' . $s );
			}
		}

		$style = get_post_meta( $form->ID, 'style', true );

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

		if ( $is_backend && $input_styles = get_post_meta( $form->ID, 'input_styles', true ) ) {
			$stylesheet .= ' .wp-block-mailster-form-outside-wrapper-' . $uniqid . ' .input{';
			$stylesheet .= $input_styles;
			$stylesheet .= '}';
		}

		if ( isset( $innerblock['attrs']['style'] ) ) {
			if ( $style = $innerblock['attrs']['style'] ) {

				$stylesheet .= '.wp-block-mailster-form-outside-wrapper-' . $uniqid . '{';
				foreach ( $style as $key => $value ) {
					$key = strtolower( preg_replace( '/([A-Z])+/', '-$1', $key ) );
					switch ( $key ) {
						case 'padding':
							// $value = json_decode( $value );
							foreach ( $value as $pk => $pv ) {
								$stylesheet .= $key . '-' . $pk . ':' . $pv . ';';
							}
							break;
						case 'background-position':
							// $value       = json_decode( $value );
							$stylesheet .= $key . ':' . ( $value->x * 100 ) . '% ' . ( $value->y * 100 ) . '%;';
							break;
						case 'background-image':
							$value       = 'url(\'' . $value . '\')';
							$stylesheet .= $key . ':' . $value . ';';
							break;
						case 'width':
						case 'height':
						case 'color':
							// $stylesheet .= $key . ':' . $value . ';';
							break;
						default:
							$stylesheet .= $key . ':' . $value . ';';
							break;
					}
				}
				$stylesheet .= '}';
			}
		}

		if ( isset( $innerblock['attrs']['css'] ) ) {
			$stylesheet .= $innerblock['attrs']['css'];
		}

		require MAILSTER_DIR . 'classes/libs/InlineStyle/autoload.php';

		$i_error = libxml_use_internal_errors( true );
		$htmldoc = new \InlineStyle\InlineStyle( $output );

		$htmldoc->applyStylesheet( $stylesheet );

		$html = $htmldoc->getHTML();
		libxml_clear_errors();
		libxml_use_internal_errors( $i_error );

		preg_match( '#<body([^>]*)>(.*)<\/body>#is', $html, $matches );
		if ( ! empty( $matches ) ) {
			$html = trim( $matches[2] );
		}

		if ( ! empty( $embeded_style ) ) {
			$html = '<style>' . $embeded_style . '</style>' . $html;
		}

		if ( $is_backend ) {
			$html = do_shortcode( $html );
		}

		return ( $html );

	}

	public function get_forms( WP_REST_Request $request ) {

		$query = get_posts(
			array(
				'post_type' => 'newsletter_form',

			)
		);

		$return = array();

		foreach ( $query as $form ) {
			$return[] = array(
				'label' => $form->post_title,
				'value' => (int) $form->ID,
			);
		}

		return $return;

	}

	public function get_fields( WP_REST_Request $request ) {

		$custom_fields = array_values( mailster()->get_custom_fields() );

		$fields = array(
			array(
				'name' => 'Email',
				'id'   => 'email',
			),
			array(
				'name' => 'First Name',
				'id'   => 'firstname',
			),
			array(
				'name' => 'Last name',
				'id'   => 'lastname',
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

		// $types = array( 'core/paragraph' );

		foreach ( $this->blocks as $block ) {
			$types[] = 'mailster/' . $block;
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



	public function register_sidebar_script() {

		if ( 'newsletter_form' != get_post_type() ) {
			return false;
		}

		wp_enqueue_script( 'mailster-form-block-editor', MAILSTER_URI . '/build/form-inspector.js', array( 'mailster-script', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-plugins', 'wp-edit-post' ), MAILSTER_VERSION );

		wp_localize_script(
			'mailster-form-block-editor',
			'xxxx',
			array(
				'fields' => array( 'asda' => 'Asdsfd' ),
			)
		);
	}



	public function block_script_styles() {
		// $suffix = SCRIPT_DEBUG ? '' : '.min';

		// wp_register_script( 'mailster-form-detail', MAILSTER_URI . 'assets/js/form-script' . $suffix . '.js', array( 'mailster-script', 'jquery' ), MAILSTER_VERSION );

		// wp_register_script( 'mailster-form-block-editor', MAILSTER_URI . 'assets/js/blocks' . $suffix . '.js', array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-plugins', 'wp-edit-post' ), MAILSTER_VERSION );
		//
	}


}
