<?php
$classes   = array( 'theme' );
$classes[] = 'theme-' . dirname( $slug );
if ( $item['is_active'] ) {
	$classes[] = 'is-active';
}
if ( $item['installed'] ) {
	$classes[] = 'is-installed';
}
if ( ! $item['is_supported'] ) {
	$classes[] = 'not-supported';
}
if ( $item['update_available'] ) {
	$classes[] = 'update-available';
}
if ( $item['envato_item_id'] ) {
	$classes[] = 'envato-item';
}
?>
<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" tabindex="0" data-slug="<?php echo esc_attr( $slug ); ?>" data-item='<?php echo esc_attr( json_encode( $item ) ); ?>'>
	<span class="spinner"></span>
	<div class="theme-screenshot">
		<img loading="lazy" alt="" class="theme-screenshot-bg" srcset="<?php echo esc_attr( $item['image'] ); ?> 1x, <?php echo esc_attr( $item['imagex2'] ); ?> 2x" src="<?php echo esc_attr( $item['image'] ); ?>" >
		<img loading="lazy" alt="" class="theme-screenshot-img" srcset="<?php echo esc_attr( $item['image'] ); ?> 1x, <?php echo esc_attr( $item['imagex2'] ); ?> 2x" src="<?php echo esc_attr( $item['image'] ); ?>" >
	</div>
	<div class="notice update-message notice-success notice-alt"></div>
	<div class="notice update-message notice-warning notice-alt"></div>
	<div class="notice update-message notice-error notice-alt"></div>
	<?php if ( ! $item['is_supported'] ) : ?>
	<div class="notice update-message notice-error notice-alt"><p><?php printf( esc_html__( 'This template requires Mailster version %s or above. Please update first.', 'mailster' ), '<strong>' . $item['requires'] . '</strong>' ); ?></p></div>
	<?php endif; ?>
	<?php if ( $item['update_available'] ) : ?>
	<div class="update-message notice inline notice-warning notice-alt theme-has-update">
		<p><?php esc_html_e( 'New version available.', 'mailster' ); ?>
		<?php if ( $item['download_url'] ) : ?>
		<a class="button-link update" data-width="800" data-height="80%" href="<?php echo esc_url( $item['download_url'] ); ?>"><?php esc_html_e( 'Update now', 'mailster' ); ?></a>
		<?php elseif ( $item['download'] ) : ?>
			<?php
			$url = add_query_arg(
				array(
					'action'   => 'mailster_template_endpoint',
					'slug'     => $slug,
					'url'      => rawurlencode( $item['download'] ),
					'_wpnonce' => wp_create_nonce( 'mailster_download_template_' . $slug ),
				),
				admin_url( 'admin-ajax.php' )
			);
			?>
		<a class="button-link request-update popup" data-width="800" data-height="80%" href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'Update now', 'mailster' ); ?></a>
		<?php endif; ?>
		</p>
	</div>
	<?php endif; ?>
	<span class="more-details"><?php esc_html_e( 'Details & Preview', 'mailster' ); ?></span>
	<div class="theme-author"><?php printf( esc_html__( 'By %s', 'mailster' ), $item['author'] ); ?></div>
	<div class="theme-id-container">
		<h3 class="theme-name">
			<?php echo esc_html( $item['name'] ); ?>
			<?php if ( $item['is_active'] ) : ?>
			<span class="theme-badge theme-default-badge"><?php esc_html_e( 'Active', 'mailster' ); ?></a>
			<?php elseif ( $item['installed'] ) : ?>
			<span class="theme-badge theme-installed-badge"><?php esc_html_e( 'Installed', 'mailster' ); ?></a>
			<?php endif; ?>
		</h3>
		<div class="theme-actions">
			<a class="button button-primary deactivate" href="<?php echo wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . $item['wpslug'], 'deactivate-plugin_' . $item['wpslug'] ); ?>" aria-label="<?php esc_attr_e( 'Deactivate', 'mailster' ); ?>"><?php esc_html_e( 'Deactivate', 'mailster' ); ?></a>
			<a class="button button-primary activate" href="<?php echo wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $item['wpslug'], 'activate-plugin_' . $item['wpslug'] ); ?>" aria-label="<?php esc_attr_e( 'Activate', 'mailster' ); ?>"><?php esc_html_e( 'Activate', 'mailster' ); ?></a>
			<?php if ( $item['wpslug'] && current_user_can( 'install_plugins' ) && current_user_can( 'update_plugins' ) ) : ?>
			<a class="button button-primary install" href="<?php echo wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . dirname( $item['wpslug'] ) . '&mailster-addon' ), 'install-plugin_' . dirname( $item['wpslug'] ) ); ?>"><?php esc_html_e( 'Install & Activate', 'mailster' ); ?></a>
			<?php elseif ( $item['download_url'] ) : ?>
			<a class="button button-primary download" data-width="800" data-height="80%" href="<?php echo esc_url( $item['download_url'] ); ?>"><?php esc_html_e( 'Download', 'mailster' ); ?></a>
			<?php endif; ?>
			<?php if ( $item['price'] ) : ?>
			<a class="button button-primary buy external" data-width="800" data-height="80%" href="<?php echo esc_url( $item['purchase_url'] ); ?>"><?php esc_html_e( 'Buy Add on', 'mailster' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</div>
