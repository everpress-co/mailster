<?php return '<!-- wp:mailster/form-wrapper {"css":{"general":".mailster-wrapper{\n    margin-bottom:1em;\n}\n"}} -->
<form method="post" novalidate class="wp-block-mailster-form-wrapper mailster-block-form"><div class="mailster-block-form-inner"><!-- wp:mailster/messages -->
<div class="wp-block-mailster-messages mailster-block-form-info mailster-wrapper" aria-hidden="true"><div class="mailster-block-form-info-success" style="width:100%;color:#ffffff;background:#6fbf4d"><div>' . esc_html__( 'Please confirm your subscription!', 'mailster' ) . '</div><div class="mailster-block-form-info-extra"></div></div><div class="mailster-block-form-info-error" style="width:100%;color:#ffffff;background:#bf4d4d"><div>' . esc_html__( 'Some fields are missing or inccorrect!', 'mailster' ) . '</div><div class="mailster-block-form-info-extra"></div></div></div>
<!-- /wp:mailster/messages -->

<!-- wp:mailster/field-firstname {"style":{"width":49}} -->
<div class="wp-block-mailster-field-firstname mailster-wrapper mailster-wrapper-type-text" style="width:49%"><label for="mailster-id-2c4fa9" class="mailster-label">' . esc_html__( 'First Name', 'mailster' ) . '</label><input name="firstname" id="mailster-id-2c4fa9" type="text" aria-required="false" aria-label="' . esc_attr__( 'First Name', 'mailster' ) . '" spellcheck="false" value="" class="input" autocomplete="given-name" placeholder=" "/></div>
<!-- /wp:mailster/field-firstname -->

<!-- wp:mailster/field-lastname {"style":{"width":49}} -->
<div class="wp-block-mailster-field-lastname mailster-wrapper mailster-wrapper-type-text" style="width:49%"><label for="mailster-id-64ce15" class="mailster-label">' . esc_html__( 'Last Name', 'mailster' ) . '</label><input name="lastname" id="mailster-id-64ce15" type="text" aria-required="false" aria-label="' . esc_attr__( 'Last Name', 'mailster' ) . '" spellcheck="false" value="" class="input" autocomplete="family-name" placeholder=" "/></div>
<!-- /wp:mailster/field-lastname -->

<!-- wp:mailster/field-email -->
<div class="wp-block-mailster-field-email mailster-wrapper mailster-wrapper-required mailster-wrapper-type-email mailster-wrapper-asterisk"><label for="mailster-id-4a142f" class="mailster-label">' . esc_html__( 'Email', 'mailster' ) . '</label><input name="email" id="mailster-id-4a142f" type="email" aria-required="true" aria-label="' . esc_attr__( 'Email', 'mailster' ) . '" spellcheck="false" required value="" class="input" autocomplete="email" placeholder=" "/></div>
<!-- /wp:mailster/field-email -->

<!-- wp:mailster/field-submit {"align":"center"} -->
<div class="wp-block-mailster-field-submit mailster-wrapper mailster-wrapper-type-submit mailster-wrapper-align-center wp-block-button"><input name="submit" id="mailster-id-02e1d6" type="submit" value="' . esc_attr__( 'Subscribe now!', 'mailster' ) . '" class="wp-block-button__link submit-button"/></div>
<!-- /wp:mailster/field-submit --></div></form>
<!-- /wp:mailster/form-wrapper -->';
