jQuery(document).ready(function ($) {

	"use strict"

	var _win = $(window),
		_doc = $(document),
		_body = $('body'),
		_iframe = $('#mailster_iframe'),
		_template_wrap = $('#template-wrap'),
		_ibody, _idoc, _container = $('#mailster_template .inside'),
		_disabled = !!$('#mailster_disabled').val(),
		_title = $('#title'),
		_subject = $('#mailster_subject'),
		_preheader = $('#mailster_preheader'),
		_content = $('#content'),
		_excerpt = $('#excerpt'),
		_modulesraw = $('#modules'),
		_plaintext = $('#plain-text-wrap'),
		_html = $('#html-wrap'),
		_head = $('#head'),
		_obar = $('#optionbar'),
		_undo = [],
		campaign_id = $('#post_ID').val(),
		_currentundo = 0,
		_clickbadgestats = $('#clickmap-stats'),
		_mailsterdata = $('[name^="mailster_data"]'),
		wpnonce = $('#mailster_nonce').val(),
		iframeloaded = false,
		timeout, refreshtimout, updatecounttimeout, modules, optionbar, charts, editbar, animateDOM = $('html,body'),
		isWebkit = 'WebkitAppearance' in document.documentElement.style,
		isMozilla = (/firefox/i).test(navigator.userAgent),
		isMSIE = (/msie|trident/i).test(navigator.userAgent),
		getSelect, selectRange, isDisabled = false,
		is_touch_device = 'ontouchstart' in document.documentElement,
		isTinyMCE = typeof tinymce == 'object',
		codemirror, codemirrorargs = {
			mode: {
				name: "htmlmixed",
				scriptTypes: [{
					matches: /\/x-handlebars-template|\/x-mustache/i,
					mode: null
				}, {
					matches: /(text|application)\/(x-)?vb(a|script)/i,
					mode: "vbscript"
				}]
			},
			tabMode: "indent",
			lineNumbers: true,
			viewportMargin: Infinity,
			autofocus: true
		};

	function _init() {

		_trigger('disable');
		_time();

		//set the document of the iframe cross browser like
		_idoc = (_iframe[0].contentWindow || _iframe[0].contentDocument);
		if (_idoc.document) _idoc = _idoc.document;

		_events();

		var iframeloadinterval = setTimeout(function () {
			if (!iframeloaded) _iframe.trigger('load');
		}, 5000);

		window.Mailster = window.Mailster || {
			refresh: function () {
				_trigger('refresh');
			},
			save: function () {
				_trigger('save');
			},
			trigger: _trigger,
			autosave: '',
		};

		_iframe
			.on('load', function () {

				if (iframeloaded) return false;
				if (!_disabled) {
					if (!optionbar) optionbar = new _optionbar();
					if (!editbar) editbar = new _editbar();
					if (!modules) modules = new _modules();

					window.Mailster.editbar = editbar;

				} else {}

				_trigger('enable');

				iframeloaded = true;
				clearInterval(iframeloadinterval);

				_ibody = _iframe.contents().find('body');

				if (_disabled) {
					//overwrite autosave function since we don't need it
					window.autosave = wp.autosave = function () {
						return true;
					};
					window.onbeforeunload = null;

					_ibody.on('click', 'a', function () {
						window.open(this.href);
						return false;
					});

				} else {

				}

				_trigger('refresh');
				if (!_content.val()) {
					_trigger('save');
				}
				$("#normal-sortables").on("sortupdate", function (event, ui) {
					_trigger('resize');
				});

				_template_wrap.removeClass('load');

				// add current content to undo list
				_undo.push(_getFrameContent());

			});



	}


	function _events() {


		if (!_disabled) {


			_doc
				.on('change', '.dynamic_embed_options_taxonomy', function () {
					var $this = $(this),
						val = $this.val();
					$this.parent().find('.button').remove();
					if (val != -1) {
						if ($this.parent().find('select').length < $this.find('option').length - 1)
							$(' <a class="button button-small add_embed_options_taxonomy">' + mailsterL10n.add + '</a>').insertAfter($this);
					} else {
						$this.parent().html('').append($this);
					}

					return false;
				})
				.on('click', '.add_embed_options_taxonomy', function () {
					var $this = $(this),
						el = $this.prev().clone();

					el.insertBefore($this).val('-1');
					$('<span> ' + mailsterL10n.or + ' </span>').insertBefore(el);
					$this.remove();

					return false;
				});






		} else {

			if (typeof autosavePeriodical != 'undefined') autosavePeriodical.repeat = false;







		}

	}


	function _scroll(pos, callback, speed) {
		pos = Math.round(pos);
		if (isNaN(speed)) speed = 200;
		if (!isMSIE && (animateDOM.scrollTop() == pos || document.scrollingElement.scrollTop == pos)) {
			callback && callback();
			return
		}
		animateDOM.stop().animate({
			'scrollTop': pos
		}, speed, function () {
			callback && callback()
		});
	}

	function _jump(val, rel) {
		val = Math.round(val);
		if (rel) {
			window.scrollBy(0, val);
		} else {
			window.scrollTo(0, val);
		}
	}

	$(window)

	.on('Mailster:refresh', function () {
		clearTimeout(refreshtimout);
		refreshtimout = setTimeout(function () {
			_trigger('resize');

			if (!_disabled) {
				_editButtons();
			} else {
				_clickBadges();
			}
		}, 10);
	})

	.on('Mailster:resize', function () {
		if (!iframeloaded) return false;
		setTimeout(function () {
			if (!_iframe[0].contentWindow.document.body) return;
			var height = _iframe.contents().find('body').outerHeight() ||
				_iframe.contents().height() ||
				_iframe[0].contentWindow.document.body.offsetHeight ||
				_iframe.contents().find("html")[0].innerHeight ||
				_iframe.contents().find("html").height();

			height = Math.max(500, height + 4);
			$('#editor-height').val(height);
			_iframe.attr("height", height);
		}, 50);
	})

	.on('Mailster:save', function () {
		if (!_disabled && iframeloaded) {

			var content = _getFrameContent();

			var length = _undo.length,
				lastundo = _undo[length - 1];

			if (lastundo != content) {

				_content.val(content);

				_preheader.prop('readonly', !content.match('{preheader}'));

				_undo = _undo.splice(0, _currentundo + 1);

				_undo.push(content);
				if (length >= mailsterL10n.undosteps) _undo.shift();
				_currentundo = _undo.length - 1;

				if (_currentundo) _obar.find('a.undo').removeClass('disabled');
				_obar.find('a.redo').addClass('disabled');

				if (wp && wp.autosave) wp.autosave.local.save();
			}

		}
	})

	.on('Mailster:disable', function () {
		isDisabled = true;
		$('.button').prop('disabled', true);
		$('input:visible').prop('disabled', true);
	})

	.on('Mailster:enable', function () {
		$('.button').prop('disabled', false);
		$('input:visible, input.wp-color-picker').prop('disabled', false);
		isDisabled = false;
	})

	.on('Mailster:selectModule', function (event) {
		if (!event.detail) return;
		var module = event.detail[0];
	})

	.on('Mailster:updateCount', function () {})

	.on('Mailster:xxx', function () {

	});

	function _trigger() {

		var triggerevent = arguments[0];
		var args = arguments[1] || null;
		var event;
		if (isMSIE) {
			event = document.createEvent("CustomEvent");
			event.initCustomEvent('Mailster:' + triggerevent, false, false, {
				'detail': args,
			});
		} else {
			event = new CustomEvent('Mailster:' + triggerevent, {
				'detail': args,
			});
		}

		window.dispatchEvent(event);
		_iframe[0].contentWindow.window.dispatchEvent(event);
	}

	function _replace(str, match, repl) {
		if (match === repl)
			return str;
		do {
			str = str.replace(match, repl);
		} while (match && str.indexOf(match) !== -1);
		return str;
	}

	function _changeElements(version) {
		var raw = _getContent(),
			reg = /\/img\/version(\d+)\//g,
			to = '/img/' + version + '/';

		html = raw.replace(reg, to);

		var m = _modulesraw.val();
		_modulesraw.val(m.replace(reg, to));

		_setContent(html);

		return;
	}

	function _getFrameContent() {

		var body = _iframe[0].contentWindow.document.body,
			clone, content, bodyattributes, attrcount, s = '';

		if (typeof body == 'null' || !body) return '';

		clone = $('<div>' + body.innerHTML + '</div>');

		clone.find('.mce-tinymce, .mce-widget, .mce-toolbar-grp, .mce-container, .screen-reader-text, .ui-helper-hidden-accessible, .wplink-autocomplete, modulebuttons, mailster, #mailster-editorimage-upload-button, button').remove();
		//remove some third party elements
		clone.find('#droplr-chrome-extension-is-installed').remove();
		clone.find('single, multi, module, modules, buttons').removeAttr('contenteditable spellcheck id dir style class selected');
		content = $.trim(clone.html().replace(/\u200c/g, '&zwnj;').replace(/\u200d/g, '&zwj;'));


		bodyattributes = body.attributes || [];
		attrcount = bodyattributes.length;

		if (attrcount) {
			while (attrcount--) {
				s = ' ' + bodyattributes[attrcount].name + '="' + $.trim(bodyattributes[attrcount].value) + '"' + s;
			}
		}
		s = $.trim(s
			.replace(/(webkit |wp\-editor|mceContentBody|position: relative;|cursor: auto;|modal-open| spellcheck="(true|false)")/g, '')
			.replace(/(class="(\s*)"|style="(\s*)")/g, ''));

		return _head.val() + "\n<body" + (s ? ' ' + s : '') + ">\n" + content + "\n</body>\n</html>";
	}

	function _getContent() {
		return _content.val() || _getFrameContent();
	}

	function _getHTMLStructure(html) {
		var parts = html.match(/([^]*)<body([^>]*)?>([^]*)<\/body>([^]*)/m);

		return {
			parts: parts ? parts : ['', '', '', '<multi>' + html + '</multi>'],
			content: parts ? parts[3] : '<multi>' + html + '</multi>',
			head: parts ? $.trim(parts[1]) : '',
			bodyattributes: parts ? $('<div' + (parts[2] || '') + '></div>')[0].attributes : ''
		};
	}

	function _setContent(content, delay, saveit, extrastyle) {

		var structure = _getHTMLStructure(content);

		var attrcount = structure.bodyattributes.length,
			doc = (isWebkit || isMozilla) ? _iframe[0].contentWindow.document : _idoc,
			headstyles = $(doc).find('head').find('link'),
			headdoc = doc.getElementsByTagName('head')[0];

		_head.val(structure.head);
		if (!extrastyle) extrastyle = '';
		headdoc.innerHTML = structure.head.replace(/([^]*)<head([^>]*)?>([^]*)<\/head>([^]*)/m, '$3' + extrastyle);
		$(headdoc).append(headstyles);

		doc.body.innerHTML = structure.content;

		if (attrcount) {
			while (attrcount--) {
				doc.body.setAttribute(structure.bodyattributes[attrcount].name, structure.bodyattributes[attrcount].value)
			}
		}

		if (delay !== false) {
			clearTimeout(timeout);
			timeout = setTimeout(function () {
				modules && modules.refresh && modules.refresh();
				_trigger('refresh');
			}, delay || 100);
		} else {
			_trigger('refresh');
		}

		if (typeof saveit == 'undefined' || saveit === true) _trigger('save');
	}

	function _getAutosaveString() {
		return _title.val() + _content.val() + _excerpt.val() + _subject.val() + _preheader.val();
	}

	function _ajax(action, data, callback, errorCallback, dataType) {

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
			url: ajaxurl,
			data: $.extend({
				action: 'mailster_' + action,
				_wpnonce: wpnonce
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

	function _sanitize(string) {
		return $.trim(string).toLowerCase().replace(/ /g, '_').replace(/[^a-z0-9_-]*/g, '');
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

	function _getRealDimensions(el, callback) {
		el = el.eq(0);
		if (el.is('img') && el.attr('src')) {
			var image = new Image(),
				factor;
			image.onload = function () {
				factor = ((image.width / el.width()).toFixed(1) || 1);
				if (callback) callback.call(this, image.width, image.height, isFinite(factor) ? parseFloat(factor) : 1)
			}
			image.src = el.attr('src');
		};
	}

	function _rgbToHex(r, g, b, hash) {
		return (hash === false ? '' : '#') + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
	}


	_init();

});