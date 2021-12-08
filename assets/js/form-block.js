(function () {
	'use strict';

	var forms = document.querySelectorAll('.mailster-block-form');

	function hasBeendShown(identifier, delay) {
		return !(get(identifier, 0) < +new Date() - delay * 1000);
	}

	Array.prototype.forEach.call(forms, function (form, i) {
		var placement = form.querySelector('.mailster-block-form-data');
		if (placement) {
			placement = JSON.parse(placement.textContent);

			if (placement.triggers) {
				var timeout;

				// trigger only if never displayed or already 60 seconds ago
				if (!hasBeendShown(placement.identifier, 10)) {
					if (-1 !== placement.triggers.indexOf('delay')) {
						initDelay(
							form,
							placement.identifier,
							placement.trigger_delay,
							timeout
						);
					}
					if (-1 !== placement.triggers.indexOf('inactive')) {
						initInactive(
							form,
							placement.identifier,
							placement.trigger_delay,
							timeout
						);
					}
					if (-1 !== placement.triggers.indexOf('scroll')) {
						initScroll(form, placement.identifier, 100);
					}
					if (-1 !== placement.triggers.indexOf('click')) {
						initClick(form, placement.identifier, 'p');
					}
				}
			}
		}

		form.addEventListener('submit', function (event) {
			event.preventDefault();

			var data = new FormData(form),
				info = form.querySelector('.mailster-block-form-info'),
				submit = form.querySelector('.submit-button'),
				infoSuccess = info.querySelector(
					'.mailster-block-form-info-success'
				),
				infoError = info.querySelector(
					'.mailster-block-form-info-error'
				);

			form.classList.remove('has-errors');
			info.classList.remove('error');
			info.classList.remove('success');
			[].forEach.call(
				document.querySelectorAll('div.mailster-wrapper.error'),
				function (wrapper) {
					wrapper.classList.remove('error');
				}
			);

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
					console.warn(response);
					return response.json();
				})
				.then(handlerResponse)
				.catch(function (error) {
					console.error(error);
				})
				.finally(function () {
					form.classList.remove('loading');
					form.removeAttribute('disabled');
					submit.removeAttribute('disabled');
				});

			function handlerResponse(response) {
				let message = response.message ? response.message : '';
				const scrollPosition =
					window.pageYOffset || document.documentElement.scrollTop;

				if (200 !== response.data.status) {
					if (message && console) console.error(message);
					if (response.data.fields) {
						form.classList.add('has-errors');
						Object.keys(response.data.fields).map(function (
							fieldid
						) {
							message += '<br>' + response.data.fields[fieldid];
							console.error(
								'[' + fieldid + ']',
								response.data.fields[fieldid]
							);
							var field = form.querySelector(
								'.wp-block-mailster-' +
									fieldid +
									', .wp-block-mailster-field-' +
									fieldid
							);
							field && field.classList.add('error');
						});
					}
					infoError.innerHTML = message;
					// for css transition use timeout
					setTimeout(function () {
						info.classList.add('error');
					}, 10);
				} else {
					console.warn('handlerResponse', response, message);
					// for css transition use timeout
					infoSuccess.innerHTML = message;
					setTimeout(function () {
						info.classList.add('success');
					}, 10);

					if (response.data.redirect) {
						window.location.href = response.data.redirect;
						return;
					}

					if (!form.classList.contains('is-profile')) {
						form.classList.add('completed');
						form.reset();
					}
				}

				if (true || scrollPosition < form.getBoundingClientRect().top) {
					form.insertBefore(info, form.firstChild);
				} else {
					form.insertBefore(info, form.lastChild);
				}
			}
		});
	});

	function openForm(form, identifier) {
		if (hasBeendShown(identifier, 10)) {
			return;
		}
		var wrap = form.closest('.wp-block-mailster-form-outer-wrapper');

		wrap.addEventListener(
			'click',
			function (event) {
				closeForm(form, identifier);
			},
			{
				once: true,
			}
		);
		form.addEventListener('click', function (event) {
			event.stopPropagation();
		});
		wrap.classList.add('active');
		form.querySelector('input.input').focus();
	}

	function closeForm(form, identifier) {
		var wrap = form.closest('.wp-block-mailster-form-outer-wrapper');
		wrap.classList.remove('active');
		set(identifier, +new Date());
	}

	function set(key, value) {
		var data = get();
		data[key] = value;
		localStorage.setItem('mailster-block-forms', JSON.stringify(data));
		return true;
	}

	function get(key, fallback = null) {
		var store = localStorage.getItem('mailster-block-forms');
		store = store ? JSON.parse(store) : {};
		if (!key) {
			return store;
		}
		if (store[key]) return store[key];
		return fallback;
	}

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

	function initDelay(form, identifier, delay, timeout) {
		timeout = setTimeout(function () {
			openForm(form, identifier);
		}, delay * 1000);
	}

	function initInactive(form, identifier, delay, timeout) {
		function resetTimer() {
			clearTimeout(timeout);
			timeout = setTimeout(function () {
				openForm(form, identifier);
			}, delay * 1000);
		}

		['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(
			function (name) {
				window.addEventListener(name, debounce(resetTimer, 500), true);
			}
		);
		resetTimer();

		return timeout;
	}

	function initScroll(form, identifier, threshold, timeout) {
		var el = document.documentElement,
			body = document.body,
			st = 'scrollTop',
			sh = 'scrollHeight',
			t = threshold / 100,
			triggered = false;

		function getScrollPercent() {
			return (
				(el[st] || body[st]) / ((el[sh] || body[sh]) - el.clientHeight)
			);
		}

		function check() {
			if (!triggered && getScrollPercent() >= t) {
				openForm(form, identifier);
				triggered = true;
			}
		}

		['scroll', 'touchstart'].forEach(function (name) {
			window.addEventListener(name, debounce(check, 100), true);
		});

		return timeout;
	}

	function initClick(form, identifier, selector) {
		var elements = document.querySelectorAll(selector);
		Array.prototype.forEach.call(elements, function (element, i) {
			element.addEventListener('click', function (event) {
				openForm(form, identifier);
			});
		});
	}

	function initExit(form, identifier) {
		setTimeout(function () {
			document.addEventListener('mouseout', function (event) {
				if (!event.toElement && !event.relatedTarget) {
					openForm(form, identifier);
				}
			});
		}, 5000);
	}

	function debounce(func, wait, immediate) {
		var timeout;

		return function executedFunction() {
			var context = this;
			var args = arguments;

			var later = function () {
				timeout = null;
				if (!immediate) func.apply(context, args);
			};

			var callNow = immediate && !timeout;

			clearTimeout(timeout);

			timeout = setTimeout(later, wait);

			if (callNow) func.apply(context, args);
		};
	}
})();
