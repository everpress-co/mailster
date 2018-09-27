<?php

abstract class MailsterImporter {

	private $name;
	private $post_data;

	public function __construct() {

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
	public function id( $value = null ) {
		return str_replace( __CLASS__, '', get_class( $this ) );
	}
	public function supports() {
		return array(
			'subscribers' => 'Subscribers',
			'lists' => 'Lists',
		);
	}

	public function display() {
		include MAILSTER_DIR . 'views/importer/module.php';
	}

	public function step3() {

		echo '<pre>' . print_r( $_POST, true ) . '</pre>';

		echo '<table class="form-table">';

		foreach ( $this->supports() as $key => $name ) : ?>

			<tr valign="top">
				<th scope="row"><?php echo esc_html( $name ) ?></th>
				<td>
					<label><input type="hidden" name="import[<?php echo $key ?>]" value=""><input type="checkbox" name="import[<?php echo $key ?>]" value="1" checked> <?php printf( esc_html__( 'Import %s', 'mailster' ), $name ) ?></label>
				</td>
			</tr>

		<?php endforeach;

		echo '</table>';

		include MAILSTER_DIR . 'views/importer/import.php';
	}

	public function post_data( $data ) {
		$this->post_data = $data;
	}


	abstract public function step2();
	abstract public function importSubscribers();
	abstract public function import( $what);



}
