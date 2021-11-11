(function () {
	'use strict';

	console.warn('FORM BLOCK INIT');
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
