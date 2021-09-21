<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) for the
 * Gutenberg block.
 *
 * @package mailster
 */

/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 *
 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/tutorials/block-tutorial/applying-styles-with-stylesheets/
 */
function base_block_init() {
	// Skip block registration if Gutenberg is not enabled/merged.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}
	$dir = dirname( __FILE__ );

	$index_js = 'base/index.js';
	wp_register_script(
		'base-block-editor',
		plugins_url( $index_js, __FILE__ ),
		array(
			'mailster-form-detail',
			'mailster-form-block-editor',
			'wp-blocks',
			'wp-i18n',
			'wp-element',
		),
		filemtime( "{$dir}/{$index_js}" )
	);

	$editor_css = 'base/editor.css';
	wp_register_style(
		'base-block-editor',
		plugins_url( $editor_css, __FILE__ ),
		array(),
		filemtime( "{$dir}/{$editor_css}" )
	);

	$style_css = 'base/style.css';
	wp_register_style(
		'base-block',
		plugins_url( $style_css, __FILE__ ),
		array(),
		filemtime( "{$dir}/{$style_css}" )
	);

	register_block_type(
		'mailster/base',
		array(
			'editor_script' => 'base-block-editor',
			'editor_style'  => 'base-block-editor',
			'style'         => 'base-block',
		)
	);
}

add_action( 'init', 'base_block_init' );
