<?php

$now = time();

$sent = $this->get_sent( $post->ID );

?>

<button type="button" class="button mailster_preflight" title="<?php esc_attr_e( 'check your spam score', 'mailster' );?> (beta)">Preflight</button>


<div id="mailster_preflight_wrap" style="display:none;">
	<div class="mailster-preflight">
		<div class="preflight-bar">
			<ul class="prefligth-emailheader">
				<li><label><?php esc_attr_e( 'From', 'mailster' );?>:</label><span class="preflight-from">Xaver</span></li>
				<li><label><?php esc_attr_e( 'Subject', 'mailster' );?>:</label><span class="preflight-subject">This is the Subject</span></li>
				<li><label><?php esc_attr_e( 'To', 'mailster' );?>:</label><span class="preflight-to">John &lt;john.doe@example.com&gt;</span></li>
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
				<li class="alignright"><span class="spinner" id="preflight-ajax-loading"></span><button class="button button-primary preflight-run"><?php esc_html_e( 'Preflight Campaign', 'mailster' );?></button></li>
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
			<div class="device-notice"><?php esc_html_e( 'Your email may look different on mobile devices.', 'mailster' );?></div>
		</div>
		<div class="score-wrap">
			<div class="preflight-score">
				<h3 class="preflight-status"><?php esc_html_e( 'Ready for Preflight!', 'mailster' );?></h3>
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
