<?php

$patterns = apply_filters(
	'mailster_form_patterns',
	array(
		'simpleform'  => array(
			'title'       => __( 'Simple form', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"id":"bfd88770","css":".input{\nborder:1px solid red\n}\n.mailster-form{\nborder:10px solid red;\n}"} -->
<div class="wp-block-mailster-form-wrapper mailster-form mailster-form-bfd88770"><!-- wp:mailster/input {"label":"Email","optionalRequired":false,"type":"email"} -->
<div class="wp-block-mailster-input"><label>Email</label><input name="asdads" type="email" value="" class="input mailster-email mailster-required" arialabel="Email" spellcheck="false"/></div>
<!-- /wp:mailster/input --></div>
<!-- /wp:mailster/form-wrapper -->',

		),
		'simpleform2' => array(
			'title'       => __( 'Simple form 2', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"style":{"color":{},"width":null,"height":null,"padding":{"top":"0em","left":"0em","right":"0em","bottom":"0em"}},"css":".wp-block-mailster-form-wrapper{\nborder:1px solid red;\n}\n.wp-block,.wp-block-cover{\nmargin:0\n}\n.wp-block-group__inner-container{\npadding:2em\n}\n.wp-block-columns.has-background{\npadding:0\n}\n.mailster-form{\nbox-shadow: 0 1.5vw 3vw -0.7vw rgb(255 0 0 / 99%);}"} -->
<div class="wp-block-mailster-form-wrapper mailster-form"><!-- wp:columns {"textColor":"primary","gradient":"electric-grass"} -->
<div class="wp-block-columns has-primary-color has-electric-grass-gradient-background has-text-color has-background"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:group {"tagName":"section"} -->
<section class="wp-block-group"><!-- wp:mailster/input {"label":"Email","optionalRequired":false,"type":"email"} -->
<div class="wp-block-mailster-input"><label>Email</label><input name="asdads" type="email" value="" class="input mailster-email mailster-required" arialabel="Email" spellcheck="false"/></div>
<!-- /wp:mailster/input -->

<!-- wp:mailster/input {"label":"Email","optionalRequired":false,"type":"email"} -->
<div class="wp-block-mailster-input"><label>Email</label><input name="asdads" type="email" value="" class="input mailster-email mailster-required" arialabel="Email" spellcheck="false"/></div>
<!-- /wp:mailster/input -->

<!-- wp:mailster/input {"label":"Email","optionalRequired":false,"type":"email"} -->
<div class="wp-block-mailster-input"><label>Email</label><input name="asdads" type="email" value="" class="input mailster-email mailster-required" arialabel="Email" spellcheck="false"/></div>
<!-- /wp:mailster/input --></section>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:cover {"url":"https://dummy.mailster.co/800x400.jpg","id":129,"dimRatio":70} -->
<div class="wp-block-cover has-background-dim-70 has-background-dim"><img class="wp-block-cover__image-background wp-image-129" alt="" src="https://dummy.mailster.co/800x400.jpg" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
<p class="has-text-align-center has-large-font-size">Signup now!</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:cover --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:mailster/form-wrapper -->',
		),
		'simpleform3' => array(
			'title'       => __( 'Simple form 3', 'mailster' ),
			'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'mailster' ),
			'content'     => '<!-- wp:mailster/form-wrapper {"style":{"color":{},"width":null,"height":null,"padding":{"top":"20em","left":"2em","right":"2em","bottom":"2em"}},"background":{"opacity":"100%","size":"cover","image":"https://dummy.mailster.co/800x400.jpg","position":{"x":0.5,"y":0.5}}} -->
<div class="wp-block-mailster-form-wrapper mailster-form"><!-- wp:mailster/input {"label":"Email","optionalRequired":false,"type":"email"} -->
<div class="wp-block-mailster-input"><label>Email</label><input name="asdads" type="email" value="" class="input mailster-email mailster-required" arialabel="Email" spellcheck="false"/></div>
<!-- /wp:mailster/input --></div>
<!-- /wp:mailster/form-wrapper -->',

		),
	)
);

$patterns_ = array();



