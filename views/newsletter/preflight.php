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

<button type="button" class="button mailster_preflight" title="<?php esc_attr_e( 'check your spam score', 'mailster' ); ?> (beta)">Preflight</button>


<div id="mailster_preflight_wrap" style="display:none;">
	<div class="mailster-preflight
	<?php
	if ( $terms_agreed ) {
		echo 'preflight-terms-agreed';}
	?>
	">
		<div class="preflight-bar">
			<ul class="prefligth-emailheader">
				<li><label><?php esc_html_e( 'From', 'mailster' ); ?>:</label><span class="preflight-from"></span></li>
				<li><label><?php esc_html_e( 'Subject', 'mailster' ); ?>:</label><span class="preflight-subject"></span></li>
				<li><label><?php esc_html_e( 'To', 'mailster' ); ?>:</label><span class="preflight-to" title="<?php esc_attr_e( 'Search for subscribers...', 'mailster' ); ?>"><input type="hidden" value="<?php echo (int) $subscriber_id; ?>" id="subscriber_id"><input type="text" class="preflight-subscriber button button-small" value="" placeholder="<?php echo esc_attr( $to ); ?>"></span></li>
			</ul>
			<div class="prefligth-images">
				<a class="button preflight-toggle-images mailster-icon"></a>
			</div>
			<div class="prefligth-resize button-group">
				<a class="button preflight-switch mailster-icon preflight-switch-desktop" data-dimensions='{"w":"100%","h":"100%"}'></a>
				<a class="button preflight-switch mailster-icon preflight-switch-mobile" data-dimensions='{"w":320,"h":640}'></a>
				<a class="button preflight-switch mailster-icon preflight-switch-landscape" data-dimensions='{"w":640,"h":320}'></a>
			</div>
			<ul class="prefligth-run">
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
			<div class="preflight-tos-box">
				<h3><?php esc_html_e( 'Preflight Terms of Service.', 'mailster' ); ?></h3>
				<p><?php esc_html_e( 'To use the Preflight service Mailster needs to send your campaign to our third party service. The email which is sent is the same as sent as test. All personalized information included but not limited to links, images, attachments etc. are sent via your current selected delivery method.', 'mailster' ); ?></p>
				<p><?php esc_html_e( 'We keep the right to track anonymously usage data.', 'mailster' ); ?></p>
				<p><label><input type="checkbox" id="preflight-agree-checkbox"><?php esc_html_e( 'I\'ve read the Terms of Service and agree.', 'mailster' ); ?></label></p>
				<?php submit_button( 'Agree', 'primary', 'preflight-agree' ); ?>
			</div>
			<div class="preflight-score">
				<h3 class="preflight-status"><?php esc_html_e( 'Ready for Preflight!', 'mailster' ); ?></h3>
			</div>
			<div class="preflight-results-wrap">
				<div class="preflight-results">
					<details id="preflight-message">
						<summary data-count="10">Message</summary>
						<div class="preflight-body">
							<details id="preflight-subject">
								<summary data-count="10">Subject</summary>
								<div class="preflight-result"></div>
							</details>
							<details id="preflight-email">
								<summary data-count="10">Email</summary>
								<div class="preflight-result"></div>
							</details>
						</div>
					</details>
					<details id="preflight-links">
						<summary data-count="10">Links</summary>
						<div class="preflight-result"></div>
					</details>
					<details id="preflight-images">
						<summary data-count="10">Images</summary>
						<div class="preflight-result"></div>
					</details>
					<details id="preflight-spam_report">
						<summary data-count="10">Spam Report</summary>
						<div class="preflight-result"></div>
					</details>
					<details id="preflight-authentication">
						<summary data-count="10">Authentication</summary>
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
