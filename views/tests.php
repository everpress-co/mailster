<?php wp_nonce_field( 'mailster_nonce', 'mailster_nonce', false ); ?>
<?php $classes = array( 'wrap', 'mailster-tests' ); ?>

<?php
	$heading = 'Test @ ' . date( 'r' ) . ' from ' . site_url();

	$textoutput = str_repeat( '=', strlen( $heading ) ) . "\n" . $heading . "\n" . str_repeat( '=', strlen( $heading ) ) . "\n";
	?>
<div class="<?php echo implode( ' ', $classes ) ?>">
<h1><?php esc_html_e( 'Mailster Tests', 'mailster' ); ?></h1>

<p><?php esc_html_e( 'Mailster will now run some tests to ensure everything is running smoothly. Please keep this browser window open until all tests are finished', 'mailster' ); ?></p>

<div class="tests-wrap no-success">
	<a class="button button-primary button-hero start-test"><?php echo esc_html( __( 'Start Tests', 'mailster' ) ) ?></a>
	<div id="progress" class="progress"><span class="bar" style="width:0%"><span></span></span></div>
	<h4 class="test-info"><?php echo esc_html( __( 'Click the button to start test', 'mailster' ) ) ?></h4>
	<div id="outputnav" class="nav-tab-wrapper hide-if-no-js">
		<a class="nav-tab nav-tab-active" href="#output"><?php echo esc_html( __( 'Output', 'mailster' ) ) ?></a>
		<a class="nav-tab" href="#textoutput"><?php echo esc_html( __( 'Text Output', 'mailster' ) ) ?></a>
	</div>
	<div class="subtab" id="subtab-output">
		<p class="tests-toggles">
			<?php echo esc_html( __( 'Show', 'mailster' ) ) ?>:
			<label class="label-error" title="<?php echo esc_attr__( 'Errors must be fixed in order to make Mailster work correctly.', 'mailster' );  ?>"> <input type="checkbox" name="" data-type="error" checked><i></i><?php echo esc_html( __( 'Errors', 'mailster' ) ) ?></label>
			<label class="label-warning" title="<?php echo esc_attr__( 'Warnings are recommended to get fixed but not required to make Mailster work.', 'mailster' );  ?>"> <input type="checkbox" name="" data-type="warning" checked><i></i><?php echo esc_html( __( 'Warnings', 'mailster' ) ) ?></label>
			<label class="label-notice" title="<?php echo esc_attr__( 'Notices normally don\'t require any action.', 'mailster' );  ?>"> <input type="checkbox" name="" data-type="notice" checked><i></i><?php echo esc_html( __( 'Notices', 'mailster' ) ) ?></label>
			<label class="label-success" title="<?php echo esc_attr__( 'Best requirements for Mailster to work.', 'mailster' );  ?>"> <input type="checkbox" name="" data-type="success"><i></i><?php echo esc_html( __( 'Success', 'mailster' ) ) ?></label>
		</p>
		<div class="tests-output"></div>
	</div>
	<div class="subtab" id="subtab-textoutput">
		<div class="tests-textoutput-wrap"><textarea class="tests-textoutput" data-pretext="<?php echo esc_attr( $textoutput ) ?>"></textarea></div>
		<p class="description"><?php esc_html_e( 'To copy the system info, click below then press Ctrl + C (PC) or Cmd + C (Mac).', 'mailster' );?></p>
	</div>

</div>

<div id="ajax-response"></div>
<br class="clear">
</div>
