<?php

class MailsterBlocks {

	private $blocks__ = array( 'form-wrapper', 'input', 'email', 'button' );
	private $blocks   = array( 'form-wrapper', 'input', 'button' );

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

		add_action( 'init', array( &$this, 'register_block_pattern' ) );
		add_action( 'init', array( &$this, 'register_block_pattern_category' ) );

	}

	public function block_init() {

		register_block_type( MAILSTER_DIR . 'blocks/form/', array( 'render_callback' => array( $this, 'render_form' ) ) );

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

			if ( $typenow != 'newsletter_form' ) {
				return;
			}

			$this->register_block_pattern();
			$this->register_block_pattern_category();
		}

		foreach ( $this->blocks as $block ) {

			$args = array();

			if ( method_exists( $this, 'render_' . $block ) ) {
				$args['render_callback'] = array( $this, 'render_' . $block );
			}
			register_block_type( MAILSTER_DIR . 'blocks/' . $block . '/', $args );
		}

	}

	public function api_init() {
		register_rest_route(
			'mailster/v1',
			'/lists',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_lists' ),
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
		register_block_pattern(
			'mailster/simplepatter',
			array(
				'title'       => __( 'Simple form', 'mailster' ),
				'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
				'categories'  => array( 'forms' ),
				'content'     => "<!-- wp:mailster/form-wrapper {\"style\":{\"padding\":{\"top\":\"1em\",\"left\":\"1em\",\"right\":\"1em\",\"bottom\":\"1em\"}},\"backgroundColor\":\"light-background\",\"textColor\":\"buttons-text\"} -->\n<div class=\"wp-block-mailster-form-wrapper has-buttons-text-color has-light-background-background-color has-text-color has-background\"><!-- wp:mailster/input {\"label\":\"Email\",\"optionalRequired\":false,\"type\":\"email\"} -->\n<div class=\"wp-block-mailster-input\"><label>Email</label><input name=\"asdads\" type=\"email\" value=\"\" class=\"input mailster-email mailster-required\" arialabel=\"Email\" spellcheck=\"false\"/></div>\n<!-- /wp:mailster/input -->\n\n<!-- wp:mailster/button /--></div>\n<!-- /wp:mailster/form-wrapper -->",
			)
		);
	}


	public function render_form( $args, $content, WP_Block $block ) {

		if ( empty( $args ) ) {
			return;
		}

		if ( ! ( $form = get_post( $args['id'] ) ) ) {
			return;
		}

		$blocks = parse_blocks( $form->post_content );
		$output = '';
		foreach ( $blocks as $block ) {
			if ( $block['blockName'] == 'mailster/form-wrapper' ) {

				echo '<pre>' . esc_html( print_r( render_block( $block ), true ) ) . '</pre>';
				$output .= render_block( $block );

			}
			error_log( print_r( $block, true ) );
			// code...
		}

		error_log( print_r( $output, true ) );

		$stylesheets = array( 'style-form.css', 'style-input.css' );

		// $output  = apply_filters( 'the_content', $form->post_content );

		$classes = array( 'wp-block-mailster-form-wrapper' );
		if ( isset( $block->attributes['className'] ) ) {
			$classes[] = $block->attributes['className'];
		}

		// $output     = '<div class=" ' . implode( ' ', $classes ) . '">' . $output . '</div>';
		$stylesheet = '';

		foreach ( $stylesheets as $s ) {
			if ( file_exists( MAILSTER_DIR . 'build/' . $s ) ) {
				$stylesheet .= file_get_contents( MAILSTER_DIR . 'build/' . $s );
			}
		}

		$stylesheet .= '.wp-block-mailster-form-wrapper{';
		$style       = get_post_meta( $form->ID, 'style', true );
		$style       = $block['attrs']['style'];
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
					// $stylesheet .= $key . ':' . $value . ';';
					break;
				default:
					$stylesheet .= $key . ':' . $value . ';';
					break;
			}
		}
		$stylesheet .= '}';

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

		error_log( print_r( $html, true ) );

		return $html;

	}

	public function get_lists( WP_REST_Request $request ) {

		$query = get_posts(
			array(
				'post_type' => 'newsletter_form',

			)
		);

		$return = array();

		foreach ( $query as $list ) {
			$return[] = array(
				'label' => $list->post_title,
				'value' => (int) $list->ID,
			);
		}

		return $return;

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

		wp_enqueue_script( 'mailster-form-block-editor', MAILSTER_URI . '/build/form-inspector.js', array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-plugins', 'wp-edit-post' ), MAILSTER_VERSION );
	}



	public function block_script_styles() {
		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'mailster-form-detail', MAILSTER_URI . 'assets/js/form-script' . $suffix . '.js', array( 'mailster-script', 'jquery' ), MAILSTER_VERSION );

		wp_register_script( 'mailster-form-block-editor', MAILSTER_URI . 'assets/js/blocks' . $suffix . '.js', array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-plugins', 'wp-edit-post' ), MAILSTER_VERSION );
	}

	public function register_blocks() {

		// register_block_type( 'mailster/base', [
		// 'editor_script' => 'mailster-form-detail',
		// ] );

		require_once MAILSTER_DIR . 'blocks/base.php';
		require_once MAILSTER_DIR . 'blocks/input.php';

		return;

		$suffix = SCRIPT_DEBUG ? '' : '.min';
		// wp_register_script( 'mailster-form-block-editor', MAILSTER_URI . 'assets/js/blocks' . $suffix . '.js', array( 'wp-blocks', 'wp-i18n', 'wp-element' ), MAILSTER_VERSION );
		// wp_register_style( 'mailster-form-block-editor', MAILSTER_URI . 'assets/css/blocks-editor' . $suffix . '.css', array(), MAILSTER_VERSION );
		// wp_register_style( 'mailster-form-block', MAILSTER_URI . 'assets/css/blocks-style' . $suffix . '.css', array(), MAILSTER_VERSION );

		$lists       = mailster( 'lists' )->get_simple();
		$lists_order = array_keys( $lists );

		$form_attributes = array(
			'formColor'      => '',
			'formBGColor'    => '',
			'buttonColor'    => '',
			'buttonBGColor'  => '',
			'align'          => '',
			'align'          => '',
			'className'      => '',
			'name'           => 'Form',
			'submit'         => 'Subscribe',
			'submittype'     => 'button',
			'doubleoptin'    => true,
			'dropdown'       => false,
			'asterisk'       => true,
			'prefill'        => false,
			'overwrite'      => false,
			'userchoice'     => false,
			'inline'         => false,
			'custom_style'   => '',
			'subject'        => esc_html__( 'Please confirm', 'mailster' ),
			'headline'       => esc_html__( 'Please confirm your Email Address', 'mailster' ),
			'link'           => esc_html__( 'Click here to confirm', 'mailster' ),
			'content'        => sprintf( esc_html__( 'You have to confirm your email address. Please click the link below to confirm. %s', 'mailster' ), "\n{link}" ),
			'gdpr'           => (bool) mailster_option( 'gdpr' ),
			'gdpr_text'      => esc_html__( 'I agree to the privacy policy and terms.', 'mailster' ),
			'gdpr_error'     => esc_html__( 'You have to agree to the privacy policy and terms!', 'mailster' ),
			'errorColor'     => '',
			'errorBGColor'   => '',
			'successColor'   => '',
			'successBGColor' => '',
			'confirmMessage' => mailster_text( 'confirmation' ),
			'successMessage' => mailster_text( 'success' ),
			'errorMessage'   => mailster_text( 'error' ),
			'lists_selected' => array( 3 ),
			'lists_order'    => $lists_order,
			'lists'          => $lists,
			'lists_a'        => array(),
		);

		$styles = array(
			array(
				'name'         => 'fancy',
				'label'        => esc_html__( 'Fancy', 'mailster' ),
				'style_handle' => 'mailster-form-block-fancy',
			),
			array(
				'name'         => 'boxed',
				'label'        => esc_html__( 'Boxed', 'mailster' ),
				'style_handle' => 'mailster-form-block-boxed',
			),
		);

		register_block_type(
			'mailster/form',
			array(
				'render_callback' => array( $this, 'render_form' ),
				'editor_script'   => 'mailster-form-block-editor',
				'editor_style'    => 'mailster-form-block-editor',
				'title'           => esc_html__( 'Newsletter Sign up', 'mailster' ),
				'description'     => esc_html__( 'Add A Newsletter Signup Form', 'mailster' ),
				'keywords'        => array( 'mailster', esc_html__( 'newsletter', 'mailster' ), esc_html__( 'signup', 'mailster' ) ),
				'category'        => 'widgets',
				'supports'        => array(
					'align' => array( 'wide', 'full' ),
					'html'  => false,
				),
				'styles'          => $styles,

				'style'           => 'mailster-form-block',
				'attributes'      => array_map(
					function( $k ) {
						$a = array(
							'type'    => gettype( $k ),
							'default' => $k,
						);
						if ( $a['type'] == 'array' ) {
							$a['items'] = null;
						};

						return $a;
					},
					$form_attributes
				),
			)
		);

		foreach ( $styles as $style ) {
			if ( isset( $style['style_handle'] ) ) {
				wp_register_style( $style['style_handle'], MAILSTER_URI . 'assets/css/blocks-style-' . $style['name'] . $suffix . '.css', array(), MAILSTER_VERSION );
			}
			register_block_style( 'mailster/form', $style );
		}

		$customfields = mailster()->get_custom_fields();

		$fields = array(
			'email'     => mailster_text( 'email' ),
			'firstname' => mailster_text( 'firstname' ),
			'lastname'  => mailster_text( 'lastname' ),
		);

		$this->register_form_block( mailster_text( 'email' ), 'email', array( 'required' => true ) );
		$this->register_form_block( mailster_text( 'firstname' ), 'firstname' );
		$this->register_form_block( mailster_text( 'lastname' ), 'lastname' );

		if ( $customfields ) {
			foreach ( $customfields as $field => $data ) {
				$fields[ $field ] = $data['name'];
			}
		}

	}

	private function register_form_block( $name, $id = null, $args = array() ) {

		if ( is_null( $id ) ) {
			$id = sanitize_key( $name );
		}

		$attributes = array(
			'label'    => array(
				'type'    => 'string',
				'default' => $name,
			),
			'required' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'width'    => array(
				'type'    => 'number',
				'default' => 100,
			),
			'margin'   => array(
				'type'    => 'number',
				'default' => 100,
			),
		);

		foreach ( $args as $key => $value ) {
			if ( isset( $attributes[ $key ] ) ) {
				$attributes[ $key ]['default'] = $value;
			} else {
				if ( is_array( $value ) ) {
					$attributes[ $key ] = $value;
				} else {
					$attributes[ $key ] = array(
						'type'    => gettype( $value ),
						'default' => $value,
					);
				}
			}
		}

		register_block_type(
			'mailster/field-' . $id,
			array(
				'supports'        => array(
					'multiple' => false,
					'html'     => false,
					'reusable' => false,
					// 'inserter' => false,
				),
				'icon'            => 'smiley',
				'title'           => $name,
				// 'parent'          => array( 'mailster/form' ),
				'category'        => 'mailster-form-fields',
				'render_callback' => is_callable( array( &$this, 'render_field_' . $id ) ) ? array( &$this, 'render_field_' . $id ) : array( &$this, 'render_field_input' ),
				'attributes'      => $attributes,
			)
		);
	}

	public function _render_form( $attr, $content ) {

		$classes      = array( 'mailster-form' );
		$formstyles   = array();
		$buttonstyles = array();

		if ( $attr['asterisk'] ) {
			$attr['className'] .= ' has-asterisk';
		}
		if ( $attr['align'] ) {
			$attr['className'] .= ' align' . $attr['align'];
		}

		if ( $attr['formColor'] ) {
			$formstyles[] = 'color:' . $attr['formColor'];
		}
		if ( $attr['formBGColor'] ) {
			$formstyles[] = 'background-color:' . $attr['formBGColor'];
		}

		if ( $attr['buttonColor'] ) {
			$buttonstyles[] = 'color:' . $attr['buttonColor'];
		}
		if ( $attr['buttonBGColor'] ) {
			$buttonstyles[] = 'background-color:' . $attr['buttonBGColor'];
		}

		ob_start();

		?>

		<div class="wp-block-mailster-form <?php echo esc_attr( $attr['className'] ); ?>"<?php echo $formstyles ? ' style="' . esc_attr( implode( ';', $formstyles ) ) . '"' : ''; ?>>
			<form action="" method="post" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" novalidate>

			<input name="_referer" type="hidden" value="<?php echo esc_attr( wp_get_referer() ); ?>">
			<input name="_nonce" type="hidden" value="<?php echo wp_create_nonce( 'adfsf' ); ?>">

			<div class="mailster-form-fields">
				<?php echo do_blocks( $content ); ?>
				<?php if ( $attr['gdpr'] ) : ?>

				<div class="wp-block-mailster-field wp-block-mailster-field-gdpr">
					<input type="hidden" name="_gdpr" value="0">
					<label>
					<input id="mailster-_gdpr-" name="_gdpr" type="checkbox" value="1" class="mailster-_gdpr mailster-required" aria-required="true" aria-label="<?php echo esc_attr( $attr['gdpr_text'] ); ?>"> <?php echo esc_html( $attr['gdpr_text'] ); ?></label>
				</div>
				<?php endif; ?>

				<div class="wp-block-mailster-field wp-block-mailster-field-submit wp-block-button">
					<button type="submit" class="wp-block-button__link" aria-label="<?php echo esc_attr( $attr['submit'] ); ?>"<?php echo $buttonstyles ? ' style="' . esc_attr( implode( ';', $buttonstyles ) ) . '"' : ''; ?>><?php echo esc_html( $attr['submit'] ); ?></button>
				</div>

			</div>
			</form>
		</div>

		<?php
		return ob_get_clean();

	}

	public function _render_field_input( $attr, $content ) {

		$attr = wp_parse_args( array( 'type' => 'text' ), $attr );
		return $this->render_field( 'input', $attr );

	}

	public function _render_field_email( $attr, $content ) {

		$attr = wp_parse_args(
			array(
				'required' => true,
				'type'     => 'email',
			),
			$attr
		);

		return $this->render_field( 'email', $attr );

	}

	private function _render_field( $name, $attr ) {

		$name = sanitize_key( $name );
		$id   = uniqid( $name . '-' );

		$attr = wp_parse_args(
			$attr,
			array(
				'label'    => '',
				'type'     => 'text',
				'required' => false,
				'value'    => '',
				'classes'  => array(),
			)
		);

		$attr['wrapperclasses'] = array(
			'wp-block-mailster-field',
			'wp-block-mailster-field-' . $name,
		);

		$styles = array();

		if ( isset( $attr['width'] ) ) {
			$styles[] = 'flex-basis:' . absint( $attr['width'] ) . '%';
		}

		// $attr['classes'][] = 'input';
		// $attr['classes'][] = 'mailster-' . $name;

		if ( $attr['required'] ) {
			// $attr['classes'][]        = 'mailster-required';
			$attr['wrapperclasses'][] = 'is-required';
		}

		ob_start();
		?>
				<div class="<?php echo esc_attr( implode( ' ', $attr['wrapperclasses'] ) ); ?>" style="<?php echo esc_attr( implode( ' ', $styles ) ); ?>">
					<label for="mailster-<?php echo esc_attr( $id ); ?>" class="mailster-label"><?php echo esc_html( $attr['label'] ); ?></label>
					<input id="mailster-<?php echo esc_attr( $id ); ?>"
						name="<?php echo esc_attr( $id ); ?>"
						type="<?php echo esc_attr( $attr['type'] ); ?>"
						value="<?php echo esc_attr( $attr['value'] ); ?>"
						class="<?php echo esc_attr( implode( ' ', $attr['classes'] ) ); ?>"
						aria-required="<?php echo ( $attr['required'] ? 'true' : 'false' ); ?>"
						aria-label="<?php echo esc_attr( $attr['label'] ); ?>"
						spellcheck="false" />
				</div>
		<?php
		return ob_get_clean();
	}

}
