<?php

$editable = ! in_array( $post->post_status, array( 'active', 'finished' ) );

if ( isset( $_GET['showstats'] ) && $_GET['showstats'] ) {
	$editable = false;
}

$listdata = wp_parse_args( $this->post_data['list_conditions'], array( 'operator' => 'OR' ) );
$ignore_lists = isset( $this->post_data['ignore_lists'] ) ? ! ! $this->post_data['ignore_lists'] : false;

$total = $this->get_totals( $post->ID );

?>
<div>
	<div id="receivers-dialog" style="display:none;">
		<div class="mailster-conditions-thickbox">
			<div class="inner">
				<p class="description">Define the conditions for your Receivers here</p>
				<?php mailster( 'conditions' )->view( isset( $listdata['conditions'] ) ? $listdata['conditions'] : array(), $listdata['operator'] ); ?>
			</div>
			<div class="foot">
				<p class="description alignleft"></p>
				<button class="button button-primary save"><?php esc_html_e( 'Save', 'mailster' ) ?></button>
				<button class="button cancel"><?php esc_html_e( 'Cancel', 'mailster' ) ?></button>
				<span class="spinner" id="conditions-ajax-loading"></span>
			</div>
		</div>
	</div>

	<div>
	<p class="lists">
		<?php


		if ( $editable ) :

			$checked = wp_parse_args( isset( $_GET['lists'] ) ? $_GET['lists'] : array(), $this->post_data['lists'] );

			mailster( 'lists' )->print_it( null, null, 'mailster_data[lists]', true, $checked );
?>
			<label><input type="checkbox" id="all_lists"> <?php esc_html_e( 'toggle all', 'mailster' );?></label>
			<ul>
				<li><label><input id="ignore_lists" type="checkbox" name="mailster_data[ignore_lists]" value="1" <?php checked( $ignore_lists ) ?>> <?php esc_html_e( 'List doesn\'t matter', 'mailster' );?> </label></li>
			</ul>

<?php

		else :

		endif;


		$meta = $this->meta( $post->ID );

		if ( $meta['ignore_lists'] ) :

			esc_html_e( 'Any List', 'mailster' );

		else :

			$list = array();

			if ( ! empty( $lists ) ) {
				esc_html_e( 'Lists', 'mailster' );
				foreach ( $lists as $list ) {
					echo ' <strong><a href="edit.php?post_type=newsletter&page=mailster_lists&ID=' . $list->ID . '">' . $list->name . '</a></strong>';
				}
			} else {
				esc_html_e( 'no lists selected', 'mailster' );
			}

		endif;
			?>
	</p>
	<?php mailster( 'conditions' )->render( isset( $listdata['conditions'] ) ? $listdata['conditions'] : array(), $listdata['operator'] ); ?>
	</div>

	<p class="textright">
		<button class="button button-small change-receivers"><?php esc_html_e( 'Change Conditions','mailster' ); ?></button>
	</p>

</div>

	<p class="totals"><?php esc_html_e( 'Total receivers', 'mailster' );?>: <span id="mailster_total"><?php echo number_format_i18n( $total ) ?></span></p>


<?php if ( ! $editable && 'autoresponder' != $post->post_status && current_user_can( 'mailster_edit_lists' ) ) : ?>

	<a class="create-new-list button" href="#"><?php esc_html_e( 'create new list', 'mailster' );?></a>
	<div class="create-new-list-wrap">
		<h4><?php esc_html_e( 'create a new list with all', 'mailster' );?></h4>
		<p>
		<select class="create-list-type">
		<?php
		$options = array(
			'sent' => __( 'who have received', 'mailster' ),
			'not_sent' => __( 'who have not received', 'mailster' ),
			'open' => __( 'who have opened', 'mailster' ),
			'open_not_click' => __( 'who have opened but not clicked', 'mailster' ),
			'click' => __( 'who have opened and clicked', 'mailster' ),
			'not_open' => __( 'who have not opened', 'mailster' ),
			);
		foreach ( $options as $id => $option ) { ?>
			<option value="<?php echo $id ?>"><?php echo $option ?></option>
		<?php } ?>
		</select>
		</p>
		<p>
			<a class="create-list button"><?php esc_html_e( 'create list', 'mailster' );?></a>
		</p>
		<p class="totals">
			<?php esc_html_e( 'Total receivers', 'mailster' );?>: <span id="mailster_total">-</span>
		</p>
	</div>
<?php endif; ?>
