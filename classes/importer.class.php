<?php

abstract class MailsterImporter {

	private $name;
	private $description;
	private $post_data;
	private $errors;
	private $round;
	private $mapping = array();

	public function __construct() {

		$this->errors = array(
			'count' => 0,
			'error_count' => 0,
			'warning_count' => 0,
			'notice_count' => 0,
			'success_count' => 0,
			'all' => array(),
			'error' => array(),
			'warning' => array(),
			'notice' => array(),
			'success' => array(),
		);
	}

	public function set( $name, $value ) {

	    $reflector = new ReflectionClass( get_class( $this ) );
	    $prop = $reflector->getProperty( $name );
	    $prop->setAccessible( true );
	    $prop->setValue( $this, $value );

	}

	public function get( $name ) {

		if ( property_exists( $this, $name ) ) {
			$reflection = new ReflectionProperty( $this, $name );
			$reflection->setAccessible( $name );
			return $reflection->getValue( $this );
		}
	}

	public function get_the( $name, $value = null ) {
		if ( is_null( $value ) ) {
			return $this->get( $name );
		} else {
			$this->set( $name, $value );
		}
	}

	public function name( $value = null ) {
		return $this->get_the( 'name', $value );
	}
	public function description( $value = null ) {
		return $this->get_the( 'description', $value );
	}
	public function id( $value = null ) {
		return str_replace( __CLASS__, '', get_class( $this ) );
	}

	public function get_errors() {

		return $this->errors;
	}

	public function get_message() {

		$time = date( 'Y-m-d H:i:s' );
		$html = '';

		foreach ( array( 'error', 'warning', 'notice', 'success' ) as $type ) {
			if ( ! $this->errors[ $type . '_count' ] ) {
				continue;
			}
			foreach ( $this->errors[ $type ] as $i => $error ) {
				$name = ucwords( $type );
				$html .= '<div class="mailster-import-result mailster-import-is-' . $type . '"><h4>' . $name . ($error['data']['link'] ? ' (<a class="mailster-import-result-link external" href="' . esc_url( $error['data']['link'] ) . '">' . __( 'More Info', 'mailster' ) . '</a>)' : '') . '</h4><div class="mailster-import-result-more">' . nl2br( $error['msg'] ) . '</div></div>';
			}
		}

		return array(
			'time' => $time,
			'html' => $html,
		);
	}

	public function get_error_counts() {

		return array(
			'error' => $this->errors['error_count'],
			'warning' => $this->errors['warning_count'],
			'notice' => $this->errors['notice_count'],
			'success' => $this->errors['success_count'],
		);

	}

	protected function error( $msg, $link = null ) {

		$this->failure( 'error', $msg, $link );

	}


	protected function warning( $msg, $link = null ) {

		$this->failure( 'warning', $msg, $link );

	}


	protected function notice( $msg, $link = null ) {

		$this->failure( 'notice', $msg, $link );

	}

	protected function success( $msg, $link = null ) {

		$this->failure( 'success', $msg, $link );

	}


	private function failure( $type, $msg, $link = null ) {

		$data = array( 'link' => $link );

		if ( ! isset( $this->errors['all'] ) ) {
			$this->errors['all'] = array();
		}
		$this->errors['all'][] = array(
			'msg' => $msg,
			'data' => $data,
		);
		if ( ! isset( $this->errors[ $type ] ) ) {
			$this->errors[ $type ] = array();
		}
		$this->errors[ $type ][] = array(
			'msg' => $msg,
			'data' => $data,
		);
		$this->errors['count']++;
		$this->errors[ $type . '_count' ]++;

	}

	public function display() {
		include MAILSTER_DIR . 'views/importer/module.php';
	}

	public function step3() {

		echo '<table class="form-table">';

		foreach ( $this->supports() as $key ) : $name = $this->get_nice_support_name( $key ) ?>

			<tr valign="top">
				<th scope="row"><?php echo esc_html( $name ) ?></th>
				<td>
					<label><input type="checkbox" name="import_part[]" value="<?php echo esc_attr( $key ) ?>" checked> <?php printf( esc_html__( 'Import %s', 'mailster' ), $name ) ?></label>
				</td>
			</tr>

		<?php endforeach;

		echo '</table>';

		include MAILSTER_DIR . 'views/importer/import.php';
	}

	public function post_data( $data ) {
		$this->post_data = $data;
	}

	private function get_nice_support_name( $key ) {
		$names = array(
			'custom_fields' => 'Custom Fields',
			'subscribers' => 'Subscribers',
			'lists' => 'Lists',
			'forms' => 'Forms',
			'campaigns' => 'Campaigns',
			'sent' => 'Sent',
			'clicks' => 'Clicks',
			'opens' => 'Opens',
		);

		if ( isset( $names[ $key ] ) ) {
			return $names[ $key ];
		}

		return ucwords( $key );
	}


	abstract public function supports();
	abstract public function step2();
	abstract public function import_subscribers();
	abstract public function import_lists();

	public function import( $what, $round = 0 ) {

		$this->set( 'round', $round );

		return $this->{'import_' . $what}();

	}
	public function get_total( $what ) {

		return $this->{'get_total_' . $what}();

	}

	protected function map( $type, $original_id, $mailster_id ) {

		if ( empty( $this->mapping ) ) {
			$this->mapping = get_transient( '_mailster_import_mapping' );
		}

		if ( ! isset( $this->mapping[ $type ] ) ) {
			$this->mapping[ $type ] = array();
		}
		$this->mapping[ $type ][ $original_id ] = $mailster_id;

		set_transient( '_mailster_import_mapping', $this->mapping );

	}
	protected function get_mapping( $type, $original_ids ) {

		$this->mapping = get_transient( '_mailster_import_mapping' );

		if ( ! isset( $this->mapping[ $type ] ) ) {
			return $original_ids;
		}

		if ( ! is_array( $original_ids ) ) {
			if ( isset( $this->mapping[ $type ][ $original_ids ] ) ) {
				return $this->mapping[ $type ][ $original_ids ];
			}
		}

		$return = array();

		foreach ( $original_ids as $original_id ) {
			if ( isset( $this->mapping[ $type ][ $original_id ] ) ) {
				$return[] = $this->mapping[ $type ][ $original_id ];
			}
		}

		return $return;

	}


}
