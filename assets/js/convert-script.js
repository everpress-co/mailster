mailster = (function (mailster, $, window, document) {
	'use strict';

	$('.convert_form_wrap')
		.on('submit', '.convert_form', function () {
			var form = $(this),
				wrap = form.parent(),
				email = wrap.find('input.email').val(),
				license = wrap.find('input.license').val(),
				error;

			form.removeClass('has-error').prop('disabled', true);
			wrap.addClass('loading');

			mailster.util.ajax(
				'convert',
				{
					email: email,
					license: license,
				},
				function (response) {
					form.prop('disabled', false);
					wrap.removeClass('loading');

					if (response.success) {
						wrap.addClass('step-2').removeClass('step-1');

						$.each(response.data.data.texts, function (i, text) {
							$('.result').append('<li>' + text + '</li>');
						});

						$('.convert-plan').html(response.data.data.plan);
					} else {
						error = response.data.error;
						form.addClass('has-error')
							.find('.error-msg')
							.html(error);
					}
				},
				function (jqXHR, textStatus, errorThrown) {
					form.prop('disabled', false);
					wrap.removeClass('loading');
					alert(errorThrown);
				}
			);

			return false;
		})
		.removeClass('loading');

	return mailster;
})(mailster || {}, jQuery, window, document);
