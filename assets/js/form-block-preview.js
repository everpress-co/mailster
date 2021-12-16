jQuery(document).ready(function ($) {
	'use strict';

	var oldUrl;
	var lastanimation = '';

	console.warn('BLOCK PREVIEW!', wp.apiFetch);

	window.addEventListener('message', function (event) {
		var data = JSON.parse(event.data),
			source = event;
		//alert(JSON.parse(event.data));

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
			classes: ['mailster-block-form-type-' + data.type],
		};

		var url = 'wp/v2/block-renderer/mailster/form?' + params.toString();

		if (url != oldUrl) {
			wp.apiFetch({
				method: 'POST',
				path: url,
				data: { post_content: data.post_content, args: args },
			})
				.then((post) => {
					$(
						'.wp-block-mailster-form-outside-wrapper-' +
							data.form_id
					).replaceWith(post.rendered);

					updateForm();

					return post;
				})
				.catch((err) => {
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

		function updateForm() {
			var form = $(
				'.wp-block-mailster-form-outside-wrapper-' + data.form_id
			);

			form.removeClass('active has-animation animation-' + lastanimation);

			if (data.options.animation) {
				form.addClass(
					'has-animation animation-' + data.options.animation
				);
				lastanimation = data.options.animation;
			}
			form.addClass('active');
			form.find('.mailster-block-form').css({
				padding: data.options.padding
					? data.options.padding + 'em'
					: 'auto',
				width: data.options.width ? data.options.width + 'vw' : 'auto',
			});

			oldUrl = url;
			event.source.postMessage(
				JSON.stringify({ success: true }),
				event.origin
			);
		}
	});
});
