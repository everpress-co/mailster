mailster = (function (mailster, $, window, document) {

	"use strict";

	var deletestatus = $('.status');

	mailster.events.push('documentReady', update_delete_count);

	mailster.$.document
		.on('change', '#delete-subscribers input,#delete-subscribers select', update_delete_count)
		.on('submit', '#delete-subscribers', function () {

			var input = prompt(mailster.l10n.manage.confirm_delete, 'delete');

			if (!input) return false;

			if ('delete' == input.toLowerCase()) {

				var data = $(this).serialize();

				deletestatus.addClass('progress spinner');

				mailster.util.ajax('delete_contacts', {
					data: data,
				}, function (response) {

					if (response.success) {
						deletestatus.html(response.msg);
					} else {
						deletestatus.html(response.msg);
					}
					deletestatus.removeClass('spinner');
					update_delete_count();

				}, function (jqXHR, textStatus, errorThrown) {

					deletestatus.html('[' + jqXHR.status + '] ' + errorThrown);

				});

			}

			return false;
		});

	function update_delete_count() {

		setTimeout(function () {
			var data = $('#delete-subscribers').serialize();
			$('#delete-subscriber-button').prop('disabled', true);

			mailster.util.ajax('get_subscriber_count', {
				data: data
			}, function (response) {

				if (response.success) {
					$('#delete-subscriber-button').val(mailster.util.sprintf(mailster.l10n.manage.delete_n_subscribers, response.count_formated)).prop('disabled', !response.count);
				}

			});
		}, 10);

	}

	return mailster;

}(mailster || {}, jQuery, window, document));