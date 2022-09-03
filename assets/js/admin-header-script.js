mailster = (function (mailster, $, window, document) {
	'use strict';

	$(document).on('click', '#mailster-admin-help', function () {
		$('.mailster-offscreen-canvas').toggleClass('is-open');
	});

	return mailster;
})(mailster || {}, jQuery, window, document);
