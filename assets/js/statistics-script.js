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

	if (typeof jQuery.datepicker == 'object') {
		$('input.datepicker').datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: new Date(),
			firstDay: mailsterL10n.start_of_week,
			showWeek: true,
			dayNames: mailsterL10n.day_names,
			dayNamesMin: mailsterL10n.day_names_min,
			monthNames: mailsterL10n.month_names,
			prevText: mailsterL10n.prev,
			nextText: mailsterL10n.next,
			showAnim: 'fadeIn',
			onClose: function () {
				var date = $(this).datepicker('getDate');
				console.log($(this).val());
				// $('.deliverydate').html($(this).val());
			}
		});

		$()


	}

	$(document)
		.on('click', '.toggle-indicator', toggleMetaBoxes)
		.on('click', '.hide-postbox-tog', function () {
			$('#' + $(this).val())[$(this).is(':checked') ? 'show' : 'hide']().removeClass('closed');
			toggleMetaBoxes();

		})
		.on('change', '.date-range-select', function () {

			console.log(mailsterL10n[$(this).val()]);
			var dates = mailsterL10n[$(this).val()];

			$('.date-range-from').datepicker("setDate", dates[0]);
			$('.date-range-to').datepicker("setDate", dates[1]);
			console.log(dates);

		})


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

	initeMetaBoxes();

	function initeMetaBoxes() {

		var boxes = $('.postbox');

		$.each(boxes, function () {
			var id = this.id;

			loadMetaBox(this);

			console.log(id);
		});

	}

	function loadMetaBox(id) {
		var el = $(id),
			ctx;

		el.find('.metabox-chart').each(function () {
			var self = $(this),
				chart = self.data('chart') || new Chart(self, {
					type: self.data('type') || 'bar',
					options: {
						scales: {
							yAxes: [{
								ticks: {
									beginAtZero: true
								}
							}]
						}
					}
				});

			chart.options.defaultColor = 'rgba(255, 99, 132, 0.5)';
			chart.data.labels = ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"];
			chart.data.datasets = [];
			chart.data.datasets.push({
				label: '# of Votes',
				data: [12, 19, 3, 5, 2, 3],
				_backgroundColor: [
					'rgba(255, 99, 132, 0.2)',
				],
				borderWidth: 1
			});


			chart.update();


			console.log(chart);
			setTimeout(function () {

				chart.options.defaultColor = 'rgba(255, 99, 132, 1)';
				chart.type = 'line';
				el.removeClass('loading');
				console.log(chart.data.datasets[0]);
				chart.data.datasets[0].data[0] = 17;

				chart.update();

			}, 2000)

		});
		el.addClass('loading');
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