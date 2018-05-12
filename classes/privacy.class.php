<?php

class MailsterPrivacy {

	public function __construct() {

		add_action( 'admin_init', array( &$this, 'init' ) );

	}


	public function init() {

		add_action( 'wp_privacy_personal_data_exporters', array( &$this, 'register_exporter' ) );
		add_action( 'wp_privacy_personal_data_erasers', array( &$this, 'register_erasers' ) );
		if ( function_exists( 'wp_add_privacy_policy_content' ) ) {
			wp_add_privacy_policy_content( __( 'Mailster' ), $this->privacy_content() );
		}

	}

	public function privacy_content() {
		return
		'<h2>' . __( 'What data Mailster collects from your subscribers', 'mailster' ) . '</h2>' .
		'<p class="wp-policy-help">' . __( 'Any paragraph with the wp-policy-help class will be hidden in the suggested changes area, but inserted into a privacy policy text editor as editable text.' ) . '</p>' .
		'<p class="wp-policy-help">' . __( 'Consider text in these paragraphs to be the template text your plugins users will start from in their privacy policies for your functionality.' ) . '</p>' .
		'<p>' . __( 'This text describes what type of information the admin should include here or what they should do with this info you provide in your template.' ) . '</p>';
	}

	public function register_exporter( $exporters ) {
		$exporters[] = array(
			'exporter_friendly_name' => __( 'Mailster Data', 'mailster' ),
			'callback' => array( &$this, 'data_export' ),
		);
		return $exporters;
	}

	public function register_erasers( $exporters ) {
		$exporters[] = array(
			'exporter_friendly_name' => __( 'Mailster Data', 'mailster' ),
			'callback' => array( &$this, 'data_erase' ),
		);
		return $exporters;
	}

	public function data_export( $email_address, $page = 1 ) {

		$subscriber = mailster( 'subscribers' )->get_by_mail( $email_address, true );

		if ( ! $subscriber ) {
			return false;
		}

		$meta = mailster( 'subscribers' )->meta( $subscriber->ID );

		$export_items = array();
		$data = array();

		foreach ( $subscriber as $key => $value ) {
			if ( empty( $value ) ) {
				continue;
			}
			$data[] = array(
				'name'  => $key,
				'value' => $value,
			);
		}
		foreach ( $meta as $key => $value ) {
			if ( empty( $value ) ) {
				continue;
			}
			$data[] = array(
				'name'  => $key,
				'value' => $value,
			);
		}

		$export_items[] = array(
			'group_id'    => 'mailster',
			'group_label' => 'Mailster',
			'item_id'     => 'maislter-' . $subscriber->ID,
			'data'        => $data,
		);
		return array(
			'data' => $export_items,
			'done' => true,
		);

	}

	public function data_erase( $email_address, $page = 1 ) {

		if ( empty( $email_address ) ) {
			return array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);
		}

		$subscriber = mailster( 'subscribers' )->get_by_mail( $email_address, true );

		if ( ! $subscriber ) {
			return false;
		}

		$messages = array();
		$items_removed  = false;
		$items_retained = false;

		if ( mailster( 'subscribers' )->remove( $subscriber->ID ) ) {
			$items_removed = true;
		} else {
			$items_retained = false;
		}

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => $items_retained,
			'messages'       => $messages,
			'done'           => true,
		);
	}

}
