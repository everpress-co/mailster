mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.chart = mailster.chart || {};


	var global_options = {
		radialBar: {
			series: [0],
			colors: [mailster.colors.main],
			chart: {
				offsetX: -10,
				type: 'radialBar',
				width: 140,
			},
			stroke: {
				_lineCap: 'round'
			},
			dataLabels: {
				_enabled: false
			},
			plotOptions: {
				radialBar: {
					hollow: {
						margin: 0,
						size: '60%'
					},
					track: {
						background: mailster.colors.track,
						margin: 0,
						strokeWidth: '33%',
					},
					dataLabels: {
						name: {
							show: false
						},
						value: {
							fontSize: '13px',
							offsetY: 6,
						}
					}
				}
			}
		}
	}

	mailster.charts = [];

	mailster.chart.create = function (element, type, options) {

		options = $.extend(true, global_options[type], options || {});

		var chart = new ApexCharts(element, options);
		chart.render();
		mailster.charts.push(chart);

		return chart;

	}

	mailster.chart.update = function (element, value) {
		for (var i = mailster.charts.length - 1; i >= 0; i--) {
			if (element === mailster.charts[i].el) {
				mailster.charts[i].updateSeries([value]);
				break;
			}
		}
	}

	return mailster;

}(mailster || {}, jQuery, window, document));