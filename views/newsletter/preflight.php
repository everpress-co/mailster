<?php

$now = time();

$sent = $this->get_sent( $post->ID );

?>

<button type="button" class="button mailster_preflight" title="<?php esc_html_e( 'check your spam score', 'mailster' );?> (beta)">Preflight</button>


<div id="mailster_preflight_wrap" style="display:none;">
	<div class="mailster-preflight">
		<div class="preflight-bar">
			<ul>
				<li>Subject: Queued Campaign<br>Preheader: Preheader text</li>
				<li><label><input type="checkbox"> Disable Images</label></li>
				<li><label><input type="checkbox"> Disable Images</label></li>
				<li>Last Test <?php echo date( 'r' ) ?></li>
				<li class="aligncenter"><a class="button preflight-switch" data-dimensions='{"w":"100%","h":"100%"}'>Full</a></li>
				<li class="aligncenter"><a class="button preflight-switch" data-dimensions='{"w":700,"h":"90%"}'>Desktop</a></li>
				<li class="aligncenter"><a class="button preflight-switch" data-dimensions='{"w":320,"h":640}'>Mobile</a></li>
				<li class="aligncenter"><a class="button preflight-switch" data-dimensions='{"w":640,"h":320}'>Landscape</a></li>
				<li class="alignright"><span class="spinner" id="preflight-ajax-loading"></span><button class="button button-primary preflight-run">Run Test</button></li>
			</ul>
		</div>
		<div class="device-wrap">
			<div class="device desktop">
				<div class="desktop-header">
					<div class="desktop-header-info"><u></u><i></i><i></i></div>
				</div>
				<div class="desktop-body">
					<div class="preview-body">
						<iframe class="mailster-preview-iframe desktop" src="" width="100%" scrolling="auto" frameborder="0" data-no-lazy=""></iframe>
					</div>
				</div>
			</div>
		</div>
		<div class="score-wrap">
			<div class="preflight-score">

				<div class="score">83</div>

				<h3 class="preflight-status">This is a text for the summery</h3>
			</div>

			<div class="preflight-results-wrap">
				<div class="preflight-results">
					<details id="preflight-spam_report">
						<summary data-count="10">Spam Report</summary>
						<div class="body"></div>
					</details>
					<details id="preflight-authentication">
						<summary data-count="10">Authentication</summary>
						<div class="body">
							<details id="preflight-spf">
								<summary data-count="10">SPF</summary>
								<div class="body"></div>
							</details>
							<details id="preflight-senderid">
								<summary data-count="10">Sender ID</summary>
								<div class="body"></div>
							</details>
							<details id="preflight-dkim">
								<summary data-count="10">DKIM</summary>
								<div class="body"></div>
							</details>
							<details id="preflight-dmarc">
								<summary data-count="10">DMARC</summary>
								<div class="body"></div>
							</details>
							<details id="preflight-rdns">
								<summary data-count="10">RDNS</summary>
								<div class="body"></div>
							</details>
							<details id="preflight-mx">
								<summary data-count="10">MX</summary>
								<div class="body"></div>
							</details>
							<details id="preflight-a">
								<summary data-count="10">A</summary>
								<div class="body"></div>
							</details>
						</div>
					</details>
					<details>
						<summary class="is-error" data-count="10">Message</summary>
						<div class="body">
							<h3>Message</h3>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent fringilla mollis tortor a scelerisque. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam pharetra massa sed orci pulvinar porta vehicula vitae elit. Suspendisse sed augue leo. Duis laoreet cursus sem in vulputate. Curabitur ullamcorper tincidunt mi nec malesuada. In convallis elit id ligula pulvinar tincidunt. In in nibh metus, ultricies viverra ante. In semper fringilla sem non interdum. Nulla at urna id urna bibendum vestibulum. Nunc aliquam turpis euismod est egestas dignissim condimentum mi vestibulum. Nam blandit dolor eget sapien tempor porttitor. Nam sollicitudin pharetra erat ac laoreet. Ut ac diam purus. Sed nec felis sed justo pretium faucibus sed non tellus.</p>
						</div>
					</details>
					<details id="preflight-links">
						<summary data-count="10">Links</summary>
						<div class="body"></div>
					</details>
					<details id="preflight-images">
						<summary data-count="10">Images</summary>
						<div class="body"></div>
					</details>
					<details id="preflight-blacklist">
						<summary data-count="10">Blacklist</summary>
						<div class="body"></div>
					</details>

				</div>
			</div>
		</div>
	</div>

</div>
