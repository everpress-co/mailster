<?php
	$cron_status = mailster( 'cron' )->check();
	$interval    = mailster_option( 'interval' );
if ( is_wp_error( $cron_status ) ) : ?>
	<div class="error inline"><p><strong><?php echo $cron_status->get_error_message(); ?></strong></p></div>
	<?php endif; ?>
	<?php $cron_service = mailster_option( 'cron_service' ); ?>
<table class="form-table cron-service <?php echo 'cron-service-' . $cron_service; ?>">
	<tr valign="top" class="wp_cron">
		<th scope="row"><?php esc_html_e( 'Interval for sending emails', 'mailster' ); ?></th>
		<td><p><?php printf( esc_html__( 'Send emails at most every %1$s minutes', 'mailster' ), '<input type="text" name="mailster_options[interval]" value="' . $interval . '" class="small-text">' ); ?></p><p class="description"><?php esc_html_e( 'Optional if a real cron service is used', 'mailster' ); ?></p></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Cron Setup', 'mailster' ); ?>
			<p class="description">
				<?php esc_html_e( 'Choose between three types of cron services you like to use.', 'mailster' ); ?>
			</p>
		</th>
		<td>
			<ul class="cron-service-wrap">
				<li class="cron-service-radio-wp_cron"><label><input type="radio" class="cron_radio" name="mailster_options[cron_service]" value="wp_cron" <?php checked( $cron_service, 'wp_cron' ); ?>> <h4><?php esc_html_e( 'Easy', 'mailster' ); ?></h4><?php esc_html_e( 'Use the WordPress native cron service.', 'mailster' ); ?></label></li>
				<li class="cron-service-radio-cron"><label><input type="radio" class="cron_radio" name="mailster_options[cron_service]" value="cron" <?php checked( $cron_service, 'cron' ); ?>> <h4><?php esc_html_e( 'Solid', 'mailster' ); ?></h4><?php esc_html_e( 'Use a real cron service.', 'mailster' ); ?></label></li>
				<li class="cron-service-radio-multi_cron"><label><input type="radio" class="cron_radio" name="mailster_options[cron_service]" value="multi_cron" <?php checked( $cron_service, 'multi_cron' ); ?>> <h4><?php esc_html_e( 'Advanced', 'mailster' ); ?></h4><?php esc_html_e( 'Use a real cron service with multiple processes.', 'mailster' ); ?></label></li>
			</ul>
			<?php if ( file_exists( MAILSTER_UPLOAD_DIR . '/CRON_LOCK' ) && ( time() - filemtime( MAILSTER_UPLOAD_DIR . '/CRON_LOCK' ) ) < 10 ) : ?>
				<div class="error inline"><p><strong><?php esc_html_e( 'Cron is currently running!', 'mailster' ); ?></strong></p></div>
			<?php endif; ?>

		<div class="cron-opts cron-opts-cron cron-opts-multi_cron">
			<p class="cron-opts cron-opts-multi_cron">
				<?php esc_html_e( 'Number of processes', 'mailster' ); ?>: <select name="mailster_options[cron_processes]">
					<option value="1" <?php selected( ! mailster_option( 'cron_processes' ) ); ?>><?php esc_html_e( 'one Single', 'mailster' ); ?></option>
				<?php $max_processes = (int) apply_filters( 'mailster_max_cron_processes', 8 ); ?>
				<?php for ( $i = 2; $i <= $max_processes; $i++ ) : ?>
					<option value="<?php echo $i; ?>" <?php selected( mailster_option( 'cron_processes', 0 ), $i ); ?>><?php echo $i; ?></option>
				<?php endfor; ?>
				</select>
			</p>
			<p>
			<input type="hidden" name="mailster_options[cron_secret]" value="<?php echo esc_attr( mailster_option( 'cron_secret' ) ); ?>" class="regular-text">
			</p>

			<p class="cron-opts cron-opts-cron">
				<a class="button button-primary solid-cron-setup" href="#"><?php esc_html_e( 'Solid Setup Guide', 'mailster' ); ?></a>
			</p>
			<p class="cron-opts cron-opts-multi_cron">
				<a class="button button-primary advanced-cron-setup" href="#"><?php esc_html_e( 'Advanced Setup Guide', 'mailster' ); ?></a>
			</p>
			<div id="solid-cron-setup" style="display: none;">
				<?php $cron_url = mailster( 'cron' )->url(); ?>
				<?php $cron_url2 = mailster( 'cron' )->url( true ); ?>
				<?php $cron_path = mailster( 'cron' )->path( true ); ?>
				<p><?php esc_html_e( 'You can keep a browser window open with following URL', 'mailster' ); ?> (<a class="switch-cron-url" href="#"><?php esc_html_e( 'alternative Cron URL', 'mailster' ); ?></a>)</p>
				<div class="verified regular-cron-url"><a href="<?php echo $cron_url; ?>" class="external"><code id="copy-cronurl-1"><?php echo $cron_url; ?></code></a> <a class="clipboard" data-clipboard-target="#copy-cronurl-1"><?php esc_html_e( 'copy', 'mailster' ); ?></a></div>
				<div class="verified alternative-cron-url"><a href="<?php echo $cron_url2; ?>" class="external"><code id="copy-cronurl-2"><?php echo $cron_url2; ?></code></a> <a class="clipboard" data-clipboard-target="#copy-cronurl-2"><?php esc_html_e( 'copy', 'mailster' ); ?></a></div>
				<p><?php esc_html_e( 'or setup a crontab with one of the following commands:', 'mailster' ); ?></p>
				<ul>
				<li class="regular-cron-url"><code id="copy-cmd-1">wget -O- '<?php echo $cron_url; ?>' > /dev/null</code> <a class="clipboard" data-clipboard-target="#copy-cmd-1"><?php esc_html_e( 'copy', 'mailster' ); ?></a></li>
				<li class="alternative-cron-url"><code id="copy-cmd-2">wget -O- '<?php echo $cron_url2; ?>' > /dev/null</code> <a class="clipboard" data-clipboard-target="#copy-cmd-2"><?php esc_html_e( 'copy', 'mailster' ); ?></a></li>
				<li class="regular-cron-url"><code id="copy-cmd-3">curl --silent '<?php echo $cron_url; ?>'</code> <a class="clipboard" data-clipboard-target="#copy-cmd-3"><?php esc_html_e( 'copy', 'mailster' ); ?></a></li>
				<li class="alternative-cron-url"><code id="copy-cmd-4">curl --silent '<?php echo $cron_url2; ?>'</code> <a class="clipboard" data-clipboard-target="#copy-cmd-4"><?php esc_html_e( 'copy', 'mailster' ); ?></a></li>
				<li class="regular-cron-url"><code id="copy-cmd-5">GET '<?php echo $cron_url; ?>' > /dev/null</code> <a class="clipboard" data-clipboard-target="#copy-cmd-5"><?php esc_html_e( 'copy', 'mailster' ); ?></a></li>
				<li class="alternative-cron-url"><code id="copy-cmd-6">GET '<?php echo $cron_url2; ?>' > /dev/null</code> <a class="clipboard" data-clipboard-target="#copy-cmd-6"><?php esc_html_e( 'copy', 'mailster' ); ?></a></li>
				<li><code id="copy-cmd-7">php <?php echo $cron_path; ?> > /dev/null</code> <a class="clipboard" data-clipboard-target="#copy-cmd-7"><?php esc_html_e( 'copy', 'mailster' ); ?></a></li>
				</ul>
				<p class="description"><?php esc_html_e( 'You can setup an interval as low as one minute, but should consider a reasonable value of 5-15 minutes as well.', 'mailster' ); ?></p>
				<p class="description"><?php esc_html_e( 'If you need help setting up a cron job please refer to the documentation that your provider offers.', 'mailster' ); ?></p>
				<p class="description"><?php printf( __( 'You can also find additional help on our %s.', 'mailster' ), '<a href="https://kb.mailster.co/how-can-i-setup-a-cron-job/" class="external">' . __( 'knowledge base', 'mailster' ) . '</a>' ); ?></p>
			</div>

			<div id="advanced-cron-setup" style="display: _none;">
			<?php $code = array(); ?>
			<?php for ( $process_id = 1; $process_id <= mailster_option( 'cron_processes' ); $process_id++ ) : ?>
				<?php
				if ( 'multi_cron' == $cron_service && ! $process_id ) :
					continue;
				endif;
				?>
				<?php $cron_path = mailster( 'cron' )->path( true, $process_id ); ?>
				<?php $code[] = $process_id . '-59/' . mailster_option( 'cron_processes' ) . ' * * * * php ' . $cron_path . ' >/dev/null 2>&1'; ?>
			<?php endfor; ?>
			<textarea rows="10" cols="40" class="large-text code" id="copy-cmd-code"><?php echo esc_html( implode( "\n", $code ) ); ?></textarea>
			<a class="clipboard" data-clipboard-target="#copy-cmd-code"><?php esc_html_e( 'copy', 'mailster' ); ?></a>
			<textarea rows="10" cols="40" class="large-text code" id="copy-cmd-code2">
