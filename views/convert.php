<div class="wrap" id="mailster-convert">
<?php
$timeformat = mailster( 'helper' )->timeformat();
$timeoffset = mailster( 'helper' )->gmt_offset( true );
$useremail  = mailster()->email();
$useremail  = 'buyer@revaxarts.com';


?>

	<div class="convert_form_wrap step-1 loading">
			<div class="convert-form-info">
				<span class="step-1"><?php esc_html_e( 'Verifying Purchase Code', 'mailster' ); ?>&hellip;</span>
				<span class="step-2"><?php esc_html_e( 'Finishing Registration', 'mailster' ); ?>&hellip;</span>
			</div>
		<form class="convert_form" action="" method="POST">
			<h1>Mailster License Migration</h1>
			<h2>You're about to convert your Mailster account to a Freemius account and we need your consent.</h2>
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
			<div class="registration_complete_check"></div>
			<div class="registration_complete_text"><?php esc_html_e( 'All Set!', 'mailster' ); ?></div>
		</form>
	</div>

</div>
