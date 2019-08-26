<?php

class MailsterPreflight {

	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ) );
		add_action( 'wp_ajax_mailster_preflight', array( &$this, 'preflight' ) );
		add_action( 'wp_ajax_mailster_preflight_result', array( &$this, 'preflight_result' ) );

	}


	public function init() {

	}


	public function preflight() {

		$return['success'] = false;

		mailster( 'ajax' )->ajax_nonce( json_encode( $return ) );

		$id = isset( $_POST['id'] ) ? sanitize_key( $_POST['id'] ) : false;

		if ( $id ) {

			$response = $this->request( $id );

			if ( is_wp_error( $response ) ) {
				$return['error'] = $response->get_error_message();
			} else {
				$return['success'] = true;
				$return['ready']   = $response->ready;
			}
		}

		mailster( 'ajax' )->json_return( $return );

	}



	public function preflight_result() {

		$return['success'] = false;

		mailster( 'ajax' )->ajax_nonce( json_encode( $return ) );

		$id       = isset( $_POST['id'] ) ? sanitize_key( $_POST['id'] ) : false;
		$endpoint = isset( $_POST['endpoint'] ) ? ( $_POST['endpoint'] ) : false;

		if ( $id ) {

			$response = $this->request( $id, $endpoint, 25 );

			if ( is_wp_error( $response ) ) {
				$return['error'] = $response->get_error_message();
			} else {
				$return['success'] = true;
				$return['status']  = $response->status;
				$return['html']    = $this->convert( $response, $endpoint );
			}
		}

		mailster( 'ajax' )->json_return( $return );

	}


	private function convert( $response, $endpoint ) {

		$html = '';

		switch ( $endpoint ) {

			case 'spam_report':
				$html .= '<p>';
				$html .= sprintf( 'SpamAssasin Score: <strong>%s</strong>.<br>', $response->score );
				$html .= '(<span class="description">' . sprintf( esc_html__( 'A score below %s is considered spam.', 'mailster' ), '<strong>' . $response->threshold . '</strong>' ) . '</span>)';
				$html .= '</p>';
				$html .= '<table class="wp-list-table widefat striped spamreport-table">';
				foreach ( $response->rules as $key => $data ) {
					$html .= '<tr>';
					$html .= '<td>' . $data->score . '</td>';
					$html .= '<td><strong>' . $data->code . '</strong><br>' . esc_html( $data->message ) . '</td>';
					$html .= '<td><a href="' . esc_attr( $data->link ) . '" target="_blank">' . esc_html__( 'info', 'mailster' ) . '</a></td>';
					$html .= '</tr>';
				}
				$html .= '</table>';
				break;

			case 'tests/spf':
			case 'tests/senderid':
				$html .= $response->message;
				$html .= '<pre>' . $response->record . '</pre>';
				break;

			case 'tests/dkim':
				if ( 'fail' == $response->result ) {
					$html = 'The DKIM Signature doesn\'t match: <pre>' . $response->signature . '</pre>';
				} elseif ( 'none' == $response->result ) {
					$html = 'You do not have a DKIM Signature setup.';
				} else {
					$html = 'You DKIM setup is correct. <pre>' . $response->signature . '</pre>';
				}
				break;

			case 'tests/dmarc':
				if ( 'fail' == $response->result ) {
					$html = 'You do not have a valid DMARC record.';
				} elseif ( 'pass' == $response->result ) {
					$html = 'You DMARC setup is correct. <pre>' . $response->record . '</pre>';
				}
				break;

			case 'tests/rdns':
				if ( 'fail' == $response->result ) {
					$html = 'Your Reverse DNS doesn\'t resolve correctly.';
				} elseif ( 'pass' == $response->result ) {
					$html = 'You Reverse DNS is correct. <p><strong>IP:</strong> ' . $response->ip . '<br><strong>HELO:</strong> ' . $response->helo . '<br><strong>DNS:</strong> ' . $response->rdns . '</p>';
				}
				break;

			case 'tests/mx':
				if ( 'fail' == $response->result ) {
					$html = 'You do not have a valid MX record.';
				} elseif ( 'pass' == $response->result ) {
					$html = 'Your server has a MX record. <br><strong>HOST:</strong> ' . $response->host . '<pre>' . $response->record . '</pre>';
				}
				break;

			case 'tests/a':
				if ( 'fail' == $response->result ) {
					$html = 'You do not have a valid A record.';
				} elseif ( 'pass' == $response->result ) {
					$html = 'Your server has an A record. <br><strong>IP:</strong> ' . $response->ip . '<pre>' . $response->record . '</pre>';
				}
				break;

			case 'tests/links':
				$html .= '<table class="wp-list-table widefat striped assets-table">';
				foreach ( $response->links as $i => $link ) {
					$html .= '<tr class="asset-' . ( $link->status ? 'valid' : 'invalid' ) . '" data-href="' . esc_attr( $link->href ) . '"" data-index="' . esc_attr( $link->index ) . '">';
					$html .= '<td><span class="asset-type asset-type-' . $link->type . ' mailster-icon"></span></td>';
					$html .= '<td>';
					if ( $link->href && 'anchor' != $link->type ) {
						$html .= '<a href="' . esc_attr( $link->href ) . '" target="_blank" title="' . esc_attr__( 'open link', 'mailster' ) . '" class="open-link mailster-icon"></a>';
					}
					$html .= '<strong class="the-link" title="' . esc_attr( $link->href ) . '">' . preg_replace( '/^https?:\/\//', '', $link->href ) . '</strong>';
					if ( $link->text ) {
						$html .= esc_html( $link->text ) . '<br>';
					}
					$html .= esc_html( $link->message ) . '</td>';
					$html .= '</tr>';
				}
				$html .= '</table>';
				break;

			case 'tests/images':
				$html .= '<table class="wp-list-table widefat striped assets-table">';
				foreach ( $response->images as $i => $image ) {
					$html .= '<tr class="asset-' . ( $image->status ? 'valid' : 'invalid' ) . '" data-src="' . esc_attr( $image->src ) . '"" data-index="' . esc_attr( $image->index ) . '">';
					$html .= '<td><span class="asset-type asset-type-image mailster-icon"></span></td>';
					$html .= '<td>'.$image->code.'</td>';
					$html .= '<td>';
					$html .= '<strong class="the-link" title="' . esc_attr( $image->src ) . '">' .  basename($image->src ) . '</strong>';
					if ( $image->alt ) {
						$html .= esc_html__( 'Alt text', 'mailster' ) . ': '.esc_html( $image->alt );
					}else{
						$html .= esc_html__( 'No Alt text found.', 'mailster' );
					}
					$html .= '</td>';
					$html .= '</tr>';
				}
				$html .= '</table>';
				error_log( print_r( $response, true ) );
				break;

			default:
				$html .= 'HTML for ' . $endpoint;
				break;
		}

		return $html;

	}


	private function request( $id, $endpoint = null, $timeout = 5 ) {

		$url  = 'https://api.preflight.email/v1';
		$url .= '/' . $id;
		if ( $endpoint ) {
			$url .= '/' . $endpoint;
		}
		$url .= '.json';

		$response = wp_remote_get(
			$url,
			array(
				'timeout' => (int) $timeout,
			)
		);

		$code    = wp_remote_retrieve_response_code( $response );
		$headers = wp_remote_retrieve_headers( $response );

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 503, esc_html__( 'The Preflight service is currently not available. Please check back later.', 'mailster' ) );
		} elseif ( 200 === $code ) {
			$body = wp_remote_retrieve_body( $response );
			$body = json_decode( $body );
			return $body;
		} elseif ( 429 === $code ) {
			return new WP_Error( $code, sprintf( esc_html__( 'You have hit the rate limit. Please try again in %s.', 'mailster' ), human_time_diff( strtotime( $headers['retry-after'] ) ) ) );
		}

	}

}
