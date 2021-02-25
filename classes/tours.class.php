<?php

class MailsterTours {

	private $metaboxes = array();

	public function __construct() {

		add_action( 'admin_init', array( &$this, 'init' ) );

	}


	public function init() {

		add_action( 'admin_enqueue_scripts', array( &$this, 'scripts_styles' ), 10, 4 );

	}


	public function scripts_styles() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		$tours = $this->get_tours();

		if ( ! empty( $tours ) ) {

			wp_enqueue_script( 'mailster-jtour', MAILSTER_URI . 'assets/js/libs/jTour.js', array( 'jquery' ), MAILSTER_VERSION, true );
			wp_enqueue_script( 'mailster-tour', MAILSTER_URI . 'assets/js/tour-script' . $suffix . '.js', array( 'mailster-jtour', 'mailster-script' ), MAILSTER_VERSION, true );
			wp_enqueue_style( 'mailster-jtour', MAILSTER_URI . 'assets/css/libs/jTour.css', array(), MAILSTER_VERSION );
			wp_enqueue_style( 'mailster-tour', MAILSTER_URI . 'assets/css/tour-style' . $suffix . '.css', array( 'mailster-jtour' ), MAILSTER_VERSION );

			wp_localize_script(
				'mailster-tour',
				'mailsterTour',
				array(
					'endpoint' => MAILSTER_URI . 'assets/tours/',
					'tours'    => $tours,
				)
			);

			mailster_localize_script(
				'mailster-tour',
				array(
					'next' => esc_html__( 'next', 'mailster' ),
					'prev' => esc_html__( 'prev', 'mailster' ),
				)
			);

		}

	}


	public function get_tours( $user_id = null ) {

		global $post, $pagenow;

		if ( is_null( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$tours = array();

		$screen = get_current_screen();

		error_log( print_r( $pagenow . '/' . $screen->post_type . '/' . $screen->id, true ) );

		switch ( $pagenow . '/' . $screen->post_type . '/' . $screen->id ) {
			case 'post-new.php/newsletter/newsletter':
				$tours[] = 'precheck';
				$tours[] = 'edit';
				$tours[] = 'play';
				break;
			case 'admin.php//newsletter_page_mailster_dashboard':
				$tours[] = 'dashboard';
				break;

			case 'edit.php/newsletter/newsletter_page_mailster_templates':
				$tours[] = 'templates';
				break;

			default:
				break;
		}

		if ( empty( $tours ) ) {
			return array();
		}

		$seen_tours = $this->get_seen_tours( $user_id );

		// get tours which are not played already
		$tours = array_values( array_diff_key( array_unique( $tours ), array_keys( $seen_tours ) ) );

		return $tours;

	}


	public function get_seen_tours( $user_id = null ) {
		if ( is_null( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$seen_tours = get_user_meta( get_current_user_id(), '__mailster_tours', true );

		if ( empty( $seen_tours ) ) {
			return array();
		}

		return $seen_tours;
	}


	public function mark_as_seen( $test_id, $user_id = null ) {
		if ( is_null( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$seen_tours             = $this->get_seen_tours( $user_id );
		$seen_tours[ $test_id ] = time();

		return update_user_meta( $user_id, '_mailster_tours', $seen_tours );

	}



}
