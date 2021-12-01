<?php

$patterns = apply_filters(
	'mailster_form_patterns',
	array(
		'basic'      => array(
			'title'       => __( 'A minimal basic form', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper -->
<form method="post" action="/mailster/subscribe" class="wp-block-mailster-form-wrapper mailster-form"><div class="mailster-form-info"><div class="mailster-form-info-success" style="color:#ffffff;background-color:#6fbf4d"></div><div class="mailster-form-info-error" style="color:#ffffff;background-color:#bf4d4d"></div></div><!-- wp:mailster/field-email -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-type-email mailster-wrapper-required"><label class="mailster-label">Email</label><input name="email" type="email" aria-required="true" aria-label="Email" spellcheck="false" required class="input" placeholder=" "/></div>
<!-- /wp:mailster/field-email -->
<!-- wp:mailster/field-submit -->
<div class="wp-block-mailster-field-submit mailster-wrapper mailster-wrapper-type-submit"><input name="submit" type="submit" value="Subscribe"/></div>
<!-- /wp:mailster/field-submit --><input type="submit" style="display:none !important"/></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		'simpleform' => array(
			'title'       => __( 'Simple form', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"style":{"backgroundColor":"#D1E4DD","borderColor":"#D1E4DD","borderWidth":"6px"}} -->
<form method="post" action="/mailster/subscribe" class="wp-block-mailster-form-wrapper mailster-form"><div class="mailster-form-info"><div class="mailster-form-info-success" style="color:#ffffff;background-color:#6fbf4d"></div><div class="mailster-form-info-error" style="color:#ffffff;background-color:#bf4d4d"></div></div><!-- wp:mailster/field-email {"inline":true,"style":{"width":60}} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-type-email mailster-wrapper-required mailster-wrapper-inline" style="width:60%"><input name="email" type="email" aria-required="true" aria-label="Email" spellcheck="false" required class="input" placeholder=" "/><label class="mailster-label">Email</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"style":{"width":40,"color":"#39414D","backgroundColor":"#D1E4DD"}} -->
<div class="wp-block-mailster-field-submit mailster-wrapper mailster-wrapper-type-submit" style="width:40%"><input name="submit" type="submit" style="color:#39414D;background-color:#D1E4DD" value="Subscribe"/></div>
<!-- /wp:mailster/field-submit -->

<!-- wp:mailster/gdpr -->
<div class="wp-block-mailster-gdpr mailster-wrapper mailster-wrapper-_gdpr"><label><input type="hidden" name="_gdpr" value="0"/><input type="checkbox" name="_gdpr" value="1"/><span>I agree to the privacy policy and terms.</span></label></div>
<!-- /wp:mailster/gdpr --><input type="submit" style="display:none !important"/></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		'oneliner'   => array(
			'title'       => __( 'One Liner', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"style":{"color":{"background":"#8ed1fc"}},"background":{"opacity":40,"fixed":false,"repeat":false,"size":"cover","image":"","position":{"x":0.5,"y":0.5}},"css":{"general":".mailster-form{\n\tpadding:2em;\n\tborder-radius:5px;\n}\n.mailster-wrapper-type-email{\n    background:#fff; \n    border-radius:5px 0 0 5px;\n}\n.mailster-wrapper-type-email .input{\n  border:0;  \n}\nh4{\n  margin:0 0 1em;\n}\n","tablet":"","mobile":""},"className":"has-background"} -->
<form method="post" action="/mailster/subscribe" class="wp-block-mailster-form-wrapper mailster-form has-background"><div class="mailster-form-info"><div class="mailster-form-info-success" style="color:#ffffff;background-color:#6fbf4d"></div><div class="mailster-form-info-error" style="color:#ffffff;background-color:#bf4d4d"></div></div><!-- wp:heading {"textAlign":"center","level":4} -->
<h4 class="has-text-align-center" id="join-our-newsletter-here">Join our Newsletter here!</h4>
<!-- /wp:heading -->

<!-- wp:mailster/field-email {"inline":true,"style":{"width":70}} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-type-email mailster-wrapper-required mailster-wrapper-inline" style="width:70%"><input name="email" type="email" aria-required="true" aria-label="Email" spellcheck="false" required class="input" placeholder=" "/><label class="mailster-label">Email</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"style":{"width":30}} -->
<div class="wp-block-mailster-field-submit mailster-wrapper mailster-wrapper-type-submit" style="width:30%"><input name="submit" type="submit" value="Subscribe"/></div>
<!-- /wp:mailster/field-submit -->

<!-- wp:mailster/gdpr -->
<div class="wp-block-mailster-gdpr mailster-wrapper mailster-wrapper-_gdpr"><label><input type="hidden" name="_gdpr" value="0"/><input type="checkbox" name="_gdpr" value="1"/><span>I agree to the privacy policy and terms.</span></label></div>
<!-- /wp:mailster/gdpr --><input type="submit" style="display:none !important"/></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		'oneliner1'  => array(
			'title'       => __( 'One Liner', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"style":{"color":{"background":"#8ed1fc"}},"background":{"opacity":40,"fixed":false,"repeat":false,"size":"cover","image":"","position":{"x":0.5,"y":0.5}},"css":{"general":".mailster-form{\n\tpadding:2em;\n\tborder-radius:5px;\n}\n.mailster-wrapper-type-email{\n    background:#fff; \n    border-radius:5px 0 0 5px;\n}\n.mailster-wrapper-type-email .input{\n  border:0;  \n}\nh4{\n  margin:0 0 1em;\n}\n","tablet":"","mobile":""},"className":"has-background"} -->
<form method="post" action="/mailster/subscribe" class="wp-block-mailster-form-wrapper mailster-form has-background"><div class="mailster-form-info"><div class="mailster-form-info-success" style="color:#ffffff;background-color:#6fbf4d"></div><div class="mailster-form-info-error" style="color:#ffffff;background-color:#bf4d4d"></div></div><!-- wp:heading {"textAlign":"center","level":4} -->
<h4 class="has-text-align-center" id="join-our-newsletter-here">Join our Newsletter here!</h4>
<!-- /wp:heading -->

<!-- wp:mailster/field-email {"inline":true,"style":{"width":70}} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-type-email mailster-wrapper-required mailster-wrapper-inline" style="width:70%"><input name="email" type="email" aria-required="true" aria-label="Email" spellcheck="false" required class="input" placeholder=" "/><label class="mailster-label">Email</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"style":{"width":30}} -->
<div class="wp-block-mailster-field-submit mailster-wrapper mailster-wrapper-type-submit" style="width:30%"><input name="submit" type="submit" value="Subscribe"/></div>
<!-- /wp:mailster/field-submit -->

<!-- wp:mailster/gdpr -->
<div class="wp-block-mailster-gdpr mailster-wrapper mailster-wrapper-_gdpr"><label><input type="hidden" name="_gdpr" value="0"/><input type="checkbox" name="_gdpr" value="1"/><span>I agree to the privacy policy and terms.</span></label></div>
<!-- /wp:mailster/gdpr --><input type="submit" style="display:none !important"/></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		'oneliner2'  => array(
			'title'       => __( 'One Liner', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"style":{"color":{"background":"#8ed1fc"}},"background":{"opacity":40,"fixed":false,"repeat":false,"size":"cover","image":"","position":{"x":0.5,"y":0.5}},"css":{"general":".mailster-form{\n\tpadding:2em;\n\tborder-radius:5px;\n}\n.mailster-wrapper-type-email{\n    background:#fff; \n    border-radius:5px 0 0 5px;\n}\n.mailster-wrapper-type-email .input{\n  border:0;  \n}\nh4{\n  margin:0 0 1em;\n}\n","tablet":"","mobile":""},"className":"has-background"} -->
<form method="post" action="/mailster/subscribe" class="wp-block-mailster-form-wrapper mailster-form has-background"><div class="mailster-form-info"><div class="mailster-form-info-success" style="color:#ffffff;background-color:#6fbf4d"></div><div class="mailster-form-info-error" style="color:#ffffff;background-color:#bf4d4d"></div></div><!-- wp:heading {"textAlign":"center","level":4} -->
<h4 class="has-text-align-center" id="join-our-newsletter-here">Join our Newsletter here!</h4>
<!-- /wp:heading -->

<!-- wp:mailster/field-email {"inline":true,"style":{"width":70}} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-type-email mailster-wrapper-required mailster-wrapper-inline" style="width:70%"><input name="email" type="email" aria-required="true" aria-label="Email" spellcheck="false" required class="input" placeholder=" "/><label class="mailster-label">Email</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"style":{"width":30}} -->
<div class="wp-block-mailster-field-submit mailster-wrapper mailster-wrapper-type-submit" style="width:30%"><input name="submit" type="submit" value="Subscribe"/></div>
<!-- /wp:mailster/field-submit -->

<!-- wp:mailster/gdpr -->
<div class="wp-block-mailster-gdpr mailster-wrapper mailster-wrapper-_gdpr"><label><input type="hidden" name="_gdpr" value="0"/><input type="checkbox" name="_gdpr" value="1"/><span>I agree to the privacy policy and terms.</span></label></div>
<!-- /wp:mailster/gdpr --><input type="submit" style="display:none !important"/></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		'oneliner3'  => array(
			'title'       => __( 'One Liner', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"style":{"color":{"background":"#8ed1fc"}},"background":{"opacity":40,"fixed":false,"repeat":false,"size":"cover","image":"","position":{"x":0.5,"y":0.5}},"css":{"general":".mailster-form{\n\tpadding:2em;\n\tborder-radius:5px;\n}\n.mailster-wrapper-type-email{\n    background:#fff; \n    border-radius:5px 0 0 5px;\n}\n.mailster-wrapper-type-email .input{\n  border:0;  \n}\nh4{\n  margin:0 0 1em;\n}\n","tablet":"","mobile":""},"className":"has-background"} -->
<form method="post" action="/mailster/subscribe" class="wp-block-mailster-form-wrapper mailster-form has-background"><div class="mailster-form-info"><div class="mailster-form-info-success" style="color:#ffffff;background-color:#6fbf4d"></div><div class="mailster-form-info-error" style="color:#ffffff;background-color:#bf4d4d"></div></div><!-- wp:heading {"textAlign":"center","level":4} -->
<h4 class="has-text-align-center" id="join-our-newsletter-here">Join our Newsletter here!</h4>
<!-- /wp:heading -->

<!-- wp:mailster/field-email {"inline":true,"style":{"width":70}} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-type-email mailster-wrapper-required mailster-wrapper-inline" style="width:70%"><input name="email" type="email" aria-required="true" aria-label="Email" spellcheck="false" required class="input" placeholder=" "/><label class="mailster-label">Email</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"style":{"width":30}} -->
<div class="wp-block-mailster-field-submit mailster-wrapper mailster-wrapper-type-submit" style="width:30%"><input name="submit" type="submit" value="Subscribe"/></div>
<!-- /wp:mailster/field-submit -->

<!-- wp:mailster/gdpr -->
<div class="wp-block-mailster-gdpr mailster-wrapper mailster-wrapper-_gdpr"><label><input type="hidden" name="_gdpr" value="0"/><input type="checkbox" name="_gdpr" value="1"/><span>I agree to the privacy policy and terms.</span></label></div>
<!-- /wp:mailster/gdpr --><input type="submit" style="display:none !important"/></form>
<!-- /wp:mailster/form-wrapper -->',

		),
	)
);
// $patterns = array();



