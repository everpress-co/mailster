
<div class="import-wrap">
<h2><?php echo esc_html__( 'Where do you like to import your subscribers from?', 'mailster' ); ?></h2>

<?php
$methods = array(
	'upload'    => esc_html__( 'Upload a CSV file', 'mailster' ),
	'paste'     => esc_html__( 'Paste the data from your spreadsheet app', 'mailster' ),
	'wordpress' => esc_html__( 'Import from your WordPress Users', 'mailster' ),
	'mailpoet'  => esc_html__( 'Import from MailPoet', 'mailster' ),
	'mailchimp' => esc_html__( 'Import from MailChimp', 'mailster' ),
);

$methods = apply_filters( 'mailster_import_methods', $methods );

if ( ! current_user_can( 'mailster_import_wordpress_users' ) ) {
	unset( $methods['wordpress'] );
}

$user_settings = wp_parse_args(	get_user_option( 'mailster_import_settings' ),
	array(
		'method' => null,
	)
);
$current       = isset( $_GET['method'] ) ? $_GET['method'] : $user_settings['method'];
?>
<?php foreach ( $methods as $id => $name ) : ?>
	<details id="manage-import-<?php echo esc_attr( $id ); ?>" <?php __checked_selected_helper( $id, $current, true, 'open' ); ?>>
		<summary><?php echo esc_html( $name ); ?></summary>
		<div class="manage-import-body">
			<?php do_action( 'mailster_import_method', $id ); ?>
			<?php do_action( 'mailster_import_method_' . $id ); ?>
		</div>
	</details>
<?php endforeach; ?>
</div>
<div class="import-result"></div>
