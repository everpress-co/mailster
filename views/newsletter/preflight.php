<?php

$now = time();

$sent         = $this->get_sent( $post->ID );
$current_user = wp_get_current_user();

$terms_agreed = get_user_meta( $current_user->ID, '_mailster_preflight_agreed', true );

if ( $subscriber = mailster( 'subscribers' )->get_by_mail( $current_user->user_email, true ) ) {

	$fullname      = $subscriber->fullname;
	$email         = $subscriber->email;
	$subscriber_id = $subscriber->ID;

} else {

	$firstname     = $current_user->user_firstname ? $current_user->user_firstname : $current_user->display_name;
	$fullname      = mailster_option( 'name_order' ) ? trim( $current_user->user_lastname . ' ' . $firstname ) : trim( $firstname . ' ' . $current_user->user_lastname );
	$email         = $current_user->user_email;
	$subscriber_id = 0;

}

$to = $fullname ? $fullname . ' <' . $email . '>' : $email;

?>
<div id="mailster_preflight_wrap" style="display:none;">
	<div class="mailster-preflight<?php echo ( $terms_agreed ) ? ' preflight-terms-agreed' : ''; ?>">
		<div class="preflight-bar">
			<ul class="preflight-emailheader">
				<li><label><?php esc_html_e( 'From', 'mailster' ); ?>:</label><span class="preflight-from"></span></li>
				<li><label><?php esc_html_e( 'Subject', 'mailster' ); ?>:</label><span class="preflight-subject"></span></li>
				<li><label><?php esc_html_e( 'To', 'mailster' ); ?>:</label><span class="preflight-to"></span><a class="change-receiver mailster-icon" title="<?php esc_attr_e( 'Change the user in the preview.', 'mailster' ); ?>"></a><span class="preflight-to-input" title="<?php esc_attr_e( 'Search for subscribers...', 'mailster' ); ?>"><input type="hidden" value="<?php echo (int) $subscriber_id; ?>" id="subscriber_id"><input type="text" class="preflight-subscriber" value="" placeholder="<?php echo esc_attr( $to ); ?>"></span></li>
			</ul>
			<div class="preflight-images button-group">
				<a class="button preflight-toggle-images mailster-icon active" title="<?php esc_attr_e( 'Toggle Images', 'mailster' ); ?>"></a>
				<a class="button preflight-toggle-structure mailster-icon" title="<?php esc_attr_e( 'Toggle Structure', 'mailster' ); ?>"></a>
			</div>
			<div class="preflight-resize button-group">
				<a class="button preflight-switch mailster-icon preflight-switch-desktop active" data-dimensions='{"w":"100%","h":"100%"}'></a>
				<a class="button preflight-switch mailster-icon preflight-switch-mobile" data-dimensions='{"w":320,"h":640}'></a>
				<a class="button preflight-switch mailster-icon preflight-switch-landscape" data-dimensions='{"w":640,"h":320}'></a>
			</div>
			<ul class="preflight-run">
				<li class="alignright"><span class="spinner" id="preflight-ajax-loading"></span><button class="button button-primary preflight-run"><?php esc_html_e( 'Preflight Campaign', 'mailster' ); ?></button></li>
			</ul>
		</div>
		<div class="device-wrap">
			<div class="device desktop">
				<div class="desktop-body">
					<div class="preview-body">
						<iframe class="mailster-preview-iframe desktop" src="" width="100%" scrolling="auto" frameborder="0" data-no-lazy=""></iframe>
					</div>
				</div>
			</div>
			<div class="device-notice"><?php esc_html_e( 'Your email may look different on mobile devices.', 'mailster' ); ?></div>
		</div>
		<div class="score-wrap">
			<div class="score-message"></div>
			<div class="preflight-tos-box">

				<?php if ( mailster()->is_verified() ) : ?>
					<h3><?php esc_html_e( 'Preflight Terms of Service.', 'mailster' ); ?></h3>
					<?php $terms = file_get_contents( MAILSTER_DIR . 'licensing/Preflight.txt' ); ?>
					<?php echo wpautop( $terms, false ); ?>
					<p><label><input type="checkbox" id="preflight-agree-checkbox"><?php esc_html_e( 'I\'ve read the Terms of Service and agree.', 'mailster' ); ?></label></p>
					<?php submit_button( 'Agree', 'primary', 'preflight-agree' ); ?>

				<?php else : ?>

					<h3><?php esc_html_e( 'Please register the plugin first!', 'mailster' ); ?></h3>
					<p><?php esc_html_e( 'To use the preflight service you have to register the Mailster plugin on the dashboard', 'mailster' ); ?></p>
					<a href="<?php echo admin_url( 'admin.php?page=mailster_dashboard' ); ?>" class="button button-primary"><?php esc_html_e( 'Go to Dashboard', 'mailster' ); ?></a>

				<?php endif; ?>

			</div>
			<div class="preflight-score">
				<h3 class="preflight-status"><?php esc_html_e( 'Ready for Preflight!', 'mailster' ); ?></h3>
			</div>
			<div class="preflight-results-wrap">
				<div class="preflight-results">
					<details id="preflight-message">
						<summary data-count="10"><?php esc_html_e( 'Message', 'mailster' ); ?></summary>
						<div class="preflight-body">
							<details id="preflight-subject">
								<summary data-count="10"><?php esc_html_e( 'Subject', 'mailster' ); ?></summary>
								<div class="preflight-result"></div>
							</details>
							<details id="preflight-email">
								<summary data-count="10"><?php esc_html_e( 'Email', 'mailster' ); ?></summary>
								<div class="preflight-result"></div>
							</details>
						</div>
					</details>
					<details id="preflight-links">
						<summary data-count="10"><?php esc_html_e( 'Links', 'mailster' ); ?></summary>
						<div class="preflight-result"></div>
					</details>
					<details id="preflight-images">
						<summary data-count="10"><?php esc_html_e( 'Images', 'mailster' ); ?></summary>
						<div class="preflight-result"></div>
					</details>
					<details id="preflight-spam_report">
						<summary data-count="10"><?php esc_html_e( 'Spam Report', 'mailster' ); ?></summary>
						<div class="preflight-result"></div>
					</details>
					<details id="preflight-authentication">
						<summary data-count="10"><?php esc_html_e( 'Authentication', 'mailster' ); ?></summary>
						<div class="preflight-body">
							<details id="preflight-spf">
								<summary data-count="10">SPF</summary>
								<div class="preflight-result"></div>
							</details>
							<details id="preflight-senderid">
								<summary data-count="10">Sender ID</summary>
								<div class="preflight-result"></div>
							</details>
							<details id="preflight-dkim">
								<summary data-count="10">DKIM</summary>
								<div class="preflight-result"></div>
							</details>
							<details id="preflight-dmarc">
								<summary data-count="10">DMARC</summary>
								<div class="preflight-result"></div>
							</details>
							<details id="preflight-rdns">
								<summary data-count="10">RDNS</summary>
								<div class="preflight-result"></div>
							</details>
							<details id="preflight-mx">
								<summary data-count="10">MX</summary>
								<div class="preflight-result"></div>
							</details>
							<details id="preflight-a">
								<summary data-count="10">A</summary>
								<div class="preflight-result"></div>
							</details>
						</div>
					</details>
					<details id="preflight-blacklist">
						<summary data-count="10">Blacklist</summary>
						<div class="preflight-result"></div>
					</details>

				</div>
			</div>
		</div>
	</div>

</div>
