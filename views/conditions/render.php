<div class="mailster-conditions-render" data-emptytext="<?php esc_attr_e( 'No Conditions defined', 'mailster' ); ?>">
	<?php foreach ( $conditions as $i => $condition_group ) : ?>
	<div class="mailster-condition-render-group">
		<?php
		if ( $i ) {
			echo '<span class="mailster-condition-operators">' . esc_html__( 'and', 'mailster' ) . '</span>';
		}
		$condition_group = array_values( $condition_group );
		foreach ( $condition_group as $j => $condition ) :
			$field    = isset( $condition['field'] ) ? $condition['field'] : $condition[0];
			$operator = isset( $condition['operator'] ) ? $condition['operator'] : $condition[1];
			$value    = isset( $condition['value'] ) ? $condition['value'] : $condition[2];
			$nice     = $this->print_condition( $condition );
			?>
		<div class="mailster-condition-render mailster-condition-render-<?php echo esc_attr( $condition['field'] ); ?>" title="<?php echo esc_attr( strip_tags( sprintf( '%s %s %s', $nice['field'], $nice['operator'], $nice['value'] ) ) ); ?>">
			<?php
			if ( $j ) {
				echo '<span class="mailster-condition-type mailster-condition-operators">' . esc_html__( 'or', 'mailster' ) . '</span>';
			}
			?>
		<span class="mailster-condition-type mailster-condition-field"><?php echo $nice['field']; ?></span>
		<span class="mailster-condition-type mailster-condition-operator"><?php echo $nice['operator']; ?></span>
		<span class="mailster-condition-type mailster-condition-value"><?php echo $nice['value']; ?></span>
			<?php
			// remove the current condition
			$c = $conditions;
			unset( $c[ $i ][ $j ] );
			$newlink = add_query_arg( 'conditions', $c, remove_query_arg( 'conditions' ) );
			?>
		<a class="mailster-condition-remove" href="<?php echo esc_url( $newlink ); ?>" title="<?php esc_attr_e( 'remove condition', 'mailster' ); ?>">✕</a>
		</div>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>
</div>
