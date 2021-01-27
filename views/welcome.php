<div class="wrap mailster-welcome-wrap">

	<h1><?php printf( esc_html__( 'Welcome to %s', 'mailster' ), 'Mailster 3.0' ); ?></h1>

	<div class="about-text">
		<?php esc_html_e( 'Send Beautiful Email Newsletters in WordPress.', 'mailster' ); ?><br>
	</div>

	<div class="mailster-badge"><?php printf( esc_html__( 'Version %s', 'mailster' ), MAILSTER_VERSION ); ?></div>

	<div class="nav-tab-wrapper">
		<a href="<?php echo admin_url( 'admin.php?page=mailster_welcome' ); ?>" class="nav-tab nav-tab-active"><?php esc_html_e( 'What\'s New', 'mailster' ); ?></a>
		<?php if ( current_user_can( 'mailster_manage_templates' ) ) : ?>
		<a href="<?php echo admin_url( 'edit.php?post_type=newsletter&page=mailster_templates&more' ); ?>" class="nav-tab"><?php esc_html_e( 'Templates', 'mailster' ); ?></a>
		<?php endif; ?>
		<?php if ( current_user_can( 'mailster_manage_addons' ) ) : ?>
		<a href="<?php echo admin_url( 'edit.php?post_type=newsletter&page=mailster_addons' ); ?>" class="nav-tab"><?php esc_html_e( 'Add Ons', 'mailster' ); ?></a>
		<?php endif; ?>
	</div>

		<div class="feature-section one-col main-feature">
			<h2>Meet the new Mailster</h2>
			<p class="about-text">We have revamped the look of the plugin and brought a fresh new icons set.</p>
			<div class="promo-video">
				<iframe id="ytplayer" type="text/html" src="https://www.youtube.com/embed/ZG9V0sSbwvo?autoplay=1&showinfo=0&modestbranding=1&controls=0&rel=0&vq=hd720" frameborder="0"></iframe>
			</div>
			<p class="about-text"><a href="<?php echo admin_url( 'post-new.php?post_type=newsletter' ); ?>" class="button button-primary button-hero">Create a new Campaign</a></p>
			<p><a href="https://kb.mailster.co/unsplash/" target="_blank" rel="noopener">Learn more</a> or <a href="https://unsplash.com/" target="_blank" rel="noopener">visit Unsplash.com</a></p>
		</div>

		<div class="feature-section two-col">
			<div class="col">
				<div class="media-container">
				</div>
				<h3>New Icons</h3>
				<p>XXX</p>
				<div class="return-to-dashboard"><a href="https://kb.mailster.co/random-dynamic-posts/" target="_blank" rel="noopener">Learn more</a></div>
			</div>
			<div class="col">
				<div class="media-container">
				</div>
				<h3>Preflight your Email Campaigns.</h3>
				<p>XXX</p>
				<div class="return-to-dashboard"><a href="https://kb.mailster.co/rss-email-campaigns/" target="_blank" rel="noopener">Learn more</a></div>
			</div>
		</div>

		<div class="feature-section two-col">
			<div class="col">
				<div class="media-container">
				</div>
				<h3>Tags</h3>
				<p>You can now segment your subscribers even further with tags.</p>
				<div class="return-to-dashboard"><a href="https://kb.mailster.co/random-dynamic-posts/" target="_blank" rel="noopener">Learn more</a></div>
			</div>
			<div class="col">
				<div class="media-container">
				</div>
				<h3>Templates</h3>
				<p>We bring more supported email templates right into the plugin.</p>
				<div class="return-to-dashboard"><a href="https://kb.mailster.co/rss-email-campaigns/" target="_blank" rel="noopener">Learn more</a></div>
			</div>
		</div>

		<div class="changelog">
			<h2>Further Improvements</h2>

			<div class="feature-section under-the-hood three-col">
				<div class="col">
					<h4>Security</h4>
					<p>We help you prevent false signups out of the box.</p>
				</div>
				<div class="col">
					<h4>PHP 8 Support.</h4>
					<p>Mailster now supports PHP 8.</p>
				</div>
				<div class="col">
					<h4>Auto Cron Settings.</h4>
					<p>Mailster will calculate your sending rate automatically which increases the average throughput about 25%.</p>
				</div>
			<div class="feature-section under-the-hood three-col">
				<div class="col">
					<h4>Improved Table Structure.</h4>
					<p>Mailster 3.0 introduces a new table structure which speeds up the queue processing.</p>
				</div>
				<div class="col">
					<h4>PHP 8 Support.</h4>
					<p>Mailster now supports PHP 8.</p>
				</div>
				<div class="col">
					<h4>Action Hook Campaigns.</h4>
					<p>Tags now have more power and can be defined fore each individual subscriber.</p>
				</div>
			</div>

		</div>
		<div class="clear"></div>

		<div class="return-to-dashboard">
			<a href="<?php echo admin_url( 'admin.php?page=mailster_dashboard' ); ?>">Back to Dashboard</a>
		</div>

<div class="clear"></div>

<div id="ajax-response"></div>
<br class="clear">
</div>
