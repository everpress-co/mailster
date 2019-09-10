// not in an iframe
if (parent.window === window) {

	var campaign_id;
	if (campaign_id = location.search.match(/id=(\d+)/i)[1]) {
		window.location = location.protocol + '//' + location.host + location.pathname.replace('admin-ajax.php', 'post.php') + '?post=' + campaign_id + '&action=edit';
	}
}

document.getElementsByTagName("html")[0].className += ' mailster-loading';

window.mailster = parent.window.mailster || {};

// block
mailster = (function (mailster, $, window, document) {
	"use strict";

	mailster.editor = mailster.editor || {};

	mailster.editor.currentmodule = false;

	mailster.editor.$ = mailster.editor.$ || {};
	mailster.editor.$.html = $('html');
	mailster.editor.$.body = $('body');

	mailster.components.windowLoad.push(
		function () {
			mailster.editor.$.html.removeClass('mailster-loading');
			mailster.editor.$.body = $('body');
			mailster.editor.$.body
				.on(
					'click',
					'a',
					function (event) {
						event.preventDefault();
					}
				)
				.on(
					'click',
					function (event) {
						if (mailster.editor.currentmodule) {
							mailster.editor.currentmodule.removeAttr('selected');
						}
					}
				)
				.on(
					'click',
					'module',
					function (event) {
						if ('MODULE' == event.target.nodeName) {
							event.stopPropagation();
							mailster.trigger('selectModule', $(this));
						}
					}
				)
				.on(
					'click',
					'button.addbutton',
					function () {
						var data = $(this).data(),
							element = decodeURIComponent(data.element.data('tmpl')) || '<a href="" editable label="Button"></a>';

						parent.window.Mailster.editbar.open({
							type: 'btn',
							offset: data.offset,
							element: $(element).attr('tmpbutton', '').appendTo(data.element),
							name: data.name
						});
						return false;
					}
				)
				.on(
					'click',
					'button.addrepeater',
					function () {
						var data = $(this).data();

						if ('TH' == data.element[0].nodeName || 'TD' == data.element[0].nodeName) {
							var table = data.element.closest('table'),
								index = data.element.prevAll().length;
							for (var i = table[0].rows.length - 1; i >= 0; i--) {
								$(table[0].rows[i].cells[index]).clone().insertAfter(table[0].rows[i].cells[index]);
							}
						} else {
							data.element.clone().insertAfter(data.element);
						}

						mailster.trigger('save');
						mailster.trigger('refresh');

						return false;
					}
				)
				.on(
					'click',
					'button.removerepeater',
					function () {
						var data = $(this).data();

						if ('TH' == data.element[0].nodeName || 'TD' == data.element[0].nodeName) {
							var table = data.element.closest('table'),
								index = data.element.prevAll().length;
							for (var i = table[0].rows.length - 1; i >= 0; i--) {
								$(table[0].rows[i].cells[index]).remove();
							}
						} else {
							data.element.remove();
						}

						mailster.trigger('save');
						mailster.trigger('refresh');

						return false;
					}
				);

			mailster.editor.loaded = true;
			mailster.trigger('refresh');

		}
	);

	// legacy buttons
	mailster.editor.$.body.find('div.modulebuttons').remove();
	(mailster.isrtl) ? mailster.editor.$.html.attr('mailster-is-rtl', ''): mailster.editor.$.html.removeAttr('mailster-is-rtl');

	return mailster;

}(mailster, jQuery, window, document));
// end block


