<?php

$editable = ! in_array( $post->post_status, array( 'active', 'finished' ) );
if ( isset( $_GET['showstats'] ) && $_GET['showstats'] ) {
	$editable = false;
}
?>
<p><?php esc_html_e( 'The Goal of this campaign should be', 'mailster' ); ?></p>
<select class="widefat" name="mailster_data[goal]">
	<option value="" <?php selected( ! $this->post_data['goal'] ); ?>><?php esc_html_e( 'No Goal defined', 'mailster' ); ?></option>
	<option value="open" <?php selected( $this->post_data['goal'], 'open' ); ?>><?php esc_html_e( 'Open the campaign', 'mailster' ); ?></option>
	<option value="click" <?php selected( $this->post_data['goal'], 'click' ); ?>><?php esc_html_e( 'Click a link or button', 'mailster' ); ?></option>
</select>
<p class="howto"><?php esc_html_e( 'A goal help you keep track of the success of the campaign and let you track the results.', 'mailster' ); ?></p>
<?php if ( $editable ) : ?>
<?php else : ?>
<?php endif; ?>
