<?php

$editable = ! in_array( $post->post_status, array( 'active', 'finished' ) );

if ( isset( $_GET['showstats'] ) && $_GET['showstats'] ) {
	$editable = false;
}

$listdata = wp_parse_args( $this->post_data['list_conditions'], array( 'operator' => 'OR' ) );
$ignore_lists = isset( $this->post_data['ignore_lists'] ) ? ! ! $this->post_data['ignore_lists'] : false;

if ( $editable ) :

	$total = $this->get_totals( $post->ID );

	?>
		<div class="">

			<div id="receivers-dialog" style="display:none;">
				<div>
				<div id="list-checkboxes" <?php if ( $ignore_lists ) { echo ' style="display:none"'; } ?>>
				<label><input type="checkbox" id="all_lists"> <?php esc_html_e( 'toggle all', 'mailster' );?></label>
					<div>
						<ul class="default">
							<?php
							$checked = wp_parse_args( isset( $_GET['lists'] ) ? $_GET['lists'] : array(), $this->post_data['lists'] );

							mailster( 'lists' )->print_it( null, null, 'mailster_data[lists]', true, $checked );

							?>
						</ul>

					</div>
				</div>
				<ul>
					<li><label><input id="ignore_lists" type="checkbox" name="mailster_data[ignore_lists]" value="1" <?php checked( $ignore_lists ) ?>> <?php esc_html_e( 'List doesn\'t matter', 'mailster' );?> </label></li>
				</ul>
				<?php
					mailster( 'conditions' )->view( isset( $listdata['conditions'] ) ? $listdata['conditions'] : array(), $listdata['operator'] );
					?>
				<div class="buttons clearfix">
					<button class="button button-primary save"><?php esc_html_e( 'Save', 'mailster' ) ?></button>
					<button class="button cancel"><?php esc_html_e( 'Cancel', 'mailster' ) ?></button>
				</div>
				</div>
			</div>
			<div>
				<?php
					mailster( 'conditions' )->render( isset( $listdata['conditions'] ) ? $listdata['conditions'] : array(), $listdata['operator'] );
					?>
			</div>
			<a class="button change-receivers"><?php esc_html_e( 'Change Conditions','mailster' ); ?></a>

		</div>
			<p class="totals"><?php esc_html_e( 'Total receivers', 'mailster' );?>: <span id="mailster_total"><?php echo number_format_i18n( $total ) ?></span></p>

	<?php else : ?>

	<div>
		<p class="lists">
	<?php

	$meta = $this->meta( $post->ID );

	if ( $meta['ignore_lists'] ) {

		esc_html_e( 'Any List', 'mailster' );

	} else {

		$lists = $this->get_lists( $post->ID );

		if ( ! empty( $lists ) ) {
			echo __( 'Lists', 'mailster' ) . ':<br>';
			foreach ( $lists as $list ) {
				echo ' <strong><a href="edit.php?post_type=newsletter&page=mailster_lists&ID=' . $list->ID . '">' . $list->name . '</a></strong>';
			}
		} else {
			esc_html_e( 'no lists selected', 'mailster' );
		}
	}
		?>
		</p>
		<?php
		if ( isset( $listdata['conditions'] ) ) {
			$fields = array(
			'email' => mailster_text( 'email' ),
			'firstname' => mailster_text( 'firstname' ),
			'lastname' => mailster_text( 'lastname' ),
				);

				$customfields = mailster()->get_custom_fields();

			foreach ( $customfields as $field => $data ) {
				$fields[ $field ] = $data['name'];
			}

			$meta = array(
				'form' => __( 'Form ID', 'mailster' ),
				'referer' => __( 'Referer', 'mailster' ),
				'ip' => __( 'IP Address', 'mailster' ),
				'signup' => __( 'Signup Date', 'mailster' ),
				'ip_signup' => __( 'Signup IP', 'mailster' ),
				'confirm' => __( 'Confirm Date', 'mailster' ),
				'ip_confirm' => __( 'Confirm IP', 'mailster' ),
				'rating' => __( 'Rating', 'mailster' ),
			);

			$wp_meta = wp_parse_args( mailster( 'helper' )->get_wpuser_meta_fields(), array(
				'wp_capabilities' => __( 'User Role', 'mailster' ),
				'wp_user_level' => __( 'User Level', 'mailster' ),
			) );

			foreach ( $meta as $field => $name ) {
				$fields[ $field ] = $name;
			}

			echo '<p>' . __( 'only if', 'mailster' ) . '<br>';

			$conditions = array();
			$operators = array(
				'is' => __( 'is', 'mailster' ),
				'is_not' => __( 'is not', 'mailster' ),
				'contains' => __( 'contains', 'mailster' ),
				'contains_not' => __( 'contains not', 'mailster' ),
				'begin_with' => __( 'begins with', 'mailster' ),
				'end_with' => __( 'ends with', 'mailster' ),
				'is_greater' => __( 'is greater', 'mailster' ),
				'is_smaller' => __( 'is smaller', 'mailster' ),
				'is_greater_equal' => __( 'is greater or equal', 'mailster' ),
				'is_smaller_equal' => __( 'is smaller or equal', 'mailster' ),
				'pattern' => __( 'match regex pattern', 'mailster' ),
				'not_pattern' => __( 'does not match regex pattern', 'mailster' ),
			);

			foreach ( $listdata['conditions'] as $condition ) {
				if ( ( ! isset( $fields[ $condition['field'] ] ) && ( ! isset( $wp_meta[ $condition['field'] ] ) ) ) ) {
					echo '<span class="mailster-icon warning"></span> ' . sprintf( __( '%s is missing!', 'mailster' ), '"' . $condition['field'] . '"' ) . '<br>';
					continue;
				}
				$conditions[] = '<strong>' . $fields[ $condition['field'] ] . '</strong> ' . $operators[ $condition['operator'] ] . ' "<strong>' . $condition['value'] . '</strong>"';
			}

			echo implode( '<br>' . strtolower( $listdata['operator'] ) . ' ', $conditions ) . '</p>';

		} ?>
</div>

<?php if ( 'autoresponder' != $post->post_status && current_user_can( 'mailster_edit_lists' ) ) : ?>

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
<?php endif; ?>
