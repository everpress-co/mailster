<?php

global $submenu, $parent_file, $submenu_file, $plugin_page, $pagenow;

// Vars.
$parent_slug = 'edit.php?post_type=newsletter';

// Generate array of navigation items.
$tabs = array();
if ( isset( $submenu[ $parent_slug ] ) ) {

	foreach ( $submenu[ $parent_slug ] as $i => $sub_item ) {

		// Check user can access page.
		if ( ! current_user_can( $sub_item[1] ) ) {
			continue;
		}


		if ( in_array( $sub_item[1], array( 'mailster_dashboard', 'mailster_tests', 'mailster_manage_templates', 'mailster_manage_addons' ) ) ) {
			continue;
		}

		// Ignore "Add New".
		if ( $i === 10 ) {
			continue;
		}

		// Define tab.
		$tab = array(
			'text' => $sub_item[0],
			'url'  => $sub_item[2],
		);


		// Convert submenu slug "test" to "$parent_slug&page=test".
		if ( ! strpos( $sub_item[2], '.php' ) ) {
			$tab['url'] = add_query_arg( array( 'page' => $sub_item[2] ), $parent_slug );
		}

		// Detect active state.
		if ( $submenu_file === $sub_item[2] || $plugin_page === $sub_item[2] ) {
			$tab['is_active'] = true;
		}
		// Special case for "Add New" page.
		if ( $i === 10 && $submenu_file === 'edit.php?post_type=newsletter' ) {
			$tab['is_active'] = true;
		}
		$tabs[] = $tab;
	}
}

?>
<div class="mailster-admin-toolbar">
	<a href="<?php echo admin_url( 'admin.php?page=mailster_dashboard' ); ?>" class="mailster-logo" title="<?php esc_attr_e( sprintf( 'Mailster %s', MAILSTER_VERSION ) ); ?>">
	<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 692.8 611.9" style="enable-background:new 0 0 692.8 611.9;" xml:space="preserve">
<path class="st0" fill="#2BB2E8" d="M471.1,24.3L346.4,176.7L221.7,24.3H0v568.1h194V273.7l152.4,207.8l152.4-207.8v318.6h194V24.3H471.1z"/>
</svg></a>

	<?php
	foreach ( $tabs as $tab ) {
		printf( '<a class="mailster-tab%s" href="%s">%s</a>', ! empty( $tab['is_active'] ) ? ' is-active' : '', esc_url( $tab['url'] ), esc_html( $tab['text'] ) );
	}
	?>
	<div role="tablist" aria-orientation="horizontal" class="panel-tabs">
		<button type="button" role="tab" aria-selected="false" aria-controls="activity-panel-help" id="activity-panel-tab-help" class="components-button panel-tab">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M12 4.75a7.25 7.25 0 100 14.5 7.25 7.25 0 000-14.5zM3.25 12a8.75 8.75 0 1117.5 0 8.75 8.75 0 01-17.5 0zM12 8.75a1.5 1.5 0 01.167 2.99c-.465.052-.917.44-.917 1.01V14h1.5v-.845A3 3 0 109 10.25h1.5a1.5 1.5 0 011.5-1.5zM11.25 15v1.5h1.5V15h-1.5z"></path></svg>
			Help
		</button></div>
</div>
