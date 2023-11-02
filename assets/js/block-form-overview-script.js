mailster = (function (mailster, $, window, document) {
	'use strict';

	mailster.$.document.on('click', '.form_preview a', function () {
		var url = $(this).attr('href');
		tb_show('', url + '?KeepThis=true&TB_iframe=true&height=600&width=800');
		return false;
	});

	return mailster;
})(mailster || {}, jQuery, window, document);
