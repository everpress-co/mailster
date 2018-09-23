<p class="description"><?php esc_html_e( 'Some of these settings may affect your website. In normal circumstance it is not required to change anything on this page.', 'mailster' ) ?></p>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Enable', 'mailster' ) ?></th>
		<td>
			<label><input type="hidden" name="mailster_options[log]" value=""><input type="checkbox" name="mailster_options[log]" value="1" <?php checked( mailster_option( 'log' ) );?>> <?php esc_html_e( 'Enable Logging', 'mailster' ) ?></label> <p class="description"><?php esc_html_e( 'Enable this option if you have issue with the security nonce on Mailster forms.', 'mailster' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Max. Log Items', 'mailster' ) ?></th>
		<td><input type="text" name="mailster_options[log_items]" value="<?php echo esc_attr( mailster_option( 'log_items' ) ); ?>" class="regular-text" style="width: 100px;">
			<p class="description"><?php esc_html_e( 'A unique string to prevent form submissions via POST. Pass this value in a \'_nonce\' variable. Keep empty to disable test.', 'mailster' ) ?></p></td>
	</tr>
</table>
