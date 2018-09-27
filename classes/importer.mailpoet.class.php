<?php

class MailsterImporterMailpoet extends MailsterImporter {

	private $name = 'MailPoet';
	private $description = 'Import Subscribers, Lists and Campaigns from MailPoet';
	private $round;

	public function step2() {
?><p><?php esc_html_e( 'You can Import following things into Mailster:', 'mailster' );?></p><?php

	}

	public function supports() {
		return array( 'subscribers', 'lists' );
	}

	public function import_subscribers() {
		error_log( 'importSubscribers ' . $this->round . $this->get_total_subscribers() );
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
