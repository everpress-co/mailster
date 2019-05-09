<div class="wrap mailster-welcome-wrap">

	<h1><?php printf( esc_html__( 'Welcome to %s', 'mailster' ), 'Mailster 2.4' ); ?></h1>

	<div class="about-text">
		<?php esc_html_e( 'Easily create, send and track your Newsletter Campaigns.', 'mailster' ); ?><br>
	</div>

	<div class="mailster-badge"><?php printf( esc_html__( 'Version %s', 'mailster' ), MAILSTER_VERSION ); ?></div>

	<div class="nav-tab-wrapper">
		<a href="<?php echo admin_url( 'admin.php?page=mailster_welcome' ) ?>" class="nav-tab nav-tab-active"><?php esc_html_e( 'What\'s New', 'mailster' ); ?></a>
		<?php if ( current_user_can( 'mailster_manage_templates' ) ) : ?>
		<a href="<?php echo admin_url( 'edit.php?post_type=newsletter&page=mailster_templates&more' ) ?>" class="nav-tab"><?php esc_html_e( 'Templates', 'mailster' ); ?></a>
		<?php endif; ?>
		<?php if ( current_user_can( 'mailster_manage_addons' ) ) : ?>
		<a href="<?php echo admin_url( 'edit.php?post_type=newsletter&page=mailster_addons' ) ?>" class="nav-tab"><?php esc_html_e( 'Add Ons', 'mailster' ); ?></a>
		<?php endif; ?>

	</div>

		<div class="feature-section one-col main-feature">
			<h2>Create beautiful Campaigns.</h2>
			<p class="about-text">You have now access to over 900.000 Photos from 129.000 photographers directly in the campaign editor.<br>Create visual stunning emails with free photos form Unsplash.</p>
			<div class="promo-video">
				<iframe id="ytplayer" type="text/html" src="https://www.youtube.com/embed/ZG9V0sSbwvo?autoplay=1&showinfo=0&modestbranding=1&controls=0&rel=0" frameborder="0"></iframe>
			</div>
			<p class="about-text"><a href="<?php echo admin_url( 'post-new.php?post_type=newsletter' ) ?>" class="button button-primary button-hero">Create a new Campaign</a></p>
			<p><a href="https://kb.mailster.co/unsplash/" target="_blank">Learn more</a> or <a href="https://unsplash.com/" target="_blank">visit Unsplash.com</a></p>
		</div>

		<div class="feature-section two-col">
			<div class="col">
				<div class="media-container">
					<img src="https://static.mailster.co/welcome/relative_tags.gif" width="505" height="284">
				</div>
				<h3>Random Dynamic Posts</h3>
				<p>You can now add a random post in your email campaigns.</p>
				<div class="return-to-dashboard"><a href="https://kb.mailster.co/random-dynamic-posts/" target="_blank">Learn more</a></div>
			</div>
			<div class="col">
				<div class="media-container">
					<img src="https://static.mailster.co/welcome/time_frame.jpg" width="505" height="284">
				</div>
				<h3>RSS Email Campaigns</h3>
				<p>Create real RSS Email Campaigns with sources from third party web sites.</p>
				<div class="return-to-dashboard"><a href="https://kb.mailster.co/rss-email-campaigns/" target="_blank">Learn more</a></div>
			</div>
		</div>

		<div class="feature-section two-col">
			<div class="col">
				<div class="media-container">
					<div class="feature-fresh-ui">
						<div class="campaign-progress"><span class="bar" style="width: 100%;"></span></div>
					</div>
				</div>
				<h3>Fresh UI</h3>
				<p>We have tweaked the look of some UI elements.</p>
				<div class="return-to-dashboard"></div>
			</div>
			<div class="col">
				<div class="media-container">
					<img src="https://static.mailster.co/welcome/subscriber_query.jpg" width="505" height="284">
				</div>
				<h3>Subscriber Query Class</h3>
				<p>Similar to the WP_Query you can now simple query your subscribers.</p>
				<div class="return-to-dashboard"></div>
			</div>
		</div>

		<div class="changelog">
			<h2>Further Improvements</h2>

			<div class="feature-section under-the-hood three-col">
				<div class="col">
					<h4>Preserved stats from deleted Subscribers.</h4>
					<p>Mailster will keep analytics data from deleted subscribers.</p>
				</div>
				<div class="col">
					<h4>Form Shortcode attributes.</h4>
					<p>Customize each form with attributes.</p>
				</div>
				<div class="col">
					<h4>Campaign-Subscriber related Tags.</h4>
					<p>Tags now have more power and can be defined fore each individual subscriber.</p>
				</div>
			</div>
			<div class="feature-section under-the-hood three-col">
				<div class="col">
					<h4>Improved Export/Import</h4>
					<p>Mailster handle imported campaigns now better and does some sanitation checks during this process.</p>
				</div>
				<div class="col">
					<h4>Translation Dashboard Info</h4>
					<p>If you use Mailster in a different Language than English you can now quickly update translations from the Dashboard.</p>
				</div>
			</div>

		</div>
		<div class="clear"></div>

		<div class="return-to-dashboard">
			<a href="<?php echo admin_url( 'admin.php?page=mailster_dashboard' ) ?>">Back to Dashboard</a>
		</div>

<div class="clear"></div>

<div id="ajax-response"></div>
<br class="clear">
</div>
