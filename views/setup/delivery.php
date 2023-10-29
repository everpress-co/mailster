<div class="mailster-setup-step-body">

<form class="mailster-setup-step-form">

<p><?php esc_html_e( 'Choose how Mailster should send your campaigns. It\'s recommend to go with a dedicate ESP to prevent rejections and server blocking.', 'mailster' ); ?></p>

<?php $method = mailster_option( 'deliverymethod', 'simple' ); ?>

<div id="deliverynav" class="nav-tab-wrapper hide-if-no-js">
	<a class="nav-tab<?php echo 'simple' == $method ? ' nav-tab-active' : ''; ?>" href="#simple"><?php esc_html_e( 'Simple', 'mailster' ); ?></a>
	<a class="nav-tab<?php echo 'smtp' == $method ? ' nav-tab-active' : ''; ?>" href="#smtp">SMTP</a>
	<a class="nav-tab<?php echo 'gmail' == $method ? ' nav-tab-active' : ''; ?>" href="#gmail">Gmail</a>
	<a class="nav-tab<?php echo 'amazonses' == $method ? ' nav-tab-active' : ''; ?>" href="#amazonses">AmazonSES</a>
	<a class="nav-tab<?php echo 'sparkpost' == $method ? ' nav-tab-active' : ''; ?>" href="#sparkpost">SparkPost</a>
	<a class="nav-tab<?php echo 'mailgun' == $method ? ' nav-tab-active' : ''; ?>" href="#mailgun">Mailgun</a>
	<a class="nav-tab<?php echo 'sendgrid' == $method ? ' nav-tab-active' : ''; ?>" href="#sendgrid">SendGrid</a>
	<a class="nav-tab<?php echo 'mailersend' == $method ? ' nav-tab-active' : ''; ?>" href="#mailersend">MailerSend</a>
	<a class="nav-tab<?php echo 'mailjet' == $method ? ' nav-tab-active' : ''; ?>" href="#mailjet">MailJet</a>
</div>

<input type="hidden" name="mailster_options[deliverymethod]" id="deliverymethod" value="<?php echo esc_attr( $method ); ?>" class="regular-text">

<div class="deliverytab" id="deliverytab-simple"<?php echo 'simple' == $method ? ' style="display:block"' : ''; ?>>
	<?php do_action( 'mailster_deliverymethod_tab_simple' ); ?>
</div>
<div class="deliverytab" id="deliverytab-smtp"<?php echo 'smtp' == $method ? ' style="display:block"' : ''; ?>>
	<?php do_action( 'mailster_deliverymethod_tab_smtp' ); ?>
</div>
<div class="deliverytab" id="deliverytab-gmail"<?php echo 'gmail' == $method ? ' style="display:block"' : ''; ?>>
	<?php
	if ( in_array( 'mailster-gmail', $active_pluginslugs ) ) :
		do_action( 'mailster_deliverymethod_tab_gmail' );
	else :
		?>
	<div class="wp-plugin">
	<a href="https://wordpress.org/plugins/mailster-gmail/" class="external">
		<img src="https://ps.w.org/mailster-gmail/assets/banner-772x250.png?v=<?php echo MAILSTER_VERSION; ?>" width="772" height="250">
	</a>
	</div>
	<a class="button button-primary quick-install" data-plugin="mailster-gmail/mailster-gmail.php" data-method="gmail">
		<?php echo in_array( 'mailster-gmail', $pluginslugs ) ? esc_html__( 'Activate Plugin', 'mailster' ) : sprintf( esc_html__( 'Install %s Extension', 'mailster' ), 'Gmail' ); ?>
	</a>
	<?php endif; ?>
</div>
<div class="deliverytab" id="deliverytab-amazonses"<?php echo 'amazonses' == $method ? ' style="display:block"' : ''; ?>>
	<?php
	if ( in_array( 'mailster-amazonses', $active_pluginslugs ) ) :
		do_action( 'mailster_deliverymethod_tab_amazonses' );
	else :
		?>
	<div class="wp-plugin">
	<a href="https://wordpress.org/plugins/mailster-amazonses/" class="external">
		<img src="https://ps.w.org/mailster-amazonses/assets/banner-772x250.png?v=<?php echo MAILSTER_VERSION; ?>" width="772" height="250">
	</a>
	</div>
	<a class="button button-primary quick-install" data-plugin="mailster-amazonses/mailster-amazonses.php" data-method="amazonses">
		<?php echo in_array( 'mailster-amazonses', $pluginslugs ) ? esc_html__( 'Activate Plugin', 'mailster' ) : sprintf( esc_html__( 'Install %s Extension', 'mailster' ), 'Amazon SES' ); ?>
	</a>
	<?php endif; ?>
</div>
<div class="deliverytab" id="deliverytab-sparkpost"<?php echo 'sparkpost' == $method ? ' style="display:block"' : ''; ?>>
	<?php
	if ( in_array( 'mailster-sparkpost', $active_pluginslugs ) ) :
		do_action( 'mailster_deliverymethod_tab_sparkpost' );
	else :
		?>
	<div class="wp-plugin">
	<a href="https://wordpress.org/plugins/mailster-sparkpost/" class="external">
		<img src="https://ps.w.org/mailster-sparkpost/assets/banner-772x250.png?v=<?php echo MAILSTER_VERSION; ?>" width="772" height="250">
	</a>
	</div>
	<a class="button button-primary quick-install" data-plugin="mailster-sparkpost/mailster-sparkpost.php" data-method="sparkpost">
		<?php echo in_array( 'mailster-sparkpost', $pluginslugs ) ? esc_html__( 'Activate Plugin', 'mailster' ) : sprintf( esc_html__( 'Install %s Extension', 'mailster' ), 'SparkPost' ); ?>
	</a>
	<?php endif; ?>
