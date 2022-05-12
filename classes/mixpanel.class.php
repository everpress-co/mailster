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
				mailster_update_option( 'usage_tracking', $track );
				if ( ! $track ) {
					mailster_update_option( 'ask_usage_tracking', false );
					mailster_notice( esc_html__( 'Thanks, we\'ll respect your opinion. You can always opt in anytime on the advanced tab in the settings!', 'mailster' ), 'info', true );
				}
			}
		}

		if ( mailster_option( 'usage_tracking' ) ) {
			add_filter( 'wp_version_check', array( &$this, 'track' ) );
		}

	}


	public function track() {

		$mp = Mixpanel::getInstance( '8e408740bb47e920ad0e8a9c75409898', array( 'host' => 'api-eu.mixpanel.com' ) );

		$id          = md5( home_url() );
		$ip          = 0;
		$ignore_time = true;

		$data = array(
			'$name'         => get_option( 'blogname' ),
			'$email'        => null,
			'Version'       => MAILSTER_VERSION,
			'URL'           => get_option( 'siteurl' ),
			'Campaigns'     => count( mailster( 'campaigns' )->get_finished() ),
			'Autoresponder' => count( mailster( 'campaigns' )->get_autoresponder() ),
			'Subscribers'   => mailster( 'subscribers' )->get_totals(),
			'Lists'         => mailster( 'lists' )->get_list_count(),
			'Forms'         => count( mailster( 'forms' )->get_all() ),
		);

		$mp->people->set( $id, $data, $ip, $ignore_time );

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
