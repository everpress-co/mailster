<?php

// remove campaigns from Visual Composer
add_filter( 'vc_settings_exclude_post_type', function( $post_types ) {
	$post_types[] = 'newsletter';
	return $post_types;
} );
