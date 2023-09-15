<div class="empty-table-wrap">

	<div class="empty-table-wrap-inner">
	

		<h2><?php esc_html_e( 'Create your first form to collect leads.', 'mailster' ); ?></h2>

		<p><?php esc_html_e( 'With Block Forms, you have the convenience of creating standard forms and popups for lead collection with ease.', 'mailster' ); ?></p>

		<p>
			<a href="<?php echo admin_url( 'post-new.php?post_type=mailster-form' ); ?>" class="button button-primary button-hero"><?php esc_html_e( 'Create new form', 'mailster' ); ?></a>
			<a href="<?php echo mailster_url( 'https://kb.mailster.co/6460f6909a2fac195e609002' ); ?>" class="button button-secondary button-hero" data-mode="modal" data-article="6460f6909a2fac195e609002"><?php esc_html_e( 'Check out our guide', 'mailster' ); ?></a>
		</p>
		<?php if ( mailster_option( 'legacy_forms' ) ) : ?>
		<p><a href="<?php echo mailster_url( 'https://kb.mailster.co/6460f6909a2fac195e609002' ); ?>" class="button button-link" data-mode="modal" data-article="6460f6909a2fac195e609002"><?php esc_html_e( 'Learn how to convert your old forms', 'mailster' ); ?></a></p>
		<?php endif; ?>
	</div>

</div>
