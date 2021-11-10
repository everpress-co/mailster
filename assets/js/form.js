(function () {
	'use strict';

	var forms = document.querySelectorAll('form.mailster-form');

	Array.prototype.forEach.call(forms, function (form, index, array) {
		var submitButton = form.querySelectorAll('.mailster-submit-wrapper a');
		var submitHandler = function (event) {
			event.preventDefault();

			form.classList.remove('has-error');
			form.classList.add('loading');

			fetch(form.getAttribute('action'), {
				method: form.getAttribute('method') || 'POST',
				body: new FormData(form),
			})
				.then(function (response) {
					if (response.ok) {
						return response.json();
					}
					return Promise.reject(response);
				})
				.then(function (data) {
					form.classList.remove('loading');
					console.warn('data', data);
				})
				.catch(function (error) {
					form.classList.add('has-error');
					form.classList.remove('loading');
					console.warn('error', error);
				});

			console.warn('ASADASD', event);
		};

		Array.prototype.forEach.call(submitButton, function (el, index, array) {
			el.addEventListener('click', submitHandler);
		});

		console.warn(form, index, array, submitButton);
		form.addEventListener('submit', submitHandler);
	});
})();

false &&
	jQuery(document).ready(function ($) {
		'use strict';

		$('body').on(
			'submit.mailster',
			'form.mailster-ajax-form',
			function (event) {
				event.preventDefault();

				var form = $(this),
					data = form.serialize(),
					info = $('<div class="mailster-form-info"></div>'),
					c;

				if ('function' === typeof window.mailster_pre_submit) {
					c = window.mailster_pre_submit.call(this, data);
					if (c === false) return false;
					if (typeof c !== 'undefined') data = c;
				}

				form.addClass('loading')
					.find('.submit-button')
					.prop('disabled', true);

				$.post(form.attr('action'), data, handlerResponse, 'JSON').fail(
					function (jqXHR, textStatus, errorThrown) {
						var response;

						try {
							response = JSON.parse(jqXHR.responseText);
							if (!response.data.html) {
								response = {
									html:
										'There was an error with the response:<br><code>[' +
										response.data.code +
										'] ' +
										response.data.message +
										'</code>',
									success: false,
								};
							}
						} catch (err) {
							response = {
								html:
									'There was an error while parsing the response:<br><code>' +
									jqXHR.responseText +
									'</code>',
								success: false,
							};
						}
						handlerResponse(response);
						if (console) console.error(jqXHR.responseText);
					}
				);

				function handlerResponse(response) {
					form.removeClass('loading has-errors')
						.find('div.mailster-wrapper')
						.removeClass('error');

					form.find('.mailster-form-info').remove();

					if ('function' === typeof window.mailster_post_submit) {
						c = window.mailster_post_submit.call(form[0], response);
						if (c === false) return false;
						if (typeof c !== 'undefined') response = c;
					}

					form.find('.submit-button').prop('disabled', false);

					if (response.data.html) info.html(response.data.html);
					if ($(document).scrollTop() < form.offset().top) {
						info.prependTo(form);
					} else {
						info.appendTo(form);
					}

					if (response.success) {
						if (!form.is('.is-profile'))
							form.find('.mailster-form-fields')
								.slideUp(100)
								.find('.mailster-wrapper')
								.find(':input')
								.prop('disabled', true)
								.filter('.input')
								.val('');

						response.data.redirect
							? (location.href = response.data.redirect)
							: info.show().addClass('success');
					} else {
						if (response.data.fields)
							$.each(response.data.fields, function (field) {
								form.addClass('has-errors')
									.find('.mailster-' + field + '-wrapper')
									.addClass('error');
							});
						info.show().addClass('error');
					}
				}
			}
		);
	});
