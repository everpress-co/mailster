<?php

class Sendria {

	static $instance;
	private $endpoint = '';
	private $messages = array();


	private function __construct( $host, $port ) {

		$this->endpoint = trailingslashit( 'http://' . $host . ':' . $port );
	}

	public static function getInstance( $host = 'localhost', $port = '1080' ) {
		if ( ! self::$instance ) {
			self::$instance = new self( $host, $port );
		}

		return self::$instance;
	}

	public function get_messages( $page = 1 ) {
		if ( ! isset( $messages[ $page ] ) ) {
			$response          = $this->request( 'api/messages', array( 'page' => $page ) );
			$messages[ $page ] = $response->data;
		}

		return $messages[ $page ];
	}
	public function get_by_subject( $subject ) {

		$page     = 1;
		$messages = array();
		$found    = false;

		while ( true ) {
			$messages = $this->get_messages( $page );
			if ( empty( $messages ) ) {
				break;
			}
			foreach ( $messages as $message ) {
				if ( $subject === $message->subject ) {
					$found = $message;
					break;
				}
			}
			++$page;
		}

		return $found;
	}


	private function request( $location, $args = array() ) {

		$url = $this->endpoint . trailingslashit( $location );
		if ( ! empty( $args ) ) {
			$url = add_query_arg( $args, $url );
		}

		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			throw new Exception( $response->get_error_message(), 1 );
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $code ) {
			throw new Exception( 'Response was ' . $code, 1 );
		}

		$body = wp_remote_retrieve_body( $response );

		$decoded = json_decode( $body );

		if ( $decoded->code !== 'OK' ) {
			throw new Exception( 'Response was ' . $decoded->code, 1 );
		}

		return $decoded;
	}
}