</div>
<div class="deliverytab" id="deliverytab-mailgun"<?php echo 'mailgun' == $method ? ' style="display:block"' : ''; ?>>
	<?php
	if ( in_array( 'mailster-mailgun', $active_pluginslugs ) ) :
		do_action( 'mailster_deliverymethod_tab_mailgun' );
	else :
		?>
	<div class="wp-plugin">
	<a href="https://wordpress.org/plugins/mailster-mailgun/" class="external">
		<img src="https://ps.w.org/mailster-mailgun/assets/banner-772x250.png?v=<?php echo MAILSTER_VERSION; ?>" width="772" height="250">
	</a>
	</div>
	<a class="button button-primary quick-install" data-plugin="mailster-mailgun/mailster-mailgun.php" data-method="mailgun">
		<?php echo in_array( 'mailster-mailgun', $pluginslugs ) ? esc_html__( 'Activate Plugin', 'mailster' ) : sprintf( esc_html__( 'Install %s Extension', 'mailster' ), 'Mailgun' ); ?>
	</a>
	<?php endif; ?>
</div>
<div class="deliverytab" id="deliverytab-sendgrid"<?php echo 'sendgrid' == $method ? ' style="display:block"' : ''; ?>>
	<?php
	if ( in_array( 'mailster-sendgrid', $active_pluginslugs ) ) :
		do_action( 'mailster_deliverymethod_tab_sendgrid' );
	else :
		?>
	<div class="wp-plugin">
	<a href="https://wordpress.org/plugins/mailster-sendgrid/" class="external">
		<img src="https://ps.w.org/mailster-sendgrid/assets/banner-772x250.png?v=<?php echo MAILSTER_VERSION; ?>" width="772" height="250">
	</a>
	</div>
	<a class="button button-primary quick-install" data-plugin="mailster-sendgrid/mailster-sendgrid.php" data-method="sendgrid">
		<?php echo in_array( 'mailster-sendgrid', $pluginslugs ) ? esc_html__( 'Activate Plugin', 'mailster' ) : sprintf( esc_html__( 'Install %s Extension', 'mailster' ), 'SendGrid' ); ?>
	</a>
	<?php endif; ?>
</div>
<div class="deliverytab" id="deliverytab-mailersend"<?php echo 'mailersend' == $method ? ' style="display:block"' : ''; ?>>
	<?php
	if ( in_array( 'mailster-mailersend', $active_pluginslugs ) ) :
		do_action( 'mailster_deliverymethod_tab_mailersend' );
	else :
		?>
	<div class="wp-plugin">
	<a href="https://wordpress.org/plugins/mailster-mailersend/" class="external">
		<img src="https://ps.w.org/mailster-mailersend/assets/banner-772x250.png?v=<?php echo MAILSTER_VERSION; ?>" width="772" height="250">
	</a>
	</div>
	<a class="button button-primary quick-install" data-plugin="mailster-mailersend/mailster-mailersend.php" data-method="mailersend">
		<?php echo in_array( 'mailster-mailersend', $pluginslugs ) ? esc_html__( 'Activate Plugin', 'mailster' ) : sprintf( esc_html__( 'Install %s Extension', 'mailster' ), 'MailerSend' ); ?>
	</a>
	<?php endif; ?>
</div>
<div class="deliverytab" id="deliverytab-mailjet"<?php echo 'mailjet' == $method ? ' style="display:block"' : ''; ?>>
	<?php
	if ( in_array( 'mailster-mailjet', $active_pluginslugs ) ) :
		do_action( 'mailster_deliverymethod_tab_mailjet' );
	else :
		?>
	<div class="wp-plugin">
	<a href="https://wordpress.org/plugins/mailster-mailjet/" class="external">
		<img src="https://ps.w.org/mailster-mailjet/assets/banner-772x250.png?v=<?php echo MAILSTER_VERSION; ?>" width="772" height="250">
	</a>
	</div>
	<a class="button button-primary quick-install" data-plugin="mailster-mailjet/mailster-mailjet.php" data-method="mailjet">
		<?php echo in_array( 'mailster-mailjet', $pluginslugs ) ? esc_html__( 'Activate Plugin', 'mailster' ) : sprintf( esc_html__( 'Install %s Extension', 'mailster' ), 'MailJet' ); ?>
	</a>
	<?php endif; ?>
</div>

</form>

</div>

<div class="mailster-setup-step-buttons" >

	<span class="alignleft status"></span>
	<i class="spinner"></i>

	<a class="button button-large skip-step" href="#privacy"><?php esc_html_e( 'Skip this Step', 'mailster' ); ?></a>
	<a class="button button-large button-primary next-step delivery-next-step" href="#privacy"><?php esc_html_e( 'Next Step', 'mailster' ); ?></a>

</div>
