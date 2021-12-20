jQuery(document).ready(function ($) {
	'use strict';

	var oldUrl;
	var lastanimation = '';

	var infoScreen = $('<div />')
		.css({
			position: 'fixed',
			top: 0,
			right: 0,
		})
		.html('0')
		.appendTo('body');

	var el = document.documentElement,
		body = document.body,
		st = 'scrollTop',
		sh = 'scrollHeight';

	function getScrollPercent() {
		var x = (el[st] || body[st]) / ((el[sh] || body[sh]) - el.clientHeight);
		return (x * 100).toFixed();
	}

	var form = $('.wp-block-mailster-form-outside-wrapper-placeholder');

	var siblings = form.siblings('h2, p').css({ outline: '1px dotted red' });

	console.warn(siblings);

	['scroll', 'touchstart'].forEach(function (name) {
		window.addEventListener(
			name,
			debounce(function () {
				infoScreen.html(getScrollPercent());
			}, 5),
			true
		);
	});

	window.addEventListener('message', function (event) {
		var data = JSON.parse(event.data),
			source = event;

		console.warn(data);

		var params = new URLSearchParams();

		params.set('context', 'edit');
		params.set('_locale', 'user');
		params.set('attributes[id]', data.form_id);
		data.options.align &&
			params.set('attributes[align]', data.options.align);

		var args = {
			width: data.options.width,
			padding: data.options.padding,
			triggers: data.options.triggers,
			trigger_delay: 2,
			trigger_inactive: 4,
			trigger_scroll: data.options.trigger_scroll,
			classes: ['mailster-block-form-type-' + data.type],
			isPreview: true,
		};

		var url = 'wp/v2/block-renderer/mailster/form?' + params.toString();

		if (url != oldUrl) {
			wp.apiFetch({
				method: 'POST',
				path: url,
				data: { post_content: data.post_content, args: args },
			})
				.then(function (post) {
					$(
						'.wp-block-mailster-form-outside-wrapper-' +
							data.form_id
					).replaceWith(post.rendered);

					updateForm();

					return post;
				})
				.catch(function (err) {
					$('.wp-block-mailster-form-outside-wrapper-' + data.form_id)
						.addClass('has-error')
						.html(err.message);
					event.source.postMessage(
						JSON.stringify({ success: false, error: err }),
						event.origin
					);
				})
				.finally(function () {
					console.warn('FINALLY');
				});
		} else {
			updateForm();
		}

		function getCSS() {
			var css = {};

			css['flex-basis'] = data.options.width
				? data.options.width + '%'
				: '100%';
			if (data.options.padding) {
				css['paddingTop'] = data.options.padding.top;
				css['paddingRight'] = data.options.padding.right;
				css['paddingBottom'] = data.options.padding.bottom;
				css['paddingLeft'] = data.options.padding.left;
			}

			return css;
		}

		function reloadFormScript() {
			var script = $('#mailster-form-block-js');
			script.remove();
			script.appendTo('head');
		}

		function updateForm() {
			var form = $(
				'.wp-block-mailster-form-outside-wrapper-' + data.form_id
			);

			form.removeClass('has-animation animation-' + lastanimation);

			if (data.options.animation) {
				form.addClass(
					'has-animation animation-' + data.options.animation
				);
				lastanimation = data.options.animation;
			}
			form.find('.mailster-block-form').css(getCSS());

			oldUrl = url;
			event.source.postMessage(
				JSON.stringify({ success: true }),
				event.origin
			);

			reloadFormScript();
		}
	});

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
});
