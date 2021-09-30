<p class="description"><?php esc_html_e( 'Copy and paste from your spreadsheet app. Mailster tries the guess the used formatting.', 'mailster' ); ?></p>
<form id="import_paste" method="post">
<textarea id="paste-import" class="widefat" rows="13" placeholder="<?php echo 'justin.case@' . $_SERVER['HTTP_HOST'] . ' Justin; Case; Custom;&#10;john.doe@' . $_SERVER['HTTP_HOST'] . ' John; Doe;;&#10;jane.roe@' . $_SERVER['HTTP_HOST'] . ' Jane; Roe;;'; ?>"></textarea>
	<section class="footer alternate">
		<p>
			<input type="submit" class="button button-primary button-large" value="<?php esc_attr_e( 'Next Step', 'mailster' ); ?> &#x2192;">
			<span class="status wp-ui-text-icon spinner"></span>
		</p>
	</section>
</form>
