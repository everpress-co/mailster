jQuery(document).ready(function ($) {

	"use strict"

	var conditions = $('.mailster-conditions'),
		cond = $('.mailster-condition'),
		condition,
		value_fields;

	cond.eq(0).appendTo($('.mailster-condition-container'));
	datepicker();

	$('.mailster-conditions-thickbox')
		.on('change', '.mailster-list-operator', function () {
			conditions.removeClass('mailster-condition-operator-is-and mailster-condition-operator-is-or').addClass('mailster-condition-operator-is-' + $(this).val().toLowerCase());
			window.mailster_updateReceiversCount();
		})
		.on('click', '.add-condition', function () {
			var id = cond.length,
				clone = cond.eq(0).clone();

			clone.removeAttr('id').appendTo(conditions);
			$.each(clone.find('input, select'), function () {
				var _this = $(this),
					name = _this.attr('name');
				_this.attr('name', name.replace(/\[\d+\]/, '[' + id + ']')).prop('disabled', false);
			});
			clone.find('.condition-field').val('').focus();
			//clone.find('select.select2').select2();
			datepicker();
			cond = $('.mailster-condition');
		})
		.on('click', '.close', function () {
			tb_remove();
		});


	conditions
		.on('click', '.remove-condition', function () {
			$(this).parent().slideUp(200, function () {
				$(this).remove();
				window.mailster_updateReceiversCount();
			});
		})
		.on('change', '.condition-field', function () {

			condition = $(this).closest('.mailster-condition');

			var value = $(this).val();
			condition.find('div.mailster-conditions-value-field').removeClass('active').find('.condition-value').prop('disabled', true);
			condition.find('div.mailster-conditions-operator-field').removeClass('active').find('.condition-operator').prop('disabled', true);

			if (condition.find('div.mailster-conditions-value-field[data-fields*="' + value + ',"]').addClass('active').find('.condition-value').prop('disabled', false).length) {
				//condition.find('div.mailster-conditions-value-field[data-fields*="' + value + '"]').show().find('.condition-value').prop('disabled', false);
			} else {
				condition.find('div.mailster-conditions-value-field-default').addClass('active').find('.condition-value').prop('disabled', false);
			}
			if (condition.find('div.mailster-conditions-operator-field[data-fields*="' + value + ',"]').addClass('active').find('.condition-operator').prop('disabled', false).length) {
				//
			} else {
				condition.find('div.mailster-conditions-operator-field-default').addClass('active').find('.condition-operator').prop('disabled', false);
			}
			window.mailster_updateReceiversCount();

		})
		.on('change', '.condition-operator', function () {
			window.mailster_updateReceiversCount();
		})
		.on('change', '.condition-value', function () {
			window.mailster_updateReceiversCount();
		})
		.on('click', '.mailster-condition-add-multiselect', function () {
			$(this).parent().clone().insertAfter($(this).parent()).find('.condition-value').select().focus();
		})
		.on('change', '.mailster-conditions-value-field-multiselect > .condition-value', function () {
			if (0 == $(this).val() && $(this).parent().parent().find('.condition-value').size() > 1) $(this).parent().remove();
		})
		.on('click', '.mailster-rating > span', function (event) {
			var _this = $(this),
				_prev = _this.prevAll(),
				_all = _this.siblings();
			_all.removeClass('enabled');
			_prev.add(_this).addClass('enabled');
			_this.parent().parent().find('.condition-value').val((_prev.length + 1) / 5).trigger('change');
		})
		.find('.condition-field').prop('disabled', false).trigger('change');

	function serialize() {
		var str = 'conditions=';

		$('.mailster-condition').each(function () {
			str += $(this).find('.condition-field').val();
			str += $(this).find('.condition-operator').val();
			str += $(this).find('.condition-value').val();
			str += '|';
		})

		console.log(encodeURIComponent(str));

	}


	function datepicker() {
		$('.mailster-conditions').find('.datepicker').datepicker({
			dateFormat: 'yy-mm-dd',
			//minDate: new Date(),
			firstDay: mailsterL10n.start_of_week,
			showWeek: true,
			dayNames: mailsterL10n.day_names,
			dayNamesMin: mailsterL10n.day_names_min,
			monthNames: mailsterL10n.month_names,
			prevText: mailsterL10n.prev,
			nextText: mailsterL10n.next,
			showAnim: 'fadeIn',
		});
	}


	function sprintf() {
		var a = Array.prototype.slice.call(arguments),
			str = a.shift(),
			total = a.length,
			reg;
		for (var i = 0; i < total; i++) {
			reg = new RegExp('%(' + (i + 1) + '\\$)?(s|d|f)');
			str = str.replace(reg, a[i]);
		}
		return str;
	}

	// window.tb_position = function () {
	// 	if (!window.TB_WIDTH || !window.TB_HEIGHT) return;
	// 	jQuery("#TB_window").css({
	// 		marginTop: '-' + parseInt((TB_HEIGHT / 2), 10) + 'px',
	// 		marginLeft: '-' + parseInt((TB_WIDTH / 2), 10) + 'px',
	// 		width: TB_WIDTH + 'px'
	// 	});
	// }


});