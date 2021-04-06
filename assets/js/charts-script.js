mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.chart = mailster.chart || {};

	var options = {
		series: [67],
		chart: {
			_height: 350,
			width: 40,
			type: 'radialBar',
			offsetY: -10
		},
		plotOptions: {
			radialBar: {
				startAngle: -135,
				endAngle: 135,
				dataLabels: {
					name: {
						fontSize: '16px',
						color: undefined,
						offsetY: 120
					},
					value: {
						offsetY: 76,
						fontSize: '22px',
						color: undefined,
						formatter: function (val) {
							return val + "%";
						}
					}
				}
			}
		},
		fill: {
			type: 'gradient',
			gradient: {
				shade: 'dark',
				shadeIntensity: 0.15,
				inverseColors: false,
				opacityFrom: 1,
				opacityTo: 1,
				stops: [0, 50, 65, 91]
			},
		},
		stroke: {
			dashArray: 4
		},
		labels: ['Median Ratio'],
	};

	var global_options = {
		radialBar: {
			series: [45],
			colors: ['#2BB3E7'],
			chart: {
				offsetX: -30,
				type: 'radialBar',
				width: 120,
				_height: 100
			},
			dataLabels: {
				_enabled: false
			},
			plotOptions: {
				radialBar: {
					hollow: {
						margin: 0,
						size: '66%'
					},
					track: {
						margin: 0,
						strokeWidth: '33%',
					},
					dataLabels: {
						name: {
							show: false
						},
						value: {
							fontSize: '14px',
							offsetY: 6,
						}
					}
				}
			}
		}
	}



	mailster.events.push('documentReady', function () {});

	mailster.charts = [];


	mailster.chart.create = function (selector, type, options) {

		var containers;
		if ('string' === typeof selector) {
			containers = document.querySelectorAll(selector);
		} else {
			containers = [selector];
		}

		for (var i = containers.length - 1; i >= 0; i--) {

			options = $.extend(true, global_options[type], options || {});
			console.log(options);


			var chart = new ApexCharts(containers[i], options);
			chart.render();
			mailster.charts.push(chart);

		}

	}

	return mailster;

}(mailster || {}, jQuery, window, document));