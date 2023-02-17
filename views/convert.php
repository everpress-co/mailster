<div class="wrap" id="mailster-convert">

<?php wp_nonce_field( 'mailster_nonce', 'mailster_nonce', false ); ?>

<?php

$timeformat = mailster( 'helper' )->timeformat();
$timeoffset = mailster( 'helper' )->gmt_offset( true );

$is_verified        = mailster()->is_verified();
$active_plugins     = get_option( 'active_plugins', array() );
$active_pluginslugs = preg_replace( '/^(.*)\/.*$/', '$1', $active_plugins );
$plugins            = array_keys( get_plugins() );
$pluginslugs        = preg_replace( '/^(.*)\/.*$/', '$1', $plugins );

?>

<h1>asdads</h1>
<h2>Thanks for using Mailster</h2>


<table class="wp-list-table widefat fixed striped table-view-list posts">
	<thead>
	<tr>
		<th scope="col" id="total" class="manage-column column-total">Total</th>
		<th scope="col" id="total" class="manage-column column-total">Total</th>
		<th scope="col" id="total" class="manage-column column-total">Total</th>
	</tr>
	</thead>

	<tbody id="the-list">
			<tr><th>Cost</th><td>$0,-</td><td>$0,-</td></tr>
			<tr><th>Auto Updates</th><td>til 23. Jun 2023</td><td>til 23. Jun 2023</td></tr>
			<tr><th>Support</th><td>until 23. Jun 2023</td><td>until 23. Jun 2023</td></tr>
			<tr><th>asdad</th><td>asda</td><td>asda</td></tr>
	</tbody>


</table>

</div>
