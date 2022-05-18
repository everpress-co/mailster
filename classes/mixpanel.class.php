<?php

class MailsterMixpanel {

	private $tracker_obj;


	public function __construct() {

		add_filter( 'plugins_loaded', array( &$this, 'init' ) );

	}


	public function init() {

		if ( isset( $_GET['mailster_allow_usage_tracking'] ) ) {
			if ( wp_verify_nonce( $_GET['_wpnonce'], 'mailster_allow_usage_tracking' ) ) {
				$track = (bool) $_GET['mailster_allow_usage_tracking'];
				mailster_update_option( 'usage_tracking', $track ? time() : false );
				if ( ! $track ) {
					mailster_update_option( 'ask_usage_tracking', false );
					mailster_notice( esc_html__( 'Thanks, we\'ll respect your opinion. You can always opt in anytime on the advanced tab in the settings!', 'mailster' ), 'info', true );
				}
			}
		}

		if ( mailster_option( 'usage_tracking' ) ) {
			add_filter( 'wp_version_check', array( &$this, 'collect' ) );
			add_filter( 'mailster_cron_worker', array( &$this, 'collect' ) );
		}

	}


	public function collect() {

		// do not run during update
		$stored_version = get_option( 'mailster_version' );
		if ( $stored_version != MAILSTER_VERSION ) {
			return;
		}
		global $wpdb;

		include ABSPATH . WPINC . '/version.php';

		if ( ! function_exists( 'get_plugins' ) ) {
			include ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$is_multisite = is_multisite();

		$user_id  = mailster_option( 'ID' );
		$ip       = mailster_get_ip();
		$local    = get_locale();
		$site_url = wp_parse_url( get_option( 'siteurl' ), PHP_URL_HOST );

		$theme          = wp_get_theme();
		$all_plugins    = get_plugins();
		$active_plugins = get_option( 'active_plugins', array() );
		$plugins        = array();

		foreach ( $all_plugins as $plugin_path => $plugin ) {
			if ( ! in_array( $plugin_path, $active_plugins ) ) {
				continue;
			}

			$plugins[] .= $plugin['Name'] . ' ' . $plugin['Version'];

		}

		$query_args = array(
			'date_query' => array(
				array(
					'after' => '1 year ago',
				),
			),
		);

		$data = array(
			'$last_seen'        => date( 'c' ),
			'$name'             => get_option( 'blogname' ),
			'$country_code'     => substr( $local, 3 ),
			'$referring_domain' => $site_url,
			'Version'           => MAILSTER_VERSION,
			'WP Version'        => $wp_version,
			'Multisite'         => $is_multisite ? get_blog_count() : false,
			'PHP'               => phpversion(),
			'MySQL'             => method_exists( $wpdb, 'db_version' ) ? $wpdb->db_version() : null,
			'Theme'             => $theme->Name . ' ' . $theme->Version,
			'Plugins'           => json_encode( $plugins ),
			'Language'          => $local,
			'RTL'               => is_rtl(),
			'Campaigns'         => count( mailster( 'campaigns' )->get_finished( $query_args ) ),
			'Autoresponder'     => count( mailster( 'campaigns' )->get_autoresponder( $query_args ) ),
			'Subscribers'       => mailster( 'subscribers' )->get_totals(),
			'Lists'             => mailster( 'lists' )->get_list_count(),
			'Forms'             => count( mailster( 'forms' )->get_all() ),
		);

		error_log( print_r( $data, true ) );

		$this->mp()->people->set( $user_id, $data, $ip );
	}


	public function remove_data() {
		$user_id = mailster_option( 'ID' );
		$this->mp()->people->deleteUser( $user_id );
	}

	private function mp() {
		return Mixpanel::getInstance( '8e408740bb47e920ad0e8a9c75409898', array( 'host' => 'api-eu.mixpanel.com' ) );
	}


	public function modify_tracking( $body ) {

		$track = array( 'send_offset', 'timezone', 'embed_images', 'track_opens', 'track_clicks', 'track_location', 'track_users', 'tags_webversion', 'gdpr_forms', 'module_thumbnails', 'charset', 'encoding', 'autoupdate', 'system_mail', 'default_template', 'frontpage_public', 'webversion_bar', 'frontpage_pagination', 'share_button', 'hasarchive', 'subscriber_notification', 'unsubscribe_notification', 'do_not_track', 'list_based_opt_in', 'single_opt_out', 'sync', 'register_comment_form', 'register_other', 'interval', 'send_at_once', 'send_limit', 'send_period', 'send_delay', 'cron_service', 'cron_lock', 'deliverymethod', 'bounce_active', 'disable_cache', 'remove_data' );

		$body['plugin_options_fields'] = array();

		foreach ( $track as $option ) {
			$body['plugin_options_fields'][ $option ] = mailster_option( $option );
		}

		$body['plugin_options'] = array_keys( $body['plugin_options_fields'] );

		$body['inactive_plugins'] = array();
		// do not track these.
		unset( $body['email'], $body['marketing_method'] );

		return $body;
	}

}
