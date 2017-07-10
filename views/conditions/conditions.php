
<div class="mailster-conditions-operator-selector">
	<select id="mailster_list_operator" class="widefat" name="mailster_data[list][operator]">
		<option value="OR"<?php selected( $operator, 'OR' ) ?> title="<?php esc_html_e( 'or', 'mailster' );?>"><?php esc_html_e( 'one of the conditions is true', 'mailster' );?></option>
		<option value="AND"<?php selected( $operator, 'AND' ) ?> title="<?php esc_html_e( 'and', 'mailster' );?>"><?php esc_html_e( 'all of the conditions are true', 'mailster' );?></option>
	</select>
</div>
<div class="mailster-conditions">
<?php foreach ( $conditions as $i => $condition ) : ?>
	<?php
		$value = $condition['value'];
		$field = $condition['field'];
		$field_operator = $this->get_field_operator( $condition['operator'] );
		?>
	<div class="mailster-conditions-group">

		<div class="mailster-condition mailster-condition-operator-is-<?php echo 'AND' == $operator ? 'and' : 'or' ?>">
			<div class="mailster-condition-operators">
				<label class="mailster-condition-operator-and"><?php esc_attr_e( 'and', 'mailster' );?></label>
				<label class="mailster-condition-operator-or"><?php esc_attr_e( 'or', 'mailster' );?></label>
			</div>
			<a class="remove-condition" title="<?php esc_html_e( 'remove condition', 'mailster' );?>">&#10005;</a>

			<div class="mailster-condition-head">
			<select name="mailster_conditions[<?php echo $i; ?>][field]" class="condition-field">
			<?php
			foreach ( $this->fields as $key => $name ) {
				echo '<option value="' . $key . '"' . selected( $condition['field'], $key, false ) . '>' . $name . '</option>';
			} ?>
				<optgroup label="<?php esc_html_e( 'Custom Fields', 'mailster' );?>">
			<?php
			foreach ( $this->custom_fields as $key => $customfield ) {
				echo '<option value="' . $key . '"' . selected( $condition['field'], $key, false ) . '>' . $customfield['name'] . '</option>';
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
			<div>
		<?php
			$path = MAILSTER_DIR . 'views/conditions/' . $field . '.php';
		if ( ! file_exists( $path ) ) {
			$path = MAILSTER_DIR . 'views/conditions/field.php';
		}
			include $path;
		?>
			</div>
		</div>

	</div>

<?php endforeach; ?>

	<div class="mailster-condition-empty">
		<a class="button add-condition">Add Condition</a>
	</div>

</div>


