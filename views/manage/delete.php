<?php if ( mailster( 'subscribers' )->get_count_by_status() ) : ?>
	<?php

	$lists   = mailster( 'lists' )->get( null, false );
	$no_list = mailster( 'lists' )->count( false );

	?>
<h2><?php echo esc_html__( 'Which subscribers do you like to delete?', 'mailster' ); ?></h2>

<form method="post" id="delete-subscribers">
	<?php wp_nonce_field( 'mailster_nonce' ); ?>

<h4><?php esc_html_e( 'Lists', 'mailster' ); ?></h4>
<section>
		<?php if ( ! empty( $lists ) ) : ?>
	<ul>
		<li><label><input type="checkbox" class="list-toggle"> <?php esc_html_e( 'toggle all', 'mailster' ); ?></label></li>
		<li>&nbsp;</li>
			<?php mailster( 'lists' )->print_it( null, false, 'lists', esc_html__( 'total', 'mailster' ) ); ?>
	</ul>
	<?php endif; ?>
		<?php if ( $no_list ) : ?>
	<ul>
		<li><label><input type="hidden" name="nolists" value="0"><input type="checkbox" name="nolists" value="1"> <?php echo esc_html__( 'subscribers not assigned to a list', 'mailster' ) . ' <span class="count">(' . number_format_i18n( $no_list ) . ' ' . esc_html__( 'total', 'mailster' ) . ')</span>'; ?></label></li>
	</ul>
	<?php endif; ?>
</section>
<h4><?php esc_html_e( 'Conditions', 'mailster' ); ?></h4>
<section>
		<?php mailster( 'conditions' )->view( array(), 'conditions' ); ?>
</section>
<h4><?php esc_html_e( 'Status', 'mailster' ); ?></h4>
<section>
	<p>
		<?php foreach ( mailster( 'subscribers' )->get_status( null, true ) as $i => $name ) : ?>
		<label><input type="checkbox" name="status[]" value="<?php echo (int) $i; ?>" checked> <?php echo esc_html( $name ); ?> </label>
		<?php endforeach; ?>
	</p>
	<p>
		<label><input type="checkbox" name="remove_lists" value="1"> <?php esc_html_e( 'Remove selected lists', 'mailster' ); ?> </label>
	</p>
	<p>
		<label><input type="checkbox" name="remove_actions" value="1"> <?php esc_html_e( 'Remove all actions from affected users', 'mailster' ); ?> </label>
	</p>
</section>
<section class="footer alternate">
<p>
	<input id="delete-subscriber-button" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Delete Subscribers permanently', 'mailster' ); ?>" />
	<span class="status wp-ui-text-icon spinner"></span>

</p>
</section>
</form>

<?php else : ?>

<h2><?php esc_html_e( 'You have no subscribers to delete!', 'mailster' ); ?></h2>

<?php endif; ?>
