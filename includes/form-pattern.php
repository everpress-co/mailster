<?php

$patterns = apply_filters(
	'mailster_form_patterns',
	array(
		'basic'            => array(
			'title'       => __( 'A minimal basic form', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"className":"mailster-form"} -->
<form method="post" action="" novalidate class="wp-block-mailster-form-wrapper mailster-block-form mailster-form"><div class="mailster-block-form-info"><div class="mailster-block-form-info-success"></div><div class="mailster-block-form-info-error"></div></div><!-- wp:mailster/field-email {"className":"mailster-wrapper-type-email"} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-type-email"><label class="mailster-label">Email</label><input name="email" type="email" aria-required="true" aria-label="Email" spellcheck="false" required class="input" placeholder=" "/></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"className":"mailster-wrapper-type-submit"} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button mailster-wrapper-type-submit"><input name="submit" type="submit" value="Subscribe" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		'simpleform'       => array(
			'title'       => __( 'Simple form', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper -->
<form method="post" action="" novalidate class="wp-block-mailster-form-wrapper mailster-block-form mailster-form"><div class="mailster-block-form-info"><div class="mailster-block-form-info-success"></div><div class="mailster-block-form-info-error"></div></div><!-- wp:mailster/field-email {"inline":true,"style":{"width":60},"className":"mailster-wrapper-type-email"} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-inline mailster-wrapper-type-email" style="width:60%"><input name="email" type="email" aria-required="true" aria-label="Email" spellcheck="false" required class="input" placeholder=" "/><label class="mailster-label">Email</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"style":{"width":40,"color":"#39414D","backgroundColor":"#D1E4DD"},"className":"mailster-wrapper-type-submit"} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button mailster-wrapper-type-submit" style="width:40%"><input name="submit" type="submit" style="color:#39414D;background-color:#D1E4DD" value="Subscribe" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		'oneliner'         => array(
			'title'       => __( 'One Liner', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper -->
<form method="post" action="" novalidate class="wp-block-mailster-form-wrapper mailster-block-form mailster-form has-background"><div class="mailster-block-form-info"><div class="mailster-block-form-info-success"></div><div class="mailster-block-form-info-error"></div></div><!-- wp:heading {"textAlign":"center","level":4,"align":"full"} -->
<h4 class="alignfull has-text-align-center" id="join-our-newsletter-here">Join our Newsletter here!</h4>
<!-- /wp:heading -->

<!-- wp:mailster/field-email {"inline":true,"style":{"width":70},"className":"mailster-wrapper-type-email"} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-inline mailster-wrapper-type-email" style="width:70%"><input name="email" type="email" aria-required="true" aria-label="Email" spellcheck="false" required class="input" placeholder=" "/><label class="mailster-label">Email</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"style":{"width":30},"className":"mailster-wrapper-type-submit"} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button mailster-wrapper-type-submit" style="width:30%"><input name="submit" type="submit" value="Subscribe" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		'with_image'       => array(
			'title'       => __( 'With image', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"backgroundColor":"#FFFFFF","fontSize":25,"padding":25,"borderRadius":"10px","background":{"opacity":100,"fixed":false,"repeat":false,"size":"contain","image":"https://user-images.githubusercontent.com/881729/147969548-3c739442-85b8-48b2-abfc-e3d2db5c0cb8.png","position":{"x":"1.00","y":"0.51"}},"css":{"general":"h2{\n\tfont-size:2em;\n\tfont-family:sans-serif;\n\tmargin-top:0.6em;\n}\n.input{\n\tborder: 0;\n\tborder-bottom-width: 3px;\n\tborder-bottom-style: solid;\n}\n.submit-button{\n\tborder-style:solid;\n\ttext-transform: uppercase;\n}\n.mailster-block-form{\n    outline:3px solid;\n\toutline-offset:-15px;\n}\n.mailster-wrapper:nth-of-type(1){\n\tmargin-top:2em;\n}\n.mailster-wrapper-required label.mailster-label::after{\n    display:none\n}\n","tablet":"","mobile":""},"style":{"borderColor":"#1A1B1F","borderRadius":"0px"},"className":"mailster-form"} -->
<form method="post" action="https://dev.local/wp-json/mailster/v1/subscribe" novalidate style="background-color:#FFFFFF" class="wp-block-mailster-form-wrapper mailster-block-form mailster-form"><div class="mailster-block-form-info"><div class="mailster-block-form-info-success"></div><div class="mailster-block-form-info-error"></div></div><!-- wp:heading {"style":{"typography":{"letterSpacing":"-0.1rem"}}} -->
<h2 style="letter-spacing:-0.1rem">Here is your 20% discount.</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Join our email list and get a special 20% discount!</p>
<!-- /wp:paragraph -->

<!-- wp:mailster/field-email {"inline":true,"style":{"width":57},"className":"mailster-wrapper-type-email"} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-inline mailster-wrapper-type-email" style="width:57%"><input name="email" type="email" aria-required="true" aria-label="Your Email address" spellcheck="false" required class="input" placeholder=" "/><label class="mailster-label">Your Email address</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"style":{"width":57,"backgroundColor":"#ffffff","borderColor":"#1A1B1F","borderWidth":"3px","color":"#1A1B1F"},"className":"mailster-wrapper-type-submit"} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button mailster-wrapper-type-submit" style="width:57%"><input name="submit" type="submit" style="color:#1A1B1F;background-color:#ffffff;border-color:#1A1B1F;border-width:3px" value="Get 20% now" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		'simple_statement' => array(
			'title'       => __( 'simple_statement', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"className":"mailster-form has-background"} -->
<form method="post" action="" novalidate class="wp-block-mailster-form-wrapper mailster-block-form mailster-form has-background"><div class="mailster-block-form-info"><div class="mailster-block-form-info-success"></div><div class="mailster-block-form-info-error"></div></div><!-- wp:heading {"textAlign":"center","align":"full"} -->
<h2 class="alignfull has-text-align-center" id="hi-john">Hi John!</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Please consider Subscribing to your Newsletter.</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":27} -->
<div style="height:27px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:mailster/field-email {"style":{"width":100},"className":"mailster-wrapper-type-email"} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-type-email" style="width:100%"><label class="mailster-label">Email</label><input name="email" type="email" aria-required="true" aria-label="Email" spellcheck="false" required class="input" placeholder=" "/></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-firstname -->
<div class="wp-block-mailster-field-firstname mailster-wrapper"><label class="mailster-label">First Name</label><input name="firstname" type="text" aria-required="false" aria-label="First Name" spellcheck="false" class="input" placeholder=" "/></div>
<!-- /wp:mailster/field-firstname -->

<!-- wp:mailster/field-submit {"style":{"width":100},"className":"mailster-wrapper-type-submit"} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button mailster-wrapper-type-submit" style="width:100%"><input name="submit" type="submit" value="Subscribe" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></form>
<!-- /wp:mailster/form-wrapper -->',

		),
	)
);
 // $patterns = array();


