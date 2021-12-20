<?php

$patterns = apply_filters(
	'mailster_form_patterns',
	array(
		'basic'      => array(
			'title'       => __( 'A minimal basic form', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"className":"mailster-form"} -->
<form method="post" action="https://dev.local/wp-json/mailster/v1/subscribe" novalidate class="wp-block-mailster-form-wrapper mailster-block-form mailster-form"><div class="mailster-block-form-info"><div class="mailster-block-form-info-success"></div><div class="mailster-block-form-info-error"></div></div><!-- wp:mailster/field-email {"className":"mailster-wrapper-type-email"} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-type-email"><label class="mailster-label">Email</label><input name="email" type="email" aria-required="true" aria-label="Email" spellcheck="false" required class="input" placeholder=" "/></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"className":"mailster-wrapper-type-submit"} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button mailster-wrapper-type-submit"><input name="submit" type="submit" value="Subscribe" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		'simpleform' => array(
			'title'       => __( 'Simple form', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"style":{"backgroundColor":"#D1E4DD","borderColor":"#D1E4DD","borderWidth":"6px"},"className":"mailster-form"} -->
<form method="post" action="https://dev.local/wp-json/mailster/v1/subscribe" novalidate class="wp-block-mailster-form-wrapper mailster-block-form mailster-form"><div class="mailster-block-form-info"><div class="mailster-block-form-info-success"></div><div class="mailster-block-form-info-error"></div></div><!-- wp:mailster/field-email {"inline":true,"style":{"width":60},"className":"mailster-wrapper-type-email"} -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-inline mailster-wrapper-type-email" style="width:60%"><input name="email" type="email" aria-required="true" aria-label="Email" spellcheck="false" required class="input" placeholder=" "/><label class="mailster-label">Email</label></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"style":{"width":40,"color":"#39414D","backgroundColor":"#D1E4DD"},"className":"mailster-wrapper-type-submit"} -->
<div class="wp-block-mailster-field-submit mailster-wrapper wp-block-button mailster-wrapper-type-submit" style="width:40%"><input name="submit" type="submit" style="color:#39414D;background-color:#D1E4DD" value="Subscribe" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></form>
<!-- /wp:mailster/form-wrapper -->',

		),
		'oneliner'   => array(
			'title'       => __( 'One Liner', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"background":{"opacity":40,"fixed":false,"repeat":false,"size":"cover","image":"","position":{"x":0.5,"y":0.5}},"css":{"general":".mailster-form{\n\tpadding:2em;\n\tborder-radius:5px;\n}\n.mailster-wrapper-type-email{\n    background:#fff; \n    border-radius:5px 0 0 5px;\n}\n.mailster-wrapper-type-email .input{\n    height:72px;\n}\nh4{\n  margin:0 0 1em;\n}\n","tablet":"","mobile":""},"style":{"color":{"background":"#8ed1fc"},"borderWidth":"0px"},"className":"mailster-form has-background"} -->
<form method="post" action="https://dev.local/wp-json/mailster/v1/subscribe" novalidate class="wp-block-mailster-form-wrapper mailster-block-form mailster-form has-background"><div class="mailster-block-form-info"><div class="mailster-block-form-info-success"></div><div class="mailster-block-form-info-error"></div></div><!-- wp:heading {"textAlign":"center","level":4,"align":"full"} -->
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
	)
);
 // $patterns = array();



