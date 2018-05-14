<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Tracking', 'mailster' ) ?></th>
		<td>
		<p><label><input type="hidden" name="mailster_options[track_opens]" value=""><input type="checkbox" name="mailster_options[track_opens]" value="1" <?php checked( mailster_option( 'track_opens' ) );?>> <?php esc_html_e( 'Track opens in your campaigns', 'mailster' ) ?></label></p>
		<p><label><input type="hidden" name="mailster_options[track_clicks]" value=""><input type="checkbox" name="mailster_options[track_clicks]" value="1" <?php checked( mailster_option( 'track_clicks' ) );?>> <?php esc_html_e( 'Track clicks in your campaigns', 'mailster' ) ?></label></p>

<?php
	$geoip = isset( $_GET['nogeo'] ) ? false : mailster_option( 'track_location' );
	$geo_db_file_countries = mailster( 'geo' )->get_file_path( 'country' );
	$geo_db_file_cities = mailster( 'geo' )->get_file_path( 'city' );
?>

		<p><label><input type="hidden" name="mailster_options[track_location]" value=""><input type="checkbox" id="mailster_geoip" name="mailster_options[track_location]" value="1" <?php checked( $geoip );?>> <?php esc_html_e( 'Track location in campaigns', 'mailster' ) ?>*</label>
			<br>&nbsp;&#x2514;&nbsp;<label><input type="hidden" name="mailster_options[track_location_update]" value=""><input type="checkbox" name="mailster_options[track_location_update]" value="1" <?php checked( mailster_option( 'track_location_update' ) );?>> <?php esc_html_e( 'Update location database automatically', 'mailster' ) ?></label>
		</p>

	<?php if ( ! mailster()->is( 'setup' ) && $geoip && is_file( $geo_db_file_cities ) ) : ?>
		<p class="description"><?php esc_html_e( 'If you don\'t find your country down below the geo database is missing or corrupt', 'mailster' ) ?></p>
		<p>
		<strong><?php esc_html_e( 'Your IP', 'mailster' ) ?>:</strong> <?php echo mailster_get_ip() ?><?php if ( mailster_is_local() ) : ?>
	<strong><?php esc_html_e( 'Geolocation is not available on localhost!', 'mailster' ) ?></strong>
	<?php endif; ?><br>
		<strong><?php esc_html_e( 'Your country', 'mailster' ) ?>:</strong> <?php echo mailster_ip2Country( '', 'name' ) ?><br>
	<?php if ( is_file( $geo_db_file_cities ) ) : ?>
		<strong><?php esc_html_e( 'Your city', 'mailster' ) ?>:</strong> <?php echo mailster_ip2City( '', 'city' ) ?>
	<?php endif; ?>
		</p>
		<p><button id="load_location_db" class="button-primary" <?php disabled( ! $geoip );?>><?php  esc_html_e( 'Update Location Database', 'mailster' );?></button>&nbsp;<span class="loading geo-ajax-loading"></span>
			<em id="location_last_update"><?php esc_html_e( 'Last update', 'mailster' ) ?>: <?php echo human_time_diff( filemtime( $geo_db_file_cities ) ) ?> <?php esc_html_e( 'ago', 'mailster' ) ?></em>
		</p>
	<?php
	endif;
	?>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Save Subscriber IP', 'mailster' ) ?></th>
		<td><label><input type="hidden" name="mailster_options[track_users]" value=""><input type="checkbox" name="mailster_options[track_users]" value="1" <?php checked( mailster_option( 'track_users' ) ) ?>> <?php esc_html_e( 'Save IP address and time of new subscribers', 'mailster' ) ?></label>
		<p class="description"><?php esc_html_e( 'In some countries it\'s required to save the IP address and the sign up time for legal reasons. Please add a note in your privacy policy if you save users data', 'mailster' ) ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Do Not Track</th>
		<td><label><input type="hidden" name="mailster_options[do_not_track]" value=""><input type="checkbox" name="mailster_options[do_not_track]" value="1" <?php checked( mailster_option( 'do_not_track' ) ) ?>> <?php esc_html_e( 'Respect users "Do Not Track" option', 'mailster' ) ?></label>
		<p class="description"><?php printf( __( 'If enabled Mailster will respect users option for not getting tracked. Read more on the %s', 'mailster' ), '<a href="http://donottrack.us/" class="external">' . __( 'official website', 'mailster' ) . '</a>' ) ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"></th>
		<td><p class="description">* This product includes GeoLite data created by MaxMind, available from <a href="https://www.maxmind.com" class="external">maxmind.com</a></p>
		</td>
	</tr>
	</table>

