<?php

class MailsterApi {

	public function __construct() {

		add_action( 'rest_api_init', array( &$this, 'init' ) );
	}


	public function init() {

		// $constrollers = glob( MAILSTER_DIR . 'classes/rest-controller/*.php' );

		require MAILSTER_DIR . 'classes/rest-controller/rest.lists.class.php';

		$controller = new Mailster_REST_List_Controller();
		$controller->register_routes();

		require MAILSTER_DIR . 'classes/rest-controller/rest.susbcribe.class.php';

		$controller = new Mailster_REST_Subscribe_Controller();
		$controller->register_routes();

	}

}
