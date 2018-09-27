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
		do_import();
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


	function do_import(import_id) {

		_ajax('third_party_import', {
			'import_id': import_id,
			'importer': $('#importer').val(),
			'post_data': mailsterL10n.post_data,
		}, function (response) {

			errors['error'] += response.errors.error;
			errors['warning'] += response.errors.warning;
			errors['notice'] += response.errors.notice;
			errors['success'] += response.errors.success;

			$(response.message.html).appendTo(output);
			textoutput.val(textoutput.val() + response.message.text);

			if (response.nextimport) {
				progressbar.width(((++imports_run) / response.total * 100) + '%');
				importinfo.html(sprintf(mailsterL10n.running_import, imports_run, response.total, response.current));
			} else {
				progressbar.width('100%');
				setTimeout(function () {
					start_button.html(mailsterL10n.restart_import).show();
					progress.hide();
					progressbar.width(0);
					importinfo.html(sprintf(mailsterL10n.imports_finished, errors.error, errors.warning, errors.notice));
				}, 500);
			}

			if (response.nextimport) {
				setTimeout(function () {
					import (response.nextimport);
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