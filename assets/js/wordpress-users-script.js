mailster = (function (mailster, $, window, document) {

	"use strict";
	$('#sync_list_check').on('change', function () {
		$('#sync_list').slideToggle(200);
		$('.sync-button').prop('disabled', true);
	});

	$('#sync_list')
		.on('click', '#add_sync_item', function () {
			var items = $('.mailster_syncitem');

			items.eq(0).clone().insertAfter(items.last()).removeAttr('title').find('select').each(function () {
				$(this).attr('name', $(this).attr('name').replace('[synclist][0]', '[synclist][' + items.length + ']'));
			});

			$('.sync-button').prop('disabled', true);

		})
		.on('click', '.remove-sync-item', function () {
			$(this).parent().remove();
			$('.sync-button').prop('disabled', true);
		})
		.on('change', 'select', function () {
			$('.sync-button').prop('disabled', true);
		})
		.on('click', '#sync_subscribers_wp', function () {
			if (event.target == this && !confirm(mailster.l10n.settings.sync_subscriber)) return false;

			var _this = $(this),
				loader = $('.sync-ajax-loading').css({
					'visibility': 'visible'
				});

			$('.sync-button').prop('disabled', true);

			mailster.util.ajax('sync_all_subscriber', {
				offset: _this.data('offset')
			}, function (response) {

				$('.sync-button').prop('disabled', false);
				if (response.success && response.count) {
					_this.data('offset', response.offset).trigger('click');
				} else {
					loader.css({
						'visibility': 'hidden'
					});
					_this.data('offset', 0);
				}

			}, function (jqXHR, textStatus, errorThrown) {

				loader.css({
					'visibility': 'hidden'
				});
				$('.sync-button').prop('disabled', false);

			});
			return false;
		})
		.on('click', '#sync_wp_subscribers', function () {
			if (event.target == this && !confirm(mailster.l10n.settings.sync_wp_user)) return false;

			var _this = $(this),
				loader = $('.sync-ajax-loading').css({
					'visibility': 'visible'
				});

			$('.sync-button').prop('disabled', true);

			mailster.util.ajax('sync_all_wp_user', {
				offset: _this.data('offset')
			}, function (response) {

				$('.sync-button').prop('disabled', false);
				if (response.success && response.count) {
					_this.data('offset', response.offset).trigger('click');
				} else {
					loader.css({
						'visibility': 'hidden'
					});
					_this.data('offset', 0);
				}

			}, function (jqXHR, textStatus, errorThrown) {

				loader.css({
					'visibility': 'hidden'
				});
				$('.sync-button').prop('disabled', false);

			});
			return false;
		})


}(mailster || {}, jQuery, window, document));