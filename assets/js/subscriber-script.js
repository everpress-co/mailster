mailster = (function (mailster, $, window, document) {
	'use strict';

	var timeout,
		email = $('#email'),
		id = $('#ID').val(),
		userimage = $('.avatar'),
		form = $('form#subscriber_form');

	$.easyPieChart &&
		$('.piechart').easyPieChart({
			animate: 1000,
			rotate: 180,
			barColor: mailster.colors.main,
			trackColor: mailster.colors.track_light,
			lineWidth: 9,
			size: 75,
			lineCap: 'butt',
			onStep: function (value) {
				this.$el.find('span').text(Math.round(value));
			},
			onStop: function (value) {
				this.$el.find('span').text(Math.round(value));
			},
		});

	$('#subscriber_form').on('submit', function () {
		clearTimeout(timeout);
		email.off('blur').off('keyup');
		$(this).submit(false);
	});

	$('.detail').on('click', function () {
		var _this = $(this).addClass('active'),
			_ul = _this.find('.click-to-edit'),
			_first = _ul.find('> li').first(),
			_last = _ul.find('> li').last();

		if (!_first.is(':hidden')) {
			_first.hide();
			_last.show().find('input').first().focus().select();
			_last.show().find('textarea').first().focus().select();
		}
	});
	$('#mailster_status').on('change', function () {
		if ($(this).val() <= 0) {
			$('.pending-info').show();
		} else {
			$('.pending-info').hide();
		}
	});
	$('.show-more-info').on('click', function () {
		$('.more-info').slideToggle(100);
	});

	$('.map.zoomable').on('click', function () {
		var _this = $(this),
			_img = _this.find('img'),
			_src = _img.attr('src'),
			_zsrc = _img.data('zoom');

		if (!_img.hasClass('zoomed')) {
			_img.attr('src', _zsrc).data('zoom', _src).addClass('zoomed');
		} else {
			_img.attr('src', _zsrc).data('zoom', _src).removeClass('zoomed');
		}
	});

	$.fn.select2 &&
		$('.tags-input').select2({
			placeholder: mailster.l10n.subscribers.choose_tags,
			tags: true,
			theme: 'mailster',
		});

	email
		.on('blur', function () {
			var _this = $(this),
				email = mailster.util.trim(_this.val());

			$(this).val(email);

			if (userimage.data('email') != email) {
				userimage.addClass('avatar-loading');
				getGravatar(email, function (response) {
					if (response.success)
						userimage
							.data('email', email)
							.removeClass('avatar-loading')
							.css({
								'background-image':
									'url(' + response.data.url.replace(/&amp;/, '&') + ')',
							});
				});
			}

			if (!email) form.prop('disabled', true);
			_this.trigger('keyup');
		})
		.on('keyup', function () {
			var _this = $(this);
			clearTimeout(timeout);
			timeout = setTimeout(function () {
				var email = mailster.util.trim(_this.val());

				mailster.util.ajax(
					'check_email',
					{
						email: email,
						id: id,
					},
					function (data) {
						form.prop('disabled', data.exists);
						$('.email-error').slideUp(100, function () {
							$(this).remove();
						});
						if (data.exists) {
							$(
								'<p class="email-error">&#9650; ' +
									mailster.l10n.subscribers.email_exists +
									'</p>'
							)
								.hide()
								.insertAfter(_this)
								.slideDown(100);
							setTimeout(function () {
								_this.focus(), 1;
							});
						}
					}
				);
			}, 400);

			form.prop('disabled', true);
		});

	function getGravatar(email, callback) {
		mailster.util.ajax(
			'get_gravatar',
			{
				email: email,
			},
			callback
		);
	}

	return mailster;
})(mailster || {}, jQuery, window, document);
