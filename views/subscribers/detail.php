<?php

$id = isset( $_GET['ID'] ) ? (int) $_GET['ID'] : null;

$is_new = isset( $_GET['new'] );

if ( ! $is_new ) {
	if ( ! ( $subscriber = $this->get( $id, true ) ) ) {
		echo '<h2>' . __( 'This user does not exist or has been deleted!', 'mailster' ) . '</h2>';
		return;
	}

	$meta = (object) $this->meta( $subscriber->ID );
	$nicename = ( ! empty( $subscriber->fullname ) ? $subscriber->fullname : $subscriber->email );

} else {

	if ( ! current_user_can( 'mailster_add_subscribers' ) ) {
		echo '<h2>' . __( 'You don\'t have the right permission to add new subscribers', 'mailster' ) . '</h2>';
		return;
	}

	$subscriber = $this->get_empty();
	if ( isset( $_POST['mailster_data'] ) ) {
		$subscriber = (object) wp_parse_args( $_POST['mailster_data'], (array) $subscriber );
	}
}

$customfields = mailster()->get_custom_fields();

$timeformat = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
$timeoffset = mailster( 'helper' )->gmt_offset( true );

$now = time();

$tabindex = 1;

?>
<div class="wrap<?php echo ( $is_new ) ? ' new' : ' status-' . $subscriber->status ?>">
<form id="subscriber_form" action="edit.php?post_type=newsletter&page=mailster_subscribers<?php echo ( $is_new ) ? '&new' : '&ID=' . $id ?>" method="post">
<input type="hidden" id="ID" name="mailster_data[ID]" value="<?php echo $subscriber->ID ?>">
<?php wp_nonce_field( 'mailster_nonce' );?>
<div style="height:0px; width:0px; overflow:hidden;"><input type="submit" name="save" value="1"></div>
<h1>
<?php

if ( $is_new ) {
	esc_html_e( 'Add new Subscriber', 'mailster' );
} else {
	printf( __( 'Edit %s', 'mailster' ), '<strong>' . $nicename . '</strong>' );
	if ( $subscriber->status == 4 ) {
		echo '<div class="error"><p>' . sprintf( __( 'This subscriber has caused an error: %s', 'mailster' ), '<strong>' . ( $meta->error ? $meta->error : __( 'unknown', 'mailster' ) ) . '</strong>' ) . '</p></div>';
	}
?>
<?php if ( current_user_can( 'mailster_add_subscribers' ) ) : ?>
	<a href="edit.php?post_type=newsletter&page=mailster_subscribers&new" class="add-new-h2"><?php esc_html_e( 'Add New', 'mailster' );?></a>
<?php endif; ?>
<?php if ( $subscriber->wp_id ) : ?><a href="user-edit.php?user_id=<?php echo $subscriber->wp_id ?>" class="add-new-h2"><?php esc_html_e( 'goto WordPress User profile', 'mailster' );?></a><?php endif; ?>
<?php }?>
	<span class="alignright">
		<?php if ( ! $is_new && $subscriber->status == 0 ) : ?>
			<input type="submit" name="confirmation" class="button button-large" value="<?php esc_html_e( 'Resend Confirmation', 'mailster' );?>" onclick="return confirm('<?php esc_attr_e( 'Do you really like to resend the confirmation?', 'mailster' );?>');">
		<?php endif; ?>
		<?php if ( ! $is_new && current_user_can( 'mailster_delete_subscribers' ) ) : ?>
			<input type="submit" name="delete" class="button button-large" value="<?php esc_html_e( 'Delete Subscriber', 'mailster' );?>" onclick="return confirm('<?php esc_attr_e( 'Do you really like to remove this subscriber?', 'mailster' );?>');">
		<?php endif; ?>
		<input type="submit" name="save" class="button button-primary button-large" value="<?php esc_html_e( 'Save', 'mailster' );?>">
	</span>
</h1>


