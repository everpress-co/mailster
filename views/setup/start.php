<h2 class="section-title"><?php esc_html_e( 'Welcome to Mailster', 'mailster' ); ?></h2>

<div class="mailster-setup-step-body">

<form class="mailster-setup-step-form">

<p><?php esc_html_e( 'Before you can start sending your campaigns Mailster needs some info to get started.', 'mailster' ); ?></p>

<p><?php esc_html_e( 'This wizard helps you to setup Mailster. All options available can be found later in the settings. You can always skip each step and adjust your settings later if you\'re not sure.', 'mailster' ); ?></p>

<p><?php printf( esc_html__( 'The wizard is separated into %d different steps:', 'mailster' ), 4 ); ?></p>

<dl>
	<dt><?php esc_html_e( 'Basic Information', 'mailster' ); ?></dt>
	<dd><?php esc_html_e( 'Mailster needs some essential informations like your personal information and also some legal stuff.', 'mailster' ); ?></dd>
</dl>
<dl>
	<dt><?php esc_html_e( 'Newsletter Homepage Setup', 'mailster' ); ?></dt>
	<dd><?php esc_html_e( 'This is where your subscribers signup, manage or cancel their subscriptions.', 'mailster' ); ?></dd>
</dl>
<dl>
	<dt><?php esc_html_e( 'Delivery Options', 'mailster' ); ?></dt>
	<dd><?php esc_html_e( 'How Mailster should delivery you campaigns.', 'mailster' ); ?></dd>
</dl>
<dl>
	<dt><?php esc_html_e( 'Privacy', 'mailster' ); ?></dt>
	<dd><?php esc_html_e( 'Mailster takes the privacy of your subscribers information seriously. Define which information Mailster should save.', 'mailster' ); ?></dd>
</dl>

<p><a class="button button-hero button-primary next-step" href="#basics"><?php esc_html_e( 'Start Wizard', 'mailster' ); ?></a> <?php esc_html_e( 'or', 'mailster' ); ?> <a href="admin.php?page=mailster_dashboard&mailster_setup_complete=<?php echo wp_create_nonce( 'mailster_setup_complete' ); ?>"><?php esc_html_e( 'skip it', 'mailster' ); ?></a></p>

</form>

</div>

<div class="mailster-setup-step-buttons" hidden>

	<span class="alignleft status"></span>
	<i class="spinner"></i>

	<a class="button button-primary next-step" href="#basics"><?php esc_html_e( 'Start Wizard', 'mailster' ); ?></a>

</div>
