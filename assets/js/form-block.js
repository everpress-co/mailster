(function () {
	'use strict';

	var forms = document.querySelectorAll('.mailster-block-form');
	var cookieTime = 10;
	var timeouts = [];

	var triggerMethods = {
		delay: function (form, placement) {
			timeouts[placement.identifier].push(
				setTimeout(function () {
					openForm(form, placement);
				}, placement.trigger_delay * 1000)
			);
		},
		inactive: function (form, placement) {
			var timeoutIndex = timeouts.length;
			function resetTimer() {
				clearTimeout(timeouts[timeoutIndex]);
				timeouts[timeoutIndex] = setTimeout(function () {
					openForm(form, placement);
				}, placement.trigger_inactive * 1000 - 500);
			}

			[
				'mousedown',
				'mousemove',
				'keypress',
				'scroll',
				'touchstart',
			].forEach(function (name) {
				window.addEventListener(name, debounce(resetTimer, 500), true);
			});
			resetTimer();
		},
		scroll: function (form, placement) {
			var el = document.documentElement,
				body = document.body,
				st = 'scrollTop',
				sh = 'scrollHeight',
				t = placement.trigger_scroll / 100,
				triggered = false;

			function getScrollPercent() {
				return (
					(el[st] || body[st]) /
					((el[sh] || body[sh]) - el.clientHeight)
				);
			}

			function check() {
				if (!triggered && getScrollPercent() >= t) {
					openForm(form, placement);
					triggered = true;
				}
			}

			['scroll', 'touchstart'].forEach(function (name) {
				window.addEventListener(name, debounce(check, 50), true);
			});
		},
		click: function (form, placement) {
			var elements = document.querySelectorAll(placement.trigger_click);
			Array.prototype.forEach.call(elements, function (element, i) {
				element.addEventListener('click', function (event) {
					openForm(form, placement, true);
				});
			});
		},
		exit: function (form, placement) {
			timeouts[placement.identifier].push(
				setTimeout(
					function () {
						document.addEventListener('mouseout', function (event) {
							if (!event.toElement && !event.relatedTarget) {
								openForm(form, placement);
							}
						});
					},
					placement.isPreview ? 0 : 3000
				)
			);
		},
	};

	Array.prototype.forEach.call(forms, function (form, i) {
		var placement = form.querySelector('.mailster-block-form-data');

		if (placement) {
			placement = JSON.parse(placement.textContent);
			if (placement.triggers) {
				timeouts[placement.identifier] = [];
				placement.triggers.forEach(function (trigger) {
					triggerMethods[trigger] &&
						triggerMethods[trigger].call(this, form, placement);
				});
			} else {
				console.warn('CONTENT');
			}
		}

		form.addEventListener('submit', function (event) {
			event.preventDefault();

			var data = new FormData(form),
				info = form.querySelector('.mailster-block-form-info'),
				submit = form.querySelector('.submit-button'),
				infoSuccess = info.querySelector(
					'.mailster-block-form-info-success .mailster-block-form-info-extra'
				),
				infoError = info.querySelector(
					'.mailster-block-form-info-error .mailster-block-form-info-extra'
				);

			form.classList.remove('has-errors');
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
					// 'Content-Type': 'application/json',
					// 'X-WP-Nonce': data.get('_nonce'), // <- here, send the nonce via the header
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
				let message = [];
				const scrollPosition =
					window.pageYOffset || document.documentElement.scrollTop;

				if (200 !== response.data.status) {
					if (message && console) {
						console.error(message);
					}
					if (response.data.fields) {
						form.classList.add('has-errors');
						Object.keys(response.data.fields).map(function (
							fieldid
						) {
							message.push(response.data.fields[fieldid]);
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
					infoError.innerHTML = message.join('<br>');
					info.classList.remove('success');
					info.classList.add('error');
				} else {
					infoError.innerHTML = message.join('<br>');
					info.classList.remove('error');
					info.classList.add('success');

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
					form.insertBefore(
						info,
						form.querySelector('.mailster-wrapper')
					);
				} else {
					form.insertBefore(info, form.lastChild);
				}
			}
		});
	});

	function hasBeendShown(placement, delay) {
		if (placement.isPreview) {
			return false;
		}
		return !(
			get(placement.identifier, 0) <
			+new Date() - (delay ? delay : cookieTime) * 1000
		);
	}

	function openForm(form, placement, explicit) {
		if (explicit || !hasBeendShown(placement)) {
			var wrap = form.closest('.wp-block-mailster-form-outside-wrapper'),
				closeButton = wrap.querySelector('.mailster-block-form-close');

			if (wrap && !wrap.classList.contains('active')) {
				setTimeout(function () {
					wrap.addEventListener(
						'click',
						function (event) {
							event.preventDefault();
							closeForm(form, placement, explicit);
						},
						{
							once: true,
						}
					);
				}, 2000);
				closeButton.addEventListener(
					'click',
					function (event) {
						event.preventDefault();
						closeForm(form, placement, explicit);
					},
					{
						once: true,
					}
				);
				document.addEventListener('keyup', function (event) {
					if (event.keyCode == 27) {
						closeForm(form, placement, explicit);
					}
				});
				form.addEventListener('click', function (event) {
					event.stopPropagation();
				});
				wrap.classList.add('active');
				document
					.querySelector('body')
					.classList.add('mailster-form-active');
				// form.querySelector('input.input').focus();
			}
		}
	}

	function closeForm(form, placement, explicit) {
		var wrap = form.closest('.wp-block-mailster-form-outside-wrapper');
		wrap.classList.remove('active');
		if (!explicit) {
			set(placement.identifier, +new Date());
		}
		timeouts[placement.identifier].forEach(function (timeout) {
			clearTimeout(timeout);
		});
		document.querySelector('body').classList.remove('mailster-form-active');
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
		if (store[key]) {
			return store[key];
		}
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

	function debounce(func, wait, immediate) {
		var timeout;

		return function executedFunction() {
			var context = this;
			var args = arguments;

			var later = function () {
				timeout = null;
				if (!immediate) {
					func.apply(context, args);
				}
			};

			var callNow = immediate && !timeout;

			clearTimeout(timeout);

			timeout = setTimeout(later, wait);

			if (callNow) {
				func.apply(context, args);
			}
		};
	}
})();
