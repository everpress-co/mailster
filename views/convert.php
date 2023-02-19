<div class="wrap" id="mailster-convert">
<?php
$useremail = mailster()->email();
?>

	<div class="convert_form_wrap step-1 loading">
			<div class="convert-form-info"></div>
		<form class="convert_form" action="" method="POST">
			<h1>Mailster License Migration</h1>
			<h2>You're about to convert your Mailster license to Freemius and we need your consent.</h2>
			<p class="howto">Freemius is our partner for delivering updates to your Mailster newsletter plugin. By submitting the form you agree to transfer the data to Freemius to process future updates.</p>
			<p class="howto">Please enter the email you like to use with your account. If you already have a Freemius account you should use that email address.</p>
			<p class="error-msg">&nbsp;</p>

			<p>
				<input type="email" class="widefat email align-center" name="email" value="<?php echo esc_attr( $useremail ); ?>" placeholder="<?php echo esc_attr( $useremail ); ?>">
			</p>
			<input type="submit" class="button button-hero button-primary dashboard-convert" value="<?php esc_attr_e( 'Migrate to Freemius now', 'mailster' ); ?>">
			<div class="howto">
				<a href="<?php echo mailster_url( 'https://kb.mailster.co/where-is-my-purchasecode' ); ?>" class="external"><?php esc_html_e( 'What is this all about?', 'mailster' ); ?></a>
			</div>
		</form>
		<form class="registration_complete">
			<h1>Mailster License Migration completed!</h1>
			<div class="registration_complete_check"></div>
			<div class="registration_complete_text"><?php esc_html_e( 'All Set!', 'mailster' ); ?></div>
			<p class="howto">Your license has successfully got converted to Freemius</p>
			<p class="howto"><a href="<?php echo admin_url( 'edit.php?post_type=newsletter&page=mailster-account' ); ?>" class="button button-primary">Your Freemius Account</a> <a href="<?php echo admin_url( 'admin.php?page=mailster_dashboard' ); ?>" class="button button-secondary">Mailster Dashboard</a></p>
			<p class="howto"><a href="<?php echo mailster_url( 'https://kb.mailster.co/working-with-subscriber-based-auto-responders/' ); ?>" class="external"><?php esc_html_e( 'Create a welcome message for new subscribers', 'mailster' ); ?></a></p>
			<p class="howto"><a href="<?php echo mailster_url( 'https://kb.mailster.co/how-can-i-customize-the-notification-template/' ); ?>" class="external"><?php esc_html_e( 'Customize the notification template', 'mailster' ); ?></a></p>
			<p class="howto"><a href="<?php echo mailster_url( 'https://kb.mailster.co/working-with-action-based-auto-responders/' ); ?>" class="external"><?php esc_html_e( 'Send your latest posts automatically', 'mailster' ); ?></a></p>
			<p class="howto"><a href="<?php echo mailster_url( 'https://kb.mailster.co/creating-a-series-in-mailster/' ); ?>" class="external"><?php esc_html_e( 'Creating a series or drip campaign', 'mailster' ); ?></a></p>
			<p class="howto"><a href="<?php echo mailster_url( 'https://kb.mailster.co/segmentation-in-mailster/' ); ?>" class="external"><?php esc_html_e( 'Learn more about segmentation', 'mailster' ); ?></a></p>
		</form>
	</div>

</div>
