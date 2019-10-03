// optionbar
mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.optionbar = {};

	mailster.optionbar.undos = [];
	mailster.optionbar.currentUndo = 0;

	mailster.optionbar.undo = function () {

		if (mailster.optionbar.currentUndo) {
			mailster.optionbar.currentUndo--;
			mailster.editor.setContent(mailster.optionbar.undos[mailster.optionbar.currentUndo], 100, false);
			mailster.$.optionbar.find('a.redo').removeClass('disabled');
			if (!mailster.optionbar.currentUndo) {
				$(this).addClass('disabled');
			}
		}

	};

	mailster.optionbar.redo = function () {
		var length = mailster.optionbar.undos.length;

		if (mailster.optionbar.currentUndo < length - 1) {
			mailster.optionbar.currentUndo++;
			mailster.editor.setContent(mailster.optionbar.undos[mailster.optionbar.currentUndo], 100, false);
			mailster.$.optionbar.find('a.undo').removeClass('disabled');
			if (mailster.optionbar.currentUndo >= length - 1) {
				$(this).addClass('disabled');
			}
		}
	}

	mailster.optionbar.removeModules = function () {
		if (confirm(mailsterL10n.remove_all_modules)) {
			var modulecontainer = mailster.$.iframe.contents().find('modules');
			var modules = modulecontainer.find('module');
			modulecontainer.slideUp(
				function () {
					modules.remove();
					modulecontainer.html('').show();
					mailster.trigger('refresh');
					mailster.trigger('save');
				}
			);
		}
	}

	mailster.optionbar.codeView = function () {

		var structure;

		if (!mailster.$.iframe.is(':visible')) {

			structure = mailster.editor.getStructure(_getFrameContent());

			mailster.$.optionbar.find('a.code').addClass('loading');
			mailster.trigger('disable');

			mailster.util.ajax(
				'toggle_codeview', {
					bodyattributes: structure.parts[2],
					content: structure.content,
					head: structure.head
				},
				function (response) {
					mailster.$.optionbar.find('a.code').addClass('active').removeClass('loading');
					mailster.$.html.hide();
					mailster.$.content.val(response.content);
					mailster.$.optionbar.find('a').not('a.redo, a.undo, a.code').addClass('disabled');

					codemirror = CodeMirror.fromTextArea(mailster.$.content.get(0), codemirrorargs);

				},
				function (jqXHR, textStatus, errorThrown) {
					mailster.$.optionbar.find('a.code').addClass('active').removeClass('loading');
					mailster.trigger('enable');
				}
			);

		} else {

			structure = mailster.editor.getStructure(codemirror.getValue());
			codemirror.clearHistory();

			mailster.$.optionbar.find('a.code').addClass('loading');
			mailster.trigger('disable');

			mailster.util.ajax(
				'toggle_codeview', {
					bodyattributes: structure.parts[2],
					content: structure.content,
					head: structure.head
				},
				function (response) {
					mailster.editor.setContent(response.content, 100, true, response.style);
					mailster.$.html.show();
					mailster.$.content.hide();
					$('.CodeMirror').remove();
					mailster.$.optionbar.find('a.code').removeClass('active').removeClass('loading');
					mailster.$.optionbar.find('a').not('a.redo, a.undo, a.code').removeClass('disabled');

					mailster.trigger('enable');

				},
				function (jqXHR, textStatus, errorThrown) {
					mailster.$.optionbar.find('a.code').addClass('active').removeClass('loading');
					mailster.trigger('enable');
				}
			);

		}
		return false;
	}

	mailster.optionbar.plainText = function () {

		if (mailster.$.iframe.is(':visible')) {

			mailster.$.optionbar.find('a.plaintext').addClass('active');
			mailster.$.html.hide();
			mailster.$.excerpt.show();
			mailster.$.plaintext.show();
			mailster.$.optionbar.find('a').not('a.redo, a.undo, a.plaintext, a.preview').addClass('disabled');

		} else {

			mailster.$.html.show();
			mailster.$.plaintext.hide();
			mailster.$.optionbar.find('a.plaintext').removeClass('active');
			mailster.$.optionbar.find('a').not('a.redo, a.undo, a.plaintext, a.preview').removeClass('disabled');

			mailster.trigger('refresh');

		}

	}

	mailster.optionbar.openSaveDialog = function () {

		tb_show(mailsterL10n.save_template, '#TB_inline?x=1&width=480&height=320&inlineId=mailster_template_save', null);
		$('#new_template_name').focus().select();
	};

	function saveTemplate() {

		mailster.trigger('disable');

		var name = $('#new_template_name').val();
		if (!name) {
			return false;
		}
		mailster.trigger('save');

		var loader = $('#new_template-ajax-loading').css('display', 'inline'),
			modules = $('#new_template_modules').is(':checked'),
			activemodules = $('#new_template_active_modules').is(':checked'),
			file = $('#new_template_saveas_dropdown').val(),
			overwrite = !!parseInt($('input[name="new_template_overwrite"]:checked').val(), 10),
			content = mailster.editor.getContent();

		mailster.util.ajax(
			'create_new_template', {
				name: name,
				modules: modules,
				activemodules: activemodules,
				overwrite: overwrite ? file : false,
				template: $('#mailster_template_name').val(),
				content: content,
				head: mailster.$.head.val()
			},
			function (response) {
				loader.hide();
				if (response.success) {
					// destroy wp object
					if (window.wp) {
						window.wp = null;
					}
					window.location = response.url;
				} else {
					alert(response.msg);
				}
			},
			function (jqXHR, textStatus, errorThrown) {
				loader.hide();
			}
		);
		return false;
	}

	mailster.optionbar.preview = function () {

		if (mailster.$.optionbar.find('a.preview').is('.loading')) {
			return false;
		}

		mailster.trigger('save');

		mailster.$.optionbar.find('a.preview').addClass('loading');
		mailster.util.ajax(
			'set_preview', {
				id: mailster.campaign_id,
				content: mailster.editor.getContent(),
				head: mailster.$.head.val(),
				issue: $('#mailster_autoresponder_issue').val(),
				subject: mailster.details.$.subject.val(),
				preheader: mailster.details.$.preheader.val()
			},
			function (response) {
				mailster.$.optionbar.find('a.preview').removeClass('loading');

				mailster.thickbox.$.preview.attr('src', ajaxurl + '?action=mailster_get_preview&hash=' + response.hash + '&_wpnonce=' + response.nonce);
				tb_show((mailster.$.title.val() ? mailster.util.sprintf(mailsterL10n.preview_for, '"' + mailster.$.title.val() + '"') : mailsterL10n.preview), '#TB_inline?hash=' + response.hash + '&_wpnonce=' + response.nonce + '&width=' + (Math.min(1200, mailster.$.window.width() - 50)) + '&height=' + (mailster.$.window.height() - 100) + '&inlineId=mailster_campaign_preview', null);

			},
			function (jqXHR, textStatus, errorThrown) {
				mailster.$.optionbar.find('a.preview').removeClass('loading');
			}
		);

	}

	mailster.optionbar.dfw = function (event) {

		if (event.type == 'mouseout' && !/DIV|H3/.test(event.target.nodeName)) {
			return;
		}

		containeroffset = mailster.$.template.offset();

		if (!mailster.$.body.hasClass('focus-on')) {
			mailster.$.body.removeClass('focus-off').addClass('focus-on');
			mailster.$.wpbody.on('mouseleave.dfw', dfw);
			mailster.$.optionbar.find('a.dfw').addClass('active');
			if (mailster.$.window.scrollTop() < containeroffset.top) {
				_scroll(containeroffset.top - 80);
			}

		} else {
			mailster.$.body.removeClass('focus-on').addClass('focus-off');
			mailster.$.wpbody.off('mouseleave', dfw);
			mailster.$.optionbar.find('a.dfw').removeClass('active');
		}

	}

	function showFiles(name) {
		var $this = $(this);
		$this.parent().find('ul').eq(0).slideToggle(100);
	}

	function changeTemplate() {

		window.onbeforeunload = null;
		window.location = this.href;
	}

	mailster.$.document
		.on('click', 'button.save-template', saveTemplate)
		.on('click', 'button.save-template-cancel', tb_remove);

	mailster.$.optionbar

		.on('click', 'a', false)
		.on('click', 'a.save-template', mailster.optionbar.openSaveDialog)
		.on('click', 'a.clear-modules', mailster.optionbar.removeModules)
		.on('click', 'a.preview', mailster.optionbar.preview)
		.on('click', 'a.undo', mailster.optionbar.undo)
		.on('click', 'a.redo', mailster.optionbar.redo)
		.on('click', 'a.code', mailster.optionbar.codeView)
		.on('click', 'a.plaintext', mailster.optionbar.plainText)
		.on('click', 'a.dfw', mailster.optionbar.dfw)

	.on('click', 'a.template', showFiles)
		.on('click', 'a.file', changeTemplate);

	mailster.events.push('editorLoaded', function () {
		mailster.optionbar.undos.push(mailster.editor.getFrameContent());
	});


	return mailster;

}(mailster, jQuery, window, document));
// end optiobar