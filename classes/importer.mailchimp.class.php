<?php

class MailsterImporterMailChimp extends MailsterImporter {

	private $name        = 'MailChimp';
	private $description = 'Import Subscribers, Lists and Campaigns from MailChimp';
	private $round;

	public function step2() {
		?>
		<p><?php esc_html_e( 'You can Import following things into Mailster:', 'mailster' ); ?></p>

		<p><?php esc_html_e( 'Please insert your API Key from MailChimp:', 'mailster' ); ?></p>
		<input type="name" name="apikey" required="">

		<?php

	}
	public function supports() {
		return array( 'subscribers' );
	}

	public function import_subscribers() {
		error_log( 'importSubscribers ' . $this->round . $this->get_total() );
		if ( $this->round >= 10 ) {
			return 0;
		}
		if ( $this->round == 1 ) {
			$this->error( 'Problem' );
			$this->notice( 'Info' );
		}
		return 100;

	}

	public function import_lists() {
		error_log( 'importLists ' . $this->round );

		return 100;
	}

	public function get_total_subscribers() {
		return 1000;
	}
	public function get_total_lists() {
		return 1000;
	}

}
