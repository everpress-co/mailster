<?php

class MailsterImporterMailpoet extends MailsterImporter {

	private $name = 'MailPoet';
	private $description = 'Import Subscribers, Lists and Campaigns from MailPoet';
	private $round;

	public function step2() {
?><p><?php esc_html_e( 'You can Import following things into Mailster:', 'mailster' );?></p><?php

	}

	public function supports() {
		return array( 'lists','campaigns','sent','clicks','opens' );
		return array( 'custom_fields','subscribers','lists','forms','campaigns','sent','clicks','opens' );
	}

	public function import_subscribers() {

		$limit = 100;
		$offset = $limit * $this->round;
		$count = 0;

		global $wpdb;
		$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}mailpoet_subscribers AS subscribers ORDER BY id LIMIT %d, %d", $offset, $limit );

		$data = $wpdb->get_results( $sql );

		$overwrite = false;
		$merge = false;
		$subscriber_notification = false;

		foreach ( $data as $subscriber ) {

			$userdata = array(
				'firstname' => $subscriber->first_name,
				'lastname' => $subscriber->last_name,
				'email' => $subscriber->email,
				'status' => $this->get_status_code( $subscriber->status ),
				'added' => strtotime( $subscriber->created_at ),
				'updated' => strtotime( $subscriber->updated_at ),
				'ip_signup' => $subscriber->subscribed_ip,
				'referer' => 'MailPoet',
			);

			$subscriber_id = mailster( 'subscribers' )->add( $userdata, $overwrite, $merge, $subscriber_notification );

			if ( is_wp_error( $subscriber_id ) ) {
				$this->error( $subscriber_id->get_error_message() );
			} else {

				$this->map( 'subscribers', $subscriber->id, $subscriber_id );

				$this->success( sprintf( 'Subscriber %s added', $subscriber->email ) );
				$sql = $wpdb->prepare( "SELECT value, name, type FROM {$wpdb->prefix}mailpoet_subscriber_custom_field AS subscriber_custom_field LEFT JOIN {$wpdb->prefix}mailpoet_custom_fields AS custom_fields ON custom_fields.id = subscriber_custom_field.custom_field_id WHERE subscriber_custom_field.subscriber_id = %d", $subscriber->id );
				$custom_fields = $wpdb->get_results( $sql );
				$insert = array();
				foreach ( $custom_fields as $custom_field ) {
					$insert[ sanitize_key( $custom_field->name ) ] = $custom_field->value;
				}

				mailster( 'subscribers' )->add_custom_value( $subscriber_id,$insert );

			}
			$count++;

		}

