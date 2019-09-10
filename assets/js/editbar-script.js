// block Editbar
mailster = (function (mailster, $, window, document) {
	"use strict";

	var e = {};

	var imagepreview = mailster.$.editbar.find('.imagepreview'),
		imagewidth = mailster.$.editbar.find('.imagewidth'),
		imageheight = mailster.$.editbar.find('.imageheight'),
		imagecrop = mailster.$.editbar.find('.imagecrop'),
		factor = mailster.$.editbar.find('.factor'),
		highdpi = mailster.$.editbar.find('.highdpi'),
		original = mailster.$.editbar.find('.original'),
		imagelink = mailster.$.editbar.find('.imagelink'),
		imageurl = mailster.$.editbar.find('.imageurl'),
		orgimageurl = mailster.$.editbar.find('.orgimageurl'),
		imagealt = mailster.$.editbar.find('.imagealt'),
		singlelink = mailster.$.editbar.find('.singlelink'),
		buttonlink = mailster.$.editbar.find('.buttonlink'),
		buttonlabel = mailster.$.editbar.find('.buttonlabel'),
		buttonalt = mailster.$.editbar.find('.buttonalt'),
		buttonnav = mailster.$.editbar.find('.button-nav'),
		buttontabs = mailster.$.editbar.find('ul.buttons'),
		editor = $('#wp-mailster-editor-wrap'),
		postsearch = $('#post-search'),
		imagesearch = $('#image-search'),
		imagesearchtype = $('[name="image-search-type"]'),
		searchstring = '',
		base,
		buttontype,
		current,
		currentimage,
		currenttext,
		currenttag,
		assetstype,
		assetslist,
		itemcount,
		checkForPostsTimeout,
		lastpostsargs,
		searchTimeout,
		checkRSSfeedInterval;

		function draggable(bool) {
			if (mailster.$.editbar.draggable) {
				if (bool !== false) {
					mailster.$.editbar.draggable("enable");
				} else {
					mailster.$.editbar.draggable("disable");
				}
			}
		}

		function disabledrag() {
			draggable(false);
		}

		function enabledrag() {
			draggable(true);
		}


		function openTab(id, trigger) {
			var $this;
			if (typeof id == 'string') {
				$this = base.find('a[href="' + id + '"]');
			} else {
				$this = $(this);
				id = $this.attr('href');
			}

			$this.parent().find('a.nav-tab').removeClass('nav-tab-active');
			$this.addClass('nav-tab-active');
			base.find('.tab').hide();
			base.find(id).show();

			if (id == '#dynamic_embed_options' && trigger !== false) $('#dynamic_embed_options_post_type').trigger('change');
			if (id == '#image_button') buttontype = 'image';
			if (id == '#text_button') buttontype = 'text';

			assetslist = base.find(id).find('.postlist').eq(0);
			return false;
		}


		function replaceImage() {
			loader();
			var f = factor.val(),
				w = current.element.width(),
				h = Math.round(w / 1.6),
				img = $('<img>', {
					'src': 'https://dummy.mailster.co/' + (w * f) + 'x' + (h * f) + '.jpg',
					'alt': current.content,
					'label': current.content,
					'width': w,
					'height': h,
					'border': 0,
					'editable': current.content
				});

			img[0].onload = function () {
				img.attr({
					'width': w,
					'height': h,
				}).removeAttr('style');
				close();
			};
			if (current.element.parent().is('a')) current.element.unwrap();
			if (!current.element.parent().is('td')) current.element.unwrap();
			current.element.replaceWith(img);
			return false;
		}


		function toggleHighDPI() {

			if ($(this).is(':checked')) {
				factor.val(2);
				mailster.$.editbar.addClass('high-dpi');
			} else {
				factor.val(1);
				mailster.$.editbar.removeClass('high-dpi');
			}
		}

		function checkForPosts() {
			clearInterval(checkForPostsTimeout);
			loader();
			checkForPostsTimeout = setTimeout(function () {

				var post_type = mailster.$.editbar.find('#dynamic_embed_options_post_type').val(),
					content = mailster.$.editbar.find('#dynamic_embed_options_content').val(),
					relative = mailster.$.editbar.find('#dynamic_embed_options_relative').val(),
					taxonomies = mailster.$.editbar.find('.dynamic_embed_options_taxonomy_wrap'),
					rss_url = $('#dynamic_rss_url').val(),
					postargs = {},
					extra = [];

				$.each(taxonomies, function (i) {
					var selects = $(this).find('select'),
						values = [];
					$.each(selects, function () {
						var val = parseInt($(this).val(), 10);
						if (val != -1 && $.inArray(val, values) == -1 && !isNaN(val)) values.push(val);
					});
					values = values.join(',');
					if (values) extra[i] = values;
				});
				postargs = {
					id: campaign_id,
					post_type: post_type,
					relative: relative,
					extra: extra,
					modulename: current.name,
					expect: current.elements.expects,
					rss_url: rss_url
				};

				if (JSON.stringify(postargs) === JSON.stringify(lastpostsargs)) {
					loader(false);
					return;
				}

				$('#dynamic_embed_options').find('h4.current-match').html('&hellip;');
				$('#dynamic_embed_options').find('div.current-tag').html('&hellip;');

				if ('rss' == post_type && !rss_url) {
					loader(false);
					return;
				}

				lastpostsargs = postargs;

				mailster.util.ajax('check_for_posts', postargs, function (response) {
					loader(false);
					if (response.success) {
						currenttext = response.pattern;
						$('#dynamic_embed_options').find('h4.current-match').html(response.title);
						$('#dynamic_embed_options').find('div.current-tag').text(response.pattern.title + "\n\n" + response.pattern[content]);
					}
				}, function (jqXHR, textStatus, errorThrown) {

					loader(false);

				});

			}, 500);

		}

	function loader(bool) {
		if (bool === false) {
			$('#editbar-ajax-loading').hide();
			mailster.$.editbar.find('.buttons').find('button').prop('disabled', false);
		} else {
			$('#editbar-ajax-loading').css('display', 'inline');
			mailster.$.editbar.find('.buttons').find('button').prop('disabled', true);
		}
	}

		function dynamicImage(val, w, h, c, o) {
			w = w || imagewidth.val();
			h = h || imageheight.val() || Math.round(w / 1.6);
			c = typeof c == 'undefined' ? imagecrop.prop(':checked') : c;
			o = typeof o == 'undefined' ? original.prop(':checked') : o;
			if (/^\{([a-z0-9-_,;:|~]+)\}$/.test(val)) {
				var f = factor.val();
				val = mailsterdata.ajaxurl + '?action=mailster_image_placeholder&tag=' + val.replace('{', '').replace('}', '') + '&w=' + Math.abs(w) + '&h=' + Math.abs(h) + '&c=' + (c ? 1 : 0) + '&o=' + (o ? 1 : 0) + '&f=' + f;
			}
			return val;
		}

		function isDynamicImage(val) {
			if (-1 !== val.indexOf('?action=mailster_image_placeholder&tag=')) {
				var m = val.match(/&tag=([a-z0-9-_,;:|~]+)&/);
				return '{' + m[1] + '}';
			}
			return false;
		}

		function change(e) {
			if ((e.keyCode || e.which) != 27 && current)
				current.element.html($(this).val());
		}

		function loadPosts(event, callback) {

			var posttypes = $('#post_type_select').find('input:checked').serialize(),
				data = {
					type: assetstype,
					posttypes: posttypes,
					search: searchstring,
					imagetype: imagesearchtype.filter(':checked').val(),
					offset: 0
				};

			if (assetstype == 'attachment') {
				data.id = currentimage.id;
			}

			assetslist.empty();
			loader();

			mailster.util.ajax('get_post_list', data, function (response) {
				loader(false);
				if (response.success) {
					itemcount = response.itemcount;
					displayPosts(response.html, true);
					callback && callback();
				}
			}, function (jqXHR, textStatus, errorThrown) {

				loader(false);

			});
		}

		function loadMorePosts() {
			var $this = $(this),
				offset = $this.data('offset'),
				type = $this.data('type');

			loader();

			var posttypes = $('#post_type_select').find('input:checked').serialize();

			mailster.util.ajax('get_post_list', {
				type: type,
				posttypes: posttypes,
				search: searchstring,
				imagetype: imagesearchtype.filter(':checked').val(),
				offset: offset,
				itemcount: itemcount
			}, function (response) {
				loader(false);
				if (response.success) {
					itemcount = response.itemcount;
					$this.remove();
					displayPosts(response.html, false);
				}
			}, function (jqXHR, textStatus, errorThrown) {

				loader(false);

			});
			return false;
		}

		function searchPost() {
			var $this = $(this),
				temp = $.trim('attachment' == assetstype ? imagesearch.val() : postsearch.val());
			if ((!$this.is(':checked') && searchstring == temp)) {
				return false;
			}
			searchstring = temp;
			clearTimeout(searchTimeout);
			searchTimeout = setTimeout(function () {
				loadPosts();
			}, 500);
		}

		function loadSingleLink() {
			$('#single-link').slideDown(200);
			singlelink.focus().select();
			assetstype = 'link';
			assetslist = base.find('.postlist').eq(0);
			loadPosts();
			return false;

		}

		function displayPosts(html, replace, list) {
			if (!list) list = assetslist;
			if (replace) list.empty();
			if (!list.html()) list.html('<ul></ul>');

			list.find('ul').append(html);
		}

		function openURL() {
			$('.imageurl-popup').toggle();
			if (!imageurl.val() && currentimage.src.indexOf(location.origin) == -1 && currentimage.src.indexOf('dummy.mailster.co') == -1) {
				imageurl.val(currentimage.src);
			}
			imageurl.focus().select();
			return false;
		}

		function openMedia() {

			if (!wp.media.frames.mailster_editbar) {

				wp.media.frames.mailster_editbar = wp.media({
					title: mailsterL10n.select_image,
					library: {
						type: 'image'
					},
					multiple: false
				});

				wp.media.frames.mailster_editbar.on('select', function () {
					var attachment = wp.media.frames.mailster_editbar.state().get('selection').first().toJSON(),
						el = $('img').data({
							id: attachment.id,
							name: attachment.name,
							src: attachment.url
						});
					loadPosts(null, function () {
						choosePic(null, el);
					});
				});
			}

			wp.media.frames.mailster_editbar.open();

		}

		function mceUpdater(editor) {
			clearTimeout(timeout);
			timeout = setTimeout(function () {
				if (!editor) return;
				var val = $.trim(editor.save());
				current.element.html(val);
			}, 100);
		}

		function close() {

			bar.removeClass('current-' + current.type).hide();
			loader(false);
			$('#single-link').hide();
			_trigger('refresh');
			_trigger('save');
			return false;

		}
		function remove() {
			if (current.element.parent().is('a')) current.element.unwrap();
			if ('btn' == current.type) {
				var wrap = current.element.closest('.textbutton'),
					parent = wrap.parent();
				if (!wrap.length) {
					wrap = current.element;
				}
				if (parent.is('buttons') && !parent.find('.textbutton').length) {
					parent.remove();
				} else {
					wrap.remove();
				}
			} else if ('img' == current.type && 'img' != current.tag) {
				current.element.attr('background', '');
			} else {
				current.element.remove();
			}
			close();
			return false;
		}

		function cancel() {
			switch (current.type) {
			case 'img':
			case 'btn':
				if (current.element.is('[tmpbutton]')) {
					current.element.remove();
				}
				break;
			default:
				current.element.html(current.content);
				//remove id to re trigger tinymce
				current.element.find('single, multi').removeAttr('id');
			}
			close();
			return false;
		}

		function changeBtn() {
			var _this = $(this),
				link = _this.data('link');
			base.find('.btnsrc').removeClass('active');
			_this.addClass('active');

			buttonalt.val(_this.attr('title'));

			if (link) {
				var pos;
				buttonlink.val(link);
				if ((pos = (link + '').indexOf('USERNAME', 0)) != -1) {
					buttonlink.focus();
					selectRange(buttonlink[0], pos, pos + 8);
				};

			}
			return false;
		}

		function toggleImgZoom() {
			$(this).toggleClass('zoom');
		}

		function choosePic(event, el) {
			var _this = el || $(this),
				id = _this.data('id'),
				name = _this.data('name'),
				src = _this.data('src');

			if (!id) return;

			currentimage = {
				id: id,
				name: name,
				src: src
			};
			loader();

			base.find('li.selected').removeClass('selected');
			_this.addClass('selected');

			if (current.element.data('id') == id) {
				imagealt.val(current.element.attr('alt'));
			} else if (current.element.attr('alt') != name) {
				imagealt.val(name);
			}
			imageurl.val('');
			imagepreview.attr('src', '').on('load', function () {

				imagepreview.off('load');

				current.width = imagepreview.width();
				current.height = imagepreview.height();
				current.asp = _this.data('asp') || (current.width / current.height);

				currentimage.asp = current.asp;
				loader(false);

				if (!imagecrop.is(':checked')) imageheight.val(Math.round(imagewidth.val() / current.asp));

				adjustImagePreview();

			}).attr('src', src);

			return currentimage;
		}

		function adjustImagePreview() {
			var x = Math.round(.5 * (current.height - (current.width * (imageheight.val() / imagewidth.val())))) || 0,
				f = parseInt(factor.val(), 10);

			imagepreview.css({
				'clip': 'rect(' + (x) + 'px,' + (current.width * f) + 'px,' + (current.height * f - x) + 'px,0px)',
				'margin-top': (-1 * x) + 'px'
			});
		}

		function choosePost() {
			var _this = $(this),
				id = _this.data('id'),
				name = _this.data('name'),
				link = _this.data('link'),
				thumbid = _this.data('thumbid');

			if (current.type == 'btn') {

				buttonlink.val(link);
				buttonalt.val(name);
				base.find('li.selected').removeClass('selected');
				_this.addClass('selected')

			} else if (current.type == 'single') {

				singlelink.val(link);
				base.find('li.selected').removeClass('selected');
				_this.addClass('selected')

			} else {

				loader();
				_ajax('get_post', {
					id: id,
					expect: current.elements.expects
				}, function (response) {
					loader(false);
					base.find('li.selected').removeClass('selected');
					_this.addClass('selected')
					if (response.success) {
						currenttext = response.pattern;
						base.find('.editbarinfo').html(mailsterL10n.curr_selected + ': <span>' + currenttext.title + '</span>');
					}
				}, function (jqXHR, textStatus, errorThrown) {

					loader(false);
					base.find('li.selected').removeClass('selected');

				});

			}
			return false;
		}
	e.open = function (data) {

		current = data;
		var el = data.element,
			module = el.closest('module'),
			top = (type != 'img') ? data.offset.top : 0,
			name = data.name || '',
			type = data.type,
			content = $.trim(el.html()),
			condition = el.find('if'),
			conditions,
			position = current.element.data('position') || 0,
			carea, cwrap, offset,
			fac = 1;

		base = mailster.$.editbar.find('div.type.' + type);

		mailster.$.editbar.addClass('current-' + type);

		current.width = el.width();
		current.height = el.height();
		current.asp = current.width / current.height;
		current.crop = el.data('crop') ? el.data('crop') : false;
		current.tag = el.prop('tagName').toLowerCase();
		current.is_percentage = el.attr('width') && el.attr('width').indexOf('%') !== -1;
		current.content = content;

		currenttag = current.element.data('tag');
		searchstring = '';

		mailster.trigger('selectModule', module);

		if (type == 'img') {

			original.prop('checked', current.original);
			imagecrop.prop('checked', current.crop).parent()[current.crop ? 'addClass' : 'removeClass']('not-cropped');
			searchstring = $.trim(imagesearch.val());

			factor.val(1);
			mailster.util.getRealDimensions(
				el,
				function (w, h, f) {
					var h = f >= 1.5;
					factor.val(f);
					highdpi.prop('checked', h);

					(h) ?  mailster.$.editbar.addClass('high-dpi'):  mailster.$.editbar.removeClass('high-dpi');

					fac = f;
				}
			);

		} else if (type == 'btn') {

			if (el.find('img').length) {

				$('#button-type- mailster.$.editbar').find('a').eq(1).trigger('click');
				var btnsrc = el.find('img').attr('src');

				if (buttonnav.length) {

					var button = bar.find("img[src='" + btnsrc + "']");

					if (button.length) {
						bar.find('ul.buttons').hide();
						var b = button.parent().parent().parent();
						bar.find('a[href="#' + b.attr('id').substr(4) + '"]').trigger('click');
					} else {
						$.each(
							bar.find('.button-nav'),
							function () {
								$(this).find('.nav-tab').eq(0).trigger('click');
							}
						);
					}

				}

				buttonlabel.val(el.find('img').attr('alt'));
				mailster.util.getRealDimensions(
					el.find('img'),
					function (w, h, f) {
						var h = f >= 1.5;
						factor.val(f);
						highdpi.prop('checked', h);
						(h) ? bar.addClass('high-dpi'): bar.removeClass('high-dpi');

						fac = f;
					}
				);

			} else {

				$('#button-type-bar').find('a').eq(0).trigger('click');
				buttonlabel.val($.trim(el.text())).focus().select();
				buttonlink.val(current.element.attr('href'));
				bar.find('ul.buttons').hide();
			}

		} else if (type == 'auto') {

			openTab('#' + (currenttag ? 'dynamic' : 'static') + '_embed_options', true);
			searchstring = $.trim(postsearch.val());

			if (currenttag) {

				var parts = currenttag.substr(1, currenttag.length - 2).split(':'),
					extra = parts[1].split(';'),
					relative = extra.shift(),
					terms = extra.length ? extra : null;

				currenttag = {
					'post_type': parts[0],
					'relative': relative,
					'terms': terms
				};

				$('#dynamic_embed_options_post_type').val(currenttag.post_type).trigger('change');
				$('#dynamic_embed_options_relative').val(currenttag.relative).trigger('change');

			} else {

			}

		} else if (type == 'codeview') {

			var textarea = base.find('textarea'),
				clone = el.clone();

			current.modulebuttons = clone.find('modulebuttons');

			clone.find('modulebuttons').remove();
			clone.find('single, multi')
				.removeAttr('contenteditable spellcheck id dir style class');

			var html = $.trim(clone.html().replace(/\u200c/g, '&zwnj;').replace(/\u200d/g, '&zwj;'));
			textarea.show().html(html);

		}

		offset = mailster.$.container.offset().top + (current.offset.top - (mailster.$.window.height() / 2) + (current.height / 2));

		offset = Math.max(mailster.$.container.offset().top - 200, offset);

		//_scroll(offset, function () {

				mailster.$.editbar.find('h4.editbar-title').html(name);
				mailster.$.editbar.find('div.type').hide();

				mailster.$.editbar.find('div.' + type).show();

				if (module.data('rss')) {
					$('#dynamic_rss_url').val(module.data('rss'));
				}

				// center the bar
				//var baroffset = _doc.scrollTop() + (mailster.$.window.height() / 2) - mailster.$.container.offset().top - (mailster.$.editbar.height() / 2);
				var baroffset = 20;

				mailster.$.editbar.css({
					top: baroffset
				});

				loader();

				if (type == 'single') {

					if (conditions) {

						$.each(
							conditions,
							function (i, condition) {
								var _b = base.find('.conditinal-area').eq(i);
								_b.find('select.condition-fields').val(condition.field);
								_b.find('select.condition-operators').val(condition.operator);
								_b.find('input.condition-value').val(condition.value);
								_b.find('input.input').val(condition.html)
							}
						);

					} else {

						var val = content.replace(/&amp;/g, '&');

						singlelink.val('');

						if (current.element.parent().is('a')) {
							var href = current.element.parent().attr('href');
							singlelink.val(href != '#' ? href : '');
							loadSingleLink();

						} else if (current.element.find('a').length) {
							var link = current.element.find('a');
							if (val == link.text()) {
								var href = link.attr('href');
								val = link.text();
								singlelink.val(href != '#' ? href : '');
							}
						}

						base.find('input').eq(0).val($.trim(val));

					}

				} else if (type == 'img') {

					var maxwidth = parseInt(el[0].style.maxWidth, 10) || el.parent().width() || el.width() || null;
					var maxheight = parseInt(el[0].style.maxHeight, 10) || el.parent().height() || el.height() || null;
					var src = el.attr('src') || el.attr('background');
					var url = isDynamicImage(src) || '';

					if (el.parent().is('a')) {
						imagelink.val(el.parent().attr('href').replace('%7B', '{').replace('%7D', '}'));
					} else {
						imagelink.val('');
					}

					imagealt.val(el.attr('alt'));
					imageurl.val(url);
					orgimageurl.val(src);

					el.data('id', el.attr('data-id'));

					$('.imageurl-popup').toggle(!!url);
					imagepreview
						.removeAttr('src')
						.attr('src', src);
					assetstype = 'attachment';
					assetslist = base.find('.imagelist');
					currentimage = {
						id: el.data('id'),
						src: src,
						width: el.width() * fac,
						height: el.height() * fac
					}
					currentimage.asp = currentimage.width / currentimage.height;
					loadPosts();
					adjustImagePreview();

				} else if (type == 'btn') {

					buttonalt.val(el.find('img').attr('alt'));
					if (el.attr('href')) {
						buttonlink.val(el.attr('href').replace('%7B', '{').replace('%7D', '}'));
					}

					assetstype = 'link';
					assetslist = base.find('.postlist').eq(0);
					loadPosts();

					$.each(
						base.find('.buttons img'),
						function () {
							var _this = $(this);
							_this.css('background-color', el.css('background-color'));
							(_this.attr('src') == btnsrc) ? _this.parent().addClass('active'): _this.parent().removeClass('active');

						}
					);

				} else if (type == 'auto') {

					assetstype = 'post';
					assetslist = base.find('.postlist').eq(0);
					loadPosts();
					current.elements = {
						single: current.element.find('single'),
						multi: current.element.find('multi'),
						buttons: current.element.find('a[editable]'),
						images: current.element.find('img[editable], td[background], th[background]'),
						expects: current.element.find('[expect]').map(
							function () {
								return $(this).attr('expect');
							}
						).toArray()
					}

					if ((current.elements.multi.length || current.elements.single.length || current.elements.images.length) > 1) {
						mailster.$.editbar.find('.editbarpostion').html(sprintf(mailsterL10n.for_area, '#' + (position + 1))).show();
					} else {
						mailster.$.editbar.find('.editbarpostion').hide();
					}

				} else if (type == 'codeview') {

					if (codemirror) {
						codemirror.clearHistory();
						codemirror.setValue('');
						base.find('.CodeMirror').remove();
					}

				}

				mailster.$.editbar.show(
					0,
					function () {

						if (type == 'single') {

							mailster.$.editbar.find('input').focus().select();

						} else if (type == 'img') {

							imagewidth.val(current.width);
							imageheight.val(current.height);
							imagecrop.prop('checked', current.crop);

						} else if (type == 'btn') {

							imagewidth.val(maxwidth);
							imageheight.val(maxheight);

						} else if (type == 'multi') {

							$('#mailster-editor').val(content);

							if (isTinyMCE && tinymce.get('mailster-editor')) {
								tinymce.get('mailster-editor').setContent(content);
								tinymce.execCommand('mceFocus', false, 'mailster-editor');
							}

						} else if (type == 'codeview') {

							codemirror = CodeMirror.fromTextArea(textarea.get(0), codemirrorargs);

						}

					}
				);

				loader(false);

		// 	},
		// 	100
		// );

	}


	mailster.events.refresh.push(function(){

		//mailster.$.container.find('.content.mailster-btn').remove();

		var cont = mailster.$.iframe.contents().find('html'),
			modulehelper = null;

		if (!cont) return;

		cont
			.off('.mailster')
			.on('click.mailster', 'img[editable]', function (event) {
				event.stopPropagation();
				var $this = $(this),
					offset = $this.offset(),
					top = offset.top + 61,
					left = offset.left,
					name = $this.attr('label'),
					type = 'img';

				mailster.editbar.open({
					'offset': offset,
					'type': type,
					'name': name,
					'element': $this
				});

			})
			.on('click.mailster', 'module td[background],module th[background]', function (event) {
				event.stopPropagation();
				modulehelper = true;
			})
			.on('click.mailster', 'td[background], th[background]', function (event) {
				event.stopPropagation();
				if (!modulehelper && event.target != this) return;
				modulehelper = null;

				var $this = $(this),
					offset = $this.offset(),
					top = offset.top + 61,
					left = offset.left,
					name = $this.attr('label'),
					type = 'img';

				mailster.editbar.open({
					'offset': offset,
					'type': type,
					'name': name,
					'element': $this
				});

			})
			.on('click.mailster', 'a[editable]', function (event) {
				event.stopPropagation();
				event.preventDefault();
				var $this = $(this),
					offset = $this.offset(),
					top = offset.top + 40,
					left = offset.left,
					name = $this.attr('label'),
					type = 'btn';

				mailster.editbar.open({
					'offset': offset,
					'type': type,
					'name': name,
					'element': $this
				});


			})
	})

	mailster.$.editbar
		.on('click', 'a', false)
		.on('click', 'a.template', o.showFiles)

	mailster.editbar = e;

	return mailster;

}(mailster, jQuery, window, document));
// end Editbar