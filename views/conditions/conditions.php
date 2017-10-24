<?php
global $post, $wp_post_statuses;

if ( empty( $conditions ) ) {
	$conditions = array();
}

	array_unshift($conditions, array(
		'field' => '',
		'operator' => '',
		'value' => '',
	));

	$forms = mailster( 'forms' )->get_all();
	$lists = mailster( 'lists' )->get();
	$all_campaigns = mailster( 'campaigns' )->get_campaigns( array( 'post__not_in' => array( $post->ID ), 'orderby' => 'post_title' ) );
	$all_campaigns_stati = wp_list_pluck( $all_campaigns, 'post_status' );
	asort( $all_campaigns_stati );
	$statuses = mailster( 'subscribers' )->get_status( null, true );

?>
<div class="mailster-conditions-operator-selector">
	<p><?php esc_attr_e( 'Send Campaign only to subscribers if', 'mailster' );?>
	<select class="mailster-list-operator" class="widefat" name="mailster_data[list][operator]">
		<option value="OR"<?php selected( $operator, 'OR' ) ?> title="<?php esc_html_e( 'or', 'mailster' );?>"><?php esc_html_e( 'one of the conditions is true', 'mailster' );?></option>
		<option value="AND"<?php selected( $operator, 'AND' ) ?> title="<?php esc_html_e( 'and', 'mailster' );?>"><?php esc_html_e( 'all of the conditions are true', 'mailster' );?></option>
	</select>
	</p>