		return $count;

	}

	public function import_lists() {

		$limit = 1;
		$offset = $limit * $this->round;
		$count = 0;

		global $wpdb;
		$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}mailpoet_segments AS segments ORDER BY id LIMIT %d, %d", $offset, $limit );

		$data = $wpdb->get_results( $sql );
		$lists = mailster( 'lists' )->get();
		$overwrite = false;

		foreach ( $data as $list ) {

			$listdata = array(
				'name' => $list->name,
				'parent_id' => 0,
				'slug' => sanitize_title( $list->name ),
				'description' => $list->description,
				'added' => strtotime( $list->created_at ),
				'updated' => strtotime( $list->updated_at ),
			);

			$list_id = mailster( 'lists' )->add( $listdata, $overwrite );

			if ( is_wp_error( $list_id ) ) {
				$this->error( $list_id->get_error_message() );
			} else {

				$this->map( 'lists', $list->id, $list_id );

				$this->success( sprintf( 'List %s added', $list->name ) );

				$sql = $wpdb->prepare( "SELECT subscribers.ID AS subscriber_id, mailpoet_subscriber_segment.created_at FROM {$wpdb->prefix}mailpoet_subscriber_segment AS mailpoet_subscriber_segment LEFT JOIN {$wpdb->prefix}mailpoet_segments AS mailpoet_segments ON mailpoet_segments.id = mailpoet_subscriber_segment.segment_id LEFT JOIN {$wpdb->prefix}mailpoet_subscribers AS mailpoet_subscribers ON mailpoet_subscriber_segment.subscriber_id = mailpoet_subscribers.id LEFT JOIN {$wpdb->prefix}mailster_subscribers AS subscribers ON subscribers.email = mailpoet_subscribers.email WHERE mailpoet_subscriber_segment.segment_id = %d", $list->id );

				$connections = $wpdb->get_results( $sql );

				foreach ( $connections as $connection ) {
					mailster( 'lists' )->assign_subscribers( $list_id, $connection->subscriber_id, false, strtotime( $connection->created_at ) );
				}
			}
			$count++;

		}
		return $count;
	}


	public function import_custom_fields() {

		$limit = 10;
		$offset = $limit * $this->round;
		$count = 0;

		global $wpdb;
		$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}mailpoet_custom_fields AS custom_fields ORDER BY id LIMIT %d, %d", $offset, $limit );

		$data = $wpdb->get_results( $sql );
		$custom_fields = mailster()->get_custom_fields();

		foreach ( $data as $custom_field ) {
			$id = sanitize_key( $custom_field->name );
			if ( isset( $custom_fields[ $id ] ) ) {
				$this->warning( sprintf( 'Custom Field %s already exists', $name ) );
			} else {
				$name = $custom_field->name;
				$default = null;
				$values = null;
				$type = $custom_field->type;
				if ( mailster()->add_custom_field( $name, $type, $values, $default, $id ) ) {
				} else {
					$this->error( sprintf( 'not able to create custom field %s', $name ) );
				}
			}
			$count++;

		}

		return $count;
	}


	public function import_forms() {

		$limit = 1;
		$offset = $limit * $this->round;
		$count = 0;

		global $wpdb;
		$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}mailpoet_forms AS mailpoet_forms ORDER BY id LIMIT %d, %d", $offset, $limit );

		$data = $wpdb->get_results( $sql );
		$forms = mailster( 'forms' )->get_all();
		$overwrite = false;

		foreach ( $data as $form ) {

			$body = maybe_unserialize( $form->body );
			$settings = maybe_unserialize( $form->settings );

			$formdata = array(
				'name' => $form->name,
				'userschoice' => false,
				'overwrite' => true,
				'style' => '',
				'custom_style' => $form->styles,
				'doubleoptin' => true,
				'confirmredirect' => '',
				'redirect' => get_permalink( $settings['success_page'] ),
				'added' => strtotime( $form->created_at ),
				'updated' => strtotime( $form->updated_at ),
			);

			$form_id = mailster( 'forms' )->add( $formdata );

			if ( is_wp_error( $form_id ) ) {
				$this->error( $form_id->get_error_message() );
			} else {

				$this->map( 'forms', $form->id, $form_id );

				$this->success( sprintf( 'Form %s added', $form->name ) );
				$this->notice( sprintf( 'Please review Form %s added', $form->name ) );

				$fields = array();
				$required = array();
				$error_msg = array();

				foreach ( $body as $field ) {

					if ( 'submit' == $field['type'] ) {
						if ( isset( $field['params']['label'] ) ) {
							mailster( 'forms' )->update(array(
								'ID' => $form_id,
								'submit' => $field['params']['label'],
							) );
						}
					} elseif ( 'text' == $field['type'] ) {
						$field_id = $field['id'];
						$name = isset( $field['params']['label'] ) ? $field['params']['label'] : $field['name'];

						if ( is_numeric( $field_id ) ) {
							$field_id = sanitize_title( $name );
						}
						$fields[ $field_id ] = $name;
						if ( isset( $field['params']['required'] ) && $field['params']['required'] || 'email' == $field_id ) {
							$required[] = $field_id;
						}
					} elseif ( 'segment' == $field['type'] ) {

						if ( isset( $field['params']['values'] ) && $field['params']['values'] ) {
							mailster( 'forms' )->update(array(
								'ID' => $form_id,
								'userschoice' => true,
							) );

							$list_ids = wp_list_pluck( $field['params']['values'], 'id' );
							$list_ids = $this->get_mapping( 'lists', $list_ids );
							mailster( 'forms' )->assign_lists( $form_id, $list_ids );

						}
					}
				}
				mailster( 'forms' )->update_fields( $form_id, $fields, $required, $error_msg );

			}
			$count++;

		}
		return $count;
	}


	public function import_campaigns() {

		$limit = 4;
		$offset = $limit * $this->round;
		$count = 0;

		global $wpdb;
		$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}mailpoet_newsletters AS mailpoet_newsletters ORDER BY id LIMIT %d, %d", $offset, $limit );

		$data = $wpdb->get_results( $sql );
		$overwrite = false;
		$timeoffset = mailster( 'helper' )->gmt_offset( true );

		foreach ( $data as $campaign ) {

			$created_at = strtotime( $campaign->created_at );
			$updated_at = strtotime( $campaign->updated_at );
			$body = json_decode( $campaign->body );

			$content = $this->get_content_from_object( $body );
			$meta = array(
				'subject' => $campaign->subject,
				'template' => mailster_option( 'default_template' ),
				'file' => 'index.html',
				'from_name' => $campaign->sender_name,
				'from_email' => $campaign->sender_address,
				'reply_to' => $campaign->reply_to_address,
				'track_opens' => mailster_option( 'track_opens' ),
				'track_clicks' => mailster_option( 'track_clicks' ),
				'head' => '',
				'timestamp' => $created_at,
				'preheader' => $campaign->preheader,
				'active' => 0,
				'autoresponder' => array(),
			);

			$sql = $wpdb->prepare( "SELECT segment_id AS lists FROM {$wpdb->prefix}mailpoet_newsletter_segment AS mailpoet_newsletter_segment WHERE newsletter_id = %d", $campaign->id );

			$lists = $wpdb->get_col( $sql );
			$meta['lists'] = (array) $this->get_mapping( 'lists', $lists );

			$sql = $wpdb->prepare( "SELECT name, value, newsletter_type FROM {$wpdb->prefix}mailpoet_newsletter_option AS mailpoet_newsletter_option LEFT JOIN {$wpdb->prefix}mailpoet_newsletter_option_fields AS mailpoet_newsletter_option_fields ON mailpoet_newsletter_option_fields.id = mailpoet_newsletter_option.option_field_id WHERE newsletter_id = %d ORDER BY mailpoet_newsletter_option.id", $campaign->id );

			$campaign_options = $wpdb->get_results( $sql );
			foreach ( $campaign_options as $campaign_option ) {
				if ( 'afterTimeNumber' == $campaign_option->name ) {
					$meta['autoresponder']['amount'] = $campaign_option->value;
				} elseif ( 'afterTimeType' == $campaign_option->name ) {
					$meta['autoresponder']['unit'] = preg_replace( '/s$/', '', $campaign_option->value );
					$meta['autoresponder']['action'] = 'mailster_subscriber_insert';
				} elseif ( 'segment' == $campaign_option->name ) {
					$meta['lists'] = array_unique( array_merge( $meta['lists'], (array) $this->get_mapping( 'lists', $campaign_option->value ) ) );
				} elseif ( 'scheduledAt' == $campaign_option->name ) {
					$meta['timestamp'] = strtotime( $campaign_option->value );
				}
			}

			$post_status = 'paused';
			if ( 'welcome' == $campaign->type ) {
				$post_status = 'autoresponder';
			} elseif ( ! empty( $campaign->sent_at ) ) {
				$meta['finished'] = strtotime( $campaign->sent_at );
				$post_status = 'finished';
				// $meta['total'] = 345;
				// $meta['sent'] = 123;
			}

			$post = new WP_Post((object) array(
				'post_title' => '[MailPoet] ' . $campaign->subject,
				'post_author' => get_current_user_id(),
				'post_content' => $content,
				'post_type' => 'newsletter',
				'post_status' => $post_status,
				'post_date' => date( 'Y-m-d H:i:s', $created_at + $timeoffset ),
				'post_date_gmt' => date( 'Y-m-d H:i:s', $created_at ),
				'post_modified' => date( 'Y-m-d H:i:s', $updated_at + $timeoffset ),
				'post_modified_gmt' => date( 'Y-m-d H:i:s', $updated_at ),
				'post_filter' => 'raw',
			));

			$campaign_id = wp_insert_post( $post );

			if ( is_wp_error( $campaign_id ) ) {
				$this->error( $campaign_id->get_error_message() );
			} else {
				$this->map( 'campaigns', $campaign->id, $campaign_id );
				$this->success( sprintf( 'Campaign %s added', $campaign->subject ) );
				$this->warning( sprintf( 'Not able to add content to Campaign %s', $campaign->subject ), admin_url( 'post.php?post=' . $campaign_id . '&action=edit' ) );

				mailster( 'campaigns' )->update_meta( $campaign_id, $meta );
			}

			$count++;

		}

		return $count;
	}


	public function import_sent() {

		$limit = 100;
		$offset = $limit * $this->round;
		$count = 0;

		global $wpdb;
		$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}mailpoet_statistics_newsletters AS mailpoet_statistics_newsletters ORDER BY id LIMIT %d, %d", $offset, $limit );

		$data = $wpdb->get_results( $sql );
		$overwrite = false;

		foreach ( $data as $sent ) {

			$insert = array(
				'subscriber_id' => $this->get_mapping( 'subscribers', $sent->subscriber_id ),
				'campaign_id' => $this->get_mapping( 'campaigns', $sent->newsletter_id ),
				'timestamp' => strtotime( $sent->sent_at ),
				'count' => 1,
				'type' => 1,
				'link_id' => 0,
			);

			$wpdb->insert( "{$wpdb->prefix}mailster_actions", $insert );
			$count++;

		}

		return $count;
	}

	public function import_clicks() {

		$limit = 100;
		$offset = $limit * $this->round;
		$count = 0;

		global $wpdb;
		$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}mailpoet_statistics_clicks AS mailpoet_statistics_clicks ORDER BY id LIMIT %d, %d", $offset, $limit );

		$data = $wpdb->get_results( $sql );
		$overwrite = false;

		foreach ( $data as $click ) {

			$link = $wpdb->get_var( $wpdb->prepare( "SELECT url FROM {$wpdb->prefix}mailpoet_newsletter_links AS mailpoet_newsletter_links WHERE mailpoet_newsletter_links.id = %d", $click->link_id ) );

			$link_id = mailster( 'actions' )->get_link_id( $link );

			$insert = array(
				'subscriber_id' => $this->get_mapping( 'subscribers', $click->subscriber_id ),
				'campaign_id' => $this->get_mapping( 'campaigns', $click->newsletter_id ),
				'timestamp' => strtotime( $click->created_at ),
				'count' => 1,
				'type' => 2,
				'link_id' => $link_id,
			);

			$wpdb->insert( "{$wpdb->prefix}mailster_actions", $insert );
			$count++;

		}

		return $count;
	}

	public function import_opens() {

		$limit = 100;
		$offset = $limit * $this->round;
		$count = 0;

		global $wpdb;
		$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}mailpoet_statistics_opens AS mailpoet_statistics_opens ORDER BY id LIMIT %d, %d", $offset, $limit );

		$data = $wpdb->get_results( $sql );
		$overwrite = false;

		foreach ( $data as $open ) {

			$insert = array(
				'subscriber_id' => $this->get_mapping( 'subscribers', $open->subscriber_id ),
				'campaign_id' => $this->get_mapping( 'campaigns', $open->newsletter_id ),
				'timestamp' => strtotime( $open->created_at ),
				'count' => 1,
				'type' => 3,
				'link_id' => 0,
			);

			$wpdb->insert( "{$wpdb->prefix}mailster_actions", $insert );

			$count++;
			continue;

			$link = $wpdb->get_var( $wpdb->prepare( "SELECT url FROM {$wpdb->prefix}mailpoet_newsletter_links AS mailpoet_newsletter_links WHERE mailpoet_newsletter_links.id = %d", $click->link_id ) );

			mailster( 'actions' )->open( $this->get_mapping( 'subscribers', $click->subscriber_id ), $this->get_mapping( 'campaigns', $click->newsletter_id ), $link, 0 , true );

		}

		return $count;
	}

	public function get_total_subscribers() {

		global $wpdb;
		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}mailpoet_subscribers";
		return (int) $wpdb->get_var( $sql );

	}
	public function get_total_custom_fields() {

		global $wpdb;
		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}mailpoet_custom_fields";
		return (int) $wpdb->get_var( $sql );

	}
	public function get_total_lists() {
		global $wpdb;
		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}mailpoet_segments";
		return (int) $wpdb->get_var( $sql );
	}
	public function get_total_forms() {
		global $wpdb;
		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}mailpoet_forms";
		return (int) $wpdb->get_var( $sql );
	}
	public function get_total_campaigns() {
		global $wpdb;
		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}mailpoet_newsletters";
		return (int) $wpdb->get_var( $sql );
	}
	public function get_total_sent() {
		global $wpdb;
		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}mailpoet_statistics_newsletters";
		return (int) $wpdb->get_var( $sql );
	}
	public function get_total_clicks() {
		global $wpdb;
		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}mailpoet_statistics_clicks";
		return (int) $wpdb->get_var( $sql );
	}
	public function get_total_opens() {
		global $wpdb;
		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}mailpoet_statistics_opens";
		return (int) $wpdb->get_var( $sql );
	}

	private function get_status_code( $status ) {
		if ( 'unconfirmed' == $status ) {
			return 0;
		} elseif ( 'subscribed' == $status ) {
			return 1;
		} elseif ( 'unsubscribed' == $status ) {
			return 2;
		} elseif ( 'bounced' == $status ) {
			return 3;
		}
		return 4;

	}

	private function get_content_from_object( $object ) {

		$html = '';

		foreach ( $object->content->blocks as $i => $block ) {
			// code...
		}

		return mailster()->sanitize_content( $html );

	}

}
