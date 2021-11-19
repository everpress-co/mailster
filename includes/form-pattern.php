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

<!-- wp:mailster/button -->
<div class="wp-block-buttons mailster-wrapper mailster-submit-wrapper"><div class="wp-block-button"><a class="wp-block-button__link" href="">Subscribe</a></div></div>
<!-- /wp:mailster/button --><input type="submit" style="display:none !important"/></form>
<!-- /wp:mailster/form-wrapper -->',

		),
	)
);

// $patterns = array();



