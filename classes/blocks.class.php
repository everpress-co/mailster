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
				'styles'          => array(
					array(
						'name'      => 'default',
						'label'     => esc_html__( 'Default', 'mailster' ),
						'isDefault' => true,
					),
					array(
						'name'  => 'outline',
						'label' => esc_html__( 'Outline', 'mailster' ),
					),
					array(
						'name'  => 'squared',
						'label' => esc_html__( 'Squared', 'mailster' ),
					),
				),

				'style'           => 'mailster-form-block',
				'attributes'      => array(
					'className'    => array(
						'type'    => 'string',
						'default' => '',
					),
					'name'         => array(
						'type'    => 'string',
						'default' => 'Form',
					),
					'submit'       => array(
						'type'    => 'string',
						'default' => 'Subscribe',
					),
					'submittype'   => array(
						'type'    => 'string',
						'default' => 'button',
					),
					'doubleoptin'  => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'asterisk'     => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'prefill'      => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'overwrite'    => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'userchoice'   => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'inline'       => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'custom_style' => array(
						'type'    => 'string',
						'default' => '',
					),
					'subject'      => array(
						'type'    => 'string',
						'default' => esc_html__( 'Please confirm', 'mailster' ),
					),
					'headline'     => array(
						'type'    => 'string',
						'default' => esc_html__( 'Please confirm your Email Address', 'mailster' ),
					),
					'link'         => array(
						'type'    => 'string',
						'default' => esc_html__( 'Click here to confirm', 'mailster' ),
					),
					'content'      => array(
						'type'    => 'string',
						'default' => sprintf( esc_html__( 'You have to confirm your email address. Please click the link below to confirm. %s', 'mailster' ), "\n{link}" ),
					),
					'gdpr'         => array(
						'type'    => 'boolean',
						'default' => (bool) mailster_option( 'gdpr' ),
					),
					'gdpr_text'    => array(
						'type'    => 'string',
						'default' => esc_html__( 'I agree to the privacy policy and terms.', 'mailster' ),
					),
					'gdpr_error'   => array(
						'type'    => 'string',
						'default' => esc_html__( 'You have to agree to the privacy policy and terms!', 'mailster' ),
					),
				),
			)
		);

		$form_blocks = array(
			'input',
			'email',
		);

		foreach ( $form_blocks as $form_block ) {

			register_block_type(
				'mailster/field-' . $form_block,
				array(
					'multiple'        => false,
					'title'           => '' . $form_block,
					'parent'          => array( 'mailster/form' ),
					'category'        => 'mailster-form-fields',
					'render_callback' => array( &$this, 'render_field_' . $form_block ),
					'attributes'      => array(
						'label'    => array(
							'type'    => 'string',
							'default' => $form_block . ' Field',
						),
						'required' => array(
							'type'    => 'boolean',
							'default' => true,
						),
					),
				)
			);

		}

	}


	public function render_form( $attr, $content ) {

		error_log( print_r( $attr, true ) );
		error_log( print_r( $content, true ) );

		$classes = array( 'mailster-form' );

		$buttonlabel = 'Submit';

		ob_start();
		?>

		<div class="wp-block-mailster-form <?php echo esc_attr( $attr['className'] ); ?>">
			<form action="" method="post" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" novalidate>

			<input name="_referer" type="hidden" value="<?php echo esc_attr( wp_get_referer() ); ?>">
			<input name="_nonce" type="hidden" value="<?php echo wp_create_nonce( 'adfsf' ); ?>">

			<div class="mailster-form-fields">
				<?php echo do_blocks( $content ); ?>
				<div class="mailster-wrapper mailster-submit-wrapper form-submit">
					<button name="submit" class="submit-button button" aria-label="<?php echo esc_attr( $attr['submit'] ); ?>"><?php echo esc_html( $attr['submit'] ); ?></button>
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
				'asterisk' => true,
				'required' => false,
				'value'    => '',
				'classes'  => array(),
			)
		);

		$attr['classes'][] = 'input';
		$attr['classes'][] = 'mailster-' . $name;

		if ( $attr['required'] ) {
			$attr['classes'][] = 'mailster-required';
		}

		ob_start();
		?>

		<div class="mailster-wrapper mailster-<?php echo esc_attr( $name ); ?>-wrapper">
			<label for="mailster-<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $attr['label'] ); ?>
			<?php if ( $attr['required'] ) : ?>
				 <span class="mailster-required">*</span>
			<?php endif; ?>
			</label>
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
