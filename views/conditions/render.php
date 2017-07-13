<?php $count = count( $conditions ); ?>
<div class="mailster-conditions-render">
<?php foreach ( $conditions as $i => $condition ) : ?>
	<?php
		$value = $condition['value'];
		$field = $condition['field'];
		$field_operator = $this->get_field_operator( $condition['operator'] );
		?>
		<div class="mailster-condition">
		<?php if ( $i ) : echo '<span class="mailster-condition-operators">' . $this->nice_name( $operator, 'operator' ) . '</span>'; endif; ?>
			<span class="mailster-condition-field"><strong><?php echo $this->nice_name( $condition['field'], 'field' ) ?></strong></span>
			<span class="mailster-condition-operator"><em><?php echo $this->nice_name( $field_operator, 'operator' ) ?></em></span>
			<span class="mailster-condition-value">&quot;<strong><?php echo $this->nice_name( $value, 'value' ) ?></strong>&quot;</span>
		</div>


<?php endforeach; ?>
</div>
