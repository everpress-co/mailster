<?php

abstract class MailsterImport {

	protected $slug;

	private $credentials;

	public function __construct() {

		$this->init();
		add_action( 'wp_ajax_mailster_importer_form_submit', array( $this, 'ajax_importer_form_submit' ) );

	}

	abstract protected function init();

	public function get_slug() {
		return $this->slug;
	}

	protected function update_credentials( $data ) {
		set_transient( 'mailster_importer_credentials_' . $this->slug, $data, 16 );
		$this->credentials = $data;
	}

	protected function get_credentials() {
		if ( ! $this->credentials ) {
			$this->credentials = get_transient( 'mailster_importer_credentials_' . $this->slug );
		}

		return $this->credentials;
	}

	private function ajax_nonce( $return = null, $nonce = 'mailster_nonce' ) {
		mailster( 'ajax' )->ajax_nonce( $return, $nonce );
	}

	public function ajax_importer_form_submit() {

		$this->ajax_nonce();

		$slug = basename( $_POST['slug'] );

		if ( $slug != $this->slug ) {
			return;
		}

		parse_str( $_POST['data'], $data );
		$return = array();

		if ( isset( $data['lists'] ) ) {

			$this->get_sample_data( $data['lists'] );

			$return['html'] = 'OK';

		} else {
			ob_start();

			$this->check_credentials( $data );

			$output = ob_get_contents();

			ob_end_clean();

			$return['html'] = $output;

		}

		wp_send_json( $return );

	}

	protected function check_credentials( $data ) {

		$this->update_credentials( $data );

		$this->import_options();

	}

	abstract protected function get_lists();

	abstract protected function get_fields();

	abstract protected function get_sample_data( $lists);

	public function import_options() {

		// get credentials form if we need it
		if ( method_exists( $this, 'credentials_form' ) && ! $this->get_credentials() ) : ?>

		<form class="importer-form" data-slug="<?php echo esc_attr( $this->slug ); ?>">
			<?php
			$this->credentials_form();
			submit_button( __( 'Next Step', 'mailster' ) . '  &#x2192;', 'primary', 'submit' );
			?>
		</form>
			<?php
		else :

			$lists = $this->get_lists();
			?>
		<form class="importer-form" data-slug="<?php echo esc_attr( $this->slug ); ?>">
		  <ul>
			<?php foreach ( $lists as $list ) : ?>
			  <li><label><input type="checkbox" name="lists[]" value="<?php echo esc_attr( $list['id'] ); ?>" checked> <?php echo esc_html( $list['name'] ); ?></label></li>
			<?php endforeach; ?>
		  </ul>
			<?php submit_button( __( 'Next Step', 'mailster' ) . '  &#x2192;', 'primary', 'submit' ); ?>
		</form>

			<?php
		endif;
	}

	protected function map_meta_fields( $meta_fields, $field_map ) {

	}

}
