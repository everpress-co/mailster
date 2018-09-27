<?php

class MailsterImporterMailpoet extends MailsterImporter {

	private $name = 'MailPoet';

	public function step2() {
?><p><?php esc_html_e( 'You can Import following things into Mailster:', 'mailster' );?></p><?php

	}

	public function importSubscribers() {
		error_log( 'importSubscribers' );
	}

	public function import( $what, $round = 0 ) {
		error_log( 'importSubscribers ' . $what );
	}

}
