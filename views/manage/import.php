
<div class="import-wrap">
<h2><?php echo esc_html__( 'Where do you like to import your subscribers from?', 'mailster' ); ?></h2>

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
<h4><?php esc_html_e( 'It\'s highly recommend cleaning your lists before importing them to.', 'mailster' ); ?></h4>
<section>

<p><?php esc_html_e( 'Lists can contain many invalid email address after some time and you may get into troubles sending to too many of them.', 'mailster' ); ?> <?php esc_html_e( 'We recommend to use a professional service upfront to clean out your list before your import them to Mailster.', 'mailster' ); ?> <?php esc_html_e( 'Please check out these services listed below for more info.', 'mailster' ); ?></p>

<p><a href="https://www.zerobounce.net/" class="external ">Zerobounce</a> | <a href="https://neverbounce.com/" class="external ">Neverbounce</a> | <a href="https://www.xverify.com/" class="external ">Xverify</a> | <a href="https://clearout.io/" class="external ">Clearout</a> | <a href="https://debounce.io/" class="external ">Debounce</a> </p>
</section>
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
