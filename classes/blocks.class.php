<?php

class MailsterBlocks {

	private $blocks__ = array( 'form-wrapper', 'input', 'email', 'button' );
	private $blocks   = array( 'form-wrapper', 'input', 'button', 'gdpr' );

	public function __construct() {

		// since 5.8
		if ( ! function_exists( 'get_allowed_block_types' ) ) {
			return;
		}

		// add_action( 'enqueue_block_editor_assets', array( &$this, 'register_sidebar_script' ) );

		add_action(
			'save_post',
			function( $post_id, $post ) {

				error_log( print_r( $post->post_content, true ) );
			},
			10,
			2
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



}
