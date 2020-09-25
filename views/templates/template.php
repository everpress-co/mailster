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
?>
<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" tabindex="0" data-slug="<?php echo esc_attr( $item['slug'] ); ?>" data-item='<?php echo json_encode( $item ); ?>'>
	<div class="theme-screenshot">
		<img loading="lazy" src="<?php echo esc_attr( $item['image'] ); ?>" alt="" class="theme-screenshot-bg">
		<?php if ( $item['index'] ) : ?>
		<iframe src="<?php echo esc_url( $item['index'] ); ?>" class="theme-screenshot-iframe" scrolling="no" allowTransparency="true" frameBorder="0" sandbox="allow-presentation" loading="lazy"></iframe>
		<?php else : ?>
		<img loading="lazy" src="<?php echo esc_attr( $item['image'] ); ?>" alt="" class="theme-screenshot-img">
		<?php endif; ?>
	</div>
	<?php if ( $item['installed'] ) : ?>
	<div class="notice notice-success notice-alt theme-is-installed"><p><?php esc_html_e( 'Installed', 'mailster' ); ?></p></div>
	<?php endif; ?>
	<?php if ( $item['update_available'] ) : ?>
	<div class="update-message notice inline notice-warning notice-alt theme-has-update"><p><?php esc_html_e( 'New version available.', 'mailster' ); ?> <button class="button-link" type="button"><?php esc_html_e( 'Update now', 'mailster' ); ?></button></p></div>
	<?php endif; ?>
	<span class="more-details">Details &amp; Preview</span>
	<div class="theme-author"><?php printf( esc_html__( 'By %s', 'mailster' ), $item['author'] ); ?></div>
	<div class="theme-id-container">
		<h3 class="theme-name">
			<?php echo esc_html( $item['name'] ); ?>
			<?php if ( $item['is_default'] ) : ?>
			<span class="theme-default-badge"><?php esc_html_e( 'Current', 'mailster' ); ?></a>
			<?php endif; ?>
			</h3>
		<div class="theme-actions">
			<?php if ( $item['installed'] ) : ?>
			<a class="button button-primary create-campaign" href="<?php echo admin_url( 'post-new.php?post_type=newsletter&template=' . $item['slug'] ); ?>" aria-label="<?php esc_attr_e( 'Create Campaign', 'mailster' ); ?>"><?php esc_html_e( 'Create Campaign', 'mailster' ); ?></a>
			<?php endif; ?>
			<?php if ( $item['download_url'] ) : ?>
			<a class="button button-primary download popup updating-message" data-width="800" data-height="80%" href="<?php echo esc_url( $item['download_url'] ); ?>"><?php esc_html_e( 'Download', 'mailster' ); ?></a>
			<?php endif; ?>
			<?php if ( $item['price'] ) : ?>
			<a class="button button-primary buy" href="<?php echo esc_url( $item['purchase_url'] ); ?>" onclick="window.open('<?php echo esc_url( $item['purchase_url'] ); ?>','MyWindow','width=800,height=1000,toolbar=no,menubar=no,location=no,status=no,scrollbars=no,resizable=no');return false;"><?php esc_html_e( 'Buy Template', 'mailster' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</div>
