<div class="template" tabindex="0" data-slug="<?php echo esc_attr( $item['slug'] ); ?>" data-item='<?php echo json_encode( $item ); ?>'>
	<div class="template-screenshot">
		<img loading="_lazy" src="<?php echo esc_attr( $item['image'] ); ?>" alt="" class="template-screenshot-bg">
		<img loading="_lazy" src="<?php echo esc_attr( $item['image'] ); ?>" alt="" class="template-screenshot-img">
	</div>
	<?php if ( $item['installed'] ) : ?>
	<div class="notice notice-success notice-alt"><p>Installed</p></div>
	<?php endif; ?>
	<?php if ( $item['update_available'] ) : ?>
	<div class="notice notice-warning notice-alt"><p>Update available</p></div>
	<?php endif; ?>
	<span class="more-details">Details &amp; Preview</span>
	<div class="template-author"><?php printf( esc_html__( 'By %s', 'mailster' ), $item['author'] ); ?></div>
	<div class="template-id-container">
		<h3 class="template-name"><?php echo esc_html( $item['name'] ); ?></h3>
		<div class="template-actions">
			<a class="button button-primary activate" href="<?php echo esc_attr( $item['url'] ); ?>" aria-label="Activate Twenty Twenty">Activate</a>
			<?php
		$item_id = (int) preg_replace( '#[^0-9]#', '', $item['url'] );
		$url     = add_query_arg(
			array(
				'license'                   => 'regular',
				'open_purchase_for_item_id' => $item_id,
				'purchasable'               => 'source',
			),
			$item['url']
		);

		error_log( print_r($url, true) );
			 ?>
			<a class="button load-customize" href="<?php echo esc_attr( $url  ); ?>" onclick="window.open('<?php echo esc_attr( $url  ); ?>','MyWindow','width=800,height=500,toolbar=no,menubar=no,location=no,status=no,scrollbars=no,resizable=no,left=320,top=200');return false;">Buy</a>
		</div>
	</div>
</div>
