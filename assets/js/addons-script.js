mailster = (function (mailster, $, window, document) {
	"use strict";

	$('a.external').on('click', function () {
		if (this.href) window.open(this.href);
		return false;
	});

	mailster.events.push('documentReady', function () {})

	return mailster;

}(mailster || {}, jQuery, window, document));