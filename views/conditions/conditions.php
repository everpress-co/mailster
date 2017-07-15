<?php

if ( empty( $conditions ) ) {
	$conditions = array();
}

	array_unshift($conditions, array(
		'field' => '',
		'operator' => '',
		'value' => '',
	));

	$forms = mailster( 'forms' )->get_all();

?>
<div class="mailster-conditions-saved">
	<select class="regular-text textright">
		<option value="OR"<?php selected( $operator, 'OR' ) ?> title="<?php esc_html_e( 'or', 'mailster' );?>"><?php esc_html_e( 'one of the conditions is true', 'mailster' );?></option>
		<option value="AND"<?php selected( $operator, 'AND' ) ?> title="<?php esc_html_e( 'and', 'mailster' );?>"><?php esc_html_e( 'all of the conditions are true', 'mailster' );?></option>
	</select>
</div>
<div class="mailster-conditions-operator-selector">
	<select id="mailster_list_operator" class="widefat" name="mailster_data[list][operator]">
		<option value="OR"<?php selected( $operator, 'OR' ) ?> title="<?php esc_html_e( 'or', 'mailster' );?>"><?php esc_html_e( 'one of the conditions is true', 'mailster' );?></option>
		<option value="AND"<?php selected( $operator, 'AND' ) ?> title="<?php esc_html_e( 'and', 'mailster' );?>"><?php esc_html_e( 'all of the conditions are true', 'mailster' );?></option>
	</select>
</div>
<div class="mailster-condition-container"></div>
<div class="mailster-conditions" data-emptytext="<?php esc_attr_e( 'Please add your first condition by clicking on the button.', 'mailster' ); ?>"><?php foreach ( $conditions as $i => $condition ) :
		$value = $condition['value'];
		$field = $condition['field'];
		$field_operator = $this->get_field_operator( $condition['operator'] );
		?><div class="mailster-condition mailster-condition-operator-is-<?php echo 'AND' == $field_operator ? 'and' : 'or' ?>">
			<div class="mailster-condition-operators">
				<label class="mailster-condition-operator-and"><?php esc_attr_e( 'and', 'mailster' );?></label>
				<label class="mailster-condition-operator-or"><?php esc_attr_e( 'or', 'mailster' );?></label>
			</div>
			<a class="remove-condition" title="<?php esc_html_e( 'remove condition', 'mailster' );?>">&#10005;</a>

			<div class="mailster-condition-head">
			<select name="mailster_conditions[<?php echo $i; ?>][field]" class="condition-field">

				<optgroup label="<?php esc_html_e( 'Fields', 'mailster' );?>">
			<?php
			foreach ( $this->fields as $key => $name ) {
				echo '<option value="' . $key . '"' . selected( $condition['field'], $key, false ) . '>' . $name . '</option>';
			} ?>
				</optgroup>

				<optgroup label="<?php esc_html_e( 'Custom Fields', 'mailster' );?>">
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


			<select name="mailster_conditions[<?php echo $i; ?>][operator]" class="condition-operator">
			<?php
			foreach ( $this->operators as $key => $name ) :
				echo '<option value="' . $key . '"' . selected( $field_operator, $key, false ) . '>' . $name . '</option>';
			endforeach; ?>
			</select>

			</div>
			<div class="mailster-conditions-value-fields">
				<div class="mailster-conditions-value-field mailster-conditions-value-field-default">
					<input type="text" class="widefat condition-value" value="<?php echo esc_attr( $value ); ?>" name="mailster_conditions[<?php echo $i; ?>][value]">
				</div>
				<div class="mailster-conditions-value-field" data-fields="rating">
					<input type="range" min="0" max="100" class="widefat condition-value" value="<?php echo esc_attr( $value ); ?>" name="mailster_conditions[<?php echo $i; ?>][value]">
				</div>
				<div class="mailster-conditions-value-field" data-fields="signup,confirm,added,updated">
					<input type="text" class="regular-text datepicker condition-value" value="<?php echo esc_attr( $value ); ?>" name="mailster_conditions[<?php echo $i; ?>][value]">
				</div>
				<div class="mailster-conditions-value-field" data-fields="wp_id">
					<input type="text" class="regular-text condition-value" value="<?php echo esc_attr( $value ); ?>" name="mailster_conditions[<?php echo $i; ?>][value]">
				</div>
				<div class="mailster-conditions-value-field" data-fields="form">
					<select name="mailster_conditions[<?php echo $i; ?>][value]" class="condition-value">
					<?php
					foreach ( $forms as $form ) :
						echo '<option value="' . $form->ID . '"' . selected( $form->ID, $value, false ) . '>#' . $form->ID . ' ' . $form->name . '</option>';
					endforeach; ?>
					</select>
				</div>
				<div class="mailster-conditions-value-field" data-fields="sent,sent__not_in,open,open__not_in,click,click__not_in">
			<?php if ( $all_campaigns = mailster( 'campaigns' )->get_campaigns( array( 'post__not_in' => array( $post->ID ) ) ) ) :

				// bypass post_status sort limitation.
				$all_campaings_stati = wp_list_pluck( $all_campaigns, 'post_status' );
				asort( $all_campaings_stati );

			?>
					<select name="mailster_conditions[<?php echo $i; ?>][value]" class="condition-value">
					<option value="0">--</option>
				<?php
				global $wp_post_statuses;
				$status = '';
				foreach ( $all_campaings_stati as $i => $c ) {
					$c = $all_campaigns[ $i ];
					if ( $status != $c->post_status ) {
						if ( $status ) {
							// echo '</optgroup>';
						}
						// echo '<optgroup label="' . $wp_post_statuses[ $c->post_status ]->label . '">';
						$status = $c->post_status;
					}
					?><option value="<?php echo $c->ID ?>" <?php selected( $value, $c->ID );?>><?php echo $c->post_title ? $c->post_title : '[' . __( 'no title', 'mailster' ) . ']' ?></option><?php
				} ?>
					</optgroup></select>
					<p class="description">Add campaigns with OR connection</p>
				<?php else : ?>
				<p><?php esc_html_e( 'No campaigns available', 'mailster' );?><input type="hidden" class="condition-value" value="<?php echo esc_attr( $value ); ?>" name="mailster_conditions[<?php echo $i; ?>][value]"></p>
			<?php endif; ?>
				</div>
			</div>

	</div><?php endforeach; ?></div>

	<div class="mailster-condition-empty">
		<a class="button add-condition">Add Condition</a>
	</div>


