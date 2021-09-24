
asd

<div class="step1">
	<div class="step1-body">
		<div class="upload-method">
			<h2><?php esc_html_e( 'Upload', 'mailster' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Upload you subscribers as comma-separated list (CSV)', 'mailster' ); ?></p>
			<form enctype="multipart/form-data" method="post" action="<?php echo admin_url( 'admin-ajax.php?action=mailster_import_subscribers_upload_handler' ); ?>">

			<?php mailster( 'manage' )->media_upload_form(); ?>

			</form>
			<br>
		</div>
		<div class="upload-method-or">
			<?php esc_html_e( 'or', 'mailster' ); ?>
		</div>
		<div class="upload-method">
			<h2><?php esc_html_e( 'Paste', 'mailster' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Copy and paste from your spreadsheet app', 'mailster' ); ?></p>
			<textarea id="paste-import" class="widefat" rows="13" placeholder="<?php esc_attr_e( 'paste your list here', 'mailster' ); ?>">
justin.case@<?php echo $_SERVER['HTTP_HOST']; ?>; Justin; Case; Custom;
john.doe@<?php echo $_SERVER['HTTP_HOST']; ?>; John; Doe
jane.roe@<?php echo $_SERVER['HTTP_HOST']; ?>; Jane; Roe
			</textarea>
		</div>

	</div>
	<div class="clear"></div>
	<h2 class="import-status">&nbsp;</h2>
</div>

<div class="step2">
	<h2 class="import-status"></h2>
	<div class="step2-body"></div>
</div>

<?php if ( current_user_can( 'mailster_import_wordpress_users' ) ) : ?>

<div id="wordpress-users">
	<h2><?php esc_html_e( 'WordPress Users', 'mailster' ); ?></h2>
	<form id="import_wordpress" method="post">
		<?php

		global $wp_roles;
		$roles = $wp_roles->get_names();

		if ( ! empty( $roles ) ) :
			?>
		<div id="wordpress-user-roles">
			<h4><?php esc_html_e( 'Import WordPress users with following roles', 'mailster' ); ?></h4>
			<p><label><input type="checkbox" class="wordpress-users-toggle" checked> <?php esc_html_e( 'toggle all', 'mailster' ); ?></label></p>
			<ul>
			<?php
			$i = 0;
			foreach ( $roles as $role => $name ) {
				if ( ! ( $i % 8 ) && $i ) {
					echo '</ul><ul>';
				}
				?>
				<li><label><input type="checkbox" name="roles[]" value="<?php echo $role; ?>" checked> <?php echo $name; ?></label></li>
				<?php
				$i++;
			}
			?>
			</ul>
			<ul>
				<li><label><input type="checkbox" name="no_role" value="1" checked> <?php esc_html_e( 'users without a role', 'mailster' ); ?></label></li>
			</ul>
		</div>
		<div id="wordpress-user-meta">
			<?php $meta_values = mailster( 'helper' )->get_wpuser_meta_fields(); ?>
			<h4><?php esc_html_e( 'Use following meta values', 'mailster' ); ?></h4>
			<p><label><input type="checkbox" class="wordpress-users-toggle"> <?php esc_html_e( 'toggle all', 'mailster' ); ?></label></p>
			<ul>
			<?php
			foreach ( $meta_values as $i => $meta_value ) {
				if ( ! ( $i % 8 ) && $i ) {
					echo '</ul><ul>';
				}
				?>
				<li><label><input type="checkbox" name="meta_values[]" value="<?php echo esc_attr( $meta_value ); ?>"> <?php echo esc_html( $meta_value ); ?></label></li>
				<?php
			}
			?>
			</ul>
		</div>
		<?php endif; ?>
		<div class="clearfix clear">
			<input type="submit" class="button button-primary button-large" value="<?php esc_attr_e( 'Next Step', 'mailster' ); ?> &#x2192;">
		</div>
	</form>
</div>

<?php endif; ?>


<?php do_action( 'mailster_import_tab' ); ?>

