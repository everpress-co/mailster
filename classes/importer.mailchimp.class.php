<?php

class MailsterImporterMailChimp extends MailsterImporter {

	private $name = 'MailChimp';

	public function step2() {
?>
		<p><?php esc_html_e( 'You can Import following things into Mailster:', 'mailster' );?></p>

		<p><?php esc_html_e( 'Please insert your API Key from MailChimp:', 'mailster' );?></p>
		<input type="name" name="apikey" required="">

<?php

	}

	public function importSubscribers() {
		error_log( 'importSubscribers' );
	}

	public function import( $what, $round = 0 ) {

		error_log( 'import ' . $what . ' round ' . $round );
	}
}