// block Modules
mailster = (function (mailster, $, window, document) {
	"use strict";

	var l10n = mailster_mce_button.l10n,
		tags = mailster_mce_button.tags,
		designs = mailster_mce_button.designs,
		changetimeout,
		change = false,
		uploader = false;

	mailster.editor.$ = mailster.editor.$ || {};


	mailster.components.windowLoad.push(
		function(){
			mailster.events.refresh.push(dom, sortable, draggable);
			mailster.events.resize.push(buttons);
			mailster.events.selectModule.push(select);
			typeof mOxie != 'undefined' && mailster.events.refresh.push(upload);
			typeof tinymce != 'undefined' && mailster.events.refresh.push(inlineEditor);
		}
	)

	function dom(){
		mailster.editor.$.container = $('modules');
		mailster.editor.$.modules = $('module');
		mailster.editor.$.images = $('img[editable]');
		mailster.editor.$.buttons = $('buttons');
		mailster.editor.$.repeatable = $('[repeatable]');
	}

	function sortable(){
		if (mailster.editor.$.container.data('sortable')) mailster.editor.$.container.sortable('destroy');

		if (mailster.editor.$.modules.length < 2) return;

		mailster.editor.$.container.sortable({
			stop: function (event, ui) {
				event.stopPropagation();
				mailster.editor.$.container.removeClass('dragging');
				setTimeout(function () {
					mailster.trigger('refresh');
					mailster.trigger('save');
				}, 200);
			},
			start: function (event, ui) {
				event.stopPropagation();
				mailster.editor.$.container.addClass('dragging');
			},
			containment: 'body',
			revert: 100,
			axis: 'y',
			placeholder: "sortable-placeholder",
			items: "> module",
			delay: 20,
			distance: 5,
			scroll: true,
			scrollSensitivity: 10,
			forcePlaceholderSize: true,
			helper: 'clone',
			zIndex: 10000

		});
	}

	function draggable(){
		if (mailster.editor.$.images.data('draggable')) mailster.editor.$.images.draggable('destroy');
		if (mailster.editor.$.images.data('droppable')) mailster.editor.$.images.droppable('destroy');

		mailster.editor.$.images
			.draggable({
				helper: "clone",
				scroll: true,
				scrollSensitivity: 10,
				opacity: 0.7,
				zIndex: 1000,
				revert: 'invalid',
				addClasses: false,
				create: function (event, ui) {
					$(event.target).removeClass('ui-draggable-handle');
				},
				start: function () {
					mailster.editor.$.body.addClass('ui-dragging');
				},
				stop: function () {
					mailster.editor.$.body.removeClass('ui-dragging');
					mailster.trigger('refresh');

				}
			})
			.droppable({
				addClasses: false,
				over: function (event, ui) {
					$(event.target).addClass('ui-drag-over');
				},
				out: function (event, ui) {
					$(event.target).removeClass('ui-drag-over');
				},
				drop: function (event, ui) {
					var org = $(ui.draggable[0]),
						target = $(event.target),
						target_id, org_id, crop, copy;

					target.removeClass('ui-drag-over');

					if (!org.is('img') || !target.is('img')) return;

					target_id = target.attr('data-id') ? parseInt(target.attr('data-id'), 10) : null;
					org_id = org.attr('data-id') ? parseInt(org.attr('data-id'), 10) : null;
					crop = org.data('crop');
					copy = org.clone();

					org.addClass('mailster-loading');
					target.addClass('mailster-loading');

					mailster.util.getRealDimensions(org, function (org_w, org_h, org_f) {
						mailster.util.getRealDimensions(target, function (target_w, target_h, target_f) {

							if (event.altKey) {
								org.removeClass('mailster-loading');
								target.removeClass('mailster-loading');
							} else if (target_id) {

								mailster.util.ajax('create_image', {
									id: target_id,
									width: org_w,
									height: org_h,
									crop: org.data('crop'),
								}, function (response) {

									org.removeAttr('src').attr({
										'data-id': target_id,
										'title': target.attr('title'),
										'alt': target.attr('alt'),
										'src': response.image.url,
										'width': Math.round(response.image.width / org_f),
										'height': Math.round(response.image.height / org_f)
									}).data('id', target_id).removeClass('mailster-loading');

								}, function (jqXHR, textStatus, errorThrown) {

									alert(textStatus + ' ' + jqXHR.status + ': ' + errorThrown + '\n\nCheck the JS console for more info!');

								});
							} else {

								org.removeAttr('src').attr({
									'data-id': 0,
									'title': target.attr('title'),
									'alt': target.attr('alt'),
									'src': target.attr('src'),
									'width': Math.round(org_w / org_f),
									'height': Math.round((org_w / (target_w / target_h)) / org_f)
								}).data('id', 0).removeClass('mailster-loading');

							}

							if (org_id) {
								mailster.util.ajax('create_image', {
									id: org_id,
									width: target_w,
									height: target_h,
									crop: target.data('crop'),
								}, function (response) {

									target.removeAttr('src').attr({
										'data-id': org_id,
										'title': org.attr('title'),
										'alt': org.attr('alt'),
										'src': response.image.url,
										'width': Math.round(response.image.width / target_f),
										'height': Math.round(response.image.height / target_f)
									}).data('id', org_id).removeClass('mailster-loading');

									mailster.trigger('refresh');

								}, function (jqXHR, textStatus, errorThrown) {

									alert(textStatus + ' ' + jqXHR.status + ': ' + errorThrown + '\n\nCheck the JS console for more info!');

								});
							} else {

								target.removeAttr('src').attr({
									'data-id': 0,
									'title': copy.attr('title'),
									'alt': copy.attr('alt'),
									'src': copy.attr('src'),
									'width': Math.round(target_w / target_f),
									'height': Math.round((target_w / (org_w / org_h)) / target_f)
								}).data('id', 0).removeClass('mailster-loading');

							}

							if (!org_id && !target_id) mailster.trigger('refresh');

						});
					});

				}
			});
	}

	function buttons(){
		if (mailster.editor.$.buttons) {
			$.each(mailster.editor.$.buttons, function () {

				var $this = $(this),
					name = $this.attr('label'),
					offset = this.getBoundingClientRect(),
					top = offset.top + 0,
					left = offset.right + 0,
					btn, tmpl;

				if ($this.data('has-buttons')) return;

				btn = $('<button class="addbutton mailster-btn mailster-btn-inline" title="' + mailsterL10n.add_button + '"></button>').appendTo($this);

				btn.data('offset', offset).data('name', name);
				btn.data('element', $this);

				$this.data('has-buttons', true);

				if (!(tmpl = $this.data('tmpl'))) {
					if ($this.find('.textbutton').length) {
						tmpl = $this.find('.textbutton').last();
					} else if ($this.find('img').length) {
						tmpl = $this.find('a[editable]').last();
					} else {
						tmpl = $('<a href="" editable label="Button"></a>');
					}
					tmpl = $('<div/>').text(encodeURIComponent(tmpl[0].outerHTML)).html();
				}

				$this.attr('data-tmpl', tmpl).data('tmpl', tmpl);

			});
		}

		$('button.addrepeater, button.removerepeater').remove();

		if (mailster.editor.$.repeatable) {
			$.each(mailster.editor.$.repeatable, function () {
				var $this = $(this),
					module = $this.closest('module'),
					name = $this.attr('label'),
					moduleoffset = module[0].getBoundingClientRect(),
					offset = this.getBoundingClientRect(),
					add_top = offset.top - moduleoffset.top,
					add_left = offset.left,
					del_top = offset.top - moduleoffset.top + 18,
					del_left = offset.left,
					btn;

				if ('TH' == this.nodeName || 'TD' == this.nodeName) {
					add_top = 0;
					add_left = offset.width - 36;
					del_top = 0;
					del_left = offset.width - 18;
				}

				btn = $('<button class="addrepeater mailster-btn mailster-btn-inline" title="' + mailsterL10n.add_repeater + '"></button>').css({
					top: add_top,
					left: add_left
				}).appendTo($this);

				btn.data('offset', offset).data('name', name);
				btn.data('element', $this);

				btn = $('<button class="removerepeater mailster-btn mailster-btn-inline" title="' + mailsterL10n.remove_repeater + '"></button>').css({
					top: del_top,
					left: del_left
				}).appendTo($this);

				btn.data('offset', offset).data('name', name);
				btn.data('element', $this);

			});
		}
	}

	function select(module){
		if (!module.length) {
			return;
		}
		if (mailster.editor.currentmodule) {
			mailster.editor.currentmodule.removeAttr('selected');
		}
		mailster.editor.currentmodule = module;
		mailster.editor.currentmodule.attr('selected', true);
	}

	function upload(){
		$.each(mailster.editor.$.images, function () {

			var _this = $(this),
				dropzone;

			if (_this.data('has-dropzone')) return;

			dropzone = new mOxie.FileDrop({
				drop_zone: this,
			});

			dropzone.ondrop = function (e) {

				if (parent.window.mailster_is_modulde_dragging) return;
				_this.removeClass('ui-drag-over-file ui-drag-over-file-alt');

				var file = dropzone.files.shift(),
					altkey = window.event && event.altKey,
					dimensions = [_this.width(), _this.height()],
					crop = _this.data('crop'),
					position = _this.offset(),
					upload = $('<upload><div class="mailster-upload-info"><div class="mailster-upload-info-bar"></div><div class="mailster-upload-info-text"></div></div></upload>'),
					preview = upload.find('.mailster-upload-info-bar'),
					previewtext = upload.find('.mailster-upload-info-text'),
					preloader = new mOxie.Image(file);

				preloader.onerror = function (e) {

					alert(mailsterL10n.unsupported_format);

				}
				preloader.onload = function (e) {

					upload.insertAfter(_this);
					_this.appendTo(upload);

					file._element = _this;
					file._altKey = altkey;
					file._crop = crop;
					file._upload = upload;
					file._preview = preview;
					file._previewtext = previewtext;
					file._dimensions = [preloader.width, preloader.height, preloader.width / preloader.height];

					preloader.downsize(dimensions[0], dimensions[1]);
					preview.css({
						'background-image': 'url(' + preloader.getAsDataURL() + ')',
						'background-size': dimensions[0] + 'px ' + (crop ? dimensions[1] : dimensions[0] / file._dimensions[2]) + 'px'
					});

					uploader.addFile(file);
				};

				preloader.load(file);

			};
			dropzone.ondragenter = function (e) {
				if (parent.window.mailster_is_modulde_dragging) return;
				_this.addClass('ui-drag-over-file');
				if (window.event && event.altKey) _this.addClass('ui-drag-over-file-alt');
			};
			dropzone.ondragleave = function (e) {
				if (parent.window.mailster_is_modulde_dragging) return;
				_this.removeClass('ui-drag-over-file ui-drag-over-file-alt');
			};
			dropzone.onerror = function (e) {
				if (parent.window.mailster_is_modulde_dragging) return;
				_this.removeClass('ui-drag-over-file ui-drag-over-file-alt');
			};

			dropzone.init();

			_this.data('has-dropzone', true);

		});


		if (!uploader) {

			$('<button id="mailster-editorimage-upload-button" />').hide().appendTo('mailster');
			uploader = new plupload.Uploader(mailsterdata.plupload);

			uploader.bind('Init', function (up) {
				$('.moxie-shim').remove();
			});

			uploader.bind('FilesAdded', function (up, files) {

				var source = files[0].getSource();

				_getRealDimensions(source._element, function (width, height, factor) {

					up.settings.multipart_params.width = width;
					up.settings.multipart_params.height = height;
					up.settings.multipart_params.factor = factor;
					up.settings.multipart_params.crop = source._crop;
					up.settings.multipart_params.altKey = source._altKey;
					up.refresh();
					up.start();
				});

			});

			uploader.bind('BeforeUpload', function (up, file) {});

			uploader.bind('UploadFile', function (up, file) {});

			uploader.bind('UploadProgress', function (up, file) {

				var source = file.getSource();

				source._preview.width(file.percent + '%');
				source._previewtext.html(file.percent + '%');

			});

			uploader.bind('Error', function (up, err) {
				var source = err.file.getSource();

				alert(err.message);

				source._element.insertAfter(source._upload);
				source._upload.remove();
			});

			uploader.bind('FileUploaded', function (up, file, response) {

				var source = file.getSource(),
					delay, height;

				try {
					response = $.parseJSON(response.response);

					source._previewtext.html(mailsterL10n.ready);
					source._element.on('load', function () {
						clearTimeout(delay);
						source._preview.fadeOut(function () {
							source._element.insertAfter(source._upload);
							source._upload.remove();
							_trigger('refresh');
						});
					});

					height = Math.round(source._element.width() / response.image.asp);

					source._element.attr({
						'src': response.image.url,
						'alt': response.name,
						'height': height,
						'data-id': response.image.id || 0
					}).data('id', response.image.id || 0);

					source._preview.height(height);

					delay = setTimeout(function () {
						source._preview.fadeOut(function () {
							source._element.insertAfter(source._upload);
							source._upload.remove();
							_trigger('refresh');
						});
					}, 3000);
				} catch (err) {
					source._preview.addClass('error').find('.mailster-upload-info-text').html(mailsterL10n.error);
					alert(mailsterL10n.error_occurs + "\n" + err.message);
					source._preview.fadeOut(function () {
						source._element.insertAfter(source._upload);
						source._upload.remove();
					});
				}

			});

			uploader.bind('UploadComplete', function (up, files) {});

			uploader.init();

		}
	}

	function inlineEditor(){
		tinymce.init($.extend(mailsterdata.tinymce.args, mailsterdata.tinymce.multi, {
			urlconverter_callback: urlconverter,
			setup: setup
		}));
		tinymce.init($.extend(mailsterdata.tinymce.args, mailsterdata.tinymce.single, {
			urlconverter_callback: urlconverter,
			setup: setup
		}));
	}

	function setup(editor) {

		editor.addButton('mailster_mce_button', {
			title: l10n.tags.title,
			type: 'menubutton',
			icon: 'icon mailster-tags-icon',
			menu: $.map(tags, function (group, id) {
				return {
					text: group.name,
					menu: $.map(group.tags, function (name, tag) {
						return {
							text: name,
							onclick: function () {
								var poststuff = '';
								switch (tag) {
								case 'webversion':
								case 'unsub':
								case 'forward':
								case 'profile':
									poststuff = 'link';
								case 'homepage':
									if (selection = editor.selection.getContent({
											format: "text"
										})) {
										editor.insertContent('<a href="{' + tag + poststuff + '}">' + selection + '</a>');
										break;
									}
								default:
									editor.insertContent('{' + tag + '} ');
								}
							}
						};

					})
				};
			})
		});

		editor.addButton('mailster_remove_element', {
			title: l10n.remove.title,
			icon: 'icon mailster-remove-icon',
			onclick: function () {
				editor.targetElm.remove();
				editor.remove();
				mailster.trigger('save');
			}
		});

		editor
			.on('change', function (event) {
				var _self = this;
				clearTimeout(changetimeout);
				changetimeout = setTimeout(function () {
					var content = event.level.content,
						c = content.match(/rgb\((\d+), ?(\d+), ?(\d+)\)/g);
					if (c) {
						for (var i = c.length - 1; i >= 0; i--) {
							content = content.replace(c[i], _hex(c[i]));
						}
						_self.bodyElement.innerHTML = content;
					}
					mailster.trigger('save');
					change = true;
				}, 100)
			})
			.on('keyup', function (event) {
				$(event.currentTarget).prop('spellcheck', true);
			})
			.on('click', function (event) {
				mailster.trigger('selectModule', $(event.currentTarget).closest('module'));
				event.stopPropagation();
				editor.focus();
			})
			.on('focus', function (event) {
				event.stopPropagation();
				editor.selection.select(editor.getBody(), true);
				if (mailster.editor.$.container.data('uiSortable')) mailster.editor.$.container.sortable('destroy');
			})
			.on('blur', function (event) {
				mailster.trigger('refresh');
			});
	}

	function urlconverter(url, node, on_save, name) {
		if ('_wp_link_placeholder' == url) {
			return url;
		} else if (/^https?:\/\/{.+}/g.test(url)) {
			return url.replace(/^https?:\/\//, '');
		} else if (/^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i.test(url)) {
			return 'mailto:' + url;
		}
		return this.documentBaseURI.toAbsolute(url, mailsterdata.tinymce.remove_script_host);
	}

	return mailster;

}(mailster, jQuery, window, document));
// end Modules

parent.window.mailster = mailster;