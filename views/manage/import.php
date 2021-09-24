
<div class="import-wrap">
<h2><?php echo esc_html__( 'Where do you like to import your Subscribers from?', 'mailster' ); ?></h2>

<?php
$methods = array(
	'file'      => esc_html__( 'Upload a CSV file', 'mailster' ),
	'paste'     => esc_html__( 'Paste the data from your spreadsheet app', 'mailster' ),
	'wordpress' => esc_html__( 'Import from your WordPress Users', 'mailster' ),
	'mailpoet'  => esc_html__( 'Import from MailPoet', 'mailster' ),
	'mailchimp' => esc_html__( 'Import from MailChimp', 'mailster' ),
);

$methods = apply_filters( 'mailster_import_methods', $methods );

if ( ! current_user_can( 'mailster_import_wordpress_users' ) ) {
	unset( $methods['wordpress'] );
}
?>

<?php foreach ( $methods as $id => $name ) : ?>
	<details id="manage-import-<?php echo esc_attr( $id ); ?>">
		<summary><?php echo esc_html( $name ); ?></summary>
		<div class="manage-import-body">
			<?php do_action( 'mailster_import_method', $id ); ?>
			<?php do_action( 'mailster_import_method_' . $id ); ?>
			<?php
			if ( file_exists( MAILSTER_DIR . 'views/manage/method-' . $id . '.php' ) ) :
				include MAILSTER_DIR . 'views/manage/method-' . $id . '.php';
			endif;
			?>
		</div>
	</details>
<?php endforeach; ?>
</div>
<div class="import-result"></div>
