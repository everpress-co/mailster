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
					enabled: false,
					initialAnimation: {
						enabled: false,
					},
				},
			},

			fill: {
				opacity: 1,
			},
			stroke: {
				curve: 'smooth',
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
			tooltip: { shared: true, x: { show: false } },
			series: [
				{
					name: '',
					data: [0, 0],
				},
			],
			legend: {
				show: false,
			},
			colors: ['rgba(43, 179, 231, 0.9)', 'rgba(58, 182, 27, .2)'],
			xaxis: {
				type: 'datetime',
				labels: {
					show: true,
				},
			},
			yaxis: {
				show: false,
				labels: {
					formatter: function (value) {
						if (!value || isNaN(value)) {
							return value;
						}
						return value.toFixed(2).toLocaleString() + ' %';
					},
				},
			},
		},
		reloadBtn =
			'<button type="button" class="handle-order-higher reload" aria-disabled="false"><span class="order-higher-indicator" aria-hidden="true"></span></button>';

	window.postboxes.add_postbox_toggles('newsletter_page_mailster_statistics');

	$(window).on('popstate', function (event) {
		var args = new URLSearchParams(window.location.search);
		changeDates(args.get('from'), args.get('to'));
	});

	$(document)
		.on('change', '.date-range-select', function () {
			var val = $(this).val();

			if (val != 'custom') {
				var dates = $(this).val().split('_');
				changeDatesDebounced(dates[0], dates[1]);
			}
		})
		.on('click', '.date-range', function () {
			$(this).addClass('is-open');
		})
		.on('click', '.reload', function () {
			changeDatesDebounced(null, null);
		})
		.on('change', '.date-range-from', function () {
			changeDatesDebounced(this.value, null);
		})
		.on('change', '.date-range-to', function () {
			changeDatesDebounced(null, this.value);
		});

	init();

	function changeDates(from, to) {
		from = from || $('.date-range-from').val();
		to = to || $('.date-range-to').val();
		var args = new URLSearchParams(window.location.search);
		args.set('from', from);
		args.set('to', to);
		window.history.pushState(
			'',
			'',
			window.location.pathname + '?' + args.toString()
		);

		$('.date-range-from')[0].valueAsDate = new Date(from);
		$('.date-range-to')[0].valueAsDate = new Date(to);

		if (
			$('.date-range-select option[value="' + from + '_' + to + '"]')
				.length
		) {
			$('.date-range-select').val(from + '_' + to);
		} else {
			$('.date-range-select').val('custom');
		}

		$('.date-range-wording').html('from ' + from + ' to ' + to);

		reload();
	}

	var changeDatesDebounced = mailster.util.debounce(changeDates, 1000);

	function init() {
		var boxes = $('.postbox');
		boxes.find('.handle-actions').prepend(reloadBtn);
		var args = new URLSearchParams(window.location.search);
		changeDates(args.get('from'), args.get('to'));
	}
	function reload() {
		var boxes = $('.postbox');

		$.each(boxes, function (i) {
			var self = $(this);
			self.addClass('loading');

			setTimeout(() => {
				loadMetaBox(self, this.id);
			}, i * 300);
		});
	}

	function getFrom() {
		return $('.date-range-from').val();
	}

	function getTo() {
		return $('.date-range-to').val();
	}

	function loadMetaBox(el, id) {
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
				options.chart.id = 'chart-' + id + '-' + j;
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
							r.value && el.find('.value').html(r.value);
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
