<div>
	<?php

	$count = count( $conditions );
	if ( count( $conditions ) > 1 ) :
	 	if ( 'OR' == $operator ) :
			esc_html_e( 'one of the conditions is true', 'mailster' );
		elseif ( 'AND' == $operator ) :
			esc_html_e( 'all of the conditions are true', 'mailster' );
		endif;
	endif;
	?>
</div>

<div class="mailster-conditions-render">
<?php foreach ( $conditions as $i => $condition ) : ?>
	<?php
		$value = $condition['value'];
		$field = $condition['field'];
		$field_operator = $this->get_field_operator( $condition['operator'] );
		?>
		<div class="mailster-condition">
		<?php if ( $i ) : echo '<em>' . $this->nice_name( $operator, 'operator' ) . '</em>'; endif; ?>
			<span class="mailster-condition-field"><strong><?php echo $this->nice_name( $condition['field'], 'field' ) ?></strong></span>
			<span class="mailster-condition-operator"><em><?php echo $this->nice_name( $field_operator, 'operator' ) ?></em></span>
			<span class="mailster-condition-value">&quot;<strong><?php echo $this->nice_name( $value, 'value' ) ?></strong>&quot;</span>
		</div>


<?php endforeach; ?>

</div>


