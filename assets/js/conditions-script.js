jQuery(document).ready(function ($) {

	"use strict"

	var conditions = $('.mailster-conditions'),
		cond = $('.mailster-condition'),
		condition,
		value_fields;

	cond.eq(0).appendTo($('.mailster-condition-container'));

	$('.mailster-conditions-thickbox')
		.on('click', '.add-condition', function () {
			var cond = $('.mailster-condition'),
				id = cond.length - 1,
				clone = cond.eq(0).clone();

			clone.hide().removeAttr('id').appendTo(conditions).slideDown(200);
			$.each(clone.find('input, select'), function () {
				var name = $(this).val('').attr('name');
				$(this).attr('name', name.replace(/\[\d+\]/, '[' + id + ']')).trigger('change');
			});
			clone.find('.datepicker').datepicker();
			clone.find('select.select2').select2();
			clone.find('.condition-field').focus();

		})


	conditions
		.on('click', '.remove-condition', function () {
			$(this).parent().slideUp(200, function () {
				$(this).remove();
			});
		})
		.on('change', '.condition-field', function () {

			condition = $(this).closest('.mailster-condition');

			var value = $(this).val();
			condition.find('div.mailster-conditions-value-field').hide().find('.condition-value').prop('disabled', true);
			condition.find('div.mailster-conditions-operator-field').hide().find('.condition-operator').prop('disabled', true);

			if (condition.find('div.mailster-conditions-value-field[data-fields*="' + value + '"]').show().find('.condition-value').prop('disabled', false).focus().select().length) {} else {
				condition.find('div.mailster-conditions-value-field-default').show().find('.condition-value').prop('disabled', false).focus().select();
			}
			if (condition.find('div.mailster-conditions-operator-field[data-fields*="' + value + '"]').show().find('.condition-operator').prop('disabled', false).length) {} else {
				condition.find('div.mailster-conditions-operator-field-default').show().find('.condition-operator').prop('disabled', false);
			}

		})
		.on('change', '.condition-operator', function () {

		})
		.on('change', '.condition-value', function () {});

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