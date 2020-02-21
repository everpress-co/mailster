<?php

class MailsterBlocks {

	private $blocks = array( 'input' );

	public function __construct() {

		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		add_action( 'init', array( &$this, 'register_blocks' ) );
		add_action( 'block_categories', array( &$this, 'block_categories' ), 10, 2 );

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

		$suffix = SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'mailster-form-block-editor', MAILSTER_URI . 'blocks/blocks' . $suffix . '.js', array( 'wp-blocks', 'wp-i18n', 'wp-element' ), MAILSTER_VERSION );
		wp_register_style( 'mailster-form-block-editor', MAILSTER_URI . 'blocks/editor' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_register_style( 'mailster-form-block', MAILSTER_URI . 'blocks/style' . $suffix . '.css', array(), MAILSTER_VERSION );

		$form_attributes = array(
			'align'           => '',
			'className'       => '',
			'name'            => 'Form',
			'submit'          => 'Subscribe',
			'submittype'      => 'button',
			'doubleoptin'     => true,
			'asterisk'        => true,
			'prefill'         => true,
			'overwrite'       => false,
			'userchoice'      => false,
			'inline'          => false,
			'custom_style'    => '',
			'subject'         => esc_html__( 'Please confirm', 'mailster' ),
			'headline'        => esc_html__( 'Please confirm your Email Address', 'mailster' ),
			'link'            => esc_html__( 'Click here to confirm', 'mailster' ),
			'content'         => sprintf( esc_html__( 'You have to confirm your email address. Please click the link below to confirm. %s', 'mailster' ), "\n{link}" ),
			'gdpr'            => (bool) mailster_option( 'gdpr' ),
			'gdpr_text'       => esc_html__( 'I agree to the privacy policy and terms.', 'mailster' ),
			'gdpr_error'      => esc_html__( 'You have to agree to the privacy policy and terms!', 'mailster' ),
			'errorColor'      => '',
			'errorBGColor'    => '',
			'successColor'    => '',
			'successBGColor'  => '',
			'confirmMessage'  => mailster_text( 'confirmation' ),
			'successMessage'  => mailster_text( 'success' ),
			'errorMessage'    => mailster_text( 'error' ),
			'lists'           => array( 1, 3 ),
			'available_lists' => mailster( 'lists' )->get(),
		);

		register_block_type(
			'mailster/form',
			array(
				'render_callback' => array( $this, 'render_form' ),
				'editor_script'   => 'mailster-form-block-editor',
				'editor_style'    => 'mailster-form-block-editor',
				'title'           => esc_html__( 'Newsletter Sign up', 'mailster' ),
				'description'     => esc_html__( 'Add A Newsletter Signup Form', 'mailster' ),
				'keywords'        => array( esc_html__( 'newsletter', 'mailster' ), esc_html__( 'signup', 'mailster' ) ),
				'category'        => 'widgets',
				'supports'        => array(
					'align' => array( 'wide', 'full' ),
				),
				'styles'          => array(
					array(
						'name'      => 'default',
						'label'     => esc_html__( 'Default', 'mailster' ),
						'isDefault' => true,
					),
					array(
						'name'  => 'fancy',
						'label' => esc_html__( 'Fancy', 'mailster' ),
					),
					array(
						'name'  => 'squared',
						'label' => esc_html__( 'Squared', 'mailster' ),
					),
				),

				'style'           => 'mailster-form-block',
				'attributes'      => array_map(
					function( $k ) {
						return array(
							'type'    => gettype( $k ),
							'default' => $k,
						);
					},
					$form_attributes
				),
			)
		);

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
				'type'    => 'string',
				'default' => '100%',
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
					// 'inserter' => false,
				),
				'icon'            => '<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" transform="scale(0.9)"><path fill="#ff0000" d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z" transform="scale(0.9)"></path></svg>',
				'icon'            => 'smiley',
				'title'           => $name,
				'parent'          => array( 'mailster/form' ),
				'category'        => 'mailster-form-fields',
				'render_callback' => is_callable( array( &$this, 'render_field_' . $id ) ) ? array( &$this, 'render_field_' . $id ) : array( &$this, 'render_field_input' ),
				'attributes'      => $attributes,
			)
		);
	}

	public function render_form( $attr, $content ) {

		$classes = array( 'mailster-form' );

		if ( $attr['asterisk'] ) {
			$attr['className'] .= ' has-asterisk';
		}
		if ( $attr['align'] ) {
			$attr['className'] .= ' align' . $attr['align'];
		}

		error_log( print_r( $attr, true ) );
		ob_start();
		?>

		<div class="wp-block-mailster-form <?php echo esc_attr( $attr['className'] ); ?>">
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
					<button type="submit" class="wp-block-button__link" aria-label="<?php echo esc_attr( $attr['submit'] ); ?>"><?php echo esc_html( $attr['submit'] ); ?></button>
				</div>

			</div>
			</form>
		</div>

		<?php
		return ob_get_clean();

	}

	public function render_field_input( $attr, $content ) {

		$attr = wp_parse_args(
			array(
				'type' => 'text',
			),
			$attr
		);
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

		// $attr['classes'][] = 'input';
		// $attr['classes'][] = 'mailster-' . $name;

		if ( $attr['required'] ) {
			// $attr['classes'][]        = 'mailster-required';
			$attr['wrapperclasses'][] = 'is-required';
		}

		ob_start();
		?>
				<div class="<?php echo esc_attr( implode( ' ', $attr['wrapperclasses'] ) ); ?>">
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
