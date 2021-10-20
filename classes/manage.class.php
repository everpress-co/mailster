<?php

class MailsterManage {

	public function __construct() {

		add_action( 'admin_menu', array( &$this, 'add_menu' ), 40 );
		add_action( 'wp_ajax_mailster_import_subscribers_upload_handler', array( &$this, 'ajax_import_subscribers_upload_handler' ) );
		add_action( 'wp_ajax_mailster_get_import_data', array( &$this, 'ajax_get_import_data' ) );
		add_action( 'wp_ajax_mailster_do_import', array( &$this, 'ajax_do_import' ) );
		add_action( 'wp_ajax_mailster_export_contacts', array( &$this, 'ajax_export_contacts' ) );
		add_action( 'wp_ajax_mailster_do_export', array( &$this, 'ajax_do_export' ) );
		add_action( 'wp_ajax_mailster_download_export_file', array( &$this, 'ajax_download_export_file' ) );
		add_action( 'wp_ajax_mailster_delete_contacts', array( &$this, 'ajax_delete_contacts' ) );
		add_action( 'wp_ajax_mailster_delete_delete_job', array( &$this, 'ajax_delete_delete_job' ) );

		add_action( 'mailster_import_method', array( &$this, 'display_import_method' ) );
		add_action( 'admin_init', array( &$this, 'admin_enqueue_scripts' ) );

		add_action( 'admin_init', array( &$this, 'load_integrations' ), 10 );

		add_action( 'mailster_cron_cleanup', array( &$this, 'delete_job' ) );
		add_action( 'mailster_cron_cleanup', array( &$this, 'empty_trash' ) );

	}


	public function load_integrations() {

		$integrations = apply_filters( 'mailster_importer', array() );

		if ( empty( $integrations ) ) {
			return;
		}

		foreach ( $integrations as $slug => $path ) {

			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}

	}


