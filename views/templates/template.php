<?php
$classes   = array( 'theme' );
$classes[] = 'theme-' . $item['slug'];
if ( $item['is_default'] ) {
	$classes[] = 'active';
}
if ( $item['installed'] ) {
	$classes[] = 'installed';
}
if ( $item['update_available'] ) {
	$classes[] = 'update-available';
}
if ( $item['envato_item_id'] ) {
	$classes[] = 'envato-item';
}
if ( $item['gumroad_url'] ) {
	$classes[] = 'gumroad-item';
}
if ( $item['purchased'] ) {
	$classes[] = 'is-purchased';
}
?>
<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" tabindex="0" data-slug="<?php echo esc_attr( $item['slug'] ); ?>" data-item='<?php echo esc_attr( json_encode( $item ) ); ?>'>
	<span class="spinner"></span>
	<div class="theme-screenshot">
		<img loading="lazy" alt="" class="theme-screenshot-bg" srcset="<?php echo esc_attr( $item['image'] ); ?> 1x, <?php echo esc_attr( $item['imagex2'] ); ?> 2x" src="<?php echo esc_attr( $item['image'] ); ?>" >
		<?php if ( $item['index'] ) : ?>
		<iframe src="<?php echo esc_url( add_query_arg( array( 'nocache' => time() ), $item['index'] ) ); ?>" class="theme-screenshot-iframe" scrolling="no" allowTransparency="true" frameBorder="0" sandbox="allow-presentation allow-scripts" loading="lazy"></iframe>
		<?php else : ?>
		<img loading="lazy" alt="" class="theme-screenshot-img" srcset="<?php echo esc_attr( $item['image'] ); ?> 1x, <?php echo esc_attr( $item['imagex2'] ); ?> 2x" src="<?php echo esc_attr( $item['image'] ); ?>" >
		<?php endif; ?>
	</div>
	<div class="notice update-message notice-success notice-alt"></div>
	<div class="notice update-message notice-error notice-alt"></div>
	<?php if ( $item['installed'] ) : ?>
	<div class="notice notice-success notice-alt theme-is-installed"><p><?php esc_html_e( 'Installed', 'mailster' ); ?></p></div>
	<?php endif; ?>
	<?php if ( $item['update_available'] ) : ?>
	<div class="update-message notice inline notice-warning notice-alt theme-has-update"><p><?php esc_html_e( 'New version available.', 'mailster' ); ?> <a class="button-link update" href="<?php echo esc_attr( $item['download_url'] ); ?>"><?php esc_html_e( 'Update now', 'mailster' ); ?></a></p></div>
	<?php endif; ?>
	<span class="more-details"><?php esc_html_e( 'Details & Preview', 'mailster' ); ?></span>
	<div class="theme-author"><?php printf( esc_html__( 'By %s', 'mailster' ), $item['author'] ); ?></div>
	<div class="theme-id-container">
		<h3 class="theme-name">
			<?php echo esc_html( $item['name'] ); ?>
			<?php if ( $item['is_default'] ) : ?>
			<span class="theme-default-badge"><?php esc_html_e( 'Current', 'mailster' ); ?></a>
			<?php endif; ?>
			</h3>
		<div class="theme-actions">
			<?php if ( $item['ID'] ) : ?>
			<a class="button button-small" href="<?php echo esc_url( 'https://mailster.dev/wp-admin/post.php?post=' . $item['ID'] . '&action=edit' ); ?>" target="_blank"><?php esc_html_e( 'Edit', 'mailster' ); ?></a>
			<?php endif; ?>
			<?php if ( $item['installed'] ) : ?>
			<a class="button button-primary create-campaign" href="<?php echo admin_url( 'post-new.php?post_type=newsletter&template=' . $item['slug'] ); ?>" aria-label="<?php esc_attr_e( 'Create Campaign', 'mailster' ); ?>"><?php esc_html_e( 'Create Campaign', 'mailster' ); ?></a>
			<?php endif; ?>
			<?php if ( $item['gumroad_url'] ) : ?>
			<a class="button button-primary buy-gumroad" href="<?php echo esc_url( $item['gumroad_url'] ); ?>?wanted=true" aria-label="<?php esc_attr_e( 'Buy via Gumroad', 'mailster' ); ?>"><?php esc_html_e( 'Buy via Gumroad', 'mailster' ); ?></a>
			<?php endif; ?>
			<?php if ( $item['download_url'] ) : ?>
			<a class="button button-primary download" data-width="800" data-height="80%" href="<?php echo esc_url( $item['download_url'] ); ?>"><?php esc_html_e( 'Download', 'mailster' ); ?></a>
			<?php elseif ( $item['download'] ) : ?>
				<?php
				$url = add_query_arg(
					array(
						'action'   => 'mailster_template_endpoint',
						'slug'     => $item['slug'],
						'url'      => rawurlencode( $item['download'] ),
						'_wpnonce' => wp_create_nonce( 'mailster_download_template_' . $item['slug'] ),
					),
					admin_url( 'admin-ajax.php' )
				);
				?>
			<a class="button button-primary request-download popup" data-width="800" data-height="80%" href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'Download', 'mailster' ); ?></a>
			<?php endif; ?>
			<?php if ( $item['price'] && ! $item['purchased'] ) : ?>
			<a class="button button-primary buy popup" data-width="800" data-height="80%" href="<?php echo esc_url( $item['purchase_url'] ); ?>"><?php esc_html_e( 'Buy Template', 'mailster' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</div>
