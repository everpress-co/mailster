mailster = (function (mailster, $, window, document) {

	"use strict";

	var clipboard = new Clipboard('.clipboard');
	mailster.events.push('documentReady', function () {
		clipboard.on('success', function (e) {
			var html = $(e.trigger).html();
			$(e.trigger).html(mailsterClipboardL10.copied);
			setTimeout(function () {
				$(e.trigger).html(html);
				e.clearSelection();
			}, 3000);
		});

		clipboard.on('error', function (e) {});
	})

	return mailster;

}(mailster || {}, jQuery, window, document));