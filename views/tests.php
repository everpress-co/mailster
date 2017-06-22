<?php wp_nonce_field( 'mailster_nonce', 'mailster_nonce', false ); ?>
<?php

$classes = array( 'wrap', 'mailster-tests' );

?>
<div class="<?php echo implode( ' ', $classes ) ?>">
<h1><?php esc_html_e( 'Mailster Tests', 'mailster' ); ?></h1>

<p>Run some tests in now if your site is compatibility with Mailster.</p>

<a class="button button-primary button-hero start-test">Start Tests</a>

<div id="tests_output"></div>

<div id="ajax-response"></div>
<br class="clear">
</div>
