<div class="imports-wrap no-success">
	<a class="button button-primary button-hero start-import"><?php esc_html_e( __( 'Start Import', 'mailster' ) ) ?></a>
	<div id="progress" class="progress"><span class="bar" style="width:0%"><span></span></span></div>
	<h4 class="import-info"><?php esc_html_e( __( 'Click the button to start import', 'mailster' ) ) ?></h4>
	<div class="subtab" id="subtab-import">
		<p class="imports-toggles">
			<?php esc_html_e( __( 'Show', 'mailster' ) ) ?>:
			<label class="label-error" title="<?php echo esc_attr__( 'Errors must be fixed in order to make Mailster work correctly.', 'mailster' );  ?>"> <input type="checkbox" name="" data-type="error" checked><i></i><?php esc_html_e( __( 'Errors', 'mailster' ) ) ?></label>
			<label class="label-warning" title="<?php echo esc_attr__( 'Warnings are recommended to get fixed but not required to make Mailster work.', 'mailster' );  ?>"> <input type="checkbox" name="" data-type="warning" checked><i></i><?php esc_html_e( __( 'Warnings', 'mailster' ) ) ?></label>
			<label class="label-notice" title="<?php echo esc_attr__( 'Notices normally don\'t require any action.', 'mailster' );  ?>"> <input type="checkbox" name="" data-type="notice" checked><i></i><?php esc_html_e( __( 'Notices', 'mailster' ) ) ?></label>
			<label class="label-success" title="<?php echo esc_attr__( 'Best requirements for Mailster to work.', 'mailster' );  ?>"> <input type="checkbox" name="" data-type="success"><i></i><?php esc_html_e( __( 'Success', 'mailster' ) ) ?></label>
		</p>
		<div class="imports-output"></div>
	</div>
</div>
