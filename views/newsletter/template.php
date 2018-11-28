<?php

$editable = ! in_array( $post->post_status, array( 'active', 'finished' ) );
if ( isset( $_GET['showstats'] ) && $_GET['showstats'] ) {
	$editable = false;
}

$modules = $this->replace_colors( $this->templateobj->get_modules_html() );

$templates = mailster( 'templates' )->get_templates();
$all_files = mailster( 'templates' )->get_all_files();

?>
<div id="template-wrap" class="load <?php if ( $editable && ! ! get_user_setting( 'mailstershowmodules', 1 ) && ! empty( $modules ) ) {	echo ' show-modules'; } if ( $editable && ! empty( $modules ) ) { echo ' has-modules'; }?>">

<?php if ( $editable ) : ?>

	<?php include 'optionbar.php' ?>
	<?php include 'editbar.php' ?>

<?php else : ?>

	<?php $stats['total'] = $this->get_clicks( $post->ID, true ); ?>
	<?php $stats['clicks'] = $this->get_clicked_links( $post->ID ); ?>

	<div id="mailster_click_stats" data-stats='<?php echo json_encode( $stats ); ?>'></div>
	<div id="clickmap-stats">
		<div class="piechart" data-percent="0" data-size="60" data-line-width="8" data-animate="500"><span>0</span>%</div>
		<p><strong class="link"></strong></p>
		<p><?php esc_html_e( 'Clicks', 'mailster' );?>: <strong class="clicks">0</strong><br><?php esc_html_e( 'Total', 'mailster' );?>: <strong class="total">0</strong></p>
	</div>
	<textarea id="content" name="content" class="hidden" autocomplete="off"><?php echo ( $post->post_content ) ?></textarea>
	<textarea id="excerpt" name="excerpt" class="hidden" autocomplete="off"><?php echo ( $post->post_excerpt ) ?></textarea>

<?php endif; ?>

	<div id="plain-text-wrap">
		<?php $autoplaintext = ! isset( $this->post_data['autoplaintext'] ) || $this->post_data['autoplaintext']?>
		<p>
			<label><input type="checkbox" id="plaintext" name="mailster_data[autoplaintext]" value="1" <?php checked( $autoplaintext );?>> <?php esc_html_e( 'Create the plain text version based on the HTML version of the campaign', 'mailster' );?></label> <a class="alignright button button-primary getplaintext"><?php esc_html_e( 'get text from HTML version', 'mailster' );?></a>
		</p>

		<textarea id="excerpt" name="excerpt" class="<?php if ( $autoplaintext ) { echo ' disabled'; } ?>" autocomplete="off" <?php disabled( $autoplaintext );?>><?php echo $post->post_excerpt ?></textarea>
	</div>

	<div id="html-wrap">
		<?php
		if ( $editable && ! empty( $modules ) ) :
			$module_list = $this->templateobj->get_module_list();
			$screenshots = $this->templateobj->get_module_screenshots();
			$screenshot_modules_folder = MAILSTER_UPLOAD_DIR . '/screenshots/' . $this->get_template() . '/modules/';
			$screenshot_modules_folder_uri = MAILSTER_UPLOAD_URI . '/screenshots/' . $this->get_template() . '/modules/';
		?>
			<div id="module-selector">
				<a class="toggle-modules mailster-btn mailster-icon" title="<?php esc_attr_e( 'Modules', 'mailster' ) ?>"></a>
				<div id="module-search-wrap">
					<input type="text" class="widefat" id="module-search" placeholder="<?php esc_attr_e( 'Search Modules...', 'mailster' ) ?>">
					<a id="module-search-remove" href="#" title="<?php esc_attr_e( 'clear search', 'mailster' );?>">&#10005;</a>
				</div>
				<div class="inner">
					<ul>
					<?php
					foreach ( $module_list as $i => $module ) {

						if ( isset( $screenshots[ $i ] ) && file_exists( $screenshot_modules_folder . $screenshots[ $i ] ) ) {
							$has_screenshots = getimagesize( $screenshot_modules_folder . $screenshots[ $i ] );
						} else {
							$has_screenshots = false;
						}

						echo '<li data-id="' . $i . '" draggable="true"><a class="mailster-btn addmodule ' . ( $has_screenshots ? 'has-screenshot" style="background-image:url(\'' . $screenshot_modules_folder_uri . $screenshots[ $i ] . '\');height:' . ( ceil( $has_screenshots[1] / 2 ) + 6 ) . 'px;' : '' ) . '" title="' . esc_attr( sprintf( __( 'Click to add %s', 'mailster' ), '"' . $module . '"' ) ) . '" data-id="' . $i . '"><span>' . esc_html( $module ) . '</span><span class="hidden">' . esc_html( strtolower( $module ) ) . '</span></a></li>';
					} ?>
					</ul>
				</div>
			</div>
		<?php endif; ?>

		<div id="iframe-wrap">
			<iframe id="mailster_iframe" src="<?php echo add_query_arg(array(
				'action' => 'mailster_get_template',
				'id' => $post->ID,
				'template' => $this->get_template(),
				'templatefile' => $this->get_file(),
				'editorstyle' => $editable,
				'_wpnonce' => wp_create_nonce( 'mailster_nonce' ),
				'nocache' => time(),
			), admin_url( 'admin-ajax.php' ) ) ?>" width="100%" height="500" scrolling="no" frameborder="0" data-no-lazy="">
			</iframe>
		</div>
	</div>

</div>

<div id="mailster_campaign_preview" style="display:none;">
	<div class="device-wrap">
		<div class="device desktop">
			<div class="desktop-header">
				<div class="desktop-header-bar"><i></i><i></i><i></i></div>
				<div class="desktop-header-info"><u></u><i></i><i></i></div>
			</div>
			<div class="desktop-body">
				<div class="preview-body">
					<iframe class="mailster-preview-iframe desktop" src="" width="100%" scrolling="auto" frameborder="0" data-no-lazy=""></iframe>
				</div>
			</div>
		</div>
		<div class="device mobile">
			<div class="mobile-header"><u></u><i></i></div>
			<div class="mobile-body">
				<div class="preview-body">
					<iframe class="mailster-preview-iframe mobile" src="" width="100%" scrolling="auto" frameborder="0" data-no-lazy=""></iframe>
				</div>
			</div>
			<div class="mobile-footer"><i></i></div>
		<p class="device-info"><?php esc_html_e( 'Your email may look different on mobile devices', 'mailster' ); ?></p>
		</div>
	</div>
</div>
<textarea id="content" autocomplete="off" name="content"><?php echo ( $post->post_content ) ?></textarea>
<textarea id="modules" autocomplete="off"><?php echo ( $modules ) ?></textarea>
<textarea id="head" name="mailster_data[head]" autocomplete="off"><?php echo ( isset( $this->post_data['head'] ) ? $this->post_data['head'] : $this->templateobj->get_head() ); ?></textarea>
