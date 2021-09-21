<?php

class MailsterBlocks {

	private $blocks = array( 'input' );

	public function __construct() {

		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		add_action( 'init', array( &$this, 'register_blocks' ) );
		// add_action( 'block_categories', array( &$this, 'block_categories' ), 10, 2 );
		// add_filter( 'allowed_block_types_all', array( &$this, 'allowed_block_types' ), 10, 2 );	}
		//
	}

	public function allowed_block_types( $allowed_block_types, $post ) {

		if ( 'newsletter_form' != get_post_type( $post ) ) {
			return $allowed_block_types;

		}

		return array( 'mailster/form', 'mailster/field-firstname', 'mailster/field-lastname' );

	}

	public function block_categories( $categories, $post ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'mailster-form-fields',
					'title' => __( 'Newsletter Form Fields', 'mailster' ),
				),

			)
		);
	}

	public function register_blocks() {

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

	public function render_form( $attr, $content ) {

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

	public function render_field_input( $attr, $content ) {

		$attr = wp_parse_args( array( 'type' => 'text' ), $attr );
		return $this->render_field( 'input', $attr );

	}

	public function render_field_email( $attr, $content ) {

		$attr = wp_parse_args(
			array(
				'required' => true,
				'type'     => 'email',
			),
			$attr
		);

		return $this->render_field( 'email', $attr );

	}

	private function render_field( $name, $attr ) {

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
