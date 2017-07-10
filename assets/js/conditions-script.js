jQuery(document).ready(function ($) {

	"use strict"

	//$("select").select2();

	$('.mailster-conditions')
		.on('click', '.add-condition', function () {
			var cond = $('.mailster-condition'),
				id = cond.length,
				clone = cond.last().clone();

			clone.hide().removeAttr('id').insertAfter(cond.last()).slideDown(200);
			$.each(clone.find('input, select'), function () {
				var name = $(this).val('').attr('name');
				$(this).attr('name', name.replace(/\[\d+\]/, '[' + id + ']'));
			});

			console.log($('.mailster-conditions').serialize());

			//serialize();

		})
		.on('click', '.remove-condition', function () {
			$(this).parent().slideUp(200, function () {
				$(this).remove();
			});
		});

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