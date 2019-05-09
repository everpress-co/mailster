<?php

$now = time();

$sent = $this->get_sent( $post->ID );

$email = mailster( 'subscribers' )->get( 500 )->email;

echo '<pre>' . print_r( $email, true ) . '</pre>';

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
				<li class="alignright"><a class="button button-primary">Run Test</a></li>
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

				<h3>This is a text for the summery</h3>
			</div>

			<div class="preflight-results-wrap">
				<div class="preflight-results">
					<details>
						<summary class="loading" data-count="10">Spam Report</summary>
						<div class="body">
							<p class="description">SpamAssasin likes you</p>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent fringilla mollis tortor a scelerisque. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam pharetra massa sed orci pulvinar porta vehicula vitae elit. Suspendisse sed augue leo. Duis laoreet cursus sem in vulputate. Curabitur ullamcorper tincidunt mi nec malesuada. In convallis elit id ligula pulvinar tincidunt. In in nibh metus, ultricies viverra ante. In semper fringilla sem non interdum. Nulla at urna id urna bibendum vestibulum. Nunc aliquam turpis euismod est egestas dignissim condimentum mi vestibulum. Nam blandit dolor eget sapien tempor porttitor. Nam sollicitudin pharetra erat ac laoreet. Ut ac diam purus. Sed nec felis sed justo pretium faucibus sed non tellus.</p>
						</div>
					</details>
					<details>
						<summary class="is-warning" data-count="10">Authentication</summary>
						<div class="body">
							<p class="description">SpamAssasin likes you</p>
							<details open>
								<summary class="is-warning" data-count="10">SPF</summary>
								<p>SPF summary</p>
							</details>
							<details open>
								<summary class="is-error" data-count="10">SenderID</summary>
								<p>SenderID summary</p>
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
					<details>
						<summary class="is-success" data-count="10">Links</summary>
						<div class="body">

							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent fringilla mollis tortor a scelerisque. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam pharetra massa sed orci pulvinar porta vehicula vitae elit. Suspendisse sed augue leo. Duis laoreet cursus sem in vulputate. Curabitur ullamcorper tincidunt mi nec malesuada. In convallis elit id ligula pulvinar tincidunt. In in nibh metus, ultricies viverra ante. In semper fringilla sem non interdum. Nulla at urna id urna bibendum vestibulum. Nunc aliquam turpis euismod est egestas dignissim condimentum mi vestibulum. Nam blandit dolor eget sapien tempor porttitor. Nam sollicitudin pharetra erat ac laoreet. Ut ac diam purus. Sed nec felis sed justo pretium faucibus sed non tellus.</p>
						</div>
					</details>
					<details>
						<summary class="is-success" data-count="10">Blacklist</summary>
						<div class="body">
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent fringilla mollis tortor a scelerisque. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam pharetra massa sed orci pulvinar porta vehicula vitae elit. Suspendisse sed augue leo. Duis laoreet cursus sem in vulputate. Curabitur ullamcorper tincidunt mi nec malesuada. In convallis elit id ligula pulvinar tincidunt. In in nibh metus, ultricies viverra ante. In semper fringilla sem non interdum. Nulla at urna id urna bibendum vestibulum. Nunc aliquam turpis euismod est egestas dignissim condimentum mi vestibulum. Nam blandit dolor eget sapien tempor porttitor. Nam sollicitudin pharetra erat ac laoreet. Ut ac diam purus. Sed nec felis sed justo pretium faucibus sed non tellus.</p>
						</div>
					</details>

				</div>
			</div>
		</div>
	</div>

</div>