# copy the current cron into a new file
crontab -l > mailstercron.txt

#remove prexisting commands
sed -i '/./{H;$!d} ; x ; s/\n### Mailster Cron start ###.*### Mailster Cron end ###//g' mailstercron.txt

# add the new entries into the file
echo -e "\n### Mailster Cron start ###" >> mailstercron.txt
<?php echo 'echo "' . implode( "\" >> mailstercron.txt\necho \"", $code ) . '" >> mailstercron.txt'; ?>

echo "### Mailster Cron end ###" >> mailstercron.txt

# install the new cron
crontab mailstercron.txt

# remove the crontab file since it has been installed and we don't use it anymore.
rm mailstercron.txt

</textarea>
			<a class="clipboard" data-clipboard-target="#copy-cmd-code2"><?php esc_html_e( 'copy', 'mailster' ); ?></a>
			</div>

		</div>

		</td>
	</tr>
	<?php for ( $process_id = 0; $process_id <= mailster_option( 'cron_processes' ); $process_id++ ) : ?>
		<?php
		if ( ( 'multi_cron' != $cron_service && $process_id ) || ( 'multi_cron' == $cron_service && ! $process_id ) ) :
			continue;
		endif;
		?>
		<?php $last_hit = get_option( 'mailster_cron_lasthit_' . $process_id, false ); ?>
	<tr valign="top" class="lasthitstats lasthitstats-<?php echo $process_id; ?>">
		<th scope="row"><?php printf( esc_html__( 'Last hit %s', 'mailster' ), $process_id ? '#' . $process_id : '' ); ?></th>
		<td>

			<?php if ( $last_hit && time() - $last_hit['timestamp'] > 720 && mailster( 'cron' )->is_locked( $process_id ) ) : ?>
				<div class="error inline">
				<p><?php printf( esc_html__( 'Looks like your Cron Lock is still in place after %1$s! Read more about why this can happen %2$s.', 'mailster' ), '<strong>' . human_time_diff( $last_hit['timestamp'] ) . '</strong>', '<a href="https://kb.mailster.co/what-is-a-cron-lock/" class="external">' . esc_html__( 'here', 'mailster' ) . '</a>' ); ?></p>
				</div>
			<?php endif; ?>
		<ul class="lasthit <?php echo ( $last_hit ) ? 'verified' : 'not-verified'; ?>">
		<?php if ( $last_hit ) : ?>
			<li>IP:
			<?php
			echo $last_hit['ip'];
			if ( $last_hit['ip'] == mailster_get_ip() ) {
				echo ' (' . esc_html__( 'probably you', 'mailster' ) . ')'; }
			?>
			</li>
			<li><?php echo $last_hit['user']; ?></li>
			<li><?php echo date( $timeformat, $last_hit['timestamp'] + $timeoffset ) . ', <strong>' . sprintf( esc_html__( '%s ago', 'mailster' ), human_time_diff( $last_hit['timestamp'] ) ) . '</strong>'; ?></li>
			<?php if ( $last_hit['oldtimestamp'] && $interv = round( ( $last_hit['timestamp'] - $last_hit['oldtimestamp'] ) / 60 ) ) : ?>
			<li><?php echo esc_html__( 'Interval', 'mailster' ) . ': <strong>' . $interv . ' ' . esc_html_x( 'min', 'short for minute', 'mailster' ) . '</strong>'; ?></li>
			<?php endif; ?>
			<?php if ( $last_hit['mail'] ) : ?>
				<?php $mails_per_sec = round( 1 / $last_hit['mail'], 2 ); ?>
			<li>
				<?php
				echo esc_html__( 'Throughput', 'mailster' ) . ': ' . round( $last_hit['mail'], 3 ) . ' ' . esc_html_x( 'sec', 'short for second', 'mailster' );
				echo '/' . esc_html__( 'mail', 'mailster' );
				?>
			 (<?php printf( esc_html__( _n( '%s mail per second', '%s mails per second', $mails_per_sec, 'mailster' ) ), $mails_per_sec ); ?>)</li>
			<?php endif; ?>
			<?php if ( $last_hit['timemax'] ) : ?>
			<li><?php echo esc_html__( 'Max Execution Time', 'mailster' ) . ': ' . round( $last_hit['timemax'], 3 ) . ' ' . esc_html_x( 'sec', 'short for second', 'mailster' ); ?></li>
			<?php endif; ?>
			<li><a href="edit.php?post_type=newsletter&page=mailster_settings&reset-lasthit=<?php echo (int) $process_id; ?>&_wpnonce=<?php echo wp_create_nonce( 'mailster-reset-lasthit' ); ?>"><?php esc_html_e( 'Reset', 'mailster' ); ?></a>
				<?php if ( mailster( 'cron' )->is_locked( $process_id ) ) : ?>
				<a href="edit.php?post_type=newsletter&page=mailster_settings&release-cronlock=<?php echo (int) $process_id; ?>&_wpnonce=<?php echo wp_create_nonce( 'mailster-release-cronlock' ); ?>"><?php esc_html_e( 'Release Cron Lock', 'mailster' ); ?></a>
			<?php endif; ?>
			</li>
		<?php else : ?>
			<li><strong><?php esc_html_e( 'Never', 'mailster' ); ?></strong></li>
			<?php if ( 'wp_cron' == $cron_service ) : ?>
			<li><?php esc_html_e( 'Your WordPress Cron is maybe not working!', 'mailster' ); ?></li>
			<?php else : ?>
			<li><?php esc_html_e( 'Please make sure this URL is triggered correctly:', 'mailster' ); ?></li>
			<li class="regular-cron-url"><code id="copy-cmd-1-<?php echo $process_id; ?>"><?php echo mailster( 'cron' )->url( false, $process_id ); ?></code> <a class="clipboard" data-clipboard-target="#copy-cmd-1-<?php echo $process_id; ?>"><?php esc_html_e( 'copy', 'mailster' ); ?></a></li>
			<li class="alternative-cron-url"><code id="copy-cmd-2-<?php echo $process_id; ?>"><?php echo mailster( 'cron' )->url( true, $process_id ); ?></code> <a class="clipboard" data-clipboard-target="#copy-cmd-2-<?php echo $process_id; ?>"><?php esc_html_e( 'copy', 'mailster' ); ?></a></li>
			<?php endif; ?>
			<li><a href="https://kb.mailster.co/how-do-i-know-if-my-cron-is-working-correctly/" class="external"><?php esc_html_e( 'Need more help?', 'mailster' ); ?></a></li>
		<?php endif; ?>
		</ul>
		</td>
	</tr>
<?php endfor; ?>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Cron Lock', 'mailster' ); ?></th>
		<td>
			<?php $cron_lock = mailster_option( 'cron_lock' ); ?>
			<select name="mailster_options[cron_lock]">
				<option value="file" <?php selected( $cron_lock, 'file' ); ?>><?php esc_html_e( 'File based', 'mailster' ); ?></option>
				<option value="db" <?php selected( $cron_lock, 'db' ); ?>><?php esc_html_e( 'Database based', 'mailster' ); ?></option>
			</select>
			<p class="description"><?php esc_html_e( 'A Cron Lock ensures your cron is not overlapping and causing duplicate emails. Select which method you like to use.', 'mailster' ); ?></p>
		</td>
	</tr>
</table>
