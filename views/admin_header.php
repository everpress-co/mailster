<?php

global $submenu, $parent_file, $submenu_file, $plugin_page, $pagenow;

$slug = 'edit.php?post_type=newsletter';

if ( ! isset( $submenu[ $slug ] ) ) {
	return;
}
$tabs = array();
foreach ( $submenu[ $slug ] as $i => $sub_item ) {

	// Check user can access page.
	if ( ! current_user_can( $sub_item[1] ) ) {
		continue;
	}
	if ( in_array( $sub_item[1], array( 'mailster_dashboard', 'mailster_tests', 'mailster_manage_templates', 'mailster_manage_addons', 'mailster_manage_subscribers' ) ) ) {
		continue;
	}
	if ( $i === 10 ) {
		$sub_item[0] = esc_html__( 'New', 'mailster' );
	}
	if ( $i !== 5 ) {
		// continue;
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
	} elseif ( ! $is_autoresponder && ( $submenu_file === $sub_item[2] || $plugin_page === $sub_item[2] ) && $pagenow !== 'post_new.php' ) {
		$tab['is_active'] = true;
	}
	$tabs[] = $tab;
}
$tabs = apply_filters( 'mailster_admin_header_tabs', $tabs );

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
		<button type="button" role="tab" aria-selected="false" aria-controls="activity-panel-help" id="mailster-admin-help" class="panel-tab">
			<svg version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g fill="none"><path d="M0,0h24v24h-24Z"></path><path stroke="#545454" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.56,7.119c1.92,2.97 1.919,6.794 0.001,9.763"></path><path stroke="#545454" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.5476,8.45245c1.95926,1.95926 1.95926,5.13585 0,7.09511c-1.95926,1.95926 -5.13585,1.95926 -7.09511,0c-1.95926,-1.95926 -1.95926,-5.13585 0,-7.09511c1.95926,-1.95926 5.13585,-1.95926 7.09511,0"></path><path stroke="#545454" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.321,7.551l2.584,-3.139c0.376,-0.457 1.064,-0.49 1.483,-0.072l1.273,1.273c0.419,0.419 0.385,1.107 -0.072,1.483l-3.139,2.584"></path><path stroke="#545454" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.679,16.449l-2.584,3.139c-0.376,0.457 -1.064,0.49 -1.483,0.072l-1.273,-1.273c-0.419,-0.419 -0.385,-1.107 0.072,-1.483l3.139,-2.584"></path><path stroke="#545454" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.551,9.679l-3.14,-2.584c-0.457,-0.376 -0.49,-1.064 -0.072,-1.483l1.273,-1.273c0.419,-0.419 1.107,-0.385 1.483,0.072l2.584,3.139"></path><path stroke="#545454" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.449,14.321l3.139,2.584c0.457,0.376 0.49,1.064 0.072,1.483l-1.273,1.273c-0.419,0.419 -1.107,0.385 -1.483,-0.072l-2.584,-3.139"></path><path stroke="#545454" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.882,19.561c-2.969,1.918 -6.794,1.919 -9.763,-0.001"></path><path stroke="#545454" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.439,7.118c-1.918,2.969 -1.919,6.793 0.001,9.763"></path><path stroke="#545454" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.881,4.44c-2.97,-1.92 -6.794,-1.919 -9.763,-0.001"></path></g></svg>
			Help
		</button>
	</div>
</div>
