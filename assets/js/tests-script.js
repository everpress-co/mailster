jQuery(document).ready(function ($) {

	"use strict"

	//general vars
	var wpnonce = $('#mailster_nonce').val(),
		output = $('#tests_output');


	$('.start-test').on('click', function () {
		test();
		return false;
	})

	function test(test_id) {

		_ajax('test', {
			'test_id': test_id,
		}, function (response) {

			$(response.message.html).appendTo(output);

			if (response.nexttest) {
				setTimeout(function () {
					test(response.nexttest);
				}, 300);
			} else {}

		}, function (jqXHR, textStatus, errorThrown) {});
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

	function _ajax(action, data, callback, errorCallback) {

		if ($.isFunction(data)) {
			if ($.isFunction(callback)) {
				errorCallback = callback;
			}
			callback = data;
			data = {};
		}
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: $.extend({
				action: 'mailster_' + action,
				_wpnonce: wpnonce
			}, data),
			success: function (data, textStatus, jqXHR) {
				callback && callback.call(this, data, textStatus, jqXHR);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				if (textStatus == 'error' && !errorThrown) return;
				if (console) console.error($.trim(jqXHR.responseText));
				errorCallback && errorCallback.call(this, jqXHR, textStatus, errorThrown);
			},
			dataType: "JSON"
		});
	}


});