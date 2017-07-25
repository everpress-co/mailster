jQuery(document).ready(function ($) {

	"use strict"

	var wpnonce = $('#mailster_nonce').val(),
		isMobile = $(document.body).hasClass('mobile'),
		$handleButtons = $('.postbox .handlediv');

	$('a.external').on('click', function () {
		if (this.href) window.open(this.href);
		return false;
	});

	$('.meta-box-sortables').sortable({
		placeholder: 'sortable-placeholder',
		connectWith: '.meta-box-sortables',
		items: '.postbox',
		handle: '.hndle',
		cursor: 'move',
		delay: (isMobile ? 200 : 0),
		distance: 2,
		tolerance: 'pointer',
		forcePlaceholderSize: true,
		helper: function (event, element) {
			return element.clone()
				.find(':input')
				.attr('name', function (i, currentName) {
					return 'sort_' + parseInt(Math.random() * 100000, 10).toString() + '_' + currentName;
				})
				.end();
		},
		opacity: 0.65,
		update: function (e, ui) {
			orderMetaBoxes();
		}
	});

	$('.postbox .handlediv')
		.each(function () {
			var $el = $(this);
			$el.attr('aria-expanded', !$el.parent('.postbox').hasClass('closed'));
		})
		.on('click', function () {
			var $el = $(this);
			$el.parent('.postbox').toggleClass('closed');
			$el.attr('aria-expanded', !$el.parent('.postbox').hasClass('closed'));
		});


	$(document)
		.on('click', '.toggle-indicator', toggleMetaBoxes)
		.on('click', '.hide-postbox-tog', function () {

			$('#' + $(this).val())[$(this).is(':checked') ? 'show' : 'hide']().removeClass('closed');
			toggleMetaBoxes();

		});

	function updateMetaBoxes() {
		orderMetaBoxes();
		toggleMetaBoxes();
	};

	function orderMetaBoxes() {

		var order = {};

		$.each($('.postbox-container'), function () {
			var col = $(this).data('id');

			$.each($(this).find('.postbox'), function () {
				if (!order[col]) {
					order[col] = [];
				}
				order[col].push(this.id);
			});

			if (order[col]) {
				order[col] = order[col].join(',');
			}

		});

		var data = {
			action: 'meta-box-order',
			_ajax_nonce: $('#meta-box-order-nonce').val(),
			page: 'newsletter_page_mailster_statistics',
			order: order
		};

		$.post(ajaxurl, data);

	}

	function toggleMetaBoxes() {

		var hidden = $('.postbox:hidden').map(function () {
				return this.id;
			}).toArray(),
			closed = $('.postbox.closed').map(function () {
				return this.id;
			}).toArray();

		var data = {
			action: 'closed-postboxes',
			closedpostboxesnonce: $('#closedpostboxesnonce').val(),
			closed: closed.length ? closed.join(',') : '',
			hidden: hidden.length ? hidden.join(',') : '',
			page: 'newsletter_page_mailster_statistics'
		};

		$.post(ajaxurl, data);

	}

	function _ajax(action, data, callback, errorCallback, dataType) {

		if ($.isFunction(data)) {
			if ($.isFunction(callback)) {
				errorCallback = callback;
			}
			callback = data;
			data = {};
		}
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: $.extend({
				action: 'mailster_' + action,
				_wpnonce: wpnonce
			}, data),
			success: function (data, textStatus, jqXHR) {
				callback && callback.call(this, data, textStatus, jqXHR);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				if (textStatus == 'error' && !errorThrown) {
					return;
				}
				if (console) {
					console.error($.trim(jqXHR.responseText));
				}
				errorCallback && errorCallback.call(this, jqXHR, textStatus, errorThrown);

			},
			dataType: dataType ? dataType : "JSON"
		});
	}

});