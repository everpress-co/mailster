<table class="form-table">
	<tr valign="top" class="settings-row settings-row-general-checks">
		<th scope="row"><?php esc_html_e( 'General Checks', 'mailster' ); ?></th>
		<td>
		<p><label><input type="hidden" name="mailster_options[check_mx]" value=""><input type="checkbox" name="mailster_options[check_mx]" value="1" <?php checked( mailster_option( 'check_mx' ) ); ?>><?php esc_html_e( 'Check MX record', 'mailster' ); ?></label><br><span class="description"><?php esc_html_e( 'Check the domain for an existing MX record. A missing MX record often indicates that there\'s no email server setup for the domain.', 'mailster' ); ?></span>
		</p>
		<p><label><input type="hidden" name="mailster_options[check_smtp]" value=""><input type="checkbox" name="mailster_options[check_smtp]" value="1" <?php checked( mailster_option( 'check_smtp' ) ); ?>><?php esc_html_e( 'Validate via SMTP', 'mailster' ); ?></label><br><span class="description"><?php esc_html_e( 'Connects the domain\'s SMTP server to check if the address really exists.', 'mailster' ); ?></p></span>
		<?php if ( class_exists( 'AKISMET' ) ) : ?>
		<p><label><input type="hidden" name="mailster_options[check_akismet]" value=""><input type="checkbox" name="mailster_options[check_akismet]" value="1" <?php checked( mailster_option( 'check_akismet' ) ); ?> ><?php esc_html_e( 'Check via Akismet', 'mailster' ); ?></label><br><span class="description"><?php esc_html_e( 'Checks via your Akismet installation.', 'mailster' ); ?></p>
		</p>
		<?php endif; ?>
		<p><label><input type="hidden" name="mailster_options[check_honeypot]" value=""><input type="checkbox" name="mailster_options[check_honeypot]" value="1" <?php checked( mailster_option( 'check_honeypot' ) ); ?> ><?php esc_html_e( 'Honeypot', 'mailster' ); ?></label><br><span class="description"><?php esc_html_e( 'Add an invisible input field to trick bots during signup.', 'mailster' ); ?></span>
		</p>
		<p><label><input type="hidden" name="mailster_options[check_ip]" value=""><input type="checkbox" name="mailster_options[check_ip]" value="1" <?php checked( mailster_option( 'check_ip' ) ); ?> ><?php esc_html_e( 'IP Check', 'mailster' ); ?></label><br><span class="description"><?php esc_html_e( 'This prevents a signup from an IP if there\'s already a pending subscriber with the same IP address. Most bots signup with the same IP address so if this checked an additional signup can only be made once the previous email has been confirmed.', 'mailster' ); ?> <?php esc_html_e( 'Only works if double-opt-in is enabled and the user is not logged in.', 'mailster' ); ?></span>
		</p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-antiflood">
		<th scope="row"><?php esc_html_e( 'Antiflood', 'mailster' ); ?></th>
			<td><p><input type="text" name="mailster_options[antiflood]" value="<?php echo mailster_option( 'antiflood' ); ?>" class="small-text"> <?php esc_html_e( 'seconds', 'mailster' ); ?><br><span class="description"><?php esc_html_e( 'Prevent repeated subscriptions from the same IP address for the given time frame.', 'mailster' ); ?></span>
			</p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-auto-click-prevention">
		<th scope="row"><?php esc_html_e( 'Auto Click Prevention', 'mailster' ); ?> <a class="infolink external" href="https://kb.mailster.co/prevent-spam-filters-automatically-opening-and-clicking-through-links-in-your-campaigns/" title="<?php esc_attr_e( 'More info on our knowledge base.', 'mailster' ); ?>"></a></th>
		<td>
		<p><label><input type="hidden" name="mailster_options[autoclickprevention]" value=""><input type="checkbox" name="mailster_options[autoclickprevention]" value="1" <?php checked( mailster_option( 'autoclickprevention' ) ); ?>><?php esc_html_e( 'Prevent automated clicks from email servers.', 'mailster' ); ?></label><br><span class="description"><?php esc_html_e( 'Some Email Security Servers automatically click on one ore more links in your campaigns which can cause wrong open and click rates.', 'mailster' ); ?> <?php esc_html_e( 'Enable this option to add an additional redirect for clicks which happens after a short time after sending.', 'mailster' ); ?></span></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-disposable-email-provider">
		<th scope="row"><?php esc_html_e( 'Disposable Email Provider', 'mailster' ); ?></th>
		<td>
		<p><label><input type="hidden" name="mailster_options[reject_dep]" value=""><input type="checkbox" name="mailster_options[reject_dep]" value="1" <?php checked( mailster_option( 'reject_dep' ) ); ?>><?php esc_html_e( 'Reject email addresses from disposable email providers (DEP).', 'mailster' ); ?></label></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-blocked-email-addresses">
		<th scope="row"><?php esc_html_e( 'Blocked Email Addresses', 'mailster' ); ?></th>
		<td>
		<p><?php esc_html_e( 'List of blocked email addresses. One email each line.', 'mailster' ); ?><br>
		<textarea name="mailster_options[blocked_emails]" placeholder="<?php echo "john@blocked.com\njane@blocked.co.uk\nhans@blocked.de"; ?>" class="code large-text" rows="10"><?php esc_attr_e( mailster_option( 'blocked_emails' ) ); ?></textarea></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-blocked-domains">
		<th scope="row"><?php esc_html_e( 'Blocked Domains', 'mailster' ); ?></th>
		<td>
		<p><?php esc_html_e( 'List of blocked domains. One domain each line.', 'mailster' ); ?><br>
		<textarea name="mailster_options[blocked_domains]" placeholder="<?php echo "blocked.com\nblocked.co.uk\nblocked.de"; ?>" class="code large-text" rows="10"><?php esc_attr_e( mailster_option( 'blocked_domains' ) ); ?></textarea></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-blocked-ip-addresses">
		<th scope="row"><?php esc_html_e( 'Blocked IP Addresses', 'mailster' ); ?></th>
		<td>
		<p><?php esc_html_e( 'List of blocked IP addresses. One domain each line.', 'mailster' ); ?><br>
		<textarea name="mailster_options[blocked_ips]" placeholder="<?php echo "192.168.1.0-192.168.1.100\n192.168.*.*\n192.*.*.*\n192.168.0.0/16\n192.169.1.0/24\n192.168.1.95\n"; ?>" class="code large-text" rows="10"><?php esc_attr_e( mailster_option( 'blocked_ips' ) ); ?></textarea></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-safe-domains">
		<th scope="row"><?php esc_html_e( 'Safe Domains', 'mailster' ); ?></th>
		<td>
		<p><?php esc_html_e( 'List domains which bypass the above rules. One domain each line.', 'mailster' ); ?><br>
		<textarea name="mailster_options[safe_domains]" placeholder="<?php echo "safe.com\nsafe.co.uk\nsafe.de"; ?>" class="code large-text" rows="10"><?php esc_attr_e( mailster_option( 'safe_domains' ) ); ?></textarea></p>
		</td>
	</tr>
</table>
