<form method="post">

	<?php $lists = mailster( 'manage' )->get_lists( 'mailpoet' ); ?>



	<section class="footer alternate">
		<p>
			<input type="submit" class="button button-primary button-large" value="<?php esc_attr_e( 'Next Step', 'mailster' ); ?> &#x2192;">
			<span class="status wp-ui-text-icon"></span>
		</p>
	</section>
</form>
