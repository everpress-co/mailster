jQuery(document).ready(function ($) {

	"use strict"

	$("select").select2();

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