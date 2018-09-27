jQuery(document).ready(function ($) {

	"use strict"

	var wpnonce = $('#mailster_nonce').val(),
		start_button = $('.start-import'),
		output = $('.imports-output'),
		textoutput = $('.imports-textoutput'),
		imports = $('.imports-wrap'),
		importinfo = $('.import-info'),
		progress = $('#progress'),
		progressbar = progress.find('.bar'),
		errors, imports_run;

	$('a.external').on('click', function () {
		if (this.href) window.open(this.href);
		return false;
	});

	start_button.on('click', function () {
		start_button.hide();
		progress.show();
		output.empty();
		textoutput.val(textoutput.data('pretext'));
		imports_run = 1;
		request_import();
		errors = {
			'error': 0,
			'warning': 0,
			'notice': 0,
			'success': 0,
		};
		return false;
	});

	output.on('click', 'a', function () {
		if (this.href) window.open(this.href);
		return false;
	});

	imports
		.on('change', 'input', function () {
			($(this).is(':checked')) ? imports.removeClass('no-' + $(this).data('type')): imports.addClass('no-' + $(this).data('type'));
		});


	function request_import() {

		_ajax('request_import', {
			'formdata': $('#import-form').serialize(),
			'postdata': mailsterL10n.post_data,
		}, function (response) {

			if (response.success) {
				do_import(response.key);
			} else {

			}

		}, function (jqXHR, textStatus, errorThrown) {});

	}

	function do_import(key) {

		_ajax('third_party_import', {
			'key': key,
		}, function (response) {

			errors['error'] += response.errors.error;
			errors['warning'] += response.errors.warning;
			errors['notice'] += response.errors.notice;
			errors['success'] += response.errors.success;

			$(response.message.html).appendTo(output);

			if (!response.finished) {
				progressbar.width((response.percentage * 100) + '%');
				importinfo.html(sprintf(mailsterL10n.running_import, response.processed, response.total, response.current));

				setTimeout(function () {
					do_import(key);
				}, response.percentage ? 200 : 1000);
			} else {
				progressbar.width('100%');
				setTimeout(function () {
					progress.hide();
					progressbar.width(0);
					importinfo.html(sprintf(mailsterL10n.import_finished, errors.error, errors.warning, errors.notice));
				}, 500);
			}

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