<?php

abstract class MailsterImport {

	public $slug;

	public $name;

	public function __construct() {

		$this->init();

	}

	abstract protected function init();


	protected function map_meta_fields( $meta_fields, $field_map ) {

	}

}
