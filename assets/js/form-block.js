(function () {
	'use strict';

	var forms = document.querySelectorAll('.mailster-block-form');

	Array.prototype.forEach.call(forms, function (form, i) {
		form.addEventListener('submit', function (event) {
			event.preventDefault();

			var data = new FormData(form),
				info = form.querySelector('.mailster-block-form-info');

			if (!info) {
				info = document.createElement('div');
				info.classList.add('mailster-block-form-info');
			}

			form.classList.add('loading');
			form.setAttribute('disabled', true);

			fetch(form.getAttribute('action'), {
				method: 'POST',
				headers: {
					//'Content-Type': 'application/json',
					//'X-WP-Nonce': data.get('_nonce'), // <- here, send the nonce via the header
				},
				body: data,
			})
				.then(function (response) {
					return response.json();
				})
				.then(handlerResponse)
				.catch(function (error) {
					console.warn('xxx', error);
					var response;
					try {
						response = JSON.parse(error);
						if (!response.data.html) {
							response = {
								data: {
									html:
										'There was an error with the response:<br><code>[' +
										response.data.code +
										'] ' +
										response.data.message +
										'</code>',
								},
								success: false,
							};
						}
					} catch (err) {
						response = {
							data: {
								html:
									'There was an error while parsing the response:<br><code>' +
									err +
									'</code>',
							},
							success: false,
						};
					}
					handlerResponse(response);
				})
				.finally(function () {
					console.warn('FIN');
					form.classList.remove('loading');
					form.classList.remove('has-errors');
					form.removeAttribute('disabled');
					form.querySelector('.submit-button').removeAttribute(
						'disabled'
					);

					[].forEach.call(
						document.querySelectorAll('div.mailster-wrapper'),
						function (wrapper) {
							wrapper.classList.remove('error');
						}
					);

					//info.remove();
					//info.classList.remove('error');
					//info.classList.remove('success');
				});

			function handlerResponse(response) {
				console.warn('HAN');
				if (200 !== response.data.status) {
				}
				console.warn(response);
				if (response.message) {
					info.innerHTML = response.message;
				}

				if (
					(window.pageYOffset || document.documentElement.scrollTop) <
					form.getBoundingClientRect().top
				) {
					form.insertBefore(info, form.firstChild);
				} else {
					form.insertBefore(info, form.lastChild);
				}

				if (response.success) {
					// for css transition use timeout
					setTimeout(function () {
						info.classList.add('success');
					}, 0);

					if (response.data.redirect) {
						window.location.href = response.data.redirect;
						return;
					}

					if (!form.classList.contains('is-profile')) {
						form.classList.add('completed');
						form.reset();
					}
				} else {
					if (response.data.fields) {
						form.classList.add('has-errors');
						Object.keys(response.data.fields).forEach(function (
							fieldid
						) {
							var field = form.querySelector(
								'.mailster-' + fieldid + '-wrapper'
							);
							field && field.classList.add('error');
						});
					}
					// for css transition use timeout
					setTimeout(function () {
						info.classList.add('error');
					}, 0);
				}
			}
		});
	});

	function serializeForm(form) {
		var obj = {};
		var formData = new FormData(form);
		for (var key of formData.keys()) {
			obj[key] = formData.getAll(key).slice(-1)[0]; // get the latest element from that array
		}

		return Object.keys(obj)
			.map(function (k) {
				return encodeURIComponent(k) + '=' + encodeURIComponent(obj[k]);
			})
			.join('&');
	}
})();
