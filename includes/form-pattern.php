<?php

$patterns = apply_filters(
	'mailster_form_patterns',
	array(
		'simpleform' => array(
			'title'       => __( 'Simple form', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper -->
<form method="post" action="/mailster/subscribe" class="wp-block-mailster-form-wrapper mailster-form"><div class="mailster-form-info"><div class="mailster-form-info-success" style="color:#ffffff;background-color:#6fbf4d"></div><div class="mailster-form-info-error" style="color:#ffffff;background-color:#bf4d4d"></div></div><!-- wp:mailster/field-email -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-type-email mailster-wrapper-required" style="width:100%"><label class="mailster-label">Email</label><input name="email" type="email" aria-required="true" aria-label="Email" spellcheck="false" required class="input" placeholder=" "/></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/gdpr -->
<div class="wp-block-mailster-gdpr mailster-wrapper mailster-wrapper-_gdpr"><label><input type="hidden" name="_gdpr" value="0"/><input type="checkbox" name="_gdpr" value="1"/><span>I agree to the privacy policy and terms.</span></label></div>
<!-- /wp:mailster/gdpr -->

<!-- wp:mailster/button {"className":"wp-block-buttons mailster-submit-wrapper"} -->
<div class="wp-block-mailster-button mailster-wrapper mailster-wrapper-type-undefined wp-block-buttons mailster-submit-wrapper" style="width:100%"><input type="submit" value="Submit"/></div>
<!-- /wp:mailster/button --><input type="submit" style="display:none !important"/></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		'oneliner'   => array(
			'title'       => __( 'One Liner', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"style":{"color":{"background":"#8ed1fc"}},"background":{"opacity":40,"fixed":false,"repeat":false,"size":"cover","image":"","position":{"x":0.5,"y":0.5}},"css":{"general":".mailster-form{\n    padding:2em;\n  border-radius:5px;\n}\n.mailster-wrapper-type-email{\n    background:#fff; \n    border-radius:5px 0 0 5px;\n}\n.mailster-wrapper-type-email .input{\n  border:0;  \n}\nh4{\n  margin:0 0 1em;\n}\ninput{\n   height:54px\n}\n","tablet":"","mobile":""}} -->
<form method="post" action="/mailster/subscribe" class="wp-block-mailster-form-wrapper mailster-form has-background" style="background-color:#8ed1fc"><div class="mailster-form-info"><div class="mailster-form-info-success" style="color:#ffffff;background-color:#6fbf4d"></div><div class="mailster-form-info-error" style="color:#ffffff;background-color:#bf4d4d"></div></div><!-- wp:heading {"textAlign":"center","level":4} -->
<h4 class="has-text-align-center" id="join-our-newsletter-here">Join our Newsletter here!</h4>
<!-- /wp:heading -->

<!-- wp:mailster/field-email {"inline":true,"style":{"width":70}} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-type-email mailster-wrapper-required mailster-wrapper-inline" style="width:70%"><input name="email" type="email" aria-required="true" aria-label="Email" spellcheck="false" required class="input" placeholder=" "/><label class="mailster-label">Email</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/button {"style":{"width":30},"className":"wp-block-buttons mailster-submit-wrapper"} -->
<div class="wp-block-mailster-button mailster-wrapper mailster-wrapper-type-undefined wp-block-buttons mailster-submit-wrapper" style="width:30%"><input type="submit" value="Submit"/></div>
<!-- /wp:mailster/button --><input type="submit" style="display:none !important"/></form>
<!-- /wp:mailster/form-wrapper -->',

		),
	)
);
$patterns = array();



