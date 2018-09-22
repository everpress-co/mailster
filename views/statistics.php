<?php wp_nonce_field( 'mailster_nonce', 'mailster_nonce', false ); ?>
<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
<?php

$classes = array( 'wrap', 'mailster-statistics' );

$dateformat = mailster( 'helper' )->dateformat();

$date_from = time() -WEEK_IN_SECONDS;
$date_to = time() -DAY_IN_SECONDS;


?>
<div class="<?php echo implode( ' ', $classes ) ?>">
<h1><?php esc_html_e( 'Statistics', 'mailster' ); ?>
	<div class="date-range"> from <?php printf( '%1$s &ndash; %2$s', date( $dateformat, $date_from ), date( $dateformat, $date_to ) ) ?> ▼
		<div class="date-range-dropdown stuffbox">
			<?php esc_html_e( 'Date Range', 'mailster' ) ?>
			<select class="widefat date-range-select">
				<option value="today"><?php esc_html_e( 'Today', 'mailster' ) ?></option>
				<option value="yesterday"><?php esc_html_e( 'Yesterday', 'mailster' ) ?></option>
				<option value="last_7_days"><?php esc_html_e( 'Last 7 Days', 'mailster' ) ?></option>
				<option value="last_week"><?php esc_html_e( 'Last Week', 'mailster' ) ?></option>
				<option value="this_month"><?php esc_html_e( 'This Month', 'mailster' ) ?></option>
				<option value="last_month"><?php esc_html_e( 'Last Month', 'mailster' ) ?></option>
				<option value="last_12_month"><?php esc_html_e( 'Last 12 Month', 'mailster' ) ?></option>
				<option value=""><?php esc_html_e( 'Custom Dates', 'mailster' ) ?></option>
			</select>
			<input type="text" class="datepicker widefat date-range-from" name="" value="<?php echo esc_attr( date( 'Y-m-d', $date_from ) ) ?>"> &ndash;
			<input type="text" class="datepicker widefat date-range-to" name="" value="<?php echo esc_attr( date( 'Y-m-d', $date_to ) ) ?>">
		</div>
	</div>
</h1>

	<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder">
			<div id="postbox-container-1" class="postbox-container" data-id="normal">
				<?php do_meta_boxes( $this->screen->id, 'normal', '' ); ?>
			</div>
			<div id="postbox-container-2" class="postbox-container" data-id="side">
				<?php do_meta_boxes( $this->screen->id, 'side', '' ); ?>
			</div>
			<div id="postbox-container-3" class="postbox-container" data-id="column3">
				<?php do_meta_boxes( $this->screen->id, 'column3' , '' ); ?>
			</div>
			<div id="postbox-container-4" class="postbox-container" data-id="column4">
				<?php do_meta_boxes( $this->screen->id, 'column4', '' ); ?>
			</div>
		</div>
	</div>

<div id="ajax-response"></div>
<br class="clear">
</div>
