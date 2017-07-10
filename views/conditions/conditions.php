<div class="mailster-conditions">

<?php foreach ( $conditions as $i => $condition ) : ?>
	<?php
		$value = $condition['value'];
		$field = $condition['field'];
		$operator = $this->get_field_operator( $condition['operator'] );
		?>
	<div class="mailster-conditions-group">

		<div class="mailster-condition" data-operator-and="<?php esc_attr_e( 'and', 'mailster' );?>" data-operator-or="<?php esc_attr_e( 'or', 'mailster' );?>">
			<div><a class="remove-condition" title="<?php esc_html_e( 'remove condition', 'mailster' );?>">&#10005;</a></div>

			<select name="mailster_data[list][conditions][<?php echo $i; ?>][field]" class="condition-field">
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


			<select name="mailster_data[list][conditions][<?php echo $i; ?>][field]" class="condition-field">
			<?php echo '<pre>ss' . print_r( $operator, true ) . '</pre>';
			foreach ( $this->operators as $key => $name ) :
				echo '<option value="' . $key . '"' . selected( $operator, $key, false ) . '>' . $name . '</option>';
			endforeach; ?>
			</select>
		<?php
			$path = MAILSTER_DIR . 'views/conditions/' . $field . '.php';
		if ( ! file_exists( $path ) ) {
			$path = MAILSTER_DIR . 'views/conditions/field.php';
		}
			include $path;
		?>
		</div>

	</div>

<?php endforeach; ?>

<?php echo '<pre>' . print_r( $conditions, true ) . '</pre>'; ?>

</div>


