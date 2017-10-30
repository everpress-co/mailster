<?php $count = count( $conditions ); ?>
<div class="mailster-conditions-render" data-emptytext="<?php esc_attr_e( 'No Conditions defined', 'mailster' ); ?>"><?php foreach ( $conditions as $i => $condition ) : ?>
	<?php
		$value = $condition['value'];
		$field = $condition['field'];
		$field_operator = $this->get_field_operator( $condition['operator'] );
		$nice = $this->print_condition( $condition );
		?>
		<div class="mailster-condition-render mailster-condition-render-<?php echo esc_attr( $condition['field'] ) ?>" title="<?php echo esc_attr( strip_tags( sprintf( '%s %s %s', $nice['field'], $nice['operator'], $nice['value'] ) ) ) ?>">
		<?php if ( $i ) : echo '<span class="mailster-condition-operators">' . $this->nice_name( $operator, 'operator' ) . '</span>'; endif; ?>
			<span class="mailster-condition-field"><?php echo $nice['field'] ?></span>
			<span class="mailster-condition-operator"><?php echo $nice['operator'] ?></span>
			<span class="mailster-condition-value"><?php echo $nice['value'] ?></span>
		</div>

<?php endforeach; ?></div>
