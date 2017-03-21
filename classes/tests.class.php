<?php

class MailsterTests {


	private $message;
	private $tests;
	private $current;

	private $errors;

	public function __construct( $test ) {

		$this->tests = $this->get_tests();
		$this->errors = (object) array(
			'error_count' => 0,
			'warning_count' => 0,
			'fatal' => new WP_Error(),
			'notice' => new WP_Error(),
			'errors' => new WP_Error(),
			'warnings' => new WP_Error(),
		);

	}

	public function __call( $method, $args ) {

	}

	public function run( $test_id, $args = array() ) {

		if ( isset( $this->tests[ $test_id ] ) ) {

			$this->current_id = $test_id;
			$this->current = $this->tests[ $test_id ];

			return call_user_func_array( array( &$this, $this->current ), $args );

		}

		return null;
	}

	public function get_tests() {
		$tests = get_class_methods( $this );
		$tests = preg_grep( '/^test_/', $tests );
		return array_values( $tests );

	}

	public function get_message() {

		$time = date( 'Y-m-d H:i:s' );

		return array(
			'test' => $this->current,
			'time' => $time,
			'html' => '<div class="mailster-test-result">' . $this->nicename( $this->current ) . ': ' . $this->message . '</div>',
		);
	}

	public function nicename( $test ) {
		return ucwords( str_replace( array( 'test_', '_' ), array( '', ' ' ), $test ) );
	}

	public function get_next() {

		if ( isset( $this->tests[ $this->current_id + 1 ] ) ) {
			return $this->current_id + 1;
		}
		return null;
	}


	private function fatal( $msg, $id = null ) {

		$this->failure( 'fatal', $msg, $id );

	}


	private function error( $msg, $id = null ) {

		$this->failure( 'error', $msg, $id );

	}


	private function warning( $msg, $id = null ) {

		$this->failure( 'warning', $msg, $id );

	}


	private function notice( $msg, $id = null ) {

		$this->failure( 'notice', $msg, $id );

	}


	private function failure( $type, $msg, $id = null ) {

		if(is_null($id)){
			$id = uniqid();
		}
		$failure = array( 'msg' => esc_html( $msg ) );

		$this->errors[ $type ]->add($id, $failure);

	}








	private function test_php_version() {
		if ( version_compare( PHP_VERSION, '5.3' ) < 0 ) {
			$this->error( sprintf( 'Mailster requires PHP version 5.3 or higher. Your current version is %s. Please update or ask your hosting provider to help you updating.', PHP_VERSION ) );
		}
	}
	private function test_wordpress_version() {
		if ( version_compare( get_bloginfo( 'version' ), '3.8' ) < 0 ) {
			$this->error( sprintf( 'Mailster requires WordPress version 3.8 or higher. Your current version is %s.', get_bloginfo( 'version' ) ) );
		}
	}

	private function test_dom_document_extension() {
		if ( ! class_exists( 'DOMDocument' ) ) {
			$this->error( 'Mailster requires the <a href="https://php.net/manual/en/class.domdocument.php" target="_blank">DOMDocument</a> library.' );
		}
	}

	private function test_fsockopen_extension() {
		if ( ! function_exists( 'fsockopen' ) ) {
			$this->warning( 'Your server does not support <a href="https://php.net/manual/en/function.fsockopen.php" target="_blank">fsockopen</a>.' );
		}
	}
	private function test_content_directory() {
		$content_dir = dirname( MAILSTER_UPLOAD_DIR );
		if ( ! is_dir( $content_dir ) || ! wp_is_writable( $content_dir ) ) {
			$this->warning( sprintf( 'Your content folder in %s is not writeable.', '"' . $content_dir . '"' ) );
		}
	}
	private function test_memory_limit() {
		if ( max( intval( @ini_get( 'memory_limit' ) ), intval( WP_MAX_MEMORY_LIMIT ) ) < 128 ) {
			$this->warning( 'Your Memory Limit is ' . size_format( WP_MEMORY_LIMIT * 1048576 ) . ', Mailster recommends at least 128 MB' );
		}
	}


}