	public function admin_enqueue_scripts() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'mailster-manage-script', MAILSTER_URI . 'assets/js/manage-script' . $suffix . '.js', array( 'mailster-import-script', 'mailster-export-script', 'mailster-delete-script' ), MAILSTER_VERSION, true );

		wp_register_script( 'mailster-import-script', MAILSTER_URI . 'assets/js/import-script' . $suffix . '.js', array( 'mailster-script', 'plupload-all', 'thickbox' ), MAILSTER_VERSION, true );
		wp_register_script( 'mailster-export-script', MAILSTER_URI . 'assets/js/export-script' . $suffix . '.js', array( 'mailster-script', 'jquery-ui-sortable', 'jquery-touch-punch' ), MAILSTER_VERSION, true );
		wp_register_script( 'mailster-delete-script', MAILSTER_URI . 'assets/js/delete-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );

		wp_register_style( 'mailster-manage-style', MAILSTER_URI . 'assets/css/manage-style' . $suffix . '.css', array( 'mailster-import-style', 'mailster-export-style', 'mailster-delete-style' ), MAILSTER_VERSION );

		wp_register_style( 'mailster-import-style', MAILSTER_URI . 'assets/css/import-style' . $suffix . '.css', array( 'thickbox' ), MAILSTER_VERSION );
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
				'current_stats'        => esc_html__( 'Currently %1$s of %2$s imported with %3$s errors. %4$s memory usage', 'mailster' ),
				'estimate_time'        => esc_html__( 'Estimate time left: %s minutes', 'mailster' ),
				'continues_in'         => esc_html__( 'Continues in %s seconds', 'mailster' ),
				'error_importing'      => esc_html__( 'There was a problem during importing contacts. Please check the error logs for more information!', 'mailster' ),
				'prepare_download'     => esc_html__( 'Preparing Download for %1$s Subscribers...%2$s', 'mailster' ),
				'write_file'           => esc_html__( 'Writing file: %1$s (%2$s)', 'mailster' ),
				'export_finished'      => esc_html__( 'Export finished', 'mailster' ),
				'downloading'          => esc_html__( 'Downloading %s Subscribers...', 'mailster' ),
				'error_export'         => esc_html__( 'There was an error while exporting', 'mailster' ),
				'confirm_import'       => esc_html__( 'Do you really like to import these contacts?', 'mailster' ),
				'import_complete'      => esc_html__( 'Import complete!', 'mailster' ),
				'choose_tags'          => esc_html__( 'Choose your tags.', 'mailster' ),
				'confirm_delete'       => esc_html__( 'You are about to delete these subscribers permanently. This step is irreversible!', 'mailster' ) . "\n" . sprintf( esc_html__( 'Type %s to confirm deletion', 'mailster' ), '"DELETE"' ),
				'confirm_job'          => esc_html__( 'Please define a name for this job!', 'mailster' ),
				'confirm_job_delete'   => esc_html__( 'Do you like to delete this job?', 'mailster' ),
				'confirm_job_default'  => esc_html__( 'Job #%s', 'mailster' ),
				'export_n_subscribers' => esc_html__( 'Export %s Subscribers', 'mailster' ),
				'delete_n_subscribers' => esc_html__( 'Delete %s Subscribers permanently', 'mailster' ),
				'onbeforeunloadimport' => esc_html__( 'You are currently importing subscribers! If you leave the page all pending subscribers don\'t get imported!', 'mailster' ),
				'onbeforeunloadexport' => esc_html__( 'Your download is preparing! If you leave this page the progress will abort!', 'mailster' ),
				'import_contacts'      => esc_html__( 'Importing Contacts...%s', 'mailster' ),
				'prepare_import'       => esc_html__( 'Preparing Import...', 'mailster' ),
				'prepare_data'         => esc_html__( 'Preparing Data', 'mailster' ),
				'uploading'            => esc_html__( 'Uploading...%s', 'mailster' ),
			)
		);

		wp_enqueue_style( 'mailster-select2', MAILSTER_URI . 'assets/css/libs/select2' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_enqueue_style( 'mailster-select2-theme', MAILSTER_URI . 'assets/css/select2' . $suffix . '.css', array( 'mailster-select2' ), MAILSTER_VERSION );
		wp_enqueue_script( 'mailster-select2', MAILSTER_URI . 'assets/js/libs/select2' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION, true );

		wp_enqueue_style( 'mailster-manage-style' );

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
		$encoding           = null;
		$removed            = 0;
		$identifier         = isset( $_POST['identifier'] ) ? $_POST['identifier'] : uniqid();
		$total              = 0;
		$total_batch        = 0;

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

			$encoding = mb_detect_encoding( $raw_data, 'auto' );

			if ( function_exists( 'mb_convert_encoding' ) ) {
				$raw_data = mb_convert_encoding( $raw_data, 'UTF-8', mb_detect_encoding( $raw_data, 'UTF-8, ISO-8859-1', true ) );
			}

			$raw_data = trim( $raw_data );

			$total_lines = substr_count( $raw_data, "\n" ) + 1;
			$data        = $this->sanitize_raw_data( $raw_data );
			if ( isset( $data['header'] ) ) {
				$header = array_shift( $data );
			}
			$total   = $total_batch = count( $data );
			$removed = $total_lines - $total;

		} elseif ( isset( $_POST['data'] ) ) {

			$return['success'] = false;

			$this->ajax_nonce( json_encode( $return ) );

			if ( ! current_user_can( 'mailster_import_subscribers' ) ) {
				wp_send_json( $return );
			}

			$raw_data = esc_textarea( stripslashes( $_POST['data'] ) );

			$encoding = mb_detect_encoding( $raw_data, 'auto' );

			// single quotes cause problems
			$raw_data = str_replace( '&#039;', "'", $raw_data );
			$raw_data = str_replace( '&quot;', '"', $raw_data );

			$raw_data = trim( $raw_data );

			$total_lines = substr_count( $raw_data, "\n" ) + 1;
			$data        = $this->sanitize_raw_data( $raw_data );
			if ( isset( $data['header'] ) ) {
				$header = array_shift( $data );
			}
			$total   = $total_batch = count( $data );
			$removed = $total_lines - $total;

		} elseif ( isset( $_POST['wordpressusers'] ) ) {

			if ( ! current_user_can( 'mailster_import_wordpress_users' ) ) {
				wp_send_json( $return );
			}

			parse_str( $_POST['wordpressusers'], $data );

			$roles       = isset( $data['roles'] ) ? (array) $data['roles'] : array();
			$no_role     = isset( $data['no_role'] );
			$meta_values = isset( $data['meta_values'] ) ? (array) $data['meta_values'] : array();
			$offset      = isset( $_POST['offset'] ) ? (int) $_POST['offset'] : 0;
			$limit       = 1000;

			if ( ! empty( $roles ) ) {
				$user_query = new WP_User_Query(
					array(
						'role__in' => $roles,
						'fields'   => 'ID',
						'number'   => $limit,
						'offset'   => $offset * $limit,
						'orderby'  => 'ID',
					)
				);

				$user_ids = $user_query->get_results();
				$total    = $user_query->get_total();
			} else {
				$user_ids = array();
			}

			// add users without a role only on the first run
			if ( ! $offset && $no_role ) {
				$no_roles = wp_get_users_with_no_role();
				$user_ids = array_merge( $user_ids, $no_roles );
				$total   += count( $no_roles );
			}

			$total_batch = count( $user_ids );

			$data = array();

			$wp_roles = wp_list_pluck( wp_roles()->roles, 'name' );

			foreach ( $user_ids as $user_id ) {
				$user = get_user_by( 'ID', $user_id );
				if ( ! $user ) {
					continue;
				}

				$roles = array_intersect_key( $wp_roles, array_flip( $user->roles ) );
				$entry = array(
					$user->user_email,
					get_user_meta( $user->ID, 'first_name', true ),
					get_user_meta( $user->ID, 'last_name', true ),
					$user->user_login,
					$user->user_nicename,
					$user->user_name,
					$user->user_registered,
					implode( ',', $roles ),
				);
				foreach ( $meta_values as $meta_value ) {
					$entry[] = get_user_meta( $user_id, $meta_value, true );
				}

				$data[] = $entry;
			}

			$header = array_merge(
				array(
					mailster_text( 'email' ),
					mailster_text( 'firstname' ),
					mailster_text( 'lastname' ),
					esc_html__( 'login', 'mailster' ),
					esc_html__( 'nickname', 'mailster' ),
					esc_html__( 'display name', 'mailster' ),
					esc_html__( 'registered', 'mailster' ),
					esc_html__( 'roles', 'mailster' ),
				),
				$meta_values
			);

		} else {

			die( 'not allowed' );

		}

		$parts     = array_chunk( $data, max( 50, round( count( $data ) / 200 ) ) );
		$partcount = count( $parts );

		$bulkimport = array(
			'identifier' => $identifier,
			'ids'        => array(),
			'imported'   => 0,
			'errors'     => 0,
			'encoding'   => $encoding,
			'parts'      => $partcount,
			'removed'    => $removed,
			'count'      => $total,
			'header'     => $header,
		);

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		if ( $total && false !== $wpdb->query( "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mailster_temp_import (ID bigint(20) NOT NULL AUTO_INCREMENT, data longtext NOT NULL, identifier char(13) NOT NULL, PRIMARY KEY (ID) ) $collate" ) ) {
			$return['identifier'] = $identifier;

			foreach ( $parts as $i => $part ) {
				$new_value = base64_encode( serialize( $part ) );

				$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}mailster_temp_import (data, identifier) VALUES (%s, %s)", $new_value, $identifier ) );

				$bulkimport['ids'][] = $i;
			}

			$return['success']     = true;
			$return['memoryusage'] = size_format( memory_get_peak_usage( true ), 2 );
			update_option( 'mailster_bulk_import', $bulkimport, 'no' );
			$return['total']       = $total;
			$return['total_batch'] = $total_batch;
			$return['finished']    = $total == $total_batch || ! $total_batch;
		} elseif ( ! $total ) {
			$return['message'] = esc_html__( 'No valid email address where found!', 'mailster' );
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

		$firstline    = $first[0];
		$data         = $first[ count( $first ) - 1 ];
		$cols         = count( $data );
		$contactcount = $return['data']['count'];
		$removed      = $return['data']['removed'];
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
			'_lists'      => esc_html__( 'Lists', 'mailster' ) . ' (' . esc_html__( 'comma separated', 'mailster' ) . ')',
			'_tags'       => esc_html__( 'Tags', 'mailster' ) . ' (' . esc_html__( 'comma separated', 'mailster' ) . ')',
			'_status'     => esc_html__( 'Status', 'mailster' ) . ' [0...4]',
			'_lang'       => esc_html__( 'Language', 'mailster' ),
			'_timeoffset' => esc_html__( 'Timeoffset to UTC', 'mailster' ),
			'_timezone'   => esc_html__( 'Timezone', 'mailster' ),
			'_lat'        => esc_html__( 'Latitude', 'mailster' ),
			'_long'       => esc_html__( 'Longitude', 'mailster' ),
			'_country'    => esc_html__( 'Country', 'mailster' ),
			'_city'       => esc_html__( 'City', 'mailster' ),
		);

		$html       = '<form id="subscriber-table">';
		$html      .= '<h2>' . esc_html__( 'Specify your Import', 'mailster' ) . '</h2>';
		$html      .= '<h4>' . esc_html__( 'Select columns', 'mailster' ) . '</h4>';
		$html      .= '<section>';
		$html      .= '<p class="description">' . esc_html__( 'Define which column represents which field', 'mailster' ) . '</p>';
		$html      .= '<div class="table-wrap">';
		$html      .= '<table class="wp-list-table widefat">';
		$html      .= '<thead>';
		$emailfield = false;
		$html      .= '<tr><td style="width:20px;">#</td>';
		for ( $i = 0; $i < $cols; $i++ ) {
			$ismail     = mailster_is_email( trim( $data[ $i ] ) );
			$header_col = ( $header && isset( $header[ $i ] ) ) ? strip_tags( $header[ $i ] ) : '';
			$select     = '<select name="order[]" class="column-selector">';
			$select    .= '<option value="-1">' . esc_html__( 'Ignore column', 'mailster' ) . '</option>';
			$select    .= '<option value="-1">----------</option>';
			$select    .= '<option value="_new">' . esc_html__( 'Create new Field', 'mailster' ) . '</option>';
			$select    .= '<option value="-1">----------</option>';
			$select    .= '<optgroup label="' . esc_html__( 'Basic', 'mailster' ) . '">';
			foreach ( $fields as $key => $value ) {
				$is_selected = ( ( $ismail && $key == 'email' && ! $emailfield && $emailfield = true ) ||
					( $header_col == mailster_text( 'firstname' ) && $key == 'firstname' ) ||
					( $header_col == mailster_text( 'lastname' ) && $key == 'lastname' ) ||
					( $header_col == 'registered' && $key == '_signup' ) );

				$select .= '<option value="' . esc_attr( $key ) . '" ' . ( $is_selected ? 'selected' : '' ) . '>' . esc_html( $value ) . '</option>';
			}
			$select .= '</optgroup>';
			if ( ! empty( $custom_fields ) ) {
				$select .= '<optgroup label="' . esc_html__( 'Custom Fields', 'mailster' ) . '">';
				foreach ( $custom_fields as $key => $d ) {
					$select .= '<option value="' . esc_attr( $key ) . '">' . $d['name'] . '</option>';
				}
				$select .= '<option value="-1">----------</option>';
				$select .= '<option value="_new">' . esc_html__( 'Create new Field', 'mailster' ) . '</option>';
				$select .= '</optgroup>';
			} else {
				$select .= '<optgroup label="' . esc_html__( 'no Custom Fields defined!', 'mailster' ) . '">';
				$select .= '<option value="_new">' . esc_html__( 'Create new Field', 'mailster' ) . '</option>';
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
		$html .= '</tr>';
		if ( ! empty( $header ) ) {
			$html .= '<tr><td style="width:20px;">&nbsp;</td>';
			for ( $i = 0; $i < $cols; $i++ ) {
				$html .= '<td>' . esc_html( $header[ $i ] ) . '</td>';
			}
			$html .= '</tr>';
		}
		$html .= '</thead>';
		$html .= '<tbody>';
		for ( $i = 0; $i < min( 10, $contactcount ); $i++ ) {
			$data  = $first[ $i ];
			$html .= '<tr class="' . ( $i % 2 ? '' : 'alternate' ) . '"><td>' . number_format_i18n( $i + 1 ) . '</td>';
			foreach ( $data as $cell ) {
				$html .= '<td title="' . strip_tags( $cell ) . '">' . esc_html( $cell ) . '</td>';
			}
			$html .= '<tr>';
		}
		if ( $contactcount > 10 ) {
			$html .= '<tr class="alternate"><td>&nbsp;</td><td colspan="' . ( $cols ) . '"><i>&hellip;' . sprintf( esc_html__( '%s contacts are hidden', 'mailster' ), number_format_i18n( $contactcount - 11 ) ) . '&hellip;</i></td>';

			$data  = $last[ count( $last ) - 1 ];
			$html .= '<tr><td>' . number_format_i18n( $contactcount ) . '</td>';
			foreach ( $data as $cell ) {
				$html .= '<td title="' . strip_tags( $cell ) . '">' . esc_html( $cell ) . '</td>';
			}
			$html .= '<tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';
		$html .= '</div>';
		if ( ! empty( $removed ) ) {
			$html .= '<div class="pending-info error inline">';
			$html .= '<p>' . sprintf( esc_html__( '%s entries without valid email address have been removed.', 'mailster' ), number_format_i18n( $removed ) ) . '</p>';
			$html .= '</div>';
		}
		$html .= '</section>';
		$html .= '<h4>' . esc_html__( 'Add contacts to following lists', 'mailster' ) . ':</h4>';
		$html .= '<section id="section-lists">';
		$html .= '<ul>';
		$lists = mailster( 'lists' )->get( null, null, true );
		if ( $lists && ! is_wp_error( $lists ) ) {
			foreach ( $lists as $list ) {
				$html .= '<li><label><input name="lists[]" value="' . esc_attr( $list->name ) . '" type="checkbox"> ' . esc_html( $list->name ) . ' <span class="count">(' . number_format_i18n( $list->subscribers ) . ')</span></label></li>';
			}
		}
		$html .= '</ul>';
		$html .= '<p><label for="new_list_name">' . esc_html__( 'Add new list', 'mailster' ) . ': </label><input type="text" id="new_list_name" value=""> <button class="button" id="addlist">' . esc_html__( 'Add', 'mailster' ) . '</button></p>';

		$html .= '</section>';
		$html .= '<h4>' . esc_html__( 'Add contacts to following tags', 'mailster' ) . '</h4>';
		$html .= '<section id="section-tags">';
		$html .= '<p>';
		$html .= '<select multiple name="tags[]" class="tags-input">';
		$html .= '<option></option>';
		$tags  = mailster( 'tags' )->get();
		foreach ( $tags as $tag ) :
			$html .= '<option value="' . esc_attr( $tag->ID ) . '">' . esc_html( $tag->name ) . '</option>';
		endforeach;
		$html .= '</select>';
		$html .= '</p>';

		$html    .= '</section>';
		$html    .= '<h4>' . esc_html__( 'Import as', 'mailster' ) . '</h4>';
		$html    .= '<section>';
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

		$html .= '</section>';
		$html .= '<h4>' . esc_html__( 'Existing subscribers', 'mailster' ) . '</h4>';
		$html .= '<section id="section-existing">';
		$html .= '<p><label> <input type="radio" name="existing" value="skip" checked> ' . esc_html__( 'skip', 'mailster' ) . '</label> &mdash; <span class="description">' . esc_html__( 'will skip the contact if the email address already exists. Status will not be changed.', 'mailster' ) . '</span><br> <label><input type="radio" name="existing" value="overwrite"> ' . esc_html__( 'overwrite', 'mailster' ) . '</label> &mdash; <span class="description">' . esc_html__( 'will overwrite all values of the contact. Status will be overwritten.', 'mailster' ) . '</span><br><input type="radio" name="existing" value="merge"> ' . esc_html__( 'merge', 'mailster' ) . '</label> &mdash; <span class="description">' . esc_html__( 'will overwrite only defined values and keep old ones. Status will not be changed unless defined via the columns.', 'mailster' ) . '</span></p>';
		$html .= '</section>';
		$html .= '<h4>' . esc_html__( 'Other', 'mailster' ) . '</h4>';
		$html .= '<section id="section-other">';
		$html .= '<p><label><input type="checkbox" id="signup" name="signup" checked>' . esc_html__( 'Use a signup date if not defined', 'mailster' ) . ': <input type="text" value="' . date( 'Y-m-d' ) . '" class="datepicker" id="signupdate" name="signupdate"></label>';
		$html .= '<br><span class="description">' . esc_html__( 'Some Auto responder require a signup date. Define it here if it is not set or missing', 'mailster' ) . '</span></p>';
		$html .= '<p><label><input type="checkbox" id="performance" name="performance"> ' . esc_html__( 'Low memory usage (slower)', 'mailster' ) . '</label></p>';
		$html .= '<input type="hidden" id="identifier" value="' . esc_attr( $identifier ) . '">';
		$html .= '</section>';
		$html .= '<section class="footer alternate">';
		$html .= '<p><input type="submit" class="do-import button button-primary" value="' . ( sprintf( esc_attr__( 'Import %s contacts', 'mailster' ), number_format_i18n( $contactcount ) ) ) . '"><span class="status wp-ui-text-icon"></span></p>';
		$html .= '</section>';
		$html .= '</form>';

		$html .= '<div id="create-new-field" style="display:none"><div>';
		$html .= '<h3>Create a new Custom field for this column</h3>';
		$html .= '<p>Create a new Custom field for this column</p>';
		$html .= '<form id="new-custom-field">';
		$html .= '<section>';
		$html .= '<p><label><input type="checkbox" id="signup" name="signup" checked>' . esc_html__( 'Use a signup date if not defined', 'mailster' ) . ': <input type="text" value="' . date( 'Y-m-d' ) . '" class="datepicker" id="signupdate" name="signupdate"></label>';
		$html .= '</section>';
		$html .= '<section class="footer alternate">';
		$html .= '<p class="alignright"><input type="submit" class="do-import button button-primary" value="' . esc_attr__( 'Create Field ', 'mailster' ) . '"></p>';
		$html .= '</section>';
		$html .= '</from>';
		$html .= '</div></div>';

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

		parse_str( $bulkdata['data'], $data );
		$order = isset( $data['order'] ) ? (array) $data['order'] : array();
		$lists = isset( $data['lists'] ) ? (array) $data['lists'] : array();
		$tags  = isset( $data['tags'] ) ? (array) $data['tags'] : array();

		$option_list_ids = array();
		$option_tag_ids  = array();

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

					if ( empty( $line ) ) {
						$bulkdata['lines']--;
						continue;
					}

					set_time_limit( 10 );

					$data       = $line;
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
					$meta        = array();
					$geo         = array();
					$coords      = array();
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
							case '_timeoffset':
								$meta[ substr( $order[ $col ], 1 ) ] = $d;
								break;
							case '_timezone':
								$offset             = mailster( 'helper' )->get_timezone_offset_by_string( $d );
								$meta['timeoffset'] = $offset;
								break;
							case '_country':
								if ( $d ) {
									$geo[0] = $d;
									$geo[1] = isset( $geo[1] ) ? $geo[1] : '';
								}
								break;
							case '_city':
								if ( $d ) {
									$geo[1] = $d;
								}
								break;
							case '_long':
								if ( $d ) {
									$coords[0] = $d;
								}
								break;
							case '_lat':
								if ( $d ) {
									$coords[1] = $d;
								}
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
							case '_new':
								// ignored column
								break;
							default:
								$insert[ $order[ $col ] ] = $d;
						}
					}

					if ( ! empty( $geo ) ) {
						$meta['geo'] = implode( '|', $geo );
					}
					if ( ! empty( $coords ) ) {
						$meta['coords'] = implode( '|', $coords );
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

						mailster( 'subscribers' )->update_meta( $subscriber_id, 0, $meta );

						$bulkdata['imported']++;
					}
				}
			}
		}

		$return['memoryusage'] = size_format( memory_get_peak_usage( true ), 2 );
		$return['errors']      = ( $bulkdata['errors'] );
		$return['imported']    = ( $bulkdata['imported'] );
		$return['total']       = ( $bulkdata['count'] );
		$return['f_errors']    = number_format_i18n( $bulkdata['errors'] );
		$return['f_imported']  = number_format_i18n( $bulkdata['imported'] );
		$return['f_total']     = number_format_i18n( $bulkdata['count'] );

		$return['html'] = '';

		if ( $bulkdata['imported'] + $bulkdata['errors'] >= $bulkdata['count'] ) {
			$return['html'] .= '<h2>' . sprintf( esc_html__( '%1$s of %2$s contacts imported.', 'mailster' ), '<strong>' . number_format_i18n( $bulkdata['imported'] ) . '</strong>', '<strong>' . number_format_i18n( $bulkdata['count'] ) . '</strong>' ) . '</h2>';
			if ( $bulkdata['errors'] ) {
				$i               = 0;
				$return['html'] .= '<h4>' . esc_html__( 'The following addresses were not imported', 'mailster' ) . ':</h4>';
				$return['html'] .= '<section>';
				$return['html'] .= '<div class="table-wrap">';
				$return['html'] .= '<table class="wp-list-table widefat">';
				$return['html'] .= '<thead><tr><td width="5%">#</td><td>' . mailster_text( 'email' ) . '</td><td>' . esc_html__( 'Reason', 'mailster' ) . '</td></tr></thead><tbody>';
				foreach ( $erroremails as $email => $reason ) {
					$return['html'] .= '<tr' . ( $i % 2 ? '' : ' class="alternate"' ) . '><td>' . ( ++$i ) . '</td><td>' . esc_html( $email ) . '</td><td>' . esc_html( $reason ) . '</td></tr></thead>';
				}
				$return['html'] .= '</tbody></table>';
				$return['html'] .= '</div>';
				$return['html'] .= '</section>';
			}

			$return['html'] .= '<section>';
			$return['html'] .= '<a href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_manage_subscribers&tab=import' ) . '" class="button button-primary">' . esc_html__( 'Import more Contacts', 'mailster' ) . '</a> ';
			$return['html'] .= '<a href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers' ) . '" class="button">' . esc_html__( 'View your Subscribers', 'mailster' ) . '</a>';
			$return['html'] .= '</section>';

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

		parse_str( $_POST['data'], $args );
		$name = basename( $_POST['name'] );

		unset( $args['_wpnonce'], $args['_wp_http_referer'] );

		$schedule = isset( $_POST['schedule'] );

		if ( $schedule ) {
			$jobs = get_option( 'mailster_manage_jobs', array() );

			$jobs[ md5( serialize( $args ) ) ] = wp_parse_args(
				array(
					'user_id'   => get_current_user_id(),
					'name'      => $name,
					'timestamp' => time(),
				),
				$args
			);

			update_option( 'mailster_manage_jobs', $jobs );

			$return['success'] = true;
			$return['msg']     = esc_html__( 'Job scheduled.', 'mailster' );

		} else {

			if ( $count = $this->delete_contacts( $args ) ) {

				$return['success'] = true;
				$return['msg']     = sprintf( esc_html__( _n( '%s Subscriber removed.', '%s Subscribers removed.', $count, 'mailster' ) ), number_format_i18n( $count ) );

			} else {

				$return['success'] = false;
				$return['msg']     = esc_html__( 'No Subscribers removed.', 'mailster' );
			}
		}

		wp_send_json( $return );

	}

	public function ajax_delete_delete_job() {

		$return['success'] = false;

		$this->ajax_nonce( json_encode( $return ) );

		if ( ! current_user_can( 'mailster_bulk_delete_subscribers' ) ) {
			$return['msg'] = 'no allowed';
			wp_send_json( $return );
		}

		$id = $_POST['id'];

		$jobs = get_option( 'mailster_manage_jobs', array() );

		if ( isset( $jobs[ $id ] ) ) {
			unset( $jobs[ $id ] );
			update_option( 'mailster_manage_jobs', $jobs );
		}

		$return['success'] = true;
		$return['msg']     = esc_html__( 'Job deleted.', 'mailster' );

		wp_send_json( $return );

	}

	public function delete_job() {

		foreach ( $jobs = get_option( 'mailster_manage_jobs', array() ) as $id => $job ) {

			// only jobs older than one hour
			if ( time() - $job['timestamp'] > MINUTE_IN_SECONDS ) {
				$this->delete_contacts( $job, true );
			}
		}

	}

	public function delete_contacts( $args, $trash = false ) {

		global $wpdb;

		$count          = 0;
		$nolists        = isset( $args['nolists'] ) ? $args['nolists'] : null;
		$remove_actions = isset( $args['remove_actions'] ) ? $args['remove_actions'] : null;
		$job            = $args;
		$current_filter = current_filter();

		$subscribers = array();

		$args = array(
			'lists'      => isset( $args['lists'] ) ? array_filter( $args['lists'], 'is_numeric' ) : array(),
			'status'     => isset( $args['status'] ) ? array_filter( $args['status'], 'is_numeric' ) : null,
			'conditions' => isset( $args['conditions'] ) ? (array) $args['conditions'] : null,
		);

		if ( $current_filter == 'mailster_cron_cleanup' ) {
			// do not delete to fast (at least one hour ago)
			$args['updated_before'] = strtotime( '-1 hour' );

		}
		$args = apply_filters( 'mailster_delete_args', $args );

		if ( ! empty( $args['lists'] ) ) {

			$subscribers = array_merge( mailster( 'subscribers' )->query( $args ), $subscribers );

		}

		if ( $nolists ) {

			$args['lists'] = -1;
			$subscribers   = array_merge( mailster( 'subscribers' )->query( $args ), $subscribers );

		}

		$count = count( $subscribers );

		$subscriber_ids = wp_list_pluck( $subscribers, 'ID' );
		$success        = mailster( 'subscribers' )->remove( $subscriber_ids, $args['status'], $remove_actions, true, $trash );

		if ( $success && $count ) {

			if ( $current_filter == 'mailster_cron_cleanup' ) {

				// send notification to the user who created the job
				if ( $user = get_user_by( 'id', $job['user_id'] ) ) {
					$n = mailster( 'notification' );
					$n->to( $user->user_email );
					$n->template( 'delete_job' );
					$n->requeue( false );
					$mail = $n->mail;
				}
			}

			$return['success'] = $n->add(
				array(
					'subscribers' => $subscribers,
					'job'         => $job,
				)
			);
		}
		return $count;

	}



	public function empty_trash() {

		return mailster( 'subscribers' )->empty_trash(
			$args = array(
				'updated_before' => strtotime( '-14 days' ),
			)
		);

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

	private function sanitize_raw_data( $raw_data ) {

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
		$separator = $this->get_separator( $lines[0] );
		$data      = array();
		$new_sep   = md5( uniqid() );
		$temp_sep  = md5( uniqid() );

		foreach ( $lines as $i => $line ) {

			// handle if separator is used in a column
			if ( preg_match_all( '/("([^"]*)")/', $line, $match ) ) {
				foreach ( $match[0] as $value ) {
					if ( false !== strpos( $value, $separator ) ) {
						$line = str_replace( $value, str_replace( $separator, $temp_sep, $value ), $line );
					}
				}
			}

			$line = str_replace( $separator, $new_sep, $line );
			$line = str_replace( $temp_sep, $separator, $line );

			// cleanup quotes
			$line = str_replace( array( "'" . $new_sep . "'", '"' . $new_sep . '"' ), $new_sep, $line );
			$line = preg_replace( '#("|\')' . preg_quote( $new_sep ) . '#', $new_sep, $line );
			$line = preg_replace( '#' . preg_quote( $new_sep ) . '("|\')#', $new_sep, $line );
			$line = preg_replace( '#^("|\')#', '', $line );
			$line = preg_replace( '#("|\')$#', '', $line );
			$line = explode( $new_sep, $line );

			$has_email = false;
			foreach ( $line as $entry ) {
				if ( mailster_is_email( $entry ) ) {
					$has_email = true;
					break;
				}
			}
			// only first row (header) or with email
			if ( $has_email ) {
				$data[] = $line;
			} elseif ( ! $has_email && ! $i ) {
				// still can contain a double quote
				$data['header'] = str_replace( '"', '', $line );
			}
		}

		return $data;
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
