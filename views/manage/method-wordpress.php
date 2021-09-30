<form id="import_wordpress" method="post">
	<?php $roles = get_editable_roles(); ?>
	<div class="inner">
		<div>
			<p><strong><?php esc_html_e( 'WordPress users roles', 'mailster' ); ?></strong></p>
			<p class="description"><?php esc_html_e( 'Select the user roles you like to import.', 'mailster' ); ?></p>
			<p><label><input type="checkbox" class="wordpress-users-toggle" checked> <?php esc_html_e( 'toggle all', 'mailster' ); ?></label></p>
			<ul>
			<?php
			foreach ( $roles as $role_key => $role ) :
				?>
				<li><label><input type="checkbox" name="roles[]" value="<?php echo esc_attr( $role_key ); ?>" checked> <?php echo esc_html( $role['name'] ); ?></label></li>
			<?php endforeach; ?>
			</ul>
			<ul>
				<li><label><input type="checkbox" name="no_role" value="1" checked> <?php esc_html_e( 'users without a role', 'mailster' ); ?></label></li>
			</ul>
		</div>
		<div>
			<?php $meta_values = mailster( 'helper' )->get_wpuser_meta_fields(); ?>
			<p><strong><?php esc_html_e( 'Handle following meta values', 'mailster' ); ?></strong></p>
			<p class="description"><?php esc_html_e( 'Select the meta fields you like to import.', 'mailster' ); ?></p>
			<p><label><input type="checkbox" class="wordpress-users-toggle"> <?php esc_html_e( 'toggle all', 'mailster' ); ?></label></p>
			<ul>
			<?php foreach ( $meta_values as $i => $meta_value ) : ?>
				<li><label><input type="checkbox" name="meta_values[]" value="<?php echo esc_attr( $meta_value ); ?>"> <?php echo esc_html( $meta_value ); ?></label></li>
			<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<section class="footer alternate">
		<p>
			<input type="submit" class="button button-primary button-large" value="<?php esc_attr_e( 'Next Step', 'mailster' ); ?> &#x2192;">
			<span class="status wp-ui-text-icon"></span>
		</p>
	</section>
</form>
