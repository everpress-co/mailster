mailster = (function (mailster, $, window, document) {

	"use strict";

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
			}
		});
	}

	window.postboxes.add_postbox_toggles('newsletter_page_mailster_statistics');

	$(document)
		.on('change', '.date-range-select', function () {

			var dates = mailster.l10n.statistics[$(this).val()];

			$('.date-range-from').datepicker("setDate", dates[0]);
			$('.date-range-to').datepicker("setDate", dates[1]);

		})

	init();

	function init() {

		var boxes = $('.postbox');

		$.each(boxes, function () {
			var id = this.id;

			loadMetaBox(this);
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

			setTimeout(function () {

				chart.options.defaultColor = 'rgba(255, 99, 132, 1)';
				chart.type = 'line';
				el.removeClass('loading');
				chart.data.datasets[0].data[0] = 17;

				chart.update();

			}, 2000)

		});
		el.addClass('loading');
	}

	return mailster;

}(mailster || {}, jQuery, window, document));