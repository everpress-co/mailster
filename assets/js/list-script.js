mailster = (function (mailster, $, window, document) {

	"use strict";

	$('.piechart').each(function () {
		var el = $(this),
			p = el.data('percent');

		mailster.chart.create(this, 'radialBar', {
			series: [Math.round(p)]
		});

	})
	$('.detail').on('click', function () {

		var _this = $(this).addClass('active'),
			_ul = _this.find('.click-to-edit'),
			_first = _ul.find('> li').first(),
			_last = _ul.find('> li').last();

		if (!_first.is(':hidden')) {
			_first.hide();
			_last.show().find('input').first().focus().select();
		}

	});

	return mailster;

}(mailster || {}, jQuery, window, document));