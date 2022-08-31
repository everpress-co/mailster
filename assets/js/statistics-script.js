mailster = (function (mailster, $, window, document) {
	'use strict';

	var apiFetch = wp.apiFetch,
		chartDefaults = {
			chart: {
				type: 'area',
				toolbar: {
					show: false,
				},
				sparkline: {
					_enabled: true,
				},
				animations: {
					initialAnimation: {
						_enabled: true,
					},
				},
			},
			stroke: {
				_curve: 'straight',
			},
			fill: {
				opacity: 1,
			},
			grid: {
				show: false,
				padding: {
					top: 20,
					left: 0,
					bottom: 0,
					right: 0,
				},
			},
			dataLabels: {
				enabled: false,
			},
			series: [
				{
					name: '',
					data: [0, 0],
				},
			],
			colors: ['#2BB3E7'],
			xaxis: {
				type: 'datetime',
				labels: {
					show: true,
				},
			},
			yaxis: {
				show: false,
			},
		},
		reloadBtn =
			'<button type="button" class="handle-order-higher reload" aria-disabled="false"><span class="order-higher-indicator" aria-hidden="true"></span></button>';

	window.postboxes.add_postbox_toggles('newsletter_page_mailster_statistics');

	$(document).on('change', '.date-range-select', function () {
		var dates = mailster.l10n.statistics[$(this).val()];

		$('.date-range-from')[0].valueAsDate = new Date(dates[0]);
		$('.date-range-to')[0].valueAsDate = new Date(dates[1]);

		reload();
	});
	$(document).on('click', '.reload', function () {
		loadMetaBox($(this).closest('.postbox'));
	});

	init();

	function init() {
		var boxes = $('.postbox');

		$.each(boxes, function (i) {
			var self = $(this);
			self.addClass('loading');
			self.find('.handle-actions').prepend(reloadBtn);
			setTimeout(() => {
				loadMetaBox(self);
			}, i * 300);
		});
	}

	function getFrom() {
		return $('.date-range-from').val();
	}

	function getTo() {
		return $('.date-range-to').val();
	}

	function loadMetaBox(id) {
		var el = $(id);

		el.find('.metabox-chart').each(function (j) {
			var self = $(this),
				metric = self.data('metric'),
				settings =
					self.data('settings') ||
					JSON.parse(self.find('script')[0].textContent),
				options = mergeDeep(chartDefaults, settings),
				chart = self.data('chart');

			var args = new URLSearchParams({ from: getFrom(), to: getTo() });

			var path =
				'mailster/v1/statistics/' + metric + '?' + args.toString();

			if (!chart && self.data('apex')) {
				chart = new ApexCharts(this, options);
				chart.render();
				self.data('chart', chart);
			}
			self.data('settings', settings);

			setTimeout(() => {
				metric &&
					apiFetch({
						path: path,
						method: 'GET',
					})
						.then((r) => {
							r.total && el.find('.total').html(r.total);
							r.metric && el.find('.metric').html(r.metric);
							r.gain && el.find('.gain').html(r.gain);
							r.html && self.html(r.html);
							//r.options && chart.updateOptions(d(r.options));
							r.series && chart.updateSeries(r.series);
						})
						.catch((error) => {
							console.warn(error);
						})
						.finally(() => {
							el.removeClass('loading');
						});
			}, j * 500);
		});
	}
	function isObject(item) {
		return item && typeof item === 'object' && !Array.isArray(item);
	}

	function mergeDeep(target, source) {
		let output = Object.assign({}, target);
		if (isObject(target) && isObject(source)) {
			Object.keys(source).forEach((key) => {
				if (isObject(source[key])) {
					if (!(key in target))
						Object.assign(output, { [key]: source[key] });
					else output[key] = mergeDeep(target[key], source[key]);
				} else {
					Object.assign(output, { [key]: source[key] });
				}
			});
		}
		return output;
	}
	return mailster;
})(mailster || {}, jQuery, window, document);