<table class="form-table">
	<tr>
		<td scope="row" class="avatar-wrap">
			<?php if ( get_option( 'show_avatars' ) ) : ?>
				<div class="avatar<?php if ( $subscriber->wp_id ) {	echo ' wp-user'; } ?>" title="<?php esc_html_e( 'Source', 'mailster' ) ?>: Gravatar.com" style="background-image:url(<?php echo $this->get_gravatar_uri( $subscriber->email, 400 ); ?>)"></div>
				<p class="info"><?php esc_html_e( 'Source', 'mailster' ) ?>: <a href="https://gravatar.com">Gravatar.com</a></p>
			<?php endif; ?>
			<?php if ( ! $is_new ) : ?>

				<h4 title="<?php esc_html_e( 'The user rating is based on different factors like open rate, click rate and bounces', 'mailster' ) ?>"><?php esc_html_e( 'User Rating', 'mailster' );?>:<br />
				<?php
					$stars = ( round( $subscriber->rating / 10, 2 ) * 50 );
					$full = max( 0, min( 5, floor( $stars ) ) );
					$half = max( 0, min( 5, round( $stars - $full ) ) );
					$empty = max( 0, min( 5, 5 - $full - $half ) );
				?>
				<?php
				echo str_repeat( '<span class="mailster-icon mailster-icon-star"></span>', $full )
				. str_repeat( '<span class="mailster-icon mailster-icon-star-half"></span>', $half )
				. str_repeat( '<span class="mailster-icon mailster-icon-star-empty"></span>', $empty )
				?>
				</h4>
			<?php endif; ?>
		</td>
		<td class="user-info">
			<h3 class="detail">
				<ul class="click-to-edit type-email">
					<li><?php echo esc_attr( $subscriber->email ); ?>&nbsp;</li>
					<li><input id="email" type="email" name="mailster_data[email]" value="<?php echo esc_attr( $subscriber->email ); ?>" placeholder="<?php echo mailster_text( 'email' ) ?>" autofocus></li>
				</ul>
				<code title="<?php printf( __( 'use %1$s as placeholder tag to replace it with %2$s', 'mailster' ), '{emailaddress}', '&quot;' . $subscriber->email . '&quot;' ) ?>">{emailaddress}</code>
			</h3>
			<div class="detail">
				<label for="mailster_firstname" class="label-type-name"><?php esc_html_e( 'Name', 'mailster' );?>:</label>
				<ul class="click-to-edit type-name">
					<li><?php echo esc_attr( $subscriber->fullname ); ?>&nbsp;</li>
					<li>
				<?php if ( mailster_option( 'name_order' ) ) : ?>
				<input id="mailster_lastname" class="" type="text" name="mailster_data[lastname]"value="<?php echo esc_attr( $subscriber->lastname ); ?>" placeholder="<?php echo mailster_text( 'lastname' ) ?>">
				<input id="mailster_firstname" type="text" name="mailster_data[firstname]" value="<?php echo esc_attr( $subscriber->firstname ); ?>" placeholder="<?php echo mailster_text( 'firstname' ) ?>">
				<?php else : ?>
				<input id="mailster_firstname" type="text" name="mailster_data[firstname]" value="<?php echo esc_attr( $subscriber->firstname ); ?>" placeholder="<?php echo mailster_text( 'firstname' ) ?>">
				<input id="mailster_lastname" class="" type="text" name="mailster_data[lastname]"value="<?php echo esc_attr( $subscriber->lastname ); ?>" placeholder="<?php echo mailster_text( 'lastname' ) ?>">
				<?php endif; ?>
					</li>
				</ul>
				<code title="<?php printf( __( 'use %1$s as placeholder tag to replace it with %2$s', 'mailster' ), '{fullname}', '&quot;' . $subscriber->fullname . '&quot;' ) ?>">{fullname}</code>
				<code title="<?php printf( __( 'use %1$s as placeholder tag to replace it with %2$s', 'mailster' ), '{lastname}', '&quot;' . $subscriber->lastname . '&quot;' ) ?>">{lastname}</code>
				<code title="<?php printf( __( 'use %1$s as placeholder tag to replace it with %2$s', 'mailster' ), '{firstname}', '&quot;' . $subscriber->firstname . '&quot;' ) ?>">{firstname}</code>
			</div>
			<div class="detail">
				<label for="mailster_status"><?php esc_html_e( 'Status', 'mailster' );?>:</label>
				<ul class="click-to-edit type-status">
					<li><?php echo $this->get_status( $subscriber->status, true ) ?>&nbsp;</li>
					<li><div class="statuses">
						<select name="mailster_data[status]" id="mailster_status">
						<?php $statuses = $this->get_status( null, true );
						foreach ( $statuses as $id => $status ) {
							if ( $id == 4 && $subscriber->status != 4 ) {
								continue;
							}
							?>
							<option value="<?php echo (int) $id ?>" <?php selected( $id, $subscriber->status ) ?> ><?php echo $status ?></option>
						<?php } ?>
						</select>
						<span class="description info"><?php esc_html_e( 'choosing "pending" as status will force a confirmation message to the subscriber', 'mailster' );?></span>
						</div>
					</li>
				</ul>
			</div>
			<?php if ( ! $is_new ) : ?>
			<div class="info">
				<strong><?php esc_html_e( 'subscribed at', 'mailster' );?>:</strong> <?php echo $subscriber->signup
				? date( $timeformat, $subscriber->signup + $timeoffset ) . ', ' . sprintf( __( '%s ago', 'mailster' ), human_time_diff( $now, $subscriber->signup ) )
				: __( 'unknown', 'mailster' ) ?>

				<div><?php $this->output_referer( $subscriber->ID );?></div>

				<a class="show-more-info alignright"><?php esc_html_e( 'more', 'mailster' );?></a>
				<ul class="more-info">
					<li><strong><?php esc_html_e( 'confirmed at', 'mailster' );?>:</strong> <?php echo $subscriber->confirm
					? date( $timeformat, $subscriber->confirm + $timeoffset ) . ', ' . sprintf( __( '%s ago', 'mailster' ), human_time_diff( $now, $subscriber->confirm ) ) . ( $subscriber->ip_confirm ? ' ' . sprintf( __( 'with IP %s', 'mailster' ), $subscriber->ip_confirm ) : '' )
					: __( 'unknown', 'mailster' ) ?>
					</li>
					<li><strong><?php esc_html_e( 'latest known IP', 'mailster' );?>:</strong> <?php echo $meta->ip
					? $meta->ip
					: __( 'unknown', 'mailster' ) ?>
					</li>
				</ul>
			</div>
			<div class="info">
				<strong><?php esc_html_e( 'latest updated', 'mailster' );?>:</strong> <?php echo $subscriber->updated
				? date( $timeformat, $subscriber->updated + $timeoffset ) . ', ' . sprintf( __( '%s ago', 'mailster' ), human_time_diff( $now, $subscriber->updated ) )
				: __( 'never', 'mailster' ) ?>
			</div>
			<?php endif; ?>
			<div class="custom-field-wrap">
			<?php
			if ( $customfields ) :
				foreach ( $customfields as $field => $data ) {
			?>
				<div class="detail">
					<label for="mailster_data_<?php echo $field ?>" class="label-type-<?php echo $data['type'] ?>"><?php echo strip_tags( $data['name'] ) ?>:</label>
						<code title="<?php printf( __( 'use %1$s as placeholder tag to replace it with %2$s', 'mailster' ), '{' . $field . '}', '&quot;' . $subscriber->{$field} . '&quot;' ) ?>">{<?php echo $field ?>}</code>
					<ul class="click-to-edit type-<?php echo $data['type'] ?>">
				<?php
				switch ( $data['type'] ) {

					case 'dropdown':
				?>
						<li><?php echo $subscriber->{$field} ? $subscriber->{$field} : __( 'nothing selected', 'mailster' ) ?></li>
						<li><select id="mailster_data_<?php echo $field ?>" name="mailster_data[<?php echo $field ?>]">
							<?php foreach ( $data['values'] as $v ) {?>
								<option value="<?php echo esc_attr( $v ) ?>" <?php selected( ( ! empty( $subscriber->{$field} ) ) ? $subscriber->{$field} : ( isset( $data['default'] ) ? $data['default'] : null ), $v ) ?>><?php echo $v ?></option>
							<?php }?>
						</select></li>
							<?php
					break;

					case 'radio':
				?>
							<li><?php echo $subscriber->{$field} ?></li>
							<li><ul>
							<?php
							$i = 0;
							foreach ( $data['values'] as $v ) { ?>
									<li><label for="mailster_data_<?php echo $field ?>_<?php echo $i++ ?>"><input type="radio" id="mailster_data_<?php echo $field ?>_<?php echo $i++ ?>" name="mailster_data[<?php echo $field ?>]" value="<?php echo esc_attr( $v ) ?>" <?php checked( $subscriber->{$field}, $v ) ?>> <?php echo $v ?> </label></li>
							<?php } ?>
							</ul>
							</li>
							<?php
					break;

					case 'checkbox':
				?>
						<li> <?php echo $subscriber->{$field} ? __( 'yes', 'mailster' ) : __( 'no', 'mailster' ) ?></li>
						<li><label for="mailster_data_<?php echo $field ?>" class="label-type-checkbox"><input type="checkbox" id="mailster_data_<?php echo $field ?>" name="mailster_data[<?php echo $field ?>]" value="1" <?php checked( $subscriber->{$field}, true ) ?>> <?php echo $data['name'] ?> </label>
							</li>
							<?php
					break;

					case 'date':
				?>
						<li><?php echo $subscriber->{$field} ? '<p>' . date( get_option( 'date_format' ), strtotime( $subscriber->{$field} ) ) . '</p>' : $subscriber->{$field} . '&nbsp;'; ?></li>
						<li><input type="text" id="mailster_data_<?php echo $field ?>" name="mailster_data[<?php echo $field ?>]" value="<?php echo esc_attr( $subscriber->{$field} ); ?>" class="regular-text input datepicker"></li>
							<?php
					break;

					case 'textarea':
				?>
						<li><?php echo $subscriber->{$field} ? '<p>' . nl2br( strip_tags( $subscriber->{$field} ) ) . '</p>' : $subscriber->{$field} . '&nbsp;'; ?></li>
						<li><textarea id="mailster_data_<?php echo $field ?>" name="mailster_data[<?php echo $field ?>]" class="regular-text input"><?php echo esc_textarea( $subscriber->{$field} ); ?></textarea></li>
							<?php
					break;

					default:
				?>
						<li><?php echo $subscriber->{$field} ? '<p>' . $subscriber->{$field} . '</p>' : $subscriber->{$field} . '&nbsp;'; ?></li>
						<li><input type="text" id="mailster_data_<?php echo $field ?>" name="mailster_data[<?php echo $field ?>]" value="<?php echo esc_attr( $subscriber->{$field} ); ?>" class="regular-text input"></li>
							<?php
				}
			?>

					</ul>
				</div>

				<?php
				}
			endif;
			?>

			</div>
			<?php do_action( 'mailster_subscriber_after_meta', $subscriber ) ?>
			<div class="detail v-top">
				<label><?php esc_html_e( 'Lists', 'mailster' );?>:</label>
				<ul class="click-to-edit type-list">
				<li>
				<?php
				$confirmed = array();
				if ( $lists = $this->get_lists( $subscriber->ID ) ) :
					foreach ( $lists as $list ) {
						if ( $list->confirmed ) {
							$confirmed[ $list->ID ] = $list->confirmed;
						}
						echo '<span title="' . $list->description . '" class="' . ($list->confirmed ? 'confirmed' : 'not-confirmed') . '">' . $list->name . '</span>';
					} else :
						echo '<span class="description">' . __( 'User has not been assigned to a list', 'mailster' ) . '</span>';

				endif; ?>
				</li>
				<li>
				<?php
				$checked = wp_list_pluck( $lists, 'ID' );
				$all_lists = mailster( 'lists' )->get();
				echo '<ul>';
				foreach ( $all_lists as $list ) {
					echo '<li>';
					echo '<label title="' . ( $list->description ? $list->description : $list->name ) . '">' . ( $list->parent_id ? '&nbsp;&#x2517;&nbsp;' : '' ) . '<input type="checkbox" value="' . $list->ID . '" name="mailster_lists[]" ' . checked( in_array( $list->ID, $checked ), true, false ) . ' class="list' . ( $list->parent_id ? ' list-parent-' . $list->parent_id : '' ) . '"> ' . $list->name . '' . '</label>';
					if ( in_array( $list->ID, $checked ) ) {
						echo '<span class="confirmation-status">' . (isset( $confirmed[ $list->ID ] ) ? sprintf( __( 'Confirmed on the %s', 'mailster' ), date( $timeformat, $confirmed[ $list->ID ] ) ) : __( 'not confirmed', 'mailster' )) . '</span>';
					}
					echo '</li>';
				}
				echo '</ul>';
				?>
				</li>
				</ul>
			</div>
		</td>
		<td class="user-meta" align="right">
			<?php if ( ! $is_new ) : ?>
				<?php if ( $meta->coords ) : $geo = explode( '|', $meta->geo );?>
					<div class="map zoomable" data-missingkey="<?php esc_attr_e( 'Please enter a valid Google API key on the settings page if the map is missing!', 'mailster' ); ?>">
					<?php $mapurl = add_query_arg( array(
							'markers' => $meta->coords,
							'zoom' => $geo[1] ? 5 : 3,
							'size' => '300x250',
							'visual_refresh' => true,
							'scale' => 2,
							'language' => get_locale(),
							'key' => mailster_option( 'google_api_key' ),
					), '//maps.googleapis.com/maps/api/staticmap' );
					?>
					<img src="<?php echo esc_url( $mapurl ) ?>" width="300" heigth="250">
					</div>
					<p class="alignright">
						<?php
						if ( $geo[1] ) {
							echo __( 'from', 'mailster' ) . sprintf( ' %s, %s', '<strong><a href="https://www.google.com/maps/@' . $meta->coords . ',11z" class="external">' . $geo[1] . '</a></strong>', '<span class="mailster-flag-24 flag-' . strtolower( $geo[0] ) . '"></span> ' . mailster( 'geo' )->code2Country( $geo[0] ) );
						}
						?>
				<?php elseif ( $meta->geo ) : ?>
				<?php $geo = explode( '|', $meta->geo );?>
				<div class="map">
					<?php $mapurl = add_query_arg( array(
							'center' => mailster( 'geo' )->code2Country( $geo[0] ),
							'zoom' => 3,
							'size' => '300x250',
							'visual_refresh' => true,
							'scale' => 2,
							'language' => get_locale(),
							'key' => mailster_option( 'google_api_key' ),
					), '//maps.googleapis.com/maps/api/staticmap' );
					?>
					<img src="<?php echo esc_url( $mapurl ) ?>" width="300" heigth="250">
				</div>
				<p class="alignright">
				<?php echo __( 'from', 'mailster' ) . ' <span class="mailster-flag-24 flag-' . strtolower( $geo[0] ) . '"></span> ' . mailster( 'geo' )->code2Country( $geo[0] );	?>
				<?php endif; ?>
					<?php if ( ! is_null( $meta->timeoffset ) ) : $t = time() + ( $meta->timeoffset * 3600 ) ?>
						<?php echo '<br>' . __( 'Local Time', 'mailster' ) . ': <span title="' . date( $timeformat, $t ) . '">' . date( get_option( 'time_format' ), $t ) . '</span>'; ?>
						<?php echo '<br>UTC ' . ( $meta->timeoffset < 0 ? '' : '+' ) . $meta->timeoffset ?>
					<?php endif; ?>
				</p>
			<?php endif; ?>
		</td>
	</tr>
