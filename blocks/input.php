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
function input_block_init() {
	// Skip block registration if Gutenberg is not enabled/merged.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}
	$dir = dirname( __FILE__ );

	$index_js = 'input/index.js';
	wp_register_script(
		'input-block-editor',
		plugins_url( $index_js, __FILE__ ),
		array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
		),
		filemtime( "{$dir}/{$index_js}" )
	);

	$editor_css = 'input/editor.css';
	wp_register_style(
		'input-block-editor',
		plugins_url( $editor_css, __FILE__ ),
		array(),
		filemtime( "{$dir}/{$editor_css}" )
	);

	$style_css = 'input/style.css';
	wp_register_style(
		'input-block',
		plugins_url( $style_css, __FILE__ ),
		array(),
		filemtime( "{$dir}/{$style_css}" )
	);

	register_block_type(
		'mailster/input',
		array(
			'editor_script' => 'input-block-editor',
			'editor_style'  => 'input-block-editor',
			'style'         => 'input-block',
		)
	);
}

add_action( 'init', 'input_block_init' );
