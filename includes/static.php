<?php

$mailster_homepage = array(
	'post_title' => esc_html__( 'Newsletter', 'mailster' ),
	'post_status' => 'publish',
	'post_type' => 'page',
	'post_name' => esc_html_x( 'newsletter-signup', 'Newsletter Homepage page slug', 'mailster' ),
	'post_content' => '[newsletter_signup]' . esc_html__( 'Signup for the newsletter', 'mailster' ) . '[newsletter_signup_form id=1][/newsletter_signup] [newsletter_confirm]' . esc_html__( 'Thanks for your interest!', 'mailster' ) . '[/newsletter_confirm] [newsletter_unsubscribe]' . esc_html__( 'Do you really want to unsubscribe?', 'mailster' ) . '[/newsletter_unsubscribe]',
);
