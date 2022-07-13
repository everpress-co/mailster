document.documentElement.setAttribute('hidden', true);
jQuery(document).ready(function ($) {
	'use strict';

	var oldUrl;
	var lastanimation = '';

	document.documentElement.removeAttribute('hidden');
	$('.wp-block-mailster-form-outside-wrapper-placeholder')[0].scrollIntoView({
		behavior: 'auto',
		block: 'center',
		inline: 'nearest',
	});

	$('a[href]')
		.css({ cursor: 'not-allowed' })
		.on('click', function (event) {
			event.preventDefault();
			return false;
		});

	window.addEventListener('message', function (event) {
		var data = JSON.parse(event.data),
			source = event;

		var params = new URLSearchParams();

		params.set('context', 'edit');
		params.set('_locale', 'user');
		params.set('attributes[id]', data.form_id);
		data.options.align &&
			params.set('attributes[align]', data.options.align);

		var args = {
			width: data.options.width,
			padding: data.options.padding,
			classes: ['mailster-block-form-type-' + data.type],
			isPreview: true,
		};

		if (data.type != 'content') {
			args['triggers'] = data.options.triggers;
			args['trigger_delay'] = 2;
			args['trigger_inactive'] = 4;
			args['trigger_scroll'] = data.options.trigger_scroll;
		}

		var url = 'wp/v2/block-renderer/mailster/form?' + params.toString();

		if (url != oldUrl) {
			wp.apiFetch({
				method: 'POST',
				path: url,
				data: { block_form_content: data.post_content, args: args },
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
						JSON.stringify({
							success: false,
							error: err,
							location: location.origin + location.pathname,
						}),
						event.origin
					);
				})
				.finally(function () {});
		} else {
			updateForm();
		}

		function getCSS() {
			var css = {};

			css['flex-basis'] = data.options.width
				? data.options.width + '%'
				: '100%';
			if (data.options.padding) {
				css['paddingTop'] = data.options.padding.top || 0;
				css['paddingRight'] = data.options.padding.right || 0;
				css['paddingBottom'] = data.options.padding.bottom || 0;
				css['paddingLeft'] = data.options.padding.left || 0;
			}

			return css;
		}

		function reloadFormScript() {
			var script = $('#mailster-form-view-script-js');
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
				JSON.stringify({
					success: true,
					location: location.origin + location.pathname,
				}),
				event.origin
			);

			reloadFormScript();
		}
	});

	function getScrollPercent() {
		var el = document.documentElement,
			body = document.body,
			st = 'scrollTop',
			sh = 'scrollHeight';
		var x = (el[st] || body[st]) / ((el[sh] || body[sh]) - el.clientHeight);
		return parseFloat((x * 100).toFixed());
	}

	function getScrollPosition() {
		return (
			document.documentElement['scrollTop'] || document.body['scrollTop']
		);
	}

	function info() {
		var infoScreen = $('<div />')
			.css({
				position: 'fixed',
				top: 0,
				right: 0,
			})
			.html('0')
			.appendTo('body');

		var form = $('.wp-block-mailster-form-outside-wrapper-placeholder');

		var siblings = form
			.siblings('h2, p')
			.css({ outline: '1px dotted red' });

		['scroll', 'touchstart'].forEach(function (name) {
			window.addEventListener(
				name,
				debounce(function () {
					infoScreen.html(getScrollPercent());
				}, 5),
				true
			);
		});
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
});
