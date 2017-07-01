jQuery(document).ready(function ($) {

	"use strict"

	//general vars
	var wpnonce = $('#mailster_nonce').val(),
		start_button = $('.start-test'),
		output = $('.tests-output'),
		textoutput = $('.tests-textoutput'),
		tests = $('.tests-wrap'),
		testinfo = $('.test-info'),
		progress = $('#progress'),
		progressbar = progress.find('.bar'),
		outputnav = $('#outputnav'),
		outputtabs = $('.subtab'),
		errors, tests_run;

	$('a.external').on('click', function () {
		if (this.href) window.open(this.href);
		return false;
	});

	start_button.on('click', function () {
		start_button.hide();
		progress.show();
		output.empty();
		textoutput.val(textoutput.data('pretext'));
		tests_run = 1;
		test();
		errors = {
			'error': 0,
			'warning': 0,
			'notice': 0,
			'success': 0,
		};
		return false;
	})

	output.on('click', 'a', function () {
		if (this.href) window.open(this.href);
		return false;
	})

	tests
		.on('change', 'input', function () {
			($(this).is(':checked')) ? tests.removeClass('no-' + $(this).data('type')): tests.addClass('no-' + $(this).data('type'));
		});

	outputnav.on('click', 'a.nav-tab', function () {
		outputnav.find('a').removeClass('nav-tab-active');
		outputtabs.hide();
		var hash = $(this).addClass('nav-tab-active').attr('href');
		$('#subtab-' + hash.substr(1)).show();
		return false;
	});


	function test(test_id) {

		_ajax('test', {
			'test_id': test_id,
		}, function (response) {

			errors[response.type]++;

			$(response.message.html).appendTo(output);
			if ('success' != response.type) {
				textoutput.val(textoutput.val() + response.message.text + '\n');
			}

			if (response.nexttest) {
				progressbar.width(((++tests_run) / response.total * 100) + '%');
				testinfo.html(sprintf(mailsterL10n.running_test, tests_run, response.total, response.current));
			} else {
				start_button.html(mailsterL10n.restart_test).show();
				progress.hide();
				progressbar.width(0);
				testinfo.html(sprintf(mailsterL10n.tests_finished, errors.error, errors.warning, errors.notice));
			}

			if (response.nexttest) {
				setTimeout(function () {
					test(response.nexttest);
				}, 100);
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