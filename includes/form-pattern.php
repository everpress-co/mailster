<?php

$patterns = apply_filters(
	'mailster_form_patterns',
	array(
		'simpleform' => array(
			'title'       => __( 'Simple form', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"style":{"width":"30px","height":"40px"},"background":{"opacity":10,"fixed":false,"repeat":false,"size":"cover","image":"https://dev.local/wp-content/uploads/2021/11/rawpixel-589084-unsplash.jpg","position":{"x":"0.92","y":"0.17"}},"css":".mailster-form{\n   outline:4px solid #121212;\n  outline-offset:-20px;\n  padding:3em;\n  border-radius:30px\n}"} -->
<form class="wp-block-mailster-form-wrapper mailster-form"><!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontWeight":"700"}},"fontSize":"larger"} -->
<h3 class="has-text-align-center has-larger-font-size" id="signup-to-our-newsletter" style="font-weight:700">Signup to our  Newsletter!</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Join our community with over [newsletter_subscribers round=100] total.</p>
<!-- /wp:paragraph -->

<!-- wp:mailster/input {"label":"Email","inline":true,"type":"email","style":{"width":100,"height":null,"padding":{"top":"0","left":"8px","right":"8px","bottom":"0"}},"className":"mailster-wrapper mailster-wrapper-required"} -->
<div class="wp-block-mailster-input mailster-wrapper mailster-wrapper-inline mailster-wrapper-required" data-label="Email" style="width:100%"><label>Email</label><input name="input_name" type="email" value="" class="input mailster-email mailster-required" arialabel="Email" spellcheck="false"/></div>
<!-- /wp:mailster/input -->

<!-- wp:mailster/input {"label":"Firstname","required":true,"inline":true,"style":{"width":49,"height":null,"padding":{"top":"0","left":"8px","right":"8px","bottom":"0"}},"className":"mailster-wrapper"} -->
<div class="wp-block-mailster-input mailster-wrapper mailster-wrapper-required mailster-wrapper-inline" data-label="Firstname" style="width:49%"><label>Firstname</label><input name="input_name" type="text" value="" class="input mailster-email mailster-required" arialabel="Firstname" spellcheck="false"/></div>
<!-- /wp:mailster/input -->

<!-- wp:mailster/input {"label":"Lastname","required":true,"inline":true,"style":{"width":49,"height":null,"padding":{"top":"0","left":"8px","right":"8px","bottom":"0"}},"className":"mailster-wrapper"} -->
<div class="wp-block-mailster-input mailster-wrapper mailster-wrapper-required mailster-wrapper-inline" data-label="Lastname" style="width:49%"><label>Lastname</label><input name="input_name" type="text" value="" class="input mailster-email mailster-required" arialabel="Lastname" spellcheck="false"/></div>
<!-- /wp:mailster/input -->

<!-- wp:mailster/button {"width":100,"className":"is-style-outline"} -->
<div class="wp-block-buttons mailster-wrapper"><div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-outline"><a class="wp-block-button__link">Get the latest news</a></div></div>
<!-- /wp:mailster/button --></form>
<!-- /wp:mailster/form-wrapper -->',

		),
	)
);

$patterns = array();



