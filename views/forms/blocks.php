<?php
/**
 * The block-based widgets editor, for use in widgets.php.
 *
 * @package WordPress
 * @subpackage Administration
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Flag that we're loading the block editor.
$current_screen = get_current_screen();
$current_screen->is_block_editor( true );

$block_editor_context = new WP_Block_Editor_Context();

// $preload_paths = array(
// array( '/wp/v2/media', 'OPTIONS' ),
// '/wp/v2/sidebars?context=edit&per_page=-1',
// '/wp/v2/widgets?context=edit&per_page=-1&_embed=about',
// );
// block_editor_rest_api_preload( $preload_paths, $block_editor_context );

$editor_settings = get_block_editor_settings(
	array_merge( get_legacy_widget_block_editor_settings(), array( 'styles' => get_block_editor_theme_styles() ) ),
	$block_editor_context
);
// The widgets editor does not support the Block Directory, so don't load any of
// its assets. This also prevents 'wp-editor' from being enqueued which we
// cannot load in the widgets screen because many widget scripts rely on `wp.editor`.
remove_action( 'enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets' );

wp_add_inline_script(
	'wp-edit-widgets',
	sprintf(
		'wp.domReady( function() {
			wp.editWidgets.initialize( "mailster-form-editor", %s );
		} );',
		wp_json_encode( $editor_settings )
	)
);

error_log( print_r( $editor_settings, true ) );
// // Preload server-registered block schemas.
// wp_add_inline_script(
// 'wp-blocks',
// 'wp.blocks.unstable__bootstrapServerSideBlockDefinitions(' . wp_json_encode( get_block_editor_server_block_settings() ) . ');'
// );

// wp_add_inline_script(
// 'wp-blocks',
// sprintf( 'wp.blocks.setCategories( %s );', wp_json_encode( get_block_categories( 'mailster-form-editor' ) ) ),
// 'after'
// );

wp_enqueue_script( 'wp-edit-widgets' );
// wp_enqueue_style( 'mailster-form-blocks-style' );
wp_enqueue_style( 'mailster-form-blocks-style', MAILSTER_URI . 'assets/css/form-blocks-style.css', array( 'block-editor', 'editor-css' ), MAILSTER_VERSION );

?>

<div id="mailster-form-editor" class="blocks-widgets-container"></div>

