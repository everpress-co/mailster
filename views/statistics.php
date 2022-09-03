<?php wp_nonce_field( 'mailster_nonce', 'mailster_nonce', false ); ?>
<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
<?php

$classes = array( 'wrap', 'mailster-statistics' );

$dateformat = mailster( 'helper' )->dateformat();

$date_from = time() - WEEK_IN_SECONDS;
$date_to   = time() - DAY_IN_SECONDS;
$from      = date( 'Y-m-d', $date_from );
$to        = date( 'Y-m-d', $date_to );

function date_value( $s1 = '', $s2 = '' ) {
	return date( 'Y-m-d', strtotime( $s1 ) ) . '_' . date( 'Y-m-d', strtotime( $s2 ) );
}

?>
<div class="<?php echo implode( ' ', $classes ); ?>">
<h1><?php esc_html_e( 'Statistics', 'mailster' ); ?>
	<div class="date-range"><span class="date-range-wording">asdasds</span>
		<div class="date-range-dropdown stuffbox">
			<div>
				<?php esc_html_e( 'Date Range', 'mailster' ); ?>
				<select class="widefat date-range-select">
					<option value="<?php esc_attr_e( date_value( 'today', 'today' ) ); ?>"><?php esc_html_e( 'Today', 'mailster' ); ?></option>
					<option value="<?php esc_attr_e( date_value( 'yesterday', 'yesterday' ) ); ?>"><?php esc_html_e( 'Yesterday', 'mailster' ); ?></option>
					<option value="<?php esc_attr_e( date_value( '- 1 week', 'yesterday' ) ); ?>"><?php esc_html_e( 'Last 7 Days', 'mailster' ); ?></option>
					<option value="<?php esc_attr_e( date_value( '- 2 week', 'yesterday - 1 week ' ) ); ?>"><?php esc_html_e( 'Previous 7 Days', 'mailster' ); ?></option>
					<option value="<?php esc_attr_e( date_value( 'last sunday', 'next sunday' ) ); ?>"><?php esc_html_e( 'This Week', 'mailster' ); ?></option>
					<option value="<?php esc_attr_e( date_value( '-2 sunday', '-1 sunday' ) ); ?>"><?php esc_html_e( 'Last Week', 'mailster' ); ?></option>
					<option value="<?php esc_attr_e( date_value( '-3 sunday', '-2 sunday' ) ); ?>"><?php esc_html_e( 'Last Last Week', 'mailster' ); ?></option>
					<option value="<?php esc_attr_e( date_value( 'first day of this month', 'last day of this month' ) ); ?>"><?php esc_html_e( 'This Month', 'mailster' ); ?></option>
					<option value="<?php esc_attr_e( date_value( 'first day of last month', 'last day of last month' ) ); ?>"><?php esc_html_e( 'Last Month', 'mailster' ); ?></option>
					<option value="<?php esc_attr_e( date_value( '- 13 month', 'last day of last month' ) ); ?>"><?php esc_html_e( 'Last 12 Month', 'mailster' ); ?></option>
					<option value="custom"><?php esc_html_e( 'Custom Dates', 'mailster' ); ?></option>
				</select>
				<input type="date" class="widefat date-range-from" name="" value="<?php echo esc_attr( $from ); ?>"> &ndash;
				<input type="date" class="widefat date-range-to" name="" value="<?php echo esc_attr( $to ); ?>">
			</div>
		</div>
	</div>
</h1>

	<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder">
			<div id="postbox-container-1" class="postbox-container" data-id="normal">
				<?php do_meta_boxes( 'newsletter_page_mailster_statistics', 'normal', '' ); ?>
			</div>
			<div id="postbox-container-2" class="postbox-container" data-id="side">
				<?php do_meta_boxes( 'newsletter_page_mailster_statistics', 'side', '' ); ?>
			</div>
			<div id="postbox-container-3" class="postbox-container" data-id="column3">
				<?php do_meta_boxes( 'newsletter_page_mailster_statistics', 'column3', '' ); ?>
			</div>
			<div id="postbox-container-4" class="postbox-container" data-id="column4">
				<?php do_meta_boxes( 'newsletter_page_mailster_statistics', 'column4', '' ); ?>
			</div>
		</div>
	</div>


</div>