<?php

	$lists   = mailster( 'lists' )->get( null, false );
	$no_list = mailster( 'lists' )->count( false );

?>
	<?php if ( ! empty( $lists ) || $no_list ) : ?>

	<div class="step1">
		<form method="post" id="delete-subscribers">
		<?php wp_nonce_field( 'mailster_nonce' ); ?>

		<h3><?php esc_html_e( 'Lists', 'mailster' ); ?>:</h3>

		<?php if ( ! empty( $lists ) ) : ?>
		<ul>
			<li><label><input type="checkbox" class="list-toggle"> <?php esc_html_e( 'toggle all', 'mailster' ); ?></label></li>
			<li>&nbsp;</li>
			<?php mailster( 'lists' )->print_it( null, false, 'lists', esc_html__( 'total', 'mailster' ) ); ?>
		</ul>
		<?php endif; ?>

		<?php if ( $no_list ) : ?>
		<ul>
			<li><label><input type="checkbox" name="nolists" value="1"> <?php esc_html_e( 'subscribers not assigned to a list', 'mailster' ) . ' <span class="count">(' . number_format_i18n( $no_list ) . ' ' . esc_html__( 'total', 'mailster' ) . ')</span>'; ?></label></li>
		</ul>
		<?php endif; ?>

		<h3><?php esc_html_e( 'Conditions', 'mailster' ); ?>:</h3>

		<?php mailster( 'conditions' )->view( array(), 'conditions' ); ?>

		<h3><?php esc_html_e( 'Status', 'mailster' ); ?>:</h3>
		<p>
			<?php foreach ( mailster( 'subscribers' )->get_status( null, true ) as $i => $name ) { ?>
			<label><input type="checkbox" name="status[]" value="<?php echo $i; ?>" checked> <?php echo $name; ?> </label>
			<?php } ?>
		</p>
		<p>
			<label><input type="checkbox" name="remove_lists" value="1"> <?php esc_html_e( 'Remove selected lists', 'mailster' ); ?> </label>
		</p>
		<p>
			<label><input type="checkbox" name="remove_actions" value="1"> <?php esc_html_e( 'Remove all actions from affected users', 'mailster' ); ?> </label>
		</p>
		<p>
			<input id="delete-subscriber-button" class="button button-large button-primary" type="submit" value="<?php esc_attr_e( 'Delete Subscribers permanently', 'mailster' ); ?>" />
		</p>
		<h2 class="delete-status"></h2>
		</form>
	</div>

	<?php else : ?>

<p><?php esc_html_e( 'No Subscriber found!', 'mailster' ); ?></p>

<?php endif; ?>
