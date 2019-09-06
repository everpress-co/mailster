<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'General Checks', 'mailster' ); ?></th>
		<td>
		<p><label><input type="hidden" name="mailster_options[check_mx]" value=""><input type="checkbox" name="mailster_options[check_mx]" value="1" <?php checked( mailster_option( 'check_mx' ) ); ?>><?php esc_html_e( 'Check MX record', 'mailster' ); ?></label><br><span class="description"><?php esc_html_e( 'Check the domain for an existing MX record. A missing MX record often indicates that there\'s no email server setup for the domain.', 'mailster' ); ?></span>
		</p>
		<p><label><input type="hidden" name="mailster_options[check_smtp]" value=""><input type="checkbox" name="mailster_options[check_smtp]" value="1" <?php checked( mailster_option( 'check_smtp' ) ); ?>><?php esc_html_e( 'Validate via SMTP', 'mailster' ); ?></label><br><span class="description"><?php esc_html_e( 'Connects the domain\'s SMTP server to check if the address really exists.', 'mailster' ); ?></span></p>
		<?php if ( class_exists( 'AKISMET' ) ) : ?>
		<p><label><input type="hidden" name="mailster_options[check_akismet]" value=""><input type="checkbox" name="mailster_options[check_akismet]" value="1" <?php checked( mailster_option( 'check_akismet' ) ); ?> ><?php esc_html_e( 'Check via Akismet', 'mailster' ); ?></label><br><span class="description"><?php esc_html_e( 'Checks via your Akismet installation.', 'mailster' ); ?></span>
		</p>
		<?php endif; ?>
		<p><label><input type="hidden" name="mailster_options[check_honeypot]" value=""><input type="checkbox" name="mailster_options[check_honeypot]" value="1" <?php checked( mailster_option( 'check_honeypot' ) ); ?> ><?php esc_html_e( 'Honeypot', 'mailster' ); ?></label><br><span class="description"><?php esc_html_e( 'Add an invisible input field to trick bots during signup.', 'mailster' ); ?></span>
		</p>
		</td>
	</tr>
	<tr valign="top">
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Antiflood', 'mailster' ); ?></th>
			<td><p><input type="text" name="mailster_options[antiflood]" value="<?php echo mailster_option( 'antiflood' ); ?>" class="small-text"> <?php esc_html_e( 'seconds', 'mailster' ); ?></p><p class="description"><?php esc_html_e( 'Prevent repeated subscriptions from the same IP address.', 'mailster' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Disposable Email Provider', 'mailster' ); ?></th>
		<td>
		<p><label><input type="hidden" name="mailster_options[reject_dep]" value=""><input type="checkbox" name="mailster_options[reject_dep]" value="1" <?php checked( mailster_option( 'reject_dep' ) ); ?>><?php esc_html_e( 'Reject email addresses from disposable email providers (DEP).', 'mailster' ); ?></label></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Blacklisted Email Addresses', 'mailster' ); ?></th>
		<td>
		<p><?php esc_html_e( 'List of blacklisted email addresses. One email each line.', 'mailster' ); ?><br>
		<textarea name="mailster_options[blacklisted_emails]" placeholder="<?php echo "john@blacklisted.com\njane@blacklisted.co.uk\nhans@blacklisted.de"; ?>" class="code large-text" rows="10"><?php echo esc_attr( mailster_option( 'blacklisted_emails' ) ); ?></textarea></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Blacklisted Domains', 'mailster' ); ?></th>
		<td>
		<p><?php esc_html_e( 'List of blacklisted domains. One domain each line.', 'mailster' ); ?><br>
		<textarea name="mailster_options[blacklisted_domains]" placeholder="<?php echo "blacklisted.com\nblacklisted.co.uk\nblacklisted.de"; ?>" class="code large-text" rows="10"><?php echo esc_attr( mailster_option( 'blacklisted_domains' ) ); ?></textarea></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'White listed Domains', 'mailster' ); ?></th>
		<td>
		<p><?php esc_html_e( 'List domains which bypass the above rules. One domain each line.', 'mailster' ); ?><br>
		<textarea name="mailster_options[whitelisted_domains]" placeholder="<?php echo "whitelisted.com\nwhitelisted.co.uk\nwhitelisted.de"; ?>" class="code large-text" rows="10"><?php echo esc_attr( mailster_option( 'whitelisted_domains' ) ); ?></textarea></p>
		</td>
	</tr>
</table>
