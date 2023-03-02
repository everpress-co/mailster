<?php

global $submenu, $submenu_file, $plugin_page, $pagenow;

$slug = 'edit.php?post_type=newsletter';

if ( ! isset( $submenu[ $slug ] ) ) {
	return;
}
$current_screen = get_current_screen();

if ( $current_screen->is_block_editor() ) {
	return;
}

$tabs    = array();
$current = null;
foreach ( $submenu[ $slug ] as $i => $sub_item ) {

	// Check user can access page.
	if ( ! current_user_can( $sub_item[1] ) ) {
		continue;
	}
	if ( in_array( $sub_item[1], array( 'mailster_dashboard', 'mailster_manage_templates', 'mailster_manage_addons', 'mailster_manage_subscribers' ) ) ) {
		continue;
	}

	if ( in_array( $sub_item[2], array( 'mailster_dashboard', 'mailster_tests' ) ) ) {
		continue;
	}

	if ( $i === 10 ) {
		$sub_item[0] = esc_html__( 'New', 'mailster' );
	}

	$tab = array(
		'text' => $sub_item[0],
		'url'  => $sub_item[2],
	);

	if ( ! strpos( $sub_item[2], '.php' ) ) {
		$tab['url'] = add_query_arg( array( 'page' => $sub_item[2] ), $slug );
	}

	$is_autoresponder = isset( $_GET['post_status'] ) && $_GET['post_status'] == 'autoresponder';

	if ( $is_autoresponder && $sub_item[1] == 'mailster_edit_autoresponders' ) {
		$tab['is_active'] = true;
		$current          = $tab;
	} elseif ( ! $is_autoresponder && ( $submenu_file === $sub_item[2] || $plugin_page === $sub_item[2] ) && $pagenow !== 'post_new.php' ) {
		$tab['is_active'] = true;
		$current          = $tab;
	}
	$tabs[] = $tab;
}

$tabs = apply_filters( 'mailster_admin_header_tabs', $tabs );

?>
<div id="mailster-admin-toolbar">
	<a href="<?php echo admin_url( 'admin.php?page=mailster_dashboard' ); ?>" class="mailster-logo" title="<?php echo esc_attr( sprintf( 'Mailster %s', MAILSTER_VERSION ) ); ?>">
		<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 692.8 611.9" xml:space="preserve"><path class="st0" fill="#2BB2E8" d="M471.1,24.3L346.4,176.7L221.7,24.3H0v568.1h194V273.7l152.4,207.8l152.4-207.8v318.6h194V24.3H471.1z"/></svg>
		<span class="screen-reader-text">Mailster Newsletter Plugin</span>
	</a>
	<?php
	foreach ( $tabs as $tab ) {
		printf( '<a class="mailster-tab%s" href="%s">%s</a>', ! empty( $tab['is_active'] ) ? ' is-active' : '', esc_url( $tab['url'] ), esc_html( $tab['text'] ) );
	}
	?>
	<div role="tablist" aria-orientation="horizontal" class="panel-tabs">
		<button type="button" role="tab" aria-selected="false" aria-controls="activity-panel-help" id="mailster-admin-help" class="panel-tab" href="<?php echo mailster_url( 'https://mailster.co/support' ); ?>">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M12 4.75a7.25 7.25 0 100 14.5 7.25 7.25 0 000-14.5zM3.25 12a8.75 8.75 0 1117.5 0 8.75 8.75 0 01-17.5 0zM12 8.75a1.5 1.5 0 01.167 2.99c-.465.052-.917.44-.917 1.01V14h1.5v-.845A3 3 0 109 10.25h1.5a1.5 1.5 0 011.5-1.5zM11.25 15v1.5h1.5V15h-1.5z"fill="#757575"></path></svg>
			<?php esc_html_e( 'Help', 'mailster' ); ?>
		</button>
	</div>
</div>
