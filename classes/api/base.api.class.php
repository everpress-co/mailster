<?php

abstract class Mailster_REST_Controller extends WP_REST_Controller {

	protected $namespace = 'mailster/v1';

	public function __construct() {

		$this->register_routes();

	}

}
