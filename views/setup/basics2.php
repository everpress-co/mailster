<div class="mailster-setup-step-body">

<form class="mailster-setup-step-form">

<p><?php esc_html_e( 'Please provide some basic information which is used for your newsletter campaigns. Mailster already pre-filled the fields with the default values but you should check them for correctness.', 'mailster' ); ?></p>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'From Name', 'mailster' ); ?></th>
		<td><input type="text" name="mailster_options[from_name]" value="<?php echo esc_attr( mailster_option( 'from_name' ) ); ?>" class="regular-text"> <p class="description"><?php esc_html_e( 'The sender name which is displayed in the from field', 'mailster' ); ?></p></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'From Address', 'mailster' ); ?></th>
		<td><input type="text" name="mailster_options[from]" value="<?php echo esc_attr( mailster_option( 'from' ) ); ?>" class="regular-text"> <p class="description"><?php esc_html_e( 'The sender email address. Ask your subscribers to white label this email address.', 'mailster' ); ?></p></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Reply To Address', 'mailster' ); ?></th>
		<td><input type="text" name="mailster_options[reply_to]" value="<?php echo esc_attr( mailster_option( 'reply_to' ) ); ?>" class="regular-text"> <p class="description"><?php esc_html_e( 'The address users can reply to', 'mailster' ); ?></p></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Logo', 'mailster' ); ?>
		</th>
		<td>
		<?php mailster( 'helper' )->media_editor_link( mailster_option( 'logo', get_theme_mod( 'custom_logo' ) ), 'mailster_options[logo]', 'full' ); ?>
		<p class="description"><label><input type="hidden" name="mailster_options[logo_high_dpi]" value=""><input type="checkbox" name="mailster_options[logo_high_dpi]" value="1" <?php checked( mailster_option( 'logo_high_dpi' ) ); ?>> <?php esc_html_e( 'Use High DPI version if available.', 'mailster' ); ?></label></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Logo Link', 'mailster' ); ?></th>
		<td><input type="text" name="mailster_options[logo_link]" value="<?php echo esc_attr( mailster_option( 'logo_link' ) ); ?>" class="regular-text"> <p class="description"><?php esc_html_e( 'A link for your logo.', 'mailster' ); ?></p></td>
	</tr>

</table>
</form>

</div>


