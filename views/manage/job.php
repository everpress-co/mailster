<?php
	$lists  = mailster( 'lists' )->get();
	$lists  = wp_list_pluck( $lists, 'name', 'ID' );
	$status = mailster( 'subscribers' )->get_status();
?>
<ul>
	<?php if ( isset( $job['status'] ) ) : ?>
	<li><?php printf( esc_html__( 'with a status of %s', 'mailster' ), '<strong>' . implode( ', ', array_intersect_key( $status, array_flip( $job['status'] ) ) ) . '</strong>' ); ?></li>
	<?php endif; ?>

	<?php if ( isset( $job['lists'] ) ) : ?>
	<li><?php printf( esc_html__( 'assigned to lists %s', 'mailster' ), '<strong>' . implode( ', ', array_intersect_key( $lists, array_flip( $job['lists'] ) ) ) . '</strong>' ); ?></li>
	<?php endif; ?>

	<?php if ( isset( $job['nolists'] ) && $job['nolists'] ) : ?>
	<li><?php esc_html_e( 'and assigned to no list.', 'mailster' ); ?></li>
	<?php endif; ?>


	<?php if ( isset( $job['conditions'] ) ) : ?>
	<li><?php mailster( 'conditions' )->render( $job['conditions'] ); ?></li>
	<?php endif; ?>

	<?php if ( isset( $job['remove_lists'] ) ) : ?>
	<li>&#10004;<?php esc_html_e( 'remove lists', 'mailster' ); ?></li>
	<?php endif; ?>

	<?php if ( isset( $job['remove_actions'] ) ) : ?>
	<li>&#10004;<?php esc_html_e( 'remove actions', 'mailster' ); ?></li>
	<?php endif; ?>
</ul>
