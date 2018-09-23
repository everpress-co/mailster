<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Mailster_Logs_Table extends WP_List_Table {

	public function __construct() {

		parent::__construct( array(
				'singular' => __( 'Log', 'mailster' ), // singular name of the loged records
				'plural' => __( 'Logs', 'mailster' ), // plural name of the loged records
				'ajax' => false, // does this table support ajax?
		) );

		add_action( 'admin_footer', array( &$this, 'script' ) );

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_views() {

		$counts = mailster( 'logs' )->get_log_count();
		$link = 'edit.php?post_type=newsletter&page=mailster_logs';

		$views = array( 'view-all' => '<a href="' . $link . '">' . __( 'All', 'mailster' ) . ' <span class="count">(' . number_format_i18n( $counts ) . ')</span></a>' );

		return $views;
	}


	public function no_items() {

		esc_html_e( 'No log found', 'mailster' ) . '.';

		if ( current_user_can( 'mailster_add_logs' ) ) {
			echo ' <a href="edit.php?post_type=newsletter&page=mailster_logs&new">' . __( 'Add New', 'mailster' ) . '</a>';
		}

	}


	/**
	 *
	 *
	 * @param unknown $text
	 * @param unknown $input_id
	 */
	public function search_box( $text, $input_id ) {

		if ( ! count( $this->items ) && ! isset( $_GET['s'] ) ) {
			return;
		}

?>
	<form id="searchform" action method="get">
	<?php if ( isset( $_GET['post_type'] ) ) : ?><input type="hidden" name="post_type" value="<?php echo esc_attr( $_GET['post_type'] ) ?>"><?php endif; ?>
	<?php if ( isset( $_GET['page'] ) ) : ?><input type="hidden" name="page" value="<?php echo esc_attr( $_GET['page'] ) ?>"><?php endif; ?>
	<?php if ( isset( $_GET['paged'] ) ) : ?><input type="hidden" name="_paged" value="<?php echo esc_attr( $_GET['paged'] ) ?>"><?php endif; ?>
	<p class="search-box">
		<label class="screen-reader-text" for="sa-search-input"><?php echo $text; ?></label>
		<input type="search" id="<?php echo $input_id ?>" name="s" value="<?php if ( isset( $_GET['s'] ) ) {
			echo esc_attr( $_GET['s'] );
}
		?>">
		<input type="submit" name="" id="search-submit" class="button" value="<?php echo esc_attr( $text ); ?>">
	</p>
	</form>
<?php
	}


	public function filter_box() {
	}


	public function script() {
	}


	/**
	 *
	 *
	 * @param unknown $item
	 * @param unknown $column_name
	 * @return unknown
	 */
	public function column_default( $item, $column_name ) {

		switch ( $column_name ) {

			case 'campaign_id':
				$campaign = mailster( 'campaigns' )->get( $item->campaign_id );
				return '<a href="' . admin_url( 'post.php?post=' . $item->campaign_id . '&action=edit' ) . '">' . $campaign->post_title . '</a>';
			case 'subscriber_id':
				$subscriber = mailster( 'subscribers' )->get( $item->subscriber_id, true );
				return '<a href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $item->subscriber_id ) . '">' . $subscriber->fullname . '</a>';

			case 'timestamp':
				$timestamp = floor( $item->{$column_name} );
				$micros = $item->{$column_name} -$timestamp;
				$return = sprintf( __( '%s ago', 'mailster' ), human_time_diff( $timestamp ) );
				$return .= '<br><span class="tiny">' . date_i18n( 'Y-m-d H:i:s', $timestamp + mailster( 'helper' )->gmt_offset( true ) ) . ' ' . $micros . '</sapn>';
			return $return;

			case 'subscribers':
			return '<a href="' . add_query_arg( array( 'logs' => array( $item->ID ) ), 'edit.php?post_type=newsletter&page=mailster_subscribers' ) . '">' . number_format_i18n( mailster( 'logs' )->get_member_count( $item->ID, 1 ) ) . '</a>';

			default:

			return $item->{$column_name};
		}
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'timestamp' => array( 'timestamp', false ),
		);
		return $sortable_columns;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_columns() {
		return mailster( 'logs' )->get_columns();
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', 'mailster' ),
		);
		return $actions;
	}


	/**
	 *
	 *
	 * @param unknown $item
	 * @return unknown
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="logs[]" value="%s" />', $item->ID
		);
	}


	/**
	 *
	 *
	 * @param unknown $current_mode
	 * @return unknown
	 */
	public function view_switcher( $current_mode ) {
		return '';
	}


	/**
	 *
	 *
	 * @param unknown $domain  (optional)
	 * @param unknown $post_id (optional)
	 */
	public function prepare_items( $domain = null, $post_id = null ) {

		global $wpdb;
		$screen = get_current_screen();
		$columns = $this->get_columns();
		$hidden = get_hidden_columns( $screen );
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$extrasql = '';

		$orderby = ! empty( $_GET['orderby'] ) ? esc_sql( $_GET['orderby'] ) : 'timestamp';
		$order = ! empty( $_GET['order'] ) ? esc_sql( $_GET['order'] ) : 'DESC';

		$sql = 'SELECT a.*';

		$sql .= " FROM {$wpdb->prefix}mailster_logs AS a";

		$extrasql .= ' WHERE 1';

		if ( isset( $_GET['s'] ) ) {
			$search = trim( addcslashes( esc_sql( $_GET['s'] ), '%_' ) );
			$search = explode( ' ', $search );

			$extrasql .= ' AND (';
			$terms = array();
			foreach ( $search as $term ) {

				if ( substr( $term, 0, 1 ) == '-' ) {
					$term = substr( $term, 1 );
					$operator = 'AND';
					$like = 'NOT LIKE';
					$end = '(1=1)';
				} else {
					$operator = 'OR';
					$like = 'LIKE';
					$end = '(1=0)';
				}

				$termsql = ' ( ';
				$termsql .= " (a.type $like '%" . $term . "%') $operator ";
				$termsql .= " (a.text $like '%" . $term . "%') $operator ";
				$termsql .= " $end )";

				$terms[] = $termsql;

			}

			$extrasql .= implode( ' AND ', $terms ) . ')';

		}

		$sql .= $extrasql;

		$sql .= ' GROUP BY a.ID';

		if ( ! empty( $orderby ) && ! empty( $order ) ) {
			$sql .= ' ORDER BY ' . $orderby . ' ' . $order;
			if ( '_sort' == $orderby ) {
				$sql .= ', name ' . $order;
			}
		}

		$totalitems = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}mailster_logs" );

		// How many to display per page?
		$perpage = 50;
		// Which page is this?
		$paged = ! empty( $_GET['paged'] ) ? esc_sql( $_GET['paged'] ) : '';
		// Page Number
		if ( empty( $paged ) || ! is_numeric( $paged ) || $paged <= 0 ) {$paged = 1;}
		// How many pages do we have in total?
		$totalpages = ceil( $totalitems / $perpage );
		// adjust the query to take pagination into account
		if ( ! empty( $paged ) && ! empty( $perpage ) ) {
			$offset = ( $paged - 1 ) * $perpage;
		}

		$this->set_pagination_args( array(
			'total_items' => $totalitems,
			'total_pages' => $totalpages,
			'per_page' => $perpage,
		) );

		if ( isset( $offset ) ) {
			$sql .= " LIMIT $offset, $perpage";
		}

		$sql = apply_filters( 'mailster_logs_prepare_items_sql', $sql );

		if ( isset( $allitems ) ) {
			$this->items = isset( $offset ) && isset( $perpage ) ? array_slice( $allitems, (int) $offset, (int) $perpage ) : $allitems;
		} else {
			$this->items = $wpdb->get_results( $sql );
		}

	}


}