</div>
<div class="mailster-condition-container"></div>
<div class="mailster-conditions mailster-condition-operator-is-<?php echo 'AND' == $operator ? 'and' : 'or' ?>" data-emptytext="<?php esc_attr_e( 'Please add your first condition.', 'mailster' ); ?>"><?php
foreach ( $conditions as $i => $condition ) :
		$value = $condition['value'];
		$field = $condition['field'];
		$field_operator = $this->get_field_operator( $condition['operator'] );
		?><div class="mailster-condition">
			<div class="mailster-condition-operators">
				<label class="mailster-condition-operator-and"><?php esc_attr_e( 'and', 'mailster' );?></label>
				<label class="mailster-condition-operator-or"><?php esc_attr_e( 'or', 'mailster' );?></label>
			</div>
			<a class="remove-condition" title="<?php esc_html_e( 'remove condition', 'mailster' );?>">&#10005;</a>

			<div class="mailster-conditions-field-fields">
				<select name="mailster_data[list][conditions][<?php echo $i; ?>][field]" class="condition-field" disabled>

					<optgroup label="<?php esc_html_e( 'Fields', 'mailster' );?>">
					<?php
					foreach ( $this->fields as $key => $name ) {
						echo '<option value="' . $key . '"' . selected( $condition['field'], $key, false ) . '>' . $name . '</option>';
					} ?>
					</optgroup>

					<optgroup label="<?php esc_html_e( 'User related', 'mailster' );?>">
					<?php
					foreach ( $this->custom_fields as $key => $customfield ) {
						echo '<option value="' . $key . '"' . selected( $condition['field'], $key, false ) . '>' . $customfield['name'] . '</option>';
					} ?>
					</optgroup>

					<optgroup label="<?php esc_html_e( 'Campaign related', 'mailster' );?>">
					<?php
					foreach ( $this->campaign_related as $key => $name ) {
						echo '<option value="' . $key . '"' . selected( $condition['field'], $key, false ) . '>' . $name . '</option>';
					} ?>
					</optgroup>

					<optgroup label="<?php esc_html_e( 'List related', 'mailster' );?>">
					<?php
					foreach ( $this->list_related as $key => $name ) {
						echo '<option value="' . $key . '"' . selected( $condition['field'], $key, false ) . '>' . $name . '</option>';
					} ?>
					</optgroup>

					<optgroup label="<?php esc_html_e( 'Meta Data', 'mailster' );?>">
					<?php
					foreach ( $this->meta_fields as $key => $name ) {
						echo '<option value="' . $key . '"' . selected( $condition['field'], $key, false ) . '>' . $name . '</option>';
					} ?>
					</optgroup>

					<optgroup label="<?php esc_html_e( 'WordPress User Meta', 'mailster' );?>">
					<?php
					foreach ( $this->wp_user_meta as $key => $name ) {
						if ( is_integer( $key ) ) {
							$key = $name;
						}
						echo '<option value="' . $key . '"' . selected( $condition['field'], $key, false ) . '>' . $name . '</option>';
					} ?>
					</optgroup>
				</select>
			</div>

			<div class="mailster-conditions-operator-fields">
				<div class="mailster-conditions-operator-field mailster-conditions-operator-field-default">
					<select name="mailster_data[list][conditions][<?php echo $i; ?>][operator]" class="condition-operator" disabled>
					<?php
					foreach ( $this->operators as $key => $name ) :
						echo '<option value="' . $key . '"' . selected( $field_operator, $key, false ) . '>' . $name . '</option>';
					endforeach; ?>
					</select>
				</div>
				<div class="mailster-conditions-operator-field" data-fields="rating,">
					<select name="mailster_data[list][conditions][<?php echo $i; ?>][operator]" class="condition-operator" disabled>
					<?php
					foreach ( $this->simple_operators as $key => $name ) :
						echo '<option value="' . $key . '"' . selected( $field_operator, $key, false ) . '>' . $name . '</option>';
					endforeach; ?>
					</select>
				</div>
				<div class="mailster-conditions-operator-field" data-fields="wp_capabilities,status,form,clienttype,">
					<select name="mailster_data[list][conditions][<?php echo $i; ?>][operator]" class="condition-operator" disabled>
					<?php
					foreach ( $this->bool_operators as $key => $name ) :
						echo '<option value="' . $key . '"' . selected( $field_operator, $key, false ) . '>' . $name . '</option>';
					endforeach; ?>
					</select>
				</div>
				<div class="mailster-conditions-operator-field" data-fields="<?php echo implode( ',', $this->time_fields ) ?>,">
					<select name="mailster_data[list][conditions][<?php echo $i; ?>][operator]" class="condition-operator" disabled>
					<?php
					foreach ( $this->date_operators as $key => $name ) :
						echo '<option value="' . $key . '"' . selected( $field_operator, $key, false ) . '>' . $name . '</option>';
					endforeach; ?>
					</select>
				</div>
				<div class="mailster-conditions-operator-field" data-fields="_sent,_sent__not_in,_open,_open__not_in,_click,_click__not_in,_click_link,_click_link__not_in,_lists__not_in,">
					<input type="hidden" name="mailster_data[list][conditions][<?php echo $i; ?>][operator]" class="condition-operator" disabled value="is">
				</div>
			</div>

			<div class="mailster-conditions-value-fields">
				<?php
				if ( is_array( $value ) ) {
					$value_arr = $value;
					$value = $value[0];
				} else {
					$value_arr = array( $value );
				}
				?>
				<div class="mailster-conditions-value-field mailster-conditions-value-field-default">
					<input type="text" class="widefat condition-value" disabled value="<?php echo esc_attr( $value ); ?>" name="mailster_data[list][conditions][<?php echo $i; ?>][value]">
				</div>
				<div class="mailster-conditions-value-field" data-fields="rating,">
					<?php
					$stars = ( round( $this->sanitize_rating( $value ) / 10, 2 ) * 50 );
					$full = max( 0, min( 5, floor( $stars ) ) );
					$half = max( 0, min( 5, round( $stars - $full ) ) );
					$empty = max( 0, min( 5, 5 - $full - $half ) );
					?>
					<div class="mailster-rating">
					<?php
					echo str_repeat( '<span class="mailster-icon enabled"></span>', $full )
					. str_repeat( '<span class="mailster-icon enabled"></span>', $half )
					. str_repeat( '<span class="mailster-icon"></span>', $empty )
					?>
					</div>
					<input type="hidden" class="condition-value" disabled value="<?php echo esc_attr( $value ); ?>" name="mailster_data[list][conditions][<?php echo $i; ?>][value]">
				</div>
				<div class="mailster-conditions-value-field" data-fields="<?php echo implode( ',', $this->time_fields ) ?>,">
					<input type="text" class="regular-text datepicker condition-value" disabled value="<?php echo esc_attr( $value ); ?>" name="mailster_data[list][conditions][<?php echo $i; ?>][value]">
				</div>
				<div class="mailster-conditions-value-field" data-fields="wp_id,">
					<input type="text" class="regular-text condition-value" disabled value="<?php echo esc_attr( $value ); ?>" name="mailster_data[list][conditions][<?php echo $i; ?>][value]">
				</div>
				<div class="mailster-conditions-value-field" data-fields="wp_capabilities,">
					<select name="mailster_data[list][conditions][<?php echo $i; ?>][value]" class="condition-value" disabled>
						<?php echo wp_dropdown_roles( $value ) ?>
					</select>
				</div>
				<div class="mailster-conditions-value-field" data-fields="status,">
					<select name="mailster_data[list][conditions][<?php echo $i; ?>][value]" class="condition-value" disabled>
						<?php foreach ( $statuses as $key => $name ) : ?>
							<option value="<?php echo intval( $key ) ?>" <?php selected( $key, $value ); ?>><?php echo esc_html( $name ) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="mailster-conditions-value-field" data-fields="form,">
					<select name="mailster_data[list][conditions][<?php echo $i; ?>][value]" class="condition-value" disabled>
					<?php
					foreach ( $forms as $form ) :
						echo '<option value="' . $form->ID . '"' . selected( $form->ID, $value, false ) . '>#' . $form->ID . ' ' . $form->name . '</option>';
					endforeach; ?>
					</select>
				</div>
				<div class="mailster-conditions-value-field" data-fields="clienttype,">
					<select name="mailster_data[list][conditions][<?php echo $i; ?>][value]" class="condition-value" disabled>
						<option value="desktop"<?php selected( $value, 'desktop' ) ?>><?php esc_html_e( 'Desktop', 'mailster' ); ?></option>
						<option value="webmail"<?php selected( $value, 'webmail' ) ?>><?php esc_html_e( 'Webmail', 'mailster' ); ?></option>
						<option value="mobile"<?php selected( $value, 'mobile' ) ?>><?php esc_html_e( 'Mobile', 'mailster' ); ?></option>
					</select>
				</div>
				<div class="mailster-conditions-value-field" data-fields="_sent,_sent__not_in,_open,_open__not_in,_click,_click__not_in,">
				<?php if ( $all_campaigns ) : ?>
					<?php foreach ( $value_arr as $k => $v ) : ?>
						<div class="mailster-conditions-value-field-multiselect">
							<span><?php esc_html_e( 'or', 'mailster' ); ?> </span>
							<select name="mailster_data[list][conditions][<?php echo $i; ?>][value][]" class="condition-value" disabled>
								<option value="0">--</option>
								<?php
								$status = '';
								foreach ( $all_campaigns_stati as $j => $c ) {
									$c = $all_campaigns[ $j ];
									if ( $status != $c->post_status ) {
										if ( $status ) {
											echo '</optgroup>';
										}
										echo '<optgroup label="' . $wp_post_statuses[ $c->post_status ]->label . '">';
										$status = $c->post_status;
									}
									?><option value="<?php echo $c->ID ?>" <?php selected( $v, $c->ID );?>><?php echo ($c->post_title ? esc_html( $c->post_title ) : '[' . esc_html__( 'no title', 'mailster' ) . ']') . ' (# ' . $c->ID . ')' ?></option><?php
								} ?>
								</optgroup>
							</select>
						<button class="button button-small mailster-condition-add-multiselect"><?php esc_html_e( 'or', 'mailster' ); ?></button>
						</div>
					<?php endforeach; ?>
				<?php else : ?>
					<p><?php esc_html_e( 'No campaigns available', 'mailster' );?><input type="hidden" class="condition-value" disabled value="0" name="mailster_data[list][conditions][<?php echo $i; ?>][value]"></p>
				<?php endif; ?>
				</div>
				<div class="mailster-conditions-value-field" data-fields="_lists__not_in,">
				<?php if ( $lists ) : ?>
					<?php foreach ( $value_arr as $k => $v ) : ?>
						<div class="mailster-conditions-value-field-multiselect">
							<span><?php esc_html_e( 'or', 'mailster' ); ?> </span>
							<select name="mailster_data[list][conditions][<?php echo $i; ?>][value][]" class="condition-value" disabled>
								<option value="0">--</option>
								<?php
								$status = '';
								foreach ( $lists as $j => $list ) { ?>
								<option value="<?php echo $list->ID ?>" <?php selected( $v, $list->ID );?>><?php echo ($list->name ? esc_html( $list->name ) : '[' . esc_html__( 'no title', 'mailster' ) . ']') ?></option>
								<?php } ?>
							</select>
						<button class="button button-small mailster-condition-add-multiselect"><?php esc_html_e( 'or', 'mailster' ); ?></button>
						</div>
					<?php endforeach; ?>
				<?php else : ?>
					<p><?php esc_html_e( 'No campaigns available', 'mailster' );?><input type="hidden" class="condition-value" disabled value="0" name="mailster_data[list][conditions][<?php echo $i; ?>][value]"></p>
				<?php endif; ?>
				</div>
				<div class="mailster-conditions-value-field" data-fields="_click_link,_click_link__not_in,">
				<?php foreach ( $value_arr as $k => $v ) : ?>
					<div class="mailster-conditions-value-field-multiselect">
					<span><?php esc_html_e( 'or', 'mailster' ); ?> </span>
						<input type="text" class="regular-text condition-value" disabled value="<?php echo esc_attr( $v ); ?>" name="mailster_data[list][conditions][<?php echo $i; ?>][value][]" placeholder="https://example.com">
					<button class="button button-small mailster-condition-add-multiselect"><?php esc_html_e( 'or', 'mailster' ); ?></button>
					</div>
				<?php endforeach; ?>
				</div>
			</div>

	</div><?php endforeach; ?></div>

	<div class="mailster-condition-empty">
		<a class="button add-condition"><?php esc_html_e( 'Add Condition', 'mailster' ); ?></a>
	</div>


