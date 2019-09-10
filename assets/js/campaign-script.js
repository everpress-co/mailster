mailster = (function (mailster, $, window, document) {
	"use strict";

	var $el = {},
		dom = {};

	$el.window = $(window);
	$el.document = $(document);
	$el.wpbody = $('#wpbody');
	$el.title = $('#title');
	$el.iframe = $('#mailster_iframe');
	$el.templateWrap = $('#template-wrap');
	$el.container = $('#mailster_template .inside');
	$el.subject = $('#mailster_subject');
	$el.preheader = $('#mailster_preheader');
	$el.content = $('#content');
	$el.excerpt = $('#excerpt');
	$el.modulesraw = $('#modules');
	$el.plaintext = $('#plain-text-wrap');
	$el.html = $('#html-wrap');
	$el.head = $('#head');
	$el.optionbar = $('#optionbar');
	$el.editbar = $('#editbar');

	for (var i in $el) {
		dom[i] = $el[i][0];
	}

	mailster.$ = $el;
	mailster.dom = dom;
	mailster.campaign_id = parseInt($('#post_ID').val(), 10);
	mailster.user_id = parseInt($('#user-id').val(), 10);

	return mailster;

}(mailster, jQuery, window, document));


// events
mailster = (function (mailster, $, window, document) {
	"use strict";

	var triggertimeout,
		isEnabled = !$('#mailster_disabled').val();

	mailster.events = {};

	mailster.events.refresh = [];
	mailster.events.resize = [];
	mailster.events.save = [];
	mailster.events.disable = [];
	mailster.events.enable = [];
	mailster.events.selectModule = [];
	mailster.events.updateCount = [];

	mailster.events.disable.push(
		function () {
			isEnabled = false;
			$('.button').prop('disabled', true);
			$('input:visible').prop('disabled', true);
		}
	)
	mailster.events.enable.push(
		function () {
			$('.button').prop('disabled', false);
			$('input:visible, input.wp-color-picker').prop('disabled', false);
			isEnabled = true;
		}
	)

	mailster.disable = function () {
		mailster.trigger('disable');
	}
	mailster.trigger = function () {

		var params = Array.prototype.slice.call(arguments),
			triggerevent = params.shift(),
			args = params || null;

		if (mailster.events[triggerevent]) {
			for (var i = 0; i < mailster.events[triggerevent].length; i++) {
				mailster.events[triggerevent][i].apply(mailster, args);
			}
			$(window).trigger('Mailster:' + triggerevent, args);
		}
		console.log('Event Mailster:' + triggerevent, mailster);
	}

	return mailster;

}(mailster, jQuery, window, document));
// end events



// editor
mailster = (function (mailster, $, window, document) {
	"use strict";

	mailster.editor = mailster.editor || {};

	mailster.editor.getFrameContent = function () {

		var body = mailster.dom.iframe.contentWindow.document.body,
			clone, content, bodyattributes, attrcount, s = '';

		if (typeof body == 'null' || !body) {
			return '';
		}

		clone = $('<div>' + body.innerHTML + '</div>');

		clone.find('.mce-tinymce, .mce-widget, .mce-toolbar-grp, .mce-container, .screen-reader-text, .ui-helper-hidden-accessible, .wplink-autocomplete, modulebuttons, mailster, #mailster-editorimage-upload-button, button').remove();

		// remove some third party elements
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
		s = $.trim(
			s
			.replace(/(webkit |wp\-editor|mceContentBody|position: relative;|cursor: auto;|modal-open| spellcheck="(true|false)")/g, '')
			.replace(/(class="(\s*)"|style="(\s*)")/g, '')
		);

		return mailster.$.head.val() + "\n<body" + (s ? ' ' + s : '') + ">\n" + content + "\n</body>\n</html>";
	}

	mailster.editor.getContent = function () {
		return mailster.$.content.val() || mailster.editor.getFrameContent();
	}

	mailster.editor.getStructure = function (html) {
		var parts = html.match(/([^]*)<body([^>]*)?>([^]*)<\/body>([^]*)/m);

		return {
			parts: parts ? parts : ['', '', '', '<multi>' + html + '</multi>'],
			content: parts ? parts[3] : '<multi>' + html + '</multi>',
			head: parts ? $.trim(parts[1]) : '',
			bodyattributes: parts ? $('<div' + (parts[2] || '') + '></div>')[0].attributes : ''
		};
	}

	mailster.editor.setContent = function (content, delay, saveit, extrastyle) {

		var structure = e.getStructure(content);

		var attrcount = structure.bodyattributes.length,
			doc = (isWebkit || isMozilla) ? mailster.$.iframe[0].contentWindow.document : _idoc,
			headstyles = $(doc).find('head').find('link'),
			headdoc = doc.getElementsByTagName('head')[0];

		_head.val(structure.head);
		if (!extrastyle) {
			extrastyle = '';
		}
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
			timeout = setTimeout(
				function () {
					modules && modules.refresh && modules.refresh();
					mailster.trigger('refresh');
				},
				delay || 100
			);
		} else {
			mailster.trigger('refresh');
		}

		if (typeof saveit == 'undefined' || saveit === true) {
			mailster.trigger('save');
		}
	}

	function initFrame() {
		if (mailster.editor.loaded) {
			return false;
		}

		mailster.$.templateWrap.removeClass('load');
		// add current content to undo list
		mailster.optionbar.undos.push(mailster.editor.getFrameContent());

		mailster.trigger('iframeLoaded');
	}

	mailster.editor.loaded = false;

	mailster.$.iframe
		.on('load', initFrame)

	return mailster;

}(mailster, jQuery, window, document));
// end editor



// block
mailster = (function (mailster, $, window, document) {
	"use strict";

	mailster.util = mailster.util || {};

	mailster.util.getRealDimensions = function (el, callback) {
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

	return mailster;

}(mailster, jQuery, window, document));
// end block






// block
mailster = (function (mailster, $, window, document) {
	"use strict";

	// mailster.block = {};

	return mailster;

}(mailster, jQuery, window, document));
// end block


