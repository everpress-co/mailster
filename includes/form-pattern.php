<?php

$patterns = apply_filters(
	'mailster_form_patterns',
	array(
		array(
			'title'       => __( 'A minimal basic form', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"css":{"general":".input, .submit-button{\n\theight:66px;\n}\n.mailster-wrapper{\n    margin:0 !important;\n}\n","tablet":"","mobile":""},"style":{"borderRadius":"0px"}} -->
<form method="post" action="" novalidate class="wp-block-mailster-form-wrapper mailster-block-form"><div class="mailster-block-form-inner"><!-- wp:mailster/field-email {"inline":true,"style":{"width":60}} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-inline mailster-wrapper-type-email" style="width:60%"><input name="email" type="email" aria-required="true" aria-label="Email" spellcheck="false" required class="input" placeholder=" "/><label class="mailster-label">Email</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"style":{"width":40,"borderRadius":"0px"}} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button mailster-wrapper-type-submit" style="width:40%"><input name="submit" type="submit" style="border-radius:0px" value="Subscribe" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></div></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		array(
			'title'       => __( 'Simple form', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"css":{"general":".input, .submit-button{\n\theight:66px;\n}\n.mailster-wrapper{\n    margin:0 !important;\n}\n","tablet":"","mobile":""},"style":{"borderRadius":"0px","borderWidth":"0px"}} -->
<form method="post" action="" novalidate class="wp-block-mailster-form-wrapper mailster-block-form"><div class="mailster-block-form-inner"><!-- wp:mailster/field-email {"inline":true,"style":{"width":60,"labelColor":"#58595b","inputColor":"#58595b","backgroundColor":"#ffffff"}} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-inline mailster-wrapper-type-email" style="width:60%"><input name="email" type="email" aria-required="true" aria-label="Email" spellcheck="false" required class="input" style="background-color:#ffffff" placeholder=" "/><label style="color:#58595b" class="mailster-label">Email</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"style":{"width":40,"borderRadius":"0px","backgroundColor":"#2bb3e7","inputColor":"#58595b","labelColor":"#58595b"}} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button mailster-wrapper-type-submit" style="width:40%"><input name="submit" type="submit" style="background-color:#2bb3e7;border-radius:0px" value="Subscribe" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></div></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		array(
			'title'       => __( 'One Liner', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"css":{"general":".input, .submit-button{\n\theight:66px;\n}\n.mailster-wrapper{\n    margin:0 !important;\n}\n","tablet":"","mobile":""},"style":{"borderRadius":"0px"}} -->
<form method="post" action="" novalidate class="wp-block-mailster-form-wrapper mailster-block-form"><div class="mailster-block-form-inner"><!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="has-text-align-center" id="hi-there">Hi there!</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Join our newsletter!</p>
<!-- /wp:paragraph -->

<!-- wp:mailster/field-email {"inline":true,"style":{"width":60}} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-inline mailster-wrapper-type-email" style="width:60%"><input name="email" type="email" aria-required="true" aria-label="Email" spellcheck="false" required class="input" placeholder=" "/><label class="mailster-label">Email</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"style":{"width":40,"borderRadius":"0px"}} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button mailster-wrapper-type-submit" style="width:40%"><input name="submit" type="submit" style="border-radius:0px" value="Subscribe" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></div></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		array(
			'title'         => __( 'With image', 'mailster' ),
			'description'   => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'viewportWidth' => 880,
			'content'       => '<!-- wp:mailster/form-wrapper {"background":{"opacity":100,"fixed":false,"repeat":false,"size":52,"image":"https://user-images.githubusercontent.com/881729/147969548-3c739442-85b8-48b2-abfc-e3d2db5c0cb8.png","position":{"x":"0.88","y":"0.02"}},"css":{"general":"h2{\n\tfont-size:2em;\n\tfont-family:sans-serif;\n\tmargin-top:0.6em;\n}\n.input{\n\tborder: 0;\n\tborder-bottom-width: 3px;\n\tborder-bottom-style: solid;\n}\n.submit-button{\n\tborder-style:solid;\n\ttext-transform: uppercase;\n}\n.mailster-block-form{\n    outline:3px solid;\n\toutline-offset:-15px;\n}\n.mailster-wrapper{\n\tmargin-top:2em;\n}\n.mailster-wrapper ~ .mailster-wrapper{\n\tmargin-top:inherit;\n}\n.mailster-wrapper-required label.mailster-label::after{\n    display:none\n}\n","tablet":"","mobile":""},"style":{"borderColor":"#1A1B1F","borderRadius":"0px","spacing":{"padding":{"top":"1.5em","right":"1.5em","bottom":"1.5em","left":"1.5em"}},"color":{"background":"#ffffff","text":"#363636"}},"backgroundColor":"#FFFFFF"} -->
<form method="post" action="" novalidate style="background-color:#ffffff;color:#363636;padding-top:1.5em;padding-right:1.5em;padding-bottom:1.5em;padding-left:1.5em" class="wp-block-mailster-form-wrapper mailster-block-form has-ffffff-background-color has-text-color has-background"><div class="mailster-block-form-inner"><!-- wp:heading {"style":{"typography":{"letterSpacing":"-0.1rem"}}} -->
<h2 id="here-is-your-20-discount" style="letter-spacing:-0.1rem">Here is your 20% discount.</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Join our email list and get a special 20% discount!</p>
<!-- /wp:paragraph -->

<!-- wp:mailster/field-email {"inline":true,"style":{"width":57}} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-inline" style="width:57%"><input name="email" type="email" aria-required="true" aria-label="Your Email address" spellcheck="false" required class="input" placeholder=" "/><label class="mailster-label">Your Email address</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"style":{"width":57,"backgroundColor":"#ffffff","borderColor":"#1A1B1F","borderWidth":"3px","color":"#1A1B1F"}} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button" style="width:57%"><input name="submit" type="submit" style="color:#1A1B1F;background-color:#ffffff;border-color:#1A1B1F;border-width:3px" value="Get 20% now" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></div></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		array(
			'title'         => __( 'neutral', 'mailster' ),
			'description'   => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'viewportWidth' => 880,
			'content'       => '<!-- wp:mailster/form-wrapper {"background":{"opacity":100,"scale":1,"fixed":false,"repeat":true,"size":62,"image":"https://user-images.githubusercontent.com/881729/148080670-359b09d6-2aa3-446e-a6d4-f8a8388297a8.jpg","position":{"x":"0.28","y":"0.26"}},"css":{"general":".mailster-block-form-inner{\n\tbackground-color:#fff;\n\tpadding:1em;\n}","tablet":"","mobile":""},"style":{"spacing":{"padding":{"top":"1em","right":"1em","bottom":"1em","left":"1em"}}},"className":"has-background"} -->
<form method="post" action="" novalidate style="padding-top:1em;padding-right:1em;padding-bottom:1em;padding-left:1em" class="wp-block-mailster-form-wrapper mailster-block-form has-background"><div class="mailster-block-form-inner"><!-- wp:heading {"textAlign":"center","align":"full"} -->
<h2 class="alignfull has-text-align-center" id="hi-john">Hi John!</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Please consider Subscribing to your Newsletter.</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":"27px"} -->
<div style="height:27px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:mailster/field-email {"style":{"width":100}} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required" style="width:100%"><label class="mailster-label">Email</label><input name="email" type="email" aria-required="true" aria-label="Email" spellcheck="false" required class="input" placeholder=" "/></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-firstname -->
<div class="wp-block-mailster-field-firstname mailster-wrapper"><label class="mailster-label">First Name</label><input name="firstname" type="text" aria-required="false" aria-label="First Name" spellcheck="false" class="input" placeholder=" "/></div>
<!-- /wp:mailster/field-firstname -->

<!-- wp:mailster/field-submit {"style":{"width":100}} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button" style="width:100%"><input name="submit" type="submit" value="Subscribe" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></div></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		array(
			'title'         => __( 'Simple', 'mailster' ),
			'description'   => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'viewportWidth' => 880,
			'content'       => '<!-- wp:mailster/form-wrapper {"style":{"spacing":{"padding":{"top":"2em","right":"2em","bottom":"2em","left":"2em"}},"color":{"gradient":"radial-gradient( circle farthest-corner at 10% 20%,  rgba(255,94,247,1) 17.8%, rgba(2,245,255,1) 100.2% )"}},"textColor":"white"} -->
<form method="post" action="" novalidate style="background:radial-gradient( circle farthest-corner at 10% 20%,  rgba(255,94,247,1) 17.8%, rgba(2,245,255,1) 100.2% );padding-top:2em;padding-right:2em;padding-bottom:2em;padding-left:2em" class="wp-block-mailster-form-wrapper mailster-block-form has-white-color has-text-color has-background"><div class="mailster-block-form-inner"><!-- wp:heading {"level":4} -->
<h4>Subscribe to ' . get_option( 'blogname' ) . '</h4>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Stay up to date with the latest news and relevant updates from us.</p>
<!-- /wp:paragraph -->

<!-- wp:mailster/field-email {"inline":true} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-inline"><input name="email" type="email" aria-required="true" aria-label="Enter your email address" spellcheck="false" required class="input" placeholder=" "/><label class="mailster-label">Enter your email address</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"style":{"width":33}} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button" style="width:33%"><input name="submit" type="submit" value="Subscribe" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></div></form>
<!-- /wp:mailster/form-wrapper -->',
		),
		array(
			'title'         => __( 'with Logo', 'mailster' ),
			'description'   => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'viewportWidth' => 880,
			'content'       => '<!-- wp:mailster/form-wrapper -->
<form method="post" action="" novalidate class="wp-block-mailster-form-wrapper mailster-block-form"><div class="mailster-block-form-inner"><!-- wp:site-logo {"align":"center"} /-->

<!-- wp:heading {"textAlign":"center"} -->
<h2 class="has-text-align-center" id="want-to-learn-how-to-get-subscribers">Want to learn how to get subscribers?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Get Your now</p>
<!-- /wp:paragraph -->

<!-- wp:mailster/field-email {"inline":true,"style":{"labelColor":"#000000"}} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-inline"><input name="email" type="email" aria-required="true" aria-label="Email" spellcheck="false" required class="input" placeholder=" "/><label style="color:#000000" class="mailster-label">Email</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button"><input name="submit" type="submit" value="Subscribe" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></div></form>
<!-- /wp:mailster/form-wrapper -->',

		),
	)
);
// $patterns = array();


