<?php

$patterns = apply_filters(
	'mailster_form_patterns',
	array(
		array(
			'title'       => __( 'A minimal basic form', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"css":{"general":".input, .submit-button{\n\theight:66px;\n}\n.mailster-wrapper{\n    margin:0 !important;\n}\n","tablet":"","mobile":""},"style":{"borderRadius":"0px"}} -->
<form method="post" action="" novalidate class="wp-block-mailster-form-wrapper mailster-block-form"><div class="mailster-block-form-inner">

<!-- wp:mailster/field-email {"id":"6e11e97f-8c86-4d34-a18f-6ce17af2217b","inline":true,"style":{"width":60},"className":"mailster-wrapper-type-email"} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-inline mailster-wrapper-asterisk mailster-wrapper-type-email" style="width:60%"><input name="email" id="mailster-input-6e11e97f-8c86-4d34-a18f-6ce17af2217b" type="email" aria-required="true" aria-label="Email" spellcheck="false" required value="" class="input" placeholder=" "/><label for="mailster-input-6e11e97f-8c86-4d34-a18f-6ce17af2217b" class="mailster-label">Email</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"id":"4742da70-5e62-4276-9855-edfd4ebe956d","style":{"width":40,"borderRadius":"0px"},"className":"mailster-wrapper-asterisk mailster-wrapper-type-submit"} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button mailster-wrapper-asterisk mailster-wrapper-type-submit" style="width:40%"><input name="submit" id="mailster-input-4742da70-5e62-4276-9855-edfd4ebe956d" type="submit" style="border-radius:0px" value="Subscribe" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></div></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		array(
			'title'       => __( 'Simple form', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"css":{"general":".input, .submit-button{\n\theight:66px;\n}\n.mailster-wrapper{\n    margin:0 !important;\n}\n","tablet":"","mobile":""},"style":{"borderRadius":"0px","borderWidth":"0px"}} -->
<form method="post" action="" novalidate class="wp-block-mailster-form-wrapper mailster-block-form"><div class="mailster-block-form-inner">

<!-- wp:mailster/field-email {"id":"9e4c21fa-8c5d-4eca-afab-380796825b7b","inline":true,"style":{"width":60,"labelColor":"#58595b","inputColor":"#58595b","backgroundColor":"#ffffff"},"className":"mailster-wrapper-type-email"} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-inline mailster-wrapper-asterisk mailster-wrapper-type-email" style="width:60%"><input name="email" id="mailster-input-9e4c21fa-8c5d-4eca-afab-380796825b7b" type="email" aria-required="true" aria-label="Email" spellcheck="false" required value="" class="input" style="color:#58595b;background-color:#ffffff" placeholder=" "/><label for="mailster-input-9e4c21fa-8c5d-4eca-afab-380796825b7b" style="color:#58595b" class="mailster-label">Email</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"id":"1ea8ee58-3f36-42bd-91fc-17080a336f74","style":{"width":40,"borderRadius":"0px","backgroundColor":"#2bb3e7","inputColor":"#58595b","labelColor":"#58595b"},"className":"mailster-wrapper-asterisk mailster-wrapper-type-submit"} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button mailster-wrapper-asterisk mailster-wrapper-type-submit" style="width:40%"><input name="submit" id="mailster-input-1ea8ee58-3f36-42bd-91fc-17080a336f74" type="submit" style="color:#58595b;background-color:#2bb3e7;border-radius:0px" value="Subscribe" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></div></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		array(
			'title'       => __( 'One Liner', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"css":{"general":".input, .submit-button{\n\theight:66px;\n}\n.mailster-wrapper{\n    margin:0 !important;\n}\n","tablet":"","mobile":""},"style":{"borderRadius":"0px"}} -->
<form method="post" action="" novalidate class="wp-block-mailster-form-wrapper mailster-block-form"><div class="mailster-block-form-inner"><!-- wp:heading {"textAlign":"center","align":"full","style":{"typography":{"fontSize":"6em"}}} -->
<h2 class="alignfull has-text-align-center" id="hi-john" style="font-size:6em">Hi John!</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Join our newsletter!</p>
<!-- /wp:paragraph -->



<!-- wp:mailster/field-email {"id":"e9aef5b1-09d2-421a-b6f8-617ea33fb09d","inline":true,"style":{"width":60},"className":"mailster-wrapper-type-email"} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-inline mailster-wrapper-asterisk mailster-wrapper-type-email" style="width:60%"><input name="email" id="mailster-input-e9aef5b1-09d2-421a-b6f8-617ea33fb09d" type="email" aria-required="true" aria-label="Email" spellcheck="false" required value="" class="input" placeholder=" "/><label for="mailster-input-e9aef5b1-09d2-421a-b6f8-617ea33fb09d" class="mailster-label">Email</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"id":"3175c88b-d86d-49aa-a964-07b3ee26e11f","style":{"width":40,"borderRadius":"0px"},"className":"mailster-wrapper-asterisk mailster-wrapper-type-submit"} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button mailster-wrapper-asterisk mailster-wrapper-type-submit" style="width:40%"><input name="submit" id="mailster-input-3175c88b-d86d-49aa-a964-07b3ee26e11f" type="submit" style="border-radius:0px" value="Subscribe" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></div></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		array(
			'title'         => __( 'With image', 'mailster' ),
			'description'   => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'viewportWidth' => 880,
			'content'       => '<!-- wp:mailster/form-wrapper {"background":{"opacity":100,"fixed":false,"repeat":false,"size":52,"image":"https://user-images.githubusercontent.com/881729/147969548-3c739442-85b8-48b2-abfc-e3d2db5c0cb8.png","position":{"x":"0.88","y":"0.02"}},"css":{"general":"h2{\n\tfont-size:2em;\n\tfont-family:sans-serif;\n\tmargin-top:0.6em;\n}\n.input{\n\tborder: 0;\n\tborder-bottom-width: 3px;\n\tborder-bottom-style: solid;\n}\n.submit-button{\n\tborder-style:solid;\n}\n.mailster-block-form{\n    outline:3px solid;\n\toutline-offset:-15px;\n}\n.mailster-wrapper{\n\tmargin-top:2em;\n}\n.mailster-wrapper ~ .mailster-wrapper{\n\tmargin-top:inherit;\n}\n.mailster-wrapper-required label.mailster-label::after{\n    display:none\n}\n","tablet":"","mobile":""},"style":{"borderColor":"#1A1B1F","borderRadius":"0px","spacing":{"padding":{"top":"2.5em","right":"2.5em","bottom":"2.5em","left":"2.5em"}},"color":{"background":"#ffffff","text":"#363636"}},"backgroundColor":"#FFFFFF"} -->
<form method="post" action="" novalidate style="background-color:#ffffff;color:#363636;padding-top:2.5em;padding-right:2.5em;padding-bottom:2.5em;padding-left:2.5em" class="wp-block-mailster-form-wrapper mailster-block-form has-ffffff-background-color has-text-color has-background"><div class="mailster-block-form-inner"><!-- wp:heading {"style":{"typography":{"letterSpacing":"-0.1rem","fontSize":"45px"}}} -->
<h2 id="here-is-your-20-discount" style="font-size:45px;letter-spacing:-0.1rem">Here is your 20% discount.</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Join our email list and get a special 20% discount!</p>
<!-- /wp:paragraph -->



<!-- wp:mailster/field-email {"id":"195d5707-554d-4253-81a1-62f40ef41350","inline":true,"style":{"width":57}} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-inline mailster-wrapper-asterisk" style="width:57%"><input name="email" id="mailster-input-195d5707-554d-4253-81a1-62f40ef41350" type="email" aria-required="true" aria-label="Your Email address" spellcheck="false" required value="" class="input" placeholder=" "/><label for="mailster-input-195d5707-554d-4253-81a1-62f40ef41350" class="mailster-label">Your Email address</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"id":"2f7a7276-c982-426c-a223-92430155badb","style":{"width":57,"backgroundColor":"#ffffff","borderColor":"#1A1B1F","borderWidth":"3px","color":"#1A1B1F","inputColor":"#151515","borderRadius":"0px","typography":{"textTransform":"uppercase","fontSize":"30px"},"spacing":{"padding":{"top":"1em","right":"1em","bottom":"1em","left":"1em"}}},"className":"mailster-wrapper-asterisk"} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button mailster-wrapper-asterisk" style="width:57%"><input name="submit" id="mailster-input-2f7a7276-c982-426c-a223-92430155badb" type="submit" style="padding-top:1em;padding-right:1em;padding-bottom:1em;padding-left:1em;font-size:30px;text-transform:uppercase;color:#151515;background-color:#ffffff;border-color:#1A1B1F;border-width:3px;border-radius:0px" value="Get 20% now" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></div></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		array(
			'title'         => __( 'Flower Power', 'mailster' ),
			'description'   => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'viewportWidth' => 880,
			'content'       => '<!-- wp:mailster/form-wrapper {"background":{"opacity":100,"scale":1,"fixed":false,"repeat":true,"size":62,"image":"https://user-images.githubusercontent.com/881729/148080670-359b09d6-2aa3-446e-a6d4-f8a8388297a8.jpg","position":{"x":"0.28","y":"0.26"}},"css":{"general":".mailster-block-form-inner{\n\tbackground-color:#fff;\n\tpadding:1em;\n}","tablet":"","mobile":""},"style":{"spacing":{"padding":{"top":"2em","right":"2em","bottom":"2em","left":"2em"}}},"className":"has-background"} -->
<form method="post" action="" novalidate style="padding-top:2em;padding-right:2em;padding-bottom:2em;padding-left:2em" class="wp-block-mailster-form-wrapper mailster-block-form has-background"><div class="mailster-block-form-inner"><!-- wp:heading {"textAlign":"center","align":"full"} -->
<h2 class="alignfull has-text-align-center" id="hi-john">Hi John!</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Please consider Subscribing to your Newsletter.</p>
<!-- /wp:paragraph -->

<!-- wp:mailster/field-email {"id":"51518e3c-5896-4002-9cba-536a9d27b069","style":{"width":100}} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-asterisk" style="width:100%"><label for="mailster-input-51518e3c-5896-4002-9cba-536a9d27b069" class="mailster-label">Email</label><input name="email" id="mailster-input-51518e3c-5896-4002-9cba-536a9d27b069" type="email" aria-required="true" aria-label="Email" spellcheck="false" required value="" class="input" placeholder=" "/></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-firstname {"id":"d431fb4a-a25f-4ec0-963a-2d3f0982f0af","className":"mailster-wrapper-asterisk"} -->
<div class="wp-block-mailster-field-firstname mailster-wrapper mailster-wrapper-asterisk"><label for="mailster-input-d431fb4a-a25f-4ec0-963a-2d3f0982f0af" class="mailster-label">First Name</label><input name="firstname" id="mailster-input-d431fb4a-a25f-4ec0-963a-2d3f0982f0af" type="text" aria-required="false" aria-label="First Name" spellcheck="false" value="" class="input" placeholder=" "/></div>
<!-- /wp:mailster/field-firstname -->

<!-- wp:mailster/field-submit {"id":"112cdf48-ab08-4d47-ada5-809ba3a32dab","style":{"width":100},"className":"mailster-wrapper-asterisk"} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button mailster-wrapper-asterisk" style="width:100%"><input name="submit" id="mailster-input-112cdf48-ab08-4d47-ada5-809ba3a32dab" type="submit" value="Subscribe" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></div></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		array(
			'title'         => __( 'Simple', 'mailster' ),
			'description'   => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'viewportWidth' => 880,
			'content'       => '<!-- wp:mailster/form-wrapper {"background":{"opacity":20,"fixed":false,"repeat":false,"size":"cover","image":"https://user-images.githubusercontent.com/881729/150156830-af1843d1-d76e-408d-bce1-58e1f303ff15.jpg","position":{"x":0.5,"y":0.5}},"style":{"spacing":{"padding":{"top":"2em","right":"2em","bottom":"2em","left":"2em"}},"color":{"gradient":"radial-gradient( circle farthest-corner at 10% 20%,  rgba(255,94,247,1) 17.8%, rgba(2,245,255,1) 100.2% )"},"labelColor":"#000000"},"textColor":"white"} -->
<form method="post" action="" novalidate style="background:radial-gradient( circle farthest-corner at 10% 20%,  rgba(255,94,247,1) 17.8%, rgba(2,245,255,1) 100.2% );padding-top:2em;padding-right:2em;padding-bottom:2em;padding-left:2em" class="wp-block-mailster-form-wrapper mailster-block-form has-white-color has-text-color has-background"><div class="mailster-block-form-inner"><!-- wp:heading {"level":4} -->
<h4 id="subscribe-to-fresh">Subscribe to Fresh</h4>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Stay up to date with the latest news and relevant updates from us.</p>
<!-- /wp:paragraph -->



<!-- wp:mailster/field-email {"id":"742a50cd-32ba-4fd7-b11c-821e2b5dd850","inline":true} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-inline mailster-wrapper-asterisk"><input name="email" id="mailster-input-742a50cd-32ba-4fd7-b11c-821e2b5dd850" type="email" aria-required="true" aria-label="Enter your email address" spellcheck="false" required value="" class="input" placeholder=" "/><label for="mailster-input-742a50cd-32ba-4fd7-b11c-821e2b5dd850" class="mailster-label">Enter your email address</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"id":"2d89fc57-6c42-4753-a629-336ae090f7e5","style":{"width":33},"className":"mailster-wrapper-asterisk"} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button mailster-wrapper-asterisk" style="width:33%"><input name="submit" id="mailster-input-2d89fc57-6c42-4753-a629-336ae090f7e5" type="submit" value="Subscribe" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></div></form>
<!-- /wp:mailster/form-wrapper -->',
		),
		array(
			'title'         => __( 'With Logo', 'mailster' ),
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



<!-- wp:mailster/field-email {"id":"f02d2b7a-df57-403d-88e6-260e7f07735a","inline":true,"style":{"labelColor":"#000000"}} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-inline mailster-wrapper-asterisk"><input name="email" id="mailster-input-f02d2b7a-df57-403d-88e6-260e7f07735a" type="email" aria-required="true" aria-label="Email" spellcheck="false" required value="" class="input" placeholder=" "/><label for="mailster-input-f02d2b7a-df57-403d-88e6-260e7f07735a" style="color:#000000" class="mailster-label">Email</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"id":"2123bcca-8c67-41d4-8890-545537de4929","className":"mailster-wrapper-asterisk"} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button mailster-wrapper-asterisk"><input name="submit" id="mailster-input-2123bcca-8c67-41d4-8890-545537de4929" type="submit" value="Subscribe" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></div></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		array(
			'title'         => __( 'Welcome', 'mailster' ),
			'description'   => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'viewportWidth' => 880,
			'content'       => '<!-- wp:mailster/form-wrapper {"background":{"opacity":20,"fixed":false,"repeat":false,"size":"cover","image":"https://user-images.githubusercontent.com/881729/150156830-af1843d1-d76e-408d-bce1-58e1f303ff15.jpg","position":{"x":0.5,"y":0.5}},"style":{"spacing":{"padding":{"top":"2em","right":"2em","bottom":"2em","left":"2em"}},"color":{"gradient":"radial-gradient( circle farthest-corner at 10% 20%,  rgba(255,94,247,1) 17.8%, rgba(2,245,255,1) 100.2% )"},"labelColor":"#000000"},"textColor":"white"} -->
<form method="post" action="" novalidate style="background:radial-gradient( circle farthest-corner at 10% 20%,  rgba(255,94,247,1) 17.8%, rgba(2,245,255,1) 100.2% );padding-top:2em;padding-right:2em;padding-bottom:2em;padding-left:2em" class="wp-block-mailster-form-wrapper mailster-block-form has-white-color has-text-color has-background"><div class="mailster-block-form-inner"><!-- wp:heading {"level":4} -->
<h4 id="subscribe-to-fresh">Subscribe to Fresh</h4>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Stay up to date with the latest news and relevant updates from us.</p>
<!-- /wp:paragraph -->

<!-- wp:mailster/field-email {"id":"67c623bd-8e29-47f5-ac69-9fafea75f39b","inline":true} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-inline mailster-wrapper-asterisk"><input name="email" id="mailster-input-67c623bd-8e29-47f5-ac69-9fafea75f39b" type="email" aria-required="true" aria-label="Enter your email address" spellcheck="false" required value="" class="input" placeholder=" "/><label for="mailster-input-67c623bd-8e29-47f5-ac69-9fafea75f39b" class="mailster-label">Enter your email address</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"id":"05ae5b60-8967-42eb-b316-ec69937761ae","style":{"width":33},"className":"mailster-wrapper-asterisk"} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button mailster-wrapper-asterisk" style="width:33%"><input name="submit" id="mailster-input-05ae5b60-8967-42eb-b316-ec69937761ae" type="submit" value="Subscribe" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></div></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		array(
			'title'         => __( 'Next Order', 'mailster' ),
			'description'   => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'viewportWidth' => 880,
			'content'       => '<!-- wp:mailster/form-wrapper {"background":{"opacity":100,"scale":1,"fixed":false,"repeat":true,"size":62,"position":{"x":"0.28","y":"0.26"}},"css":{"general":".mailster-block-form-inner{\n\toutline:3px solid;\n\tpadding:1.2em;\n}","tablet":"","mobile":""},"style":{"spacing":{"padding":{"top":"2em","right":"2em","bottom":"2em","left":"2em"}},"color":{"background":"#FFFFFF"}},"className":"has-background"} -->
<form method="post" action="" novalidate style="background-color:#FFFFFF;padding-top:2em;padding-right:2em;padding-bottom:2em;padding-left:2em" class="wp-block-mailster-form-wrapper mailster-block-form has-background"><div class="mailster-block-form-inner"><!-- wp:heading {"textAlign":"center","align":"full"} -->
<h2 class="alignfull has-text-align-center" id="hi-john">Get 20% off your next order</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Subscribe to our newsletter below and<br>get a discount code to get 20% off your next order.</p>
<!-- /wp:paragraph -->

<!-- wp:mailster/messages -->
<div class="wp-block-mailster-messages mailster-block-form-info"><div class="mailster-block-form-info-success" style="width:undefined%;color:#ffffff;background:#6fbf4d"><div>Please confirm your subscription!</div><div class="mailster-block-form-info-extra"></div></div><div class="mailster-block-form-info-error" style="width:undefined%;color:#ffffff;background:#bf4d4d"><div>Following fields are missing or incorrect</div><div class="mailster-block-form-info-extra"></div></div></div>
<!-- /wp:mailster/messages -->

<!-- wp:mailster/field-firstname {"id":"d431fb4a-a25f-4ec0-963a-2d3f0982f0af","style":{"width":49},"className":"mailster-wrapper-asterisk"} -->
<div class="wp-block-mailster-field-firstname mailster-wrapper mailster-wrapper-asterisk" style="width:49%"><label for="mailster-input-d431fb4a-a25f-4ec0-963a-2d3f0982f0af" class="mailster-label">First Name</label><input name="firstname" id="mailster-input-d431fb4a-a25f-4ec0-963a-2d3f0982f0af" type="text" aria-required="false" aria-label="First Name" spellcheck="false" value="" class="input" placeholder=" "/></div>
<!-- /wp:mailster/field-firstname -->

<!-- wp:mailster/field-lastname {"id":"0f3fbf6b-77b6-44c2-8ba9-7e30859c6e91","style":{"width":49}} -->
<div class="wp-block-mailster-field-lastname mailster-wrapper" style="width:49%"><label for="mailster-input-0f3fbf6b-77b6-44c2-8ba9-7e30859c6e91" class="mailster-label">Last Name</label><input name="lastname" id="mailster-input-0f3fbf6b-77b6-44c2-8ba9-7e30859c6e91" type="text" aria-required="false" aria-label="Last Name" spellcheck="false" value="" class="input" placeholder=" "/></div>
<!-- /wp:mailster/field-lastname -->

<!-- wp:mailster/field-email {"id":"51518e3c-5896-4002-9cba-536a9d27b069","style":{"width":100}} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-asterisk" style="width:100%"><label for="mailster-input-51518e3c-5896-4002-9cba-536a9d27b069" class="mailster-label">Email</label><input name="email" id="mailster-input-51518e3c-5896-4002-9cba-536a9d27b069" type="email" aria-required="true" aria-label="Email" spellcheck="false" required value="" class="input" placeholder=" "/></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"id":"112cdf48-ab08-4d47-ada5-809ba3a32dab","style":{"width":100},"className":"mailster-wrapper-asterisk"} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button mailster-wrapper-asterisk" style="width:100%"><input name="submit" id="mailster-input-112cdf48-ab08-4d47-ada5-809ba3a32dab" type="submit" value="Subscribe" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></div></form>
<!-- /wp:mailster/form-wrapper -->',

		),
	)
);
// $patterns = array();


