mailster = (function (mailster, $, window, document) {
	'use strict';

	$('.convert_form_wrap')
		.on('submit', '.convert_form', function () {
			var form = $(this),
				wrap = form.parent(),
				email = wrap.find('input.email').val(),
				error;

			form.removeClass('has-error').prop('disabled', true);
			wrap.addClass('loading');

			mailster.util.ajax(
				'convert',
				{
					email: email,
				},
				function (response) {
					form.prop('disabled', false);
					wrap.removeClass('loading');

					console.warn(response);

					if (response.success) {
						wrap.addClass('step-2').removeClass('step-1');
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
					alert(mailster.l10n.convert.error + '\n\n' + errorThrown);
				}
			);

			return false;
		})
		.on('submit', '.convert_form_2', function () {
			var form = $(this),
				wrap = form.parent(),
				purchasecode = wrap
					.find('input.convert-form-purchasecode')
					.val(),
				slug = wrap.find('input.convert-form-slug').val(),
				error;

			form.removeClass('has-error').prop('disabled', true);
			wrap.addClass('loading');

			mailster.util.ajax(
				'convert',
				{
					purchasecode: purchasecode,
					slug: slug,
					data: form.serialize(),
				},
				function (response) {
					form.prop('disabled', false);
					wrap.removeClass('loading');
					if (response.success) {
						wrap.addClass('step-3').removeClass('step-2');
						mailster.$.document.trigger('verified.' + slug, [
							response.data.purchasecode,
							response.data.username,
							response.data.email,
						]);
					} else {
						if (
							response.data.code == 406 ||
							response.data.code == 679 ||
							response.data.code == 680
						) {
							form = wrap.find('.convert_form');
							form.parent()
								.removeClass('step-2')
								.addClass('step-1');
						}
						error = response.data.error;
						error +=
							' (<a href="https://evp.to/error-' +
							response.data.code +
							'" target="_blank" rel="noopener">' +
							mailster.l10n.convert.help +
							'</a>)';
						form.addClass('has-error')
							.find('.error-msg')
							.html(error);
					}
				},
				function (jqXHR, textStatus, errorThrown) {
					form.prop('disabled', false);
					wrap.removeClass('loading');
					alert(mailster.l10n.convert.error + '\n\n' + errorThrown);
				}
			);

			return false;
		})
		.removeClass('loading');

	function popup(url, width, height, windowname) {
		var dualScreenLeft =
				window.screenLeft != undefined
					? window.screenLeft
					: screen.left,
			dualScreenTop =
				window.screenTop != undefined ? window.screenTop : screen.top,
			windowWidth = window.innerWidth
				? window.innerWidth
				: document.documentElement.clientWidth
				? document.documentElement.clientWidth
				: screen.width,
			windowHeight = window.innerHeight
				? window.innerHeight
				: document.documentElement.clientHeight
				? document.documentElement.clientHeight
				: screen.height,
			left = windowWidth / 2 - width / 2 + dualScreenLeft,
			top = windowHeight / 2 - height / 2 + dualScreenTop,
			newWindow = window.open(
				url,
				windowname,
				'scrollbars=auto,resizable=1,menubar=0,toolbar=0,location=0,directories=0,status=0, width=' +
					width +
					', height=' +
					height +
					', top=' +
					top +
					', left=' +
					left
			);

		if (window.focus) newWindow.focus();
	}

	window.verifyMailster = function (slug, purchasecode, username, email) {
		var wrap = $('.convert_form_wrap-' + slug);
		wrap.find('input.convert-form-purchasecode').val(purchasecode);
		wrap.find('input.username').val(username);
		wrap.find('input.email').val(email);

		if (purchasecode) {
			wrap.find('.convert_form')
				.parent()
				.removeClass('step-1')
				.addClass('step-2');
		} else {
			wrap.find('.convert_form').submit();
		}
	};

	return mailster;
})(mailster || {}, jQuery, window, document);