</table>
<?php

if ( ! $is_new ) :

	$sent = $this->get_sent( $subscriber->ID );
	$openrate = $this->get_open_rate( $subscriber->ID );
	$clickrate = $this->get_click_rate( $subscriber->ID );
	$aclickrate = $this->get_adjusted_click_rate( $subscriber->ID );

?>
		<div class="stats-wrap">
			<table id="stats">
				<tr>
				<td><span class="verybold"><?php echo $sent ?></span> <?php echo _n( 'Campaign sent', 'Campaigns sent', $sent, 'mailster' ) ?></td>
				<td width="60">
				<div id="stats_open" class="piechart" data-percent="<?php echo $openrate * 100 ?>"><span>0</span>%</div>
				</td>
				<td><span class="verybold"></span> <?php esc_html_e( 'open rate', 'mailster' ) ?></td>
				<td width="60">
				<div id="stats_click" class="piechart" data-percent="<?php echo $clickrate * 100 ?>"><span>0</span>%</div>
				</td>
				<td><span class="verybold"></span> <?php esc_html_e( 'click rate', 'mailster' ) ?></td>
				<td width="60">
				<div id="stats_click" class="piechart" data-percent="<?php echo $aclickrate * 100 ?>"><span>0</span>%</div>
				</td>
				<td><span class="verybold"></span> <?php esc_html_e( 'adjusted click rate', 'mailster' ) ?></td>
				</tr>
			</table>
		</div>

		<?php if ( $clients = $this->get_clients( $subscriber->ID ) ) : ?>
		<div class="clients-wrap">

			<?php $mostpopular = array_shift( $clients ); ?>

			<h3><?php esc_html_e( 'Most popular client', 'mailster' ) ?>: <span class="mailster-icon client-<?php echo $mostpopular['type'] ?>"></span><?php echo $mostpopular['name'] . ' <span class="count">(' . round( $mostpopular['percentage'] * 100, 2 ) . '%)</span> ' ?></h3>

		<?php if ( ! empty( $clients ) ) : ?>
			<p><?php esc_html_e( 'Other used clients', 'mailster' ) ?>:
			<?php
			foreach ( $clients as $client ) {
				echo '<span class="mailster-icon client-' . $client['type'] . '"></span> <strong>' . $client['name'] . '</strong> <span class="count">(' . round( $client['percentage'] * 100, 2 ) . '%)</span>, ';
			}
			?>

			</p>
		<?php endif; ?>

	</div>
	<?php endif; ?>
	<div class="activity-wrap">
		<?php

		if ( $activities = $this->get_activity( $subscriber->ID ) ) :

			$open_time = $this->open_time( $subscriber->ID );
			$click_time = $this->click_time( $subscriber->ID );

				?>
				<h3><?php esc_html_e( 'Activity', 'mailster' ); ?></h3>
				<p>
				<?php if ( $open_time ) : ?>
				<?php
				printf( __( '%1$s needs about %2$s to open a campaign', 'mailster' ), ( $subscriber->fullname ? $subscriber->fullname : __( 'User', 'mailster' ) ), '<strong>' . human_time_diff( $now + $open_time ) . '</strong>' ); ?>
				<?php
				if ( $click_time ) {
					printf( __( 'and %1$s to click a link', 'mailster' ), '<strong>' . human_time_diff( $now + $click_time ) . '</strong>' );
				} ?>
				<?php else : ?>
				<?php esc_html_e( 'User has never opened a campaign', 'mailster' ); ?>
				<?php endif; ?>
					</p>
					<table class="wp-list-table widefat activities">
				<thead>
					<tr><th><?php esc_html_e( 'Date', 'mailster' );?></th><th></th><th><?php esc_html_e( 'Action', 'mailster' );?></th><th><?php esc_html_e( 'Campaign', 'mailster' );?></th><th></th></tr>
				</thead>
				<tbody>
					<?php foreach ( $activities as $i => $activity ) { ?>
					<tr class="<?php if ( ! ( $i % 2 ) ) { echo ' alternate'; } ?>">
						<td><?php echo $now - $activity->timestamp < 3600 ? sprintf( __( '%s ago', 'mailster' ), human_time_diff( $now, $activity->timestamp ) ) : date( $timeformat, $activity->timestamp + $timeoffset ); ?></td>
						<td><?php
						switch ( $activity->type ) {
							case 1:
								echo '<span class="mailster-icon mailster-icon-progress"></span></td><td>';
								printf( __( 'Campaign %s has been sent', 'mailster' ), '<a href="' . admin_url( 'post.php?post=' . $activity->campaign_id . '&action=edit' ) . '">' . $activity->campaign_title . '</a>' );
								break;
							case 2:
									echo '<span class="mailster-icon mailster-icon-open"></span></td><td>';
									printf( __( 'opened Campaign %s', 'mailster' ), '<a href="' . admin_url( 'post.php?post=' . $activity->campaign_id . '&action=edit' ) . '">' . $activity->campaign_title . '</a>' );
								break;
							case 3:
									echo '<span class="mailster-icon mailster-icon-click"></span></td><td>';
									printf( __( 'clicked %1$s in Campaign %2$s', 'mailster' ), '<a href="' . $activity->link . '">' . __( 'a link', 'mailster' ) . '</a>', '<a href="' . admin_url( 'post.php?post=' . $activity->campaign_id . '&action=edit' ) . '">' . $activity->campaign_title . '</a>' );
								break;
							case 4:
									echo '<span class="mailster-icon mailster-icon-unsubscribe"></span></td><td>';
									$unsub_status = $this->meta( $subscriber->ID, 'unsubscribe', $activity->campaign_id );
								if ( 'link_unsubscribe_list' == $unsub_status ) {
									echo __( 'unsubscribed from a list', 'mailster' );
								} else {
									echo __( 'unsubscribed your newsletter', 'mailster' );
								}
								break;
							case 5:
									echo '<span class="mailster-icon mailster-icon-bounce"></span></td><td>';
									printf( __( 'Soft bounce (%d tries)', 'mailster' ), $activity->count );

								break;
							case 6:
									echo '<span class="mailster-icon mailster-icon-bounce hard"></span></td><td>';
									echo __( 'Hard bounce', 'mailster' );
								break;
							case 7:
									echo '<span class="mailster-icon mailster-icon-error"></span></td><td>';
									echo __( 'Error', 'mailster' );
								break;
							default:
									echo '</td><td>';
								break;
						} ?>

						</td>
						<td><a href="<?php echo admin_url( 'post.php?post=' . $activity->campaign_id . '&action=edit' ) ?>"><?php echo $activity->campaign_title ?></a></td>
						<td width="50%">
						<?php if ( $activity->campaign_status == 'trash' ) : ?>
							<?php esc_html_e( 'campaign deleted', 'mailster' );?>

						<?php elseif ( $activity->type == 1 && current_user_can( 'publish_newsletters' ) ) : ?>
							<a href="<?php echo add_query_arg( array( 'resendcampaign' => 1, '_wpnonce' => wp_create_nonce( 'mailster-resend-campaign' ), 'campaign_id' => $activity->campaign_id ) ); ?>" class="button button-small" onclick="return confirm('<?php printf( esc_attr__( 'Do you really like to resend campaign %1$s to %2$s?', 'mailster' ), "\\n\'" . $activity->campaign_title . "\'", "\'" . $nicename . "\'" ); ?>');">
							<?php esc_html_e( 'resend this campaign', 'mailster' );?>
							</a>

						<?php elseif ( $activity->link && $activity->type == 3 ) : ?>
							<a href="<?php echo $activity->link ?>"><?php echo $activity->link ?></a>

						<?php elseif ( $activity->type == 4 && $unsub_status = $this->meta( $subscriber->ID, 'unsubscribe', $activity->campaign_id ) ) :
							$message = mailster( 'helper' )->get_unsubscribe_message( $unsub_status ); ?>
							<p class="unsubscribe-message code">[<?php echo esc_html( $unsub_status ) ?>] <?php echo esc_html( $message ); ?></p>

						<?php elseif ( ( $activity->type == 5 || $activity->type == 6 ) && $bounce_status = $this->meta( $subscriber->ID, 'bounce', $activity->campaign_id ) ) :
							$message = mailster( 'helper' )->get_bounce_message( $bounce_status ); ?>
							<p class="bounce-message code"><?php echo esc_html( $message ); ?></p>

						<?php elseif ( $activity->error && $activity->type == 7 ) : ?>
							<p class="error-message code"><strong class="red"><?php echo $activity->error ?></strong></p>
						<?php endif; ?>
						</td>
					</tr>
			<?php } ?>
				</tbody>
			</table>
		<?php else : ?>
		<p class="description"><?php esc_html_e( 'no activity yet', 'mailster' );?></p>
		<?php endif; ?>
	</div>

<?php endif; // !is_new ?>
</form>
</div>
