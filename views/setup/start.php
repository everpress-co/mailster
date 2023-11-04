<?php if ( mailster()->is_trial() ) : ?>
<h2 class="section-title"><?php esc_html_e( 'Thanks for Testing Mailster!', 'mailster' ); ?></h2>
<?php else : ?>
<h2 class="section-title"><?php esc_html_e( 'Welcome to Mailster', 'mailster' ); ?></h2>
<?php endif; ?>
<div class="mailster-setup-step-body">

<form class="mailster-setup-step-form">

<p><?php esc_html_e( 'Before you can start sending your campaigns Mailster needs some info to get started.', 'mailster' ); ?></p>

<p><?php esc_html_e( 'This wizard helps you to setup Mailster. All options available can be found later in the settings. You can always skip each step and adjust your settings later if you\'re not sure.', 'mailster' ); ?></p>
<?php if ( mailster()->is_trial() ) : ?>
	<p><?php esc_html_e( 'You are currently using the trial version of Mailster. You can use all features of Mailster for 14 days. After that you need to purchase a license to continue using Mailster.', 'mailster' ); ?></p>

<?php endif; ?>
<p><a class="button button-hero button-primary next-step" href="#basics"><?php esc_html_e( 'Start Wizard', 'mailster' ); ?></a> <?php esc_html_e( 'or', 'mailster' ); ?> <a href="admin.php?page=mailster_dashboard&mailster_setup_complete=<?php echo wp_create_nonce( 'mailster_setup_complete' ); ?>"><?php esc_html_e( 'skip it', 'mailster' ); ?></a></p>

</form>

</div>

<div class="mailster-setup-step-buttons" hidden>

	<span class="alignleft status"></span>
	<i class="spinner"></i>

	<a class="button button-primary next-step" href="#basics"><?php esc_html_e( 'Start Wizard', 'mailster' ); ?></a>

</div>
