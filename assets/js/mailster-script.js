window.mailster = window.mailster || {};

mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster = mailster || {};

	var components = {
		documentReady: [],
		documentReadyDeferred: [],
		windowLoad: [],
		windowLoadDeferred: []
	};

	mailster.status = {
		documentReadyRan: false,
		windowLoadPending: false
	};

	$(document).ready(documentReady);
	$(window).on("load", windowLoad);

	function documentReady(context) {

		context = typeof context === typeof undefined ? $ : context;
		components.documentReady.concat(components.documentReadyDeferred).forEach(function (component) {
			component(context);
		});
		mailster.status.documentReadyRan = true;
		if (mailster.status.windowLoadPending) {
			windowLoad(mailster.setContext());
		}
	}

	function windowLoad(context) {
		if (mailster.status.documentReadyRan) {
			mailster.status.windowLoadPending = false;
			context = typeof context === "object" ? $ : context;
			components.windowLoad.concat(components.windowLoadDeferred).forEach(function (component) {
				component(context);
			});
		} else {
			mailster.status.windowLoadPending = true;
		}
	}

	mailster.setContext = function (contextSelector) {
		var context = $;
		if (typeof contextSelector !== typeof undefined) {
			return function (selector) {
				return $(contextSelector).find(selector);
			};
		}
		return context;
	};

	mailster.components = components;
	mailster.documentReady = documentReady;
	mailster.windowLoad = windowLoad;

	return mailster;
}(window.mailster, jQuery, window, document));


mailster = (function (mailster, $, window, document) {
	"use strict";

	mailster.util = {};

	mailster.util.requestAnimationFrame = window.requestAnimationFrame ||
		window.mozRequestAnimationFrame ||
		window.webkitRequestAnimationFrame ||
		window.msRequestAnimationFrame;

	mailster.util.documentReady = function ($) {};

	mailster.util.windowLoad = function ($) {};

	mailster.util.ajax = function (action, data, callback, errorCallback, dataType) {

		if ($.isFunction(data)) {
			if ($.isFunction(callback)) {
				errorCallback = callback;
			}
			callback = data;
			data = {};
		}

		dataType = dataType ? dataType : "JSON";
		$.ajax({
			type: 'POST',
			url: mailster.ajaxurl,
			data: $.extend({
				action: 'mailster_' + action,
				_wpnonce: mailster.wpnonce
			}, data),
			success: function (data, textStatus, jqXHR) {
				callback && callback.call(this, data, textStatus, jqXHR);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				var response = $.trim(jqXHR.responseText);
				if (textStatus == 'error' && !errorThrown) return;
				if (console) console.error(response);
				if ('JSON' == dataType) {
					var maybe_json = response.match(/{(.*)}$/);
					if (maybe_json && callback) {
						try {
							callback.call(this, $.parseJSON(maybe_json[0]));
						} catch (e) {
							if (console) console.error(e);
						}
						return;
					}
				}
				errorCallback && errorCallback.call(this, jqXHR, textStatus, errorThrown);
				alert(textStatus + ' ' + jqXHR.status + ': ' + errorThrown + '\n\n' + mailsterL10n.check_console)

			},
			dataType: dataType
		});
	}

	mailster.util.sanitize = function (string) {
		return $.trim(string).toLowerCase().replace(/ /g, '_').replace(/[^a-z0-9_-]*/g, '');
	}

	mailster.util.sprintf = function () {
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

	mailster.util.isWebkit = 'WebkitAppearance' in document.documentElement.style;
	mailster.util.isMozilla = (/firefox/i).test(navigator.userAgent);
	mailster.util.isMSIE = (/msie|trident/i).test(navigator.userAgent);
	mailster.util.isTouchDevice = 'ontouchstart' in document.documentElement;
	mailster.util.isTinyMCE = typeof tinymce == 'object';


	mailster.components.documentReady.push(mailster.util.documentReady);
	mailster.components.windowLoad.push(mailster.util.windowLoad);
	return mailster;

}(mailster, jQuery, window, document));