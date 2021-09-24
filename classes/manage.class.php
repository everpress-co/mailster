<?php

class MailsterManage {

	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ) );

	}


	public function init() {

		add_action( 'admin_menu', array( &$this, 'add_menu' ), 40 );
		add_action( 'wp_ajax_mailster_import_subscribers_upload_handler', array( &$this, 'ajax_import_subscribers_upload_handler' ) );
		add_action( 'wp_ajax_mailster_get_import_data', array( &$this, 'ajax_get_import_data' ) );
		add_action( 'wp_ajax_mailster_do_import', array( &$this, 'ajax_do_import' ) );
		add_action( 'wp_ajax_mailster_export_contacts', array( &$this, 'ajax_export_contacts' ) );
		add_action( 'wp_ajax_mailster_do_export', array( &$this, 'ajax_do_export' ) );
		add_action( 'wp_ajax_mailster_download_export_file', array( &$this, 'ajax_download_export_file' ) );
		add_action( 'wp_ajax_mailster_delete_contacts', array( &$this, 'ajax_delete_contacts' ) );
		add_action( 'wp_ajax_mailster_delete_old_bulk_jobs', array( &$this, 'ajax_delete_old_bulk_jobs' ) );

		add_action( 'mailster_import_method', array( &$this, 'display_import_method' ) );

		add_action( 'admin_init', array( &$this, 'admin_enqueue_scripts' ) );

	}


	public function admin_enqueue_scripts() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'mailster-manage-script', MAILSTER_URI . 'assets/js/manage-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );

		wp_register_script( 'mailster-import-script', MAILSTER_URI . 'assets/js/import-script' . $suffix . '.js', array( 'mailster-script', 'plupload-all' ), MAILSTER_VERSION, true );
		wp_register_script( 'mailster-export-script', MAILSTER_URI . 'assets/js/export-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );
		wp_register_script( 'mailster-delete-script', MAILSTER_URI . 'assets/js/delete-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );

		wp_register_style( 'mailster-manage-style', MAILSTER_URI . 'assets/css/manage-style' . $suffix . '.css', array(), MAILSTER_VERSION );

		wp_register_style( 'mailster-import-style', MAILSTER_URI . 'assets/css/import-style' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_register_style( 'mailster-export-style', MAILSTER_URI . 'assets/css/export-style' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_register_style( 'mailster-delete-style', MAILSTER_URI . 'assets/css/delete-style' . $suffix . '.css', array(), MAILSTER_VERSION );

	}


	public function add_menu() {

		$page = add_submenu_page( 'edit.php?post_type=newsletter', esc_html__( 'Manage Subscribers', 'mailster' ), esc_html__( 'Manage Subscribers', 'mailster' ), 'mailster_manage_subscribers', 'mailster_manage_subscribers', array( &$this, 'subscriber_manage' ) );
		add_action( 'load-' . $page, array( &$this, 'scripts_styles' ) );

	}


	public function scripts_styles() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'mailster-manage-script' );
		wp_enqueue_script( 'mailster-import-script' );

		global $wp_locale;

		mailster_localize_script(
			'conditions',
			array(
				'next'          => esc_html__( 'next', 'mailster' ),
				'prev'          => esc_html__( 'prev', 'mailster' ),
				'start_of_week' => get_option( 'start_of_week' ),
				'day_names'     => $wp_locale->weekday,
				'day_names_min' => array_values( $wp_locale->weekday_abbrev ),
				'month_names'   => array_values( $wp_locale->month ),
			)
		);
		mailster_localize_script(
			'manage',
			array(
				'select_status'        => esc_html__( 'Please select the status for the importing contacts!', 'mailster' ),
				'select_emailcolumn'   => esc_html__( 'Please select at least the column with the email addresses!', 'mailster' ),
				'import_contacts'      => esc_html__( 'Importing Contacts...%s', 'mailster' ),
				'prepare_import'       => esc_html__( 'Preparing Import...', 'mailster' ),
				'current_stats'        => esc_html__( 'Currently %1$s of %2$s imported with %3$s errors. %4$s memory usage', 'mailster' ),
				'estimate_time'        => esc_html__( 'Estimate time left: %s minutes', 'mailster' ),
				'continues_in'         => esc_html__( 'Continues in %s seconds', 'mailster' ),
				'error_importing'      => esc_html__( 'There was a problem during importing contacts. Please check the error logs for more information!', 'mailster' ),
				'prepare_download'     => esc_html__( 'Preparing Download for %1$s Subscribers...%2$s', 'mailster' ),
				'write_file'           => esc_html__( 'Writing file: %s', 'mailster' ),
				'export_finished'      => esc_html__( 'Export finished', 'mailster' ),
				'downloading'          => esc_html__( 'Downloading %s Subscribers...', 'mailster' ),
				'error_export'         => esc_html__( 'There was an error while exporting', 'mailster' ),
				'confirm_import'       => esc_html__( 'Do you really like to import these contacts?', 'mailster' ),
				'import_complete'      => esc_html__( 'Import complete!', 'mailster' ),
				'choose_tags'          => esc_html__( 'Choose your tags.', 'mailster' ),
				'confirm_delete'       => esc_html__( 'You are about to delete these subscribers permanently. This step is irreversible!', 'mailster' ) . "\n" . sprintf( esc_html__( 'Type %s to confirm deletion', 'mailster' ), '"DELETE"' ),
				'delete_n_subscribers' => esc_html__( 'Delete %s Subscribers permanently', 'mailster' ),
				'onbeforeunloadimport' => esc_html__( 'You are currently importing subscribers! If you leave the page all pending subscribers don\'t get imported!', 'mailster' ),
				'onbeforeunloadexport' => esc_html__( 'Your download is preparing! If you leave this page the progress will abort!', 'mailster' ),
			)
		);
		mailster_localize_script(
			'import',
			array(
				'prepare_data' => esc_html__( 'Preparing Data', 'mailster' ),
				'uploading'    => esc_html__( 'Uploading...%s', 'mailster' ),
			)
		);

		wp_enqueue_style( 'mailster-select2', MAILSTER_URI . 'assets/css/libs/select2' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_enqueue_style( 'mailster-select2-theme', MAILSTER_URI . 'assets/css/select2' . $suffix . '.css', array( 'mailster-select2' ), MAILSTER_VERSION );
		wp_enqueue_script( 'mailster-select2', MAILSTER_URI . 'assets/js/libs/select2' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION, true );

		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-touch-punch' );
		wp_enqueue_style( 'mailster-manage-style' );
		wp_enqueue_style( 'mailster-import-style' );

		wp_enqueue_style( 'jquery-style', MAILSTER_URI . 'assets/css/libs/jquery-ui' . $suffix . '.css' );
		wp_enqueue_style( 'jquery-datepicker', MAILSTER_URI . 'assets/css/datepicker' . $suffix . '.css' );

		wp_enqueue_script( 'jquery-ui-datepicker' );

	}


	public function subscriber_manage() {

		include MAILSTER_DIR . 'views/manage.php';
	}


	public function ajax_import_subscribers_upload_handler() {

		global $wpdb;

		$memory_limit       = ini_get( 'memory_limit' );
		$max_execution_time = ini_get( 'max_execution_time' );
		$header             = false;

		set_time_limit( 0 );

		if ( (int) $max_execution_time < 300 ) {
			ini_set( 'max_execution_time', 300 );
		}
		if ( (int) $memory_limit < 256 ) {
			ini_set( 'memory_limit', '256M' );
		}

		if ( isset( $_FILES['async-upload'] ) ) {

			if ( ! current_user_can( 'mailster_import_subscribers' ) ) {
				die( 'not allowed' );
			}

			$file     = $_FILES['async-upload'];
			$raw_data = ( file_get_contents( $file['tmp_name'] ) );

			if ( function_exists( 'mb_convert_encoding' ) ) {
				$raw_data = mb_convert_encoding( $raw_data, 'UTF-8', mb_detect_encoding( $raw_data, 'UTF-8, ISO-8859-1', true ) );
			}
		} elseif ( isset( $_POST['data'] ) ) {

			$return['success'] = false;

			$this->ajax_nonce( json_encode( $return ) );

			if ( ! current_user_can( 'mailster_import_subscribers' ) ) {

				wp_send_json( $return );
			}

			$raw_data = esc_textarea( stripslashes( $_POST['data'] ) );
			// single quotes cause problems
			$raw_data = str_replace( '&#039;', "'", $raw_data );

		} elseif ( isset( $_POST['wordpressusers'] ) ) {

			if ( ! current_user_can( 'mailster_import_wordpress_users' ) ) {

				wp_send_json( $return );
			}

			parse_str( $_POST['wordpressusers'], $data );

			$roles       = isset( $data['roles'] ) ? (array) $data['roles'] : array();
			$no_role     = isset( $data['no_role'] );
			$meta_values = isset( $data['meta_values'] ) ? (array) $data['meta_values'] : array();

			$sql = "SELECT u.user_email, IF(meta_role.meta_value = 'a:0:{}',NULL,meta_role.meta_value) AS '_role', meta_firstname.meta_value AS 'firstname', meta_lastname.meta_value AS 'lastname', u.display_name, u.user_nicename, u.user_registered";

			foreach ( $meta_values as $i => $meta_value ) {
				$sql .= ", meta_$i.meta_value AS '$meta_value'";
			}

			$sql .= " FROM {$wpdb->users} AS u";
			$sql .= " LEFT JOIN {$wpdb->usermeta} AS meta_role ON meta_role.user_id = u.id AND meta_role.meta_key = '{$wpdb->prefix}capabilities'";
			$sql .= " LEFT JOIN {$wpdb->usermeta} AS meta_firstname ON meta_firstname.user_id = u.id AND meta_firstname.meta_key = 'first_name'";
			$sql .= " LEFT JOIN {$wpdb->usermeta} AS meta_lastname ON meta_lastname.user_id = u.id AND meta_lastname.meta_key = 'last_name'";
			foreach ( $meta_values as $i => $meta_value ) {
				$sql .= " LEFT JOIN {$wpdb->usermeta} AS meta_$i ON meta_$i.user_id = u.id AND meta_$i.meta_key = '$meta_value'";
			}

			$sql .= ' WHERE meta_role.user_id IS NOT NULL';

			$users = $wpdb->get_results( $sql );

			$header = array_merge(
				array(
					mailster_text( 'email' ),
					mailster_text( 'firstname' ),
					mailster_text( 'lastname' ),
					esc_html__( 'nickname', 'mailster' ),
					esc_html__( 'display name', 'mailster' ),
					esc_html__( 'registered', 'mailster' ),
				),
				$meta_values
			);

			$raw_data = '';

			foreach ( $users as $user ) {

				$line = '';

				// no role set and roles is a must
				if ( ! $user->_role && ! $no_role ) {
					continue;
				}

				// role is set but not in the list
				if ( $user->_role && ! array_intersect( array_keys( unserialize( $user->_role ) ), $roles ) ) {
					continue;
				}

				foreach ( $user as $key => $data ) {
					if ( $key == '_role' ) {
						continue;
					}

					if ( $key == 'firstname' && ! $data ) {
						$line = $user->display_name;
					}

					$line .= $data . ';';
				}

				$raw_data .= substr( $line, 0, -1 ) . "\n";

			}
		} else {

			die( 'not allowed' );

		}

		$raw_data = ( trim( str_replace( array( "\r", "\r\n", "\n\n" ), "\n", $raw_data ) ) );

		if ( function_exists( 'mb_convert_encoding' ) ) {
			$encoding = mb_detect_encoding( $raw_data, 'auto' );
		} else {
			$encoding = 'UTF-8';
		}
		if ( $encoding != 'UTF-8' ) {
			$raw_data = utf8_encode( $raw_data );
			$encoding = mb_detect_encoding( $raw_data, 'auto' );
		}
		$lines     = explode( "\n", $raw_data );
		$parts     = array_chunk( $lines, max( 50, round( count( $lines ) / 200 ) ) );
		$partcount = count( $parts );

		$bulkimport = array(
			'ids'       => array(),
			'imported'  => 0,
			'errors'    => 0,
			'encoding'  => $encoding,
			'parts'     => $partcount,
			'lines'     => count( $lines ),
			'header'    => $header,
			'separator' => $this->get_separator( implode( $parts[0] ) ),
		);

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		if ( false !== $wpdb->query( "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mailster_temp_import (ID bigint(20) NOT NULL AUTO_INCREMENT, data longtext NOT NULL, identifier char(13) NOT NULL, PRIMARY KEY (ID) ) $collate" ) ) {
			$return['identifier'] = $identifier = uniqid();

			for ( $i = 0; $i < $partcount; $i++ ) {

				$part = $parts[ $i ];

				// remove quotes;
				$part = str_replace( array( "'" . $bulkimport['separator'] . "'", '"' . $bulkimport['separator'] . '"' ), $bulkimport['separator'], $part );
				$part = preg_replace( '#^("|\')#', '', $part );
				$part = preg_replace( '#("|\')$#', '', $part );

				$new_value = base64_encode( serialize( $part ) );

				$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}mailster_temp_import (data, identifier) VALUES (%s, %s)", $new_value, $identifier ) );

				$bulkimport['ids'][] = $i;
			}

			$return['success']     = true;
			$return['memoryusage'] = size_format( memory_get_peak_usage( true ), 2 );
			if ( get_option( 'mailster_bulk_import' ) !== false ) {
				update_option( 'mailster_bulk_import', $bulkimport );
			} else {
				add_option( 'mailster_bulk_import', $bulkimport, '', 'no' );
			}
		} else {
			$return['message'] = $wpdb->last_error;
		}

		if ( isset( $return ) ) {

			wp_send_json( $return );
		}

	}


	public function ajax_get_import_data() {

		global $wpdb;

		$return['success'] = false;

		$this->ajax_nonce( json_encode( $return ) );

		if ( ! current_user_can( 'mailster_import_subscribers' ) ) {

			wp_send_json( $return );
		}

		$return['identifier'] = $identifier = $_POST['identifier'];

		$return['data'] = get_option( 'mailster_bulk_import' );

		// get first and last entry
		$entries = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT
(SELECT data FROM {$wpdb->prefix}mailster_temp_import WHERE identifier = %s ORDER BY ID ASC LIMIT 1) AS first, (SELECT data FROM {$wpdb->prefix}mailster_temp_import WHERE identifier = %s ORDER BY ID DESC LIMIT 1) AS last",
				$identifier,
				$identifier
			)
		);

		$first = unserialize( base64_decode( $entries->first ) );
		$last  = unserialize( base64_decode( $entries->last ) );

		$firstline    = explode( $return['data']['separator'], $first[0] );
		$data         = explode( $return['data']['separator'], $first[ count( $first ) - 1 ] );
		$cols         = count( $data );
		$contactcount = $return['data']['lines'];
		$header       = $return['data']['header'];

		$custom_fields = mailster()->get_custom_fields();

		$fields     = array(
			'email'      => mailster_text( 'email' ),
			'firstname'  => mailster_text( 'firstname' ),
			'lastname'   => mailster_text( 'lastname' ),
			'first_last' => mailster_text( 'firstname' ) . '&#x23B5;' . mailster_text( 'lastname' ),
			'last_first' => mailster_text( 'lastname' ) . '&#x23B5;' . mailster_text( 'firstname' ),
		);
		$meta_dates = array(
			'_signup'         => esc_html__( 'Signup Date', 'mailster' ),
			'_confirm'        => esc_html__( 'Confirm Date', 'mailster' ),
			'_confirm_signup' => esc_html__( 'Signup + Confirm Date', 'mailster' ),
		);
		$meta_ips   = array(
			'_ip'                => esc_html__( 'IP Address', 'mailster' ),
			'_ip_signup'         => esc_html__( 'Signup IP Address', 'mailster' ),
			'_ip_confirm'        => esc_html__( 'Confirm IP Address', 'mailster' ),
			'_ip_confirm_signup' => esc_html__( 'Confirm + Signup IP Address', 'mailster' ),
			'_ip_all'            => esc_html__( 'all IP Addresses', 'mailster' ),
		);
		$meta_other = array(
			'_lists'  => esc_html__( 'Lists', 'mailster' ) . ' (' . esc_html__( 'comma separated', 'mailster' ) . ')',
			'_tags'   => esc_html__( 'Tags', 'mailster' ) . ' (' . esc_html__( 'comma separated', 'mailster' ) . ')',
			'_status' => esc_html__( 'Status', 'mailster' ) . ' [0...6]',
			'_lang'   => esc_html__( 'Language', 'mailster' ),
		);

		$html       = '<h2>' . esc_html__( 'Select columns', 'mailster' ) . '</h2>';
		$html      .= '<p class="description">' . esc_html__( 'Define which column represents which field', 'mailster' ) . '</p>';
		$html      .= '<form id="subscriber-table" class="stuffbox"><table class="wp-list-table widefat">';
		$html      .= '<thead><tr><td style="width:20px;">#</td>';
		$emailfield = false;
		for ( $i = 0; $i < $cols; $i++ ) {
			$ismail     = mailster_is_email( trim( $data[ $i ] ) );
			$header_col = ( $header && isset( $header[ $i ] ) ) ? $header[ $i ] : '';
			$select     = '<select name="order[]">';
			$select    .= '<option value="-1">' . esc_html__( 'Ignore column', 'mailster' ) . '</option>';
			$select    .= '<option value="-1">----------</option>';
			$select    .= '<optgroup label="' . esc_html__( 'Basic', 'mailster' ) . '">';
			foreach ( $fields as $key => $value ) {
				$is_selected = ( ( $ismail && $key == 'email' && ! $emailfield && $emailfield = true ) ||
					( strip_tags( $header_col ) == mailster_text( 'firstname' ) && $key == 'firstname' ) ||
					( strip_tags( $header_col ) == mailster_text( 'lastname' ) && $key == 'lastname' ) );

				$select .= '<option value="' . esc_attr( $key ) . '" ' . ( $is_selected ? 'selected' : '' ) . '>' . esc_html( $value ) . '</option>';
			}
			$select .= '</optgroup>';
			if ( ! empty( $custom_fields ) ) {
				$select .= '<optgroup label="' . esc_html__( 'Custom Fields', 'mailster' ) . '">';
				foreach ( $custom_fields as $key => $d ) {
					$select .= '<option value="' . esc_attr( $key ) . '">' . $d['name'] . '</option>';
				}
				$select .= '</optgroup>';
			} else {
				$select .= '<optgroup label="' . esc_html__( 'no Custom Fields defined!', 'mailster' ) . '">';
				$select .= '</optgroup>';
			}
			$select .= '<optgroup label="' . esc_html__( 'Time Options', 'mailster' ) . '">';
			foreach ( $meta_dates as $key => $value ) {
				$is_selected = ( ( strip_tags( $firstline[ $i ] ) == esc_html__( 'registered', 'mailster' ) && $key == '_signup' ) );
				$select     .= '<option value="' . esc_attr( $key ) . '" ' . ( $is_selected ? 'selected' : '' ) . '>' . esc_html( $value ) . '</option>';
			}
			$select .= '</optgroup>';
			$select .= '<optgroup label="' . esc_html__( 'IP Options', 'mailster' ) . '">';
			foreach ( $meta_ips as $key => $value ) {
				$is_selected = ( ( strip_tags( $firstline[ $i ] ) == esc_html__( 'registered', 'mailster' ) && $key == '_signup' ) );
				$select     .= '<option value="' . esc_attr( $key ) . '" ' . ( $is_selected ? 'selected' : '' ) . '>' . esc_html( $value ) . '</option>';
			}
			$select .= '</optgroup>';
			$select .= '<optgroup label="' . esc_html__( 'Other Meta', 'mailster' ) . '">';
			foreach ( $meta_other as $key => $value ) {
				$is_selected = ( ( strip_tags( $firstline[ $i ] ) == esc_html__( 'registered', 'mailster' ) && $key == '_signup' ) );
				$select     .= '<option value="' . esc_attr( $key ) . '" ' . ( $is_selected ? 'selected' : '' ) . '>' . esc_html( $value ) . '</option>';
			}
			$select .= '</optgroup>';
			$select .= '</select>';
			$html   .= '<td>' . $select . '</td>';
		}
		$html .= '</tr></thead>';

		$html .= '<tbody>';
		for ( $i = 0; $i < min( 10, $contactcount ); $i++ ) {
			$data  = explode( $return['data']['separator'], ( $first[ $i ] ) );
			$html .= '<tr class="' . ( $i % 2 ? '' : 'alternate' ) . '"><td>' . number_format_i18n( $i + 1 ) . '</td>';
			foreach ( $data as $cell ) {
				$html .= '<td title="' . strip_tags( $cell ) . '">' . esc_html( $cell ) . '</td>';
			}
			$html .= '<tr>';
		}
		if ( $contactcount > 10 ) {
			$html .= '<tr class="alternate"><td>&nbsp;</td><td colspan="' . ( $cols ) . '"><span class="description">&hellip;' . sprintf( esc_html__( '%s contacts are hidden', 'mailster' ), number_format_i18n( $contactcount - 11 ) ) . '&hellip;</span></td>';

			$data  = explode( $return['data']['separator'], array_pop( $last ) );
			$html .= '<tr><td>' . number_format_i18n( $contactcount ) . '</td>';
			foreach ( $data as $cell ) {
				$html .= '<td title="' . strip_tags( $cell ) . '">' . esc_html( $cell ) . '</td>';
			}
			$html .= '<tr>';
		}
		$html .= '</tbody>';

		$html .= '</table></form>';
		$html .= '<div class="stuffbox import-options">';
		$html .= '<div class="submit-button-box alignright"><button class="do-import button button-hero button-primary">' . ( sprintf( esc_html__( 'Import %s contacts', 'mailster' ), '<strong>' . number_format_i18n( $contactcount ) . '</strong>' ) ) . '*</button><p>* ' . esc_html__( 'Please make sure you have the permission to import these contacts!', 'mailster' ) . '</p></div>';
		$html .= '<h3>' . esc_html__( 'Add contacts to following lists', 'mailster' ) . ':</h3>';
		$html .= '<form id="lists"><ul>';
		$lists = mailster( 'lists' )->get( null, null, true );
		if ( $lists && ! is_wp_error( $lists ) ) {
			foreach ( $lists as $list ) {
				$html .= '<li><label><input name="lists[]" value="' . esc_attr( $list->name ) . '" type="checkbox"> ' . esc_html( $list->name ) . ' <span class="count">(' . number_format_i18n( $list->subscribers ) . ')</span></label></li>';
			}
		}
		$html .= '</ul></form>';
		$html .= '<p><label for="new_list_name">' . esc_html__( 'Add new list', 'mailster' ) . ': </label><input type="text" id="new_list_name" value=""> <button class="button" id="addlist">' . esc_html__( 'Add', 'mailster' ) . '</button></p>';

		$html .= '<h3>' . esc_html__( 'Add contacts to following tags', 'mailster' ) . ':</h3>';
		$html .= '<form id="tags"><p>';
		$html .= '<select multiple name="tags[]" class="tags-input">';
		$html .= '<option></option>';
		$tags  = mailster( 'tags' )->get();
		foreach ( $tags as $tag ) :
			$html .= '<option value="' . esc_attr( $tag->ID ) . '">' . esc_html( $tag->name ) . '</option>';
		endforeach;
		$html .= '</select>';
		$html .= '</p></form>';

		$html    .= '<h3>' . esc_html__( 'Import as', 'mailster' ) . ':</h3><p>';
		$html    .= '<p>';
		$statuses = mailster( 'subscribers' )->get_status( null, true );
		foreach ( $statuses as $i => $name ) {
			if ( in_array( $i, array( 4, 5, 6 ) ) ) {
				continue;
			}

			$html .= '<label><input type="radio" name="status" value="' . (int) $i . '" ' . checked( 1, $i, false ) . '> ' . esc_html( $name ) . ' </label>';
		}
		$html .= '</p>';
		$html .= '<p class="description">' . esc_html__( 'The status will be applied to contacts if no other is defined via the columns.', 'mailster' ) . '</p>';
		$html .= '<div class="pending-info error inline"><p><strong>' . esc_html__( 'Choosing "pending" as status will force a confirmation message to the subscribers.', 'mailster' ) . '</strong></p></div>';

		$html .= '<h3>' . esc_html__( 'Existing subscribers', 'mailster' ) . ':</h3><p><label> <input type="radio" name="existing" value="skip" checked> ' . esc_html__( 'skip', 'mailster' ) . '</label> &mdash; <span class="description">' . esc_html__( 'will skip the contact if the email address already exists. Status will not be changed.', 'mailster' ) . '</span><br> <label><input type="radio" name="existing" value="overwrite"> ' . esc_html__( 'overwrite', 'mailster' ) . '</label> &mdash; <span class="description">' . esc_html__( 'will overwrite all values of the contact. Status will be overwritten.', 'mailster' ) . '</span><br><input type="radio" name="existing" value="merge"> ' . esc_html__( 'merge', 'mailster' ) . '</label> &mdash; <span class="description">' . esc_html__( 'will overwrite only defined values and keep old ones. Status will not be changed unless defined via the columns.', 'mailster' ) . '</span></p>';
		$html .= '<h3>' . esc_html__( 'Other', 'mailster' ) . ':</h3><p><label>';
		$html .= '<p><label><input type="checkbox" id="signup" name="signup" checked>' . esc_html__( 'Use a signup date if not defined', 'mailster' ) . ': <input type="text" value="' . date( 'Y-m-d' ) . '" class="datepicker" id="signupdate" name="signupdate"></label>';
		$html .= '<br><span class="description">' . esc_html__( 'Some Auto responder require a signup date. Define it here if it is not set or missing', 'mailster' ) . '</span></p>';
		$html .= '<p><label><input type="checkbox" id="performance" name="performance"> ' . esc_html__( 'Low memory usage (slower)', 'mailster' ) . '</label></p>';
		$html .= '<input type="hidden" id="identifier" value="' . esc_attr( $identifier ) . '">';
		$html .= '</div>';

		$return['html'] = $html;

		wp_send_json( $return );

	}


	public function ajax_do_import() {

		global $wpdb;

		define( 'MAILSTER_DO_BULKIMPORT', true );

		$memory_limit       = ini_get( 'memory_limit' );
		$max_execution_time = ini_get( 'max_execution_time' );

		ini_set( 'display_errors', 0 );

		set_time_limit( 0 );

		if ( (int) $max_execution_time < 300 ) {
			ini_set( 'max_execution_time', 300 );
		}
		if ( (int) $memory_limit < 256 ) {
			ini_set( 'memory_limit', '256M' );
		}

		$return['success'] = false;

		$this->ajax_nonce( json_encode( $return ) );

		if ( ! current_user_can( 'mailster_import_subscribers' ) ) {
			wp_send_json( $return );
		}

		$timeoffset = mailster( 'helper' )->gmt_offset( true );

		$bulkdata    = wp_parse_args( $_POST['options'], get_option( 'mailster_bulk_import' ) );
		$erroremails = get_option( 'mailster_bulk_import_errors', array() );

		$bulkdata['existing']    = esc_attr( $bulkdata['existing'] );
		$bulkdata['keepstatus']  = ! ! ( $bulkdata['keepstatus'] === 'true' );
		$bulkdata['performance'] = ! ! ( $bulkdata['performance'] === 'true' );
		$bulkdata['signupdate']  = $bulkdata['signupdate'];

		parse_str( $bulkdata['order'], $order );
		$order = isset( $order['order'] ) ? $order['order'] : array();
		parse_str( $bulkdata['lists'], $lists );
		$lists = isset( $lists['lists'] ) ? $lists['lists'] : array();
		parse_str( $bulkdata['tags'], $tags );
		$tags = isset( $tags['tags'] ) ? $tags['tags'] : array();

		$option_list_ids = array();
		$option_tag_ids  = array();

		if ( isset( $lists ) ) {
			foreach ( (array) $lists as $list ) {

				$list_id = mailster( 'lists' )->get_by_name( $list, 'ID' );

				if ( ! $list_id ) {
					$list_id = mailster( 'lists' )->add( $list );
					if ( is_wp_error( $list_id ) ) {
						continue;
					}
				}

				$option_list_ids[] = $list_id;
			}
		}

		if ( isset( $tags ) ) {
			foreach ( (array) $tags as $tag ) {

				if ( is_numeric( $tag ) ) {
					$tag_id = mailster( 'tags' )->get( $tag );

				} else {
					$tag_id = mailster( 'tags' )->get_by_name( $tag, 'ID' );
				}

				if ( ! $tag_id ) {
					$tag_id = mailster( 'tags' )->add( $tag );
					if ( is_wp_error( $tag_id ) ) {
						continue;
					}
				} elseif ( isset( $tag_id->ID ) ) {
					$tag_id = $tag_id->ID;
				}

				$option_tag_ids[] = $tag_id;
			}
		}

		$parts_at_once = $bulkdata['performance'] ? 2 : 8;
		$list_cache    = array();

		$bulkdata['current'] = (int) $_POST['id'];

		$sql = "SELECT data FROM {$wpdb->prefix}mailster_temp_import WHERE identifier = %s ORDER BY ID ASC LIMIT %d, $parts_at_once";

		$raw_list_data = $wpdb->get_col( $wpdb->prepare( $sql, $bulkdata['identifier'], $bulkdata['current'] * $parts_at_once ) );

		$return['sql'] = $wpdb->prepare( $sql, $bulkdata['identifier'], $bulkdata['current'] * $parts_at_once );

		if ( $raw_list_data ) {

			foreach ( $raw_list_data as $raw_list ) {

				$raw_list = unserialize( base64_decode( $raw_list ) );

				// each entry
				foreach ( $raw_list as $line ) {

					$list_array = array();
					$tag_array  = array();
					$list_ids   = $option_list_ids;
					$tag_ids    = $option_tag_ids;

					if ( ! trim( $line ) ) {
						$bulkdata['lines']--;
						continue;
					}

					set_time_limit( 10 );

					$data       = explode( $bulkdata['separator'], $line );
					$line_count = count( $data );

					$insert = array(
						'signup'     => 0,
						'confirm'    => 0,
						'ip'         => '',
						'ip_signup'  => '',
						'ip_confirm' => '',
						'lang'       => '',
					);

					$insert      = array();
					$statusnames = array_flip( mailster( 'subscribers' )->get_status( null, true ) );

					// each column
					for ( $col = 0; $col < $line_count; $col++ ) {

						$d = trim( $data[ $col ] );
						if ( ! isset( $order[ $col ] ) ) {
							continue;
						}
						switch ( $order[ $col ] ) {

							case 'email':
								$insert[ $order[ $col ] ] = strtolower( $d );
								break;
							case '_signup':
							case '_confirm':
								if ( ! is_numeric( $d ) && ! empty( $d ) ) {
									$d = strtotime( $d );
								}

							case '_ip':
							case '_ip_signup':
							case '_ip_confirm':
							case '_lang':
								$insert[ substr( $order[ $col ], 1 ) ] = $d;
								break;
							case '_status':
								if ( is_numeric( $d ) ) {
									$insert[ substr( $order[ $col ], 1 ) ] = $d;
								} elseif ( is_string( $d ) && isset( $statusnames[ $d ] ) ) {
									$insert[ substr( $order[ $col ], 1 ) ] = $statusnames[ $d ];
								}
								break;
							case '_lists':
								$list_array = explode( ',', $d );
								$list_array = array_map( 'trim', $list_array );

								break;
							case '_tags':
								$tag_array = explode( ',', $d );
								$tag_array = array_map( 'trim', $tag_array );

								break;
							case '_ip_all':
								$insert['ip'] = $d;
							case '_ip_confirm_signup':
								$insert['ip_signup']  = $d;
								$insert['ip_confirm'] = $d;
								break;
							case '_confirm_signup':
								if ( ! is_numeric( $d ) && ! empty( $d ) ) {
									$d = strtotime( $d );
								}

								$insert['signup']  = $d;
								$insert['confirm'] = $d;
								break;

							case 'first_last':
								$split               = explode( ' ', $d );
								$insert['firstname'] = $split[0];
								$insert['lastname']  = $split[1];
								break;
							case 'last_first':
								$split               = explode( ' ', $d );
								$insert['firstname'] = $split[1];
								$insert['lastname']  = $split[0];
								break;
							case '-1':
								// ignored column
								break;
							default:
								$insert[ $order[ $col ] ] = $d;
						}
					}

					if ( ! mailster_is_email( $insert['email'] ) ) {
						$erroremails[ $insert['email'] ] = esc_html__( 'Email address is invalid.', 'mailster' );
						$bulkdata['errors']++;
						continue;
					}

					if ( ! isset( $insert['signup'] ) || empty( $insert['signup'] ) ) {
						$insert['signup'] = $bulkdata['signupdate'] ? strtotime( $bulkdata['signupdate'] ) - $timeoffset : 0;
					}

					if ( empty( $insert['signup'] ) && 'merge' == $bulkdata['existing'] ) {
						unset( $insert['signup'] );
					}

					if ( ! isset( $insert['confirm'] ) ) {
						$insert['confirm'] = 0;
					}

					$insert['referer'] = 'import';

					switch ( $bulkdata['existing'] ) {
						case 'merge':
							if ( $exists = mailster( 'subscribers' )->get_by_mail( $insert['email'] ) ) {

								$insert['ID'] = $exists->ID;
								if ( ! isset( $insert['status'] ) ) {
									$insert['status'] = $exists->status;
								}
								$subscriber_id = mailster( 'subscribers' )->update( $insert, true, true );

							} else {

								if ( ! isset( $insert['status'] ) ) {
									$insert['status'] = $bulkdata['status'];
								}

								$subscriber_id = mailster( 'subscribers' )->add( $insert, false );
							}

							break;
						case 'overwrite':
							if ( ! isset( $insert['status'] ) ) {
								$insert['status'] = $bulkdata['status'];
							}
							$subscriber_id = mailster( 'subscribers' )->add( $insert, true );
							break;
						case 'skip':
							if ( ! isset( $insert['status'] ) ) {
								$insert['status'] = $bulkdata['status'];
							}
							$subscriber_id = mailster( 'subscribers' )->add( $insert, false );
							break;
					}

					if ( is_wp_error( $subscriber_id ) ) {
						$erroremails[ $insert['email'] ] = $subscriber_id->get_error_message();
						$bulkdata['errors']++;
					} else {

						foreach ( $list_array as $list ) {

							if ( empty( $list ) ) {
								continue;
							}

							if ( isset( $list_cache[ $list ] ) ) {
								$list_id = $list_cache[ $list ];
							} else {
								$list_id = mailster( 'lists' )->get_by_name( $list, 'ID' );
							}

							if ( ! $list_id ) {
								$list_id = mailster( 'lists' )->add( $list );
								if ( is_wp_error( $list_id ) ) {
									continue;
								}
								$list_cache[ $list ] = $list_id;
							}

							$list_ids[] = $list_id;

						}

						if ( ! empty( $list_ids ) ) {
							$list_ids = array_unique( $list_ids );
							$added    = null;
							if ( $insert['status'] != 0 ) {
								$added = isset( $insert['signup'] ) ? $insert['signup'] : time();
							}
							mailster( 'subscribers' )->assign_lists( $subscriber_id, $list_ids, $bulkdata['existing'] == 'overwrite', $added );
						}

						foreach ( $tag_array as $tag ) {

							if ( empty( $tag ) ) {
								continue;
							}

							if ( isset( $tag_cache[ $tag ] ) ) {
								$tag_id = $tag_cache[ $tag ];
							} else {
								$tag_id = mailster( 'tags' )->get_by_name( $tag, 'ID' );
							}

							if ( ! $tag_id ) {
								$tag_id = mailster( 'tags' )->add( $tag );
								if ( is_wp_error( $tag_id ) ) {
									continue;
								}
								$tag_cache[ $tag ] = $tag_id;
							}

							$tag_ids[] = $tag_id;

						}

						if ( ! empty( $tag_ids ) ) {
							$tag_ids = array_unique( $tag_ids );
							mailster( 'subscribers' )->assign_tags( $subscriber_id, $tag_ids, $bulkdata['existing'] == 'overwrite' );
						}

						$bulkdata['imported']++;
					}
				}
			}
		}

		$return['memoryusage'] = size_format( memory_get_peak_usage( true ), 2 );
		$return['errors']      = ( $bulkdata['errors'] );
		$return['imported']    = ( $bulkdata['imported'] );
		$return['total']       = ( $bulkdata['lines'] );
		$return['f_errors']    = number_format_i18n( $bulkdata['errors'] );
		$return['f_imported']  = number_format_i18n( $bulkdata['imported'] );
		$return['f_total']     = number_format_i18n( $bulkdata['lines'] );

		$return['html'] = '';

		if ( $bulkdata['imported'] + $bulkdata['errors'] >= $bulkdata['lines'] ) {
			$return['html'] .= '<p>' . sprintf( esc_html__( '%1$s of %2$s contacts imported', 'mailster' ), '<strong>' . number_format_i18n( $bulkdata['imported'] ) . '</strong>', '<strong>' . number_format_i18n( $bulkdata['lines'] ) . '</strong>' ) . '<p>';
			if ( $bulkdata['errors'] ) {
				$i      = 0;
				$table  = '<p>' . esc_html__( 'The following addresses were not imported', 'mailster' ) . ':</p>';
				$table .= '<table class="wp-list-table widefat fixed">';
				$table .= '<thead><tr><td width="5%">#</td><td>' . mailster_text( 'email' ) . '</td><td>' . esc_html__( 'Reason', 'mailster' ) . '</td></tr></thead><tbody>';
				foreach ( $erroremails as $email => $reason ) {
					$table .= '<tr' . ( $i % 2 ? '' : ' class="alternate"' ) . '><td>' . ( ++$i ) . '</td><td>' . esc_html( $email ) . '</td><td>' . esc_html( $reason ) . '</td></tr></thead>';
				}
				$table          .= '</tbody></table>';
				$return['html'] .= $table;
			}

			delete_option( 'mailster_bulk_import' );
			delete_option( 'mailster_bulk_import_errors' );
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mailster_temp_import" );
			$return['wpusers'] = mailster( 'subscribers' )->wp_id();

		} else {

			update_option( 'mailster_bulk_import', $bulkdata );
			update_option( 'mailster_bulk_import_errors', $erroremails );

		}
		$return['success'] = true;

		wp_send_json( $return );
	}


	public function ajax_export_contacts() {

		global $wpdb, $wp_filesystem;
		$return['success'] = false;

		$this->ajax_nonce( json_encode( $return ) );

		if ( ! current_user_can( 'mailster_export_subscribers' ) ) {
			$return['msg'] = esc_html__( 'You are not allowed to export subscribers!', 'mailster' );

			wp_send_json( $return );
		}

		parse_str( $_POST['data'], $d );

		$listids    = isset( $d['lists'] ) ? array_filter( $d['lists'], 'is_numeric' ) : array();
		$statuses   = isset( $d['status'] ) ? array_filter( $d['status'], 'is_numeric' ) : array();
		$conditions = isset( $d['conditions'] ) ? (array) $d['conditions'] : array();

		$args = array(
			'lists'      => $listids,
			'status'     => $statuses,
			'conditions' => $conditions,
			'return_ids' => true,
		);

		$args = apply_filters( 'mailster_export_args', $args, $d );

		$data = mailster( 'subscribers' )->query( $args );

		if ( isset( $d['nolists'] ) && $d['nolists'] ) {

			$args['lists'] = -1;
			$data2         = mailster( 'subscribers' )->query( $args );

			$data = array_unique( array_merge( $data, $data2 ) );

		}

		$return['count'] = count( $data );

		if ( $return['count'] ) {

			if ( ! is_dir( MAILSTER_UPLOAD_DIR ) ) {
				wp_mkdir_p( MAILSTER_UPLOAD_DIR );
			}

			$filename = MAILSTER_UPLOAD_DIR . '/~mailster_export_' . date( 'Y-m-d-H-i-s' ) . '.tmp';

			update_option( 'mailster_export_filename', $filename );
			unset( $d['_wpnonce'], $d['_wp_http_referer'] );
			update_user_option( get_current_user_id(), 'mailster_export_settings', $d );

			try {

				add_filter(
					'filesystem_method',
					function() {
						return 'direct';
					}
				);
				mailster_require_filesystem();

				if ( ! ( $return['success'] = $wp_filesystem->put_contents( $filename, '', FS_CHMOD_FILE ) ) ) {
					$return['msg'] = sprintf( esc_html__( 'Not able to create file in %s. Please make sure WordPress can write files to your filesystem!', 'mailster' ), MAILSTER_UPLOAD_DIR );
				} else {

				}
			} catch ( Exception $e ) {

				$return['success'] = false;
				$return['msg']     = $e->getMessage();
			}
		} else {

			$return['msg'] = esc_html__( 'No Subscribers found!', 'mailster' );
		}

		wp_send_json( $return );

	}


	public function ajax_do_export() {

		global $wpdb;

		$return['success'] = false;

		$this->ajax_nonce( json_encode( $return ) );

		if ( ! current_user_can( 'mailster_export_subscribers' ) ) {
			$return['msg'] = esc_html__( 'You are not allowed to export subscribers!', 'mailster' );

			wp_send_json( $return );
		}

		$filename = get_option( 'mailster_export_filename' );

		if ( ! file_exists( $filename ) || ! wp_is_writable( $filename ) ) {
			$return['msg'] = esc_html__( 'Not able to write export file', 'mailster' );

			wp_send_json( $return );
		}

		parse_str( $_POST['data'], $d );

		$offset   = (int) $_POST['offset'];
		$limit    = (int) $_POST['limit'];
		$raw_data = array();

		$listids  = isset( $d['lists'] ) ? array_filter( $d['lists'], 'is_numeric' ) : array();
		$statuses = isset( $d['status'] ) ? array_filter( $d['status'], 'is_numeric' ) : array();

		$encoding     = $d['encoding'];
		$outputformat = $d['outputformat'];
		$separator    = $d['separator'];
		if ( 'tab' == $separator ) {
			$separator = "\t";
		}

		$dateformat = $d['dateformat'];

		$useheader = $offset === 0 && $d['header'];

		$custom_fields      = mailster()->get_custom_fields();
		$meta_keys          = mailster( 'subscribers' )->get_meta_keys();
		$custom_date_fields = mailster()->get_custom_date_fields();
		$custom_field_names = array_merge( array( 'firstname', 'lastname' ), array_keys( $custom_fields ) );
		$custom_field_names = array_keys( array_intersect_key( array_flip( $custom_field_names ), array_flip( $d['column'] ) ) );

		if ( $useheader ) {

			$row = array();

			foreach ( $d['column'] as $col ) {
				switch ( $col ) {
					case '_number':
						$val = '#';
						break;
					case 'ID':
						$val = esc_html__( 'ID', 'mailster' );
						break;
					case 'email':
					case 'firstname':
					case 'lastname':
						$val = mailster_text( $col, $col );
						break;
					case '_listnames':
						$val = esc_html__( 'Lists', 'mailster' );
						break;
					case '_tagnames':
						$val = esc_html__( 'Tags', 'mailster' );
						break;
					case 'hash':
						$val = esc_html__( 'Hash', 'mailster' );
						break;
					case 'status':
						$val = esc_html__( 'Status', 'mailster' );
						break;
					case '_statuscode':
						$val = esc_html__( 'Statuscode', 'mailster' );
						break;
					case 'ip':
						$val = esc_html__( 'IP Address', 'mailster' );
						break;
					case 'signup':
						$val = esc_html__( 'Signup Date', 'mailster' );
						break;
					case 'ip_signup':
						$val = esc_html__( 'Signup IP', 'mailster' );
						break;
					case 'confirm':
						$val = esc_html__( 'Confirm Date', 'mailster' );
						break;
					case 'ip_confirm':
						$val = esc_html__( 'Confirm IP', 'mailster' );
						break;
					case 'added':
						$val = esc_html__( 'Added', 'mailster' );
						break;
					case 'updated':
						$val = esc_html__( 'Updated', 'mailster' );
						break;
					case 'rating':
						$val = esc_html__( 'Rating', 'mailster' );
						break;
					default:
						if ( isset( $custom_fields[ $col ] ) ) {
							$val = $custom_fields[ $col ]['name'];
						} elseif ( $meta_keys[ $col ] ) {
							$val = $meta_keys[ $col ];
						} else {
							$val = ucwords( $col );
						}
				}

				$val = apply_filters( 'mailster_export_heading_' . $col, $val, $d );

				if ( function_exists( 'mb_convert_encoding' ) ) {
					$val = mb_convert_encoding( $val, $encoding, 'UTF-8' );
				}

				switch ( $separator ) {
					case ',':
					case "\t":
						$row[] = str_replace( $separator, ' ', $val );
						break;
					default:
						$row[] = str_replace( $separator, ',', $val );
				}
			}

			$raw_data[] = $row;

		}

		$offset = $offset * $limit;

		$all_fields = isset( $d['column'] ) ? (array) $d['column'] : array();
		$special    = array_values( preg_grep( '/^_/', $all_fields ) );
		$fields     = preg_grep( '/^(?!_)/', $all_fields );
		$meta       = array_values( array_intersect( $fields, mailster( 'subscribers' )->get_meta_keys( true ) ) );
		$fields     = array_values( array_diff( $fields, $meta ) );
		$conditions = isset( $d['conditions'] ) ? (array) $d['conditions'] : array();

		if ( in_array( '_statuscode', $special ) ) {
			$fields[] = 'status';
		}

		$args = array(
			'lists'      => $listids,
			'status'     => $statuses,
			'fields'     => $fields,
			'meta'       => $meta,
			'conditions' => $conditions,
			'limit'      => $limit,
			'offset'     => $offset,
		);

		$args = apply_filters( 'mailster_export_args', $args, $d );

		$data = mailster( 'subscribers' )->query( $args );

		if ( isset( $d['nolists'] ) && $d['nolists'] ) {

			$args['lists'] = -1;
			$data2         = mailster( 'subscribers' )->query( $args );

			$data = array_merge( $data, $data2 );
		}

		$counter = 1 + $offset;

		$statusnames = mailster( 'subscribers' )->get_status( null, true );

		foreach ( $data as $user ) {

			$row = array_flip( $all_fields );

			foreach ( $row as $key => $empty ) {

				switch ( $key ) {
					case '_number':
						$val = $counter;
						break;
					case 'id':
						$val = $user->ID;
						break;
					case 'email':
						$val = $user->email;
						break;
					case '_listnames':
						$list = mailster( 'subscribers' )->get_lists( $user->ID );
						$val  = implode( ', ', wp_list_pluck( $list, 'name' ) );
						break;
					case '_tagnames':
						$tag = mailster( 'subscribers' )->get_tags( $user->ID );
						$val = implode( ', ', wp_list_pluck( $tag, 'name' ) );
						break;
					case 'status':
						$val = $statusnames[ $user->status ];
						break;
					case '_statuscode':
						$val = $user->status;
						break;
					case 'ip':
					case 'ip_signup':
					case 'ip_comfirm':
						$val = isset( $user->{$key} ) ? $user->{$key} : '';
						break;
					case 'added':
					case 'updated':
					case 'signup':
					case 'confirm':
					case 'gdpr':
						$val = ! empty( $user->{$key} ) ? ( $dateformat ? date( $dateformat, $user->{$key} ) : $user->{$key} ) : '';
						break;
					case 'rating':
						$val = $user->rating;
						break;
					default:
						$val = isset( $user->{$key} ) ? $user->{$key} : '';
						if ( $dateformat && in_array( $key, $custom_date_fields ) ) {
							$val = date( $dateformat, strtotime( $user->{$key} ) );
						}

						// remove line breaks
						$val = preg_replace( "/[\n\r]/", ' ', $val );
				}

				$val = apply_filters( 'mailster_export_field_' . $key, $val, $d );

				if ( function_exists( 'mb_convert_encoding' ) ) {
					$val = mb_convert_encoding( $val, $encoding, 'UTF-8' );
				}

				switch ( $separator ) {
					case ',':
					case "\t":
						$row[ $key ] = str_replace( $separator, ' ', $val );
						break;
					default:
						$row[ $key ] = str_replace( $separator, ',', $val );
				}
			}

			$raw_data[] = $row;

			$counter++;
		}

		$output = '';

		if ( 'html' == $outputformat ) {

			if ( $useheader ) {
				$firstrow = array_shift( $raw_data );
				$output  .= '<tr>' . "\n";
				foreach ( $firstrow as $key => $r ) {
					$output .= '<th>' . strip_tags( $r ) . '</th>' . "\n";
				}
				$output .= '</tr>' . "\n";
			}
			foreach ( $raw_data as $row ) {
				$output .= '<tr>' . "\n";
				foreach ( $row as $key => $r ) {
					$output .= '<td>' . esc_html( $r ) . '</td>' . "\n";
				}
				$output .= '</tr>' . "\n";
			}
		} elseif ( 'xls' == $outputformat ) {

			if ( $useheader ) {
				$firstrow = array_shift( $raw_data );
				$output  .= '<mailster:Row mailster:StyleID="1">' . "\n";
				foreach ( $firstrow as $key => $r ) {
					$output .= '<mailster:Cell><mailster:Data mailster:Type="String">' . strip_tags( $r ) . '</mailster:Data></mailster:Cell>' . "\n";
				}
				$output .= '</mailster:Row>' . "\n";
			}
			foreach ( $raw_data as $row ) {
				$output .= '<mailster:Row>' . "\n";

				foreach ( $row as $key => $r ) {
					$type = 'String';
					if ( in_array( $key, array( 'ID', '_number', '_statuscode', 'rating', 'timeoffset' ) ) ) {
						$type = 'Number';
					}
					$output .= '<mailster:Cell><mailster:Data mailster:Type="' . $type . '">' . esc_html( $r ) . '</mailster:Data></mailster:Cell>' . "\n";
				}
				$output .= '</mailster:Row>' . "\n";
			}
		} else {
			foreach ( $raw_data as $row ) {
				$output .= implode( $separator, $row ) . "\n";
			}
		}

		try {

			if ( $output ) {
				mailster( 'helper' )->file_put_contents( $filename, $output, 'a' );
				$file_size = @filesize( $filename );

				$return['success'] = true;
			} else {
				$return['finished'] = true;

				$finalname = MAILSTER_UPLOAD_DIR . '/mailster_export_' . date( 'Y-m-d-H-i-s' ) . '.' . $outputformat;
				if ( file_exists( $filename ) ) {
					$return['success'] = copy( $filename, $finalname );
					$file_size         = filesize( $filename );
					update_option( 'mailster_export_filename', $finalname );
					unlink( $filename );
				}
				$return['filename'] = admin_url( 'admin-ajax.php?action=mailster_download_export_file&file=' . basename( $finalname ) . '&format=' . $outputformat . '&_wpnonce=' . wp_create_nonce( 'mailster_nonce' ) );
			}

			$return['total'] = $file_size ? size_format( $file_size, 2 ) : 0;

		} catch ( Exception $e ) {

			$return['success'] = false;
			$return['msg']     = $e->getMessage();

		}

		wp_send_json( $return );
	}


	public function ajax_download_export_file() {

		$this->ajax_nonce( 'not allowed' );

		$folder = MAILSTER_UPLOAD_DIR;

		$filename = basename( $_REQUEST['file'] );
		$file     = $folder . '/' . $filename;

		if ( ! file_exists( $file ) ) {
			die( 'not found' );
		}

		$format = $_REQUEST['format'];

		send_nosniff_header();
		nocache_headers();

		switch ( $format ) {
			case 'html':
				header( 'Content-Type: text/html; name="' . $filename . '"' );
				break;
			case 'xls':
				header( 'Content-Type: application/vnd.ms-excel; name="' . $filename . '"' );
				break;
			case 'csv':
				header( 'Content-Type: text/csv; name="' . $filename . '"' );
				header( 'Content-Transfer-Encoding: binary' );
				break;
			default;
			die( 'format not allowed' );
		}

		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Connection: close' );

		if ( 'html' == $format ) {
			echo '<table>' . "\n";
		} elseif ( 'xls' == $format ) {
			echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
			echo '<mailster:Workbook xmlns:mailster="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
			echo '<mailster:Styles><mailster:Style mailster:ID="1"><mailster:Font mailster:Bold="1"/></mailster:Style></mailster:Styles>' . "\n";
			echo '<mailster:Worksheet mailster:Name="' . esc_attr__( 'Mailster Subscribers', 'mailster' ) . '">' . "\n";
			echo '<mailster:Table>' . "\n";
		}

		readfile( $file );

		if ( 'html' == $format ) {
			echo '</table>';
		} elseif ( 'xls' == $format ) {
			echo '</mailster:Table>' . "\n";
			echo '</mailster:Worksheet>' . "\n";
			echo '</mailster:Workbook>';
		}

		mailster_require_filesystem();

		global $wp_filesystem;

		$wp_filesystem->delete( $file );
		exit;

	}


	public function ajax_delete_contacts() {

		$return['success'] = false;

		$this->ajax_nonce( json_encode( $return ) );

		if ( ! current_user_can( 'mailster_bulk_delete_subscribers' ) ) {
			$return['msg'] = 'no allowed';

			wp_send_json( $return );
		}

		parse_str( $_POST['data'], $d );

		$count      = 0;
		$listids    = isset( $d['lists'] ) ? array_filter( $d['lists'], 'is_numeric' ) : array();
		$statuses   = isset( $d['status'] ) ? array_filter( $d['status'], 'is_numeric' ) : null;
		$conditions = isset( $d['conditions'] ) ? $d['conditions'] : null;

		if ( isset( $d['nolists'] ) ) {

			$subscriber_ids = mailster( 'subscribers' )->query(
				array(
					'lists'      => -1,
					'status'     => $statuses,
					'conditions' => $conditions,
					'return_ids' => true,
				)
			);

			$count += count( $subscriber_ids );

			$return['success'] = mailster( 'subscribers' )->remove( $subscriber_ids, $statuses, isset( $d['remove_actions'] ) );

		}

		if ( ! empty( $listids ) ) {

			$subscriber_ids = mailster( 'subscribers' )->query(
				array(
					'lists'      => $listids,
					'status'     => $statuses,
					'conditions' => $conditions,
					'return_ids' => true,
				)
			);

			$count += count( $subscriber_ids );

			$return['success'] = mailster( 'subscribers' )->remove( $subscriber_ids, $statuses, isset( $d['remove_actions'] ) );

			if ( $return['success'] && ! empty( $listids ) && isset( $d['remove_lists'] ) ) {

				mailster( 'lists' )->remove( $listids );

			}
		}

		if ( $return['success'] && $count ) {

			mailster()->optimize_tables( array( 'subscribers', 'lists' ) );
			$return['msg'] = sprintf( esc_html__( _n( '%s Subscriber removed', '%s Subscribers removed', $count, 'mailster' ) ), number_format_i18n( $count ) );

		} else {

			$return['msg'] = esc_html__( 'No Subscribers removed', 'mailster' );
		}

		wp_send_json( $return );

	}


	/**
	 *
	 *
	 * @param unknown $return (optional)
	 * @param unknown $nonce  (optional)
	 */
	private function ajax_nonce( $return = null, $nonce = 'mailster_nonce' ) {
		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], $nonce ) ) {
			if ( is_string( $return ) ) {
				wp_die( $return );
			} else {
				die( $return );
			}
		}

	}



	public function display_import_method( $id ) {

		$plugins = array(
			'mailpoet'  => 'mailster-mailpoet/mailster-mailpoet.php',
			'mailchimp' => 'mailster-mailchimp/mailster-mailchimp.php',
		);

		if ( isset( $plugins[ $id ] ) && ! is_plugin_active( $plugins[ $id ] ) ) {
			echo '<p>' . sprintf( esc_html__( 'To import subscribers from %s you need an additional addon.', 'mailster' ), ucwords( $id ) ) . '</p>';
			echo '<a class="button button-primary install-addon" data-slug="' . esc_attr( $plugins[ $id ] ) . '">' . esc_html__( 'Install Addon' ) . '</a>';
		}

	}

	/**
	 *
	 *
	 * @param unknown $string
	 * @param unknown $fallback (optional)
	 * @return unknown
	 */
	private function get_separator( $string, $fallback = ';' ) {
		$seps      = array( ';', ',', '|', "\t" );
		$max       = 0;
		$separator = false;
		foreach ( $seps as $sep ) {
			$count = substr_count( $string, $sep );
			if ( $count > $max ) {
				$separator = $sep;
				$max       = $count;
			}
		}

		if ( $separator ) {
			return $separator;
		}

		return $fallback;
	}


}
