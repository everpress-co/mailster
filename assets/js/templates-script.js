mailster = (function (mailster, $, window, document) {

	"use strict";

	var filterbar = $('.wp-filter'),
		templatebrowser = $('.theme-browser'),
		filterlinks = filterbar.find('.filter-links a'),
		searchform = filterbar.find('.search-form'),
		searchfield = filterbar.find('.wp-filter-search'),
		typeselector = filterbar.find('#typeselector'),
		searchdelay,
		currentfilter,
		lastsearchquery = '',
		lastsearchtype = '',
		currentpage = 1,
		total = 0,
		currentdisplayed = 0,
		searchquery = searchfield.val(),
		searchtype = typeselector.val(),
		templates = [],
		busy = false;

	filterlinks
		.on('click', function (event) {
			event.preventDefault();
			setFilter($(this).data('sort'));
			return;
		});

	searchform.on('submit', function (event) {
		event.preventDefault();
		searchdelay && clearTimeout(searchdelay);
		search();
	})

	searchfield.on('keyup change', function (event) {
		if (13 == event.keyCode) {
			return;
		}
		searchdelay && clearTimeout(searchdelay);
		searchdelay = setTimeout(search, 1000);
	});
	typeselector.on('change', function (event) {
		searchdelay && clearTimeout(searchdelay);
		search();
	});

	templatebrowser
		.on('click', '.theme-screenshot', function () {
			overlay.open($(this).closest('.theme'));
		})
		.on('click', '.download', function (event) {
			event.preventDefault();
			$(this).addClass('updating-message');
			downloadTemplateFromUrl(this.href, $(this).closest('.theme').data('slug'));
		})
		.on('click', '.popup', function () {
			var href = this.href;

			if (!/^https?/.test(href)) return true;

			var dimensions = $(this).data(),
				dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left,
				dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top,
				width = mailster.$.window.width(),
				height = mailster.$.window.height(),
				left, top, newWindow;

			if (/%/.test(dimensions.width)) {
				dimensions.width = width * (parseInt(dimensions.width, 10) / 100);
			}

			if (/%/.test(dimensions.height)) {
				dimensions.height = height * (parseInt(dimensions.height, 10) / 100);
			}

			left = ((width / 2) - (dimensions.width / 2)) + dualScreenLeft;
			top = ((height / 2) - (dimensions.height / 2)) + dualScreenTop;
			newWindow = window.open(href, 'mailster_themebrowser', 'scrollbars=auto,resizable=1,menubar=0,toolbar=0,location=0,directories=0,status=0, width=' + dimensions.width + ', height=' + dimensions.height + ', top=' + top + ', left=' + left);

			if (window.focus)
				newWindow.focus();

			return false;

		});

	$('.upload-template').on('click', function () {
		$('.upload-field').toggle();
	})

	mailster.$.window
		.on('popstate', function (event) {
			//updateState(event);
		})
		.on('click', '.upload-template', function () {
			$('.upload-field').show();
		});

	mailster.events.push('documentReady', function () {
		uploader_init();
		mailster.$.window.on('scroll.mailster', mailster.util.throttle(maybeLoadTemplates, 500))
	});

	var overlay = function () {

		if (this === window) return new overlay();

		var overlay = $('.theme-overlay'),
			currentTemplate = null,
			prevTemplate = null,
			nextTemplate = null,
			current = null,
			nextbtn = overlay.find('.right'),
			prevbtn = overlay.find('.left'),
			closebtn = overlay.find('.close'),
			deletebtn = overlay.find('.delete-theme'),
			data = {};

		var open = function (template) {
				currentTemplate = template;
				data = template.data('item');
				console.log(data);
				overlay.find('.theme-name').html(data.name + '<span class="theme-version">' + data.updated + '</span>');
				overlay.find('.theme-author-name').html(data.author);
				overlay.find('.theme-description').html(data.description);
				overlay.find('.theme-tags').html(data.tags ? '<span>Tags:</span> ' + data.tags.join(', ') : '');
				overlay.find('.theme-screenshots img').attr('src', data.image_full).attr('srcset', data.image_full + ' 1x, ' + data.image_fullx2 + ' 2x');
				overlay.find('.theme-screenshots iframe').attr('src', data.index);
				prevTemplate = currentTemplate.prev();
				nextTemplate = currentTemplate.next();
				prevbtn.prop('disabled', !prevTemplate.length)[!prevTemplate.length ? 'addClass' : 'removeClass']('disabled');
				nextbtn.prop('disabled', !nextTemplate.length)[!nextTemplate.length ? 'addClass' : 'removeClass']('disabled');
				overlay.show();
			},

			close = function () {
				overlay.hide();
			},

			next = function () {
				open(currentTemplate.next());
			},

			prev = function () {
				open(currentTemplate.prev());
			},

			remove = function () {
				if (confirm(mailster.util.sprintf(mailster.l10n.templates.confirm_delete, '"' + data.name + '"'))) {
					deleteTemplate(data.slug);
					close();
				}
				return false;
			},

			init = function () {
				nextbtn.on('click', next);
				prevbtn.on('click', prev);
				closebtn.on('click', close);
				deletebtn.on('click', remove);
			};

		init();

		return {
			open: open,
			close: close,
			next: next,
			prev: prev,
			delete: remove,
		}

	}();


	function init() {
		searchfield.val(getQueryStringParameter('search'));
		typeselector.val(getQueryStringParameter('type') || 'term');
		currentfilter = getQueryStringParameter('browse') || 'installed';
		if (getQueryStringParameter('search')) {
			search();
		} else {
			setFilter(currentfilter);

		}
	}

	function maybeLoadTemplates() {

		var bottom = mailster.util.top() + mailster.$.window.height();

		if (!busy && bottom > Math.round(document.documentElement.scrollHeight * 0.9) && total > currentdisplayed) {
			currentpage++;
			query();
		}
	}

	function setFilter(filter) {
		currentfilter && $('body').removeClass('browse-' + currentfilter) && filterlinks.filter('[data-sort="' + currentfilter + '"]').removeClass('current');
		currentfilter = filter || false;
		if (currentfilter) {
			resetSearch();
			filterlinks.filter('[data-sort="' + currentfilter + '"]').addClass('current');
			setQueryStringParameter('browse', currentfilter);
			$('body').addClass('browse-' + currentfilter);
			query();
		}
	}

	function resetFilter() {
		currentfilter && $('body').removeClass('browse-' + currentfilter) && filterlinks.filter('[data-sort="' + currentfilter + '"]').removeClass('current');
		removeQueryStringParameter('browse');
		currentfilter = false;
		currentpage = 1;
		templates = [];
	}

	function resetSearch() {
		removeQueryStringParameter('search');
		removeQueryStringParameter('type');
		lastsearchtype = '';
		lastsearchquery = '';
		searchfield.val('');
		typeselector.val('term');
		currentpage = 1;
		templates = [];
	}

	function search() {
		searchquery = $.trim(searchfield.val());
		// Escape the term string for RegExp meta characters.
		searchquery = searchquery.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');

		searchtype = typeselector.val();
		if (lastsearchquery != searchquery || lastsearchtype != searchtype) {
			lastsearchtype = searchtype;
			lastsearchquery = searchquery;
			resetFilter();
			query();
			setQueryStringParameter('search', searchquery);
			setQueryStringParameter('type', searchtype);
		}
		return;


	}

	function downloadTemplate(slug) {
		var template = $('[data-slug="' + slug + '"]');

		busy = true;


		template.addClass('loading');

		mailster.util.ajax('download_template', {
			slug: slug,
		}, function (response) {

			// template.animate({width:0, 'margin-right':0}, function(){
			// 	template.remove();
			// 	busy = false;
			// });

		}, function (jqXHR, textStatus, errorThrown) {})

	}

	function downloadTemplateFromUrl(url, slug) {

		var template = $('[data-slug="' + slug + '"]');

		busy = true;

		template.find('.request-download').addClass('updating-message');

		mailster.util.ajax('download_template', {
			url: url,
			slug: slug,
		}, function (response) {


			if (response.redirect) {
				//document.location = response.redirect;
			}
			template.find('.updating-message').removeClass('updating-message');
			setFilter('installed');

			busy = false;

		}, function (jqXHR, textStatus, errorThrown) {})

	}

	function deleteTemplate(slug) {

		var template = $('[data-slug="' + slug + '"]');

		busy = true;

		template.addClass('loading');

		mailster.util.ajax('delete_template', {
			slug: slug,
		}, function (response) {

			template.animate({
				width: 0,
				'margin-right': 0
			}, function () {
				template.remove();
				busy = false;
			});

		}, function (jqXHR, textStatus, errorThrown) {})

	}

	function query() {

		if (currentpage == 1) {
			$('body').removeClass('no-results');
			$('body').addClass('loading-content');
			$('.theme-browser').html('');
			templates = [];
		}
		busy = true;

		mailster.util.ajax('query_templates', {
			search: searchfield.val(),
			type: typeselector.val(),
			browse: getQueryStringParameter('browse'),
			page: currentpage,
		}, function (response) {

			if (currentpage == 1) {
				$('body').removeClass('loading-content');
				$('.theme-count').html(response.total);
				total = response.total;
			}
			templates.concat(response.templates);
			$('.theme-browser').append(response.html);
			currentdisplayed = $('.theme').length;

			!currentdisplayed && $('body').addClass('no-results');

			busy = false;
		}, function (jqXHR, textStatus, errorThrown) {})
	}


	function getQueryStringParameter(name) {
		var params = new URLSearchParams(window.location.search);
		return params.get(name);
	}

	function setQueryStringParameter(name, value) {
		var params = new URLSearchParams(window.location.search);
		params.set(name, value);
		window.history.pushState({}, "", decodeURIComponent(window.location.pathname + '?' + params));
	}

	function removeQueryStringParameter(name) {
		var params = new URLSearchParams(window.location.search);
		params.delete(name)
		window.history.pushState({}, "", decodeURIComponent(window.location.pathname + '?' + params));
	}

	function uploader_init() {

		var uploader = new plupload.Uploader(wpUploaderInit),
			uploadinfo = $('.uploadinfo');

		uploader.bind('Init', function (up) {
			var uploaddiv = $('#plupload-upload-ui');

			if (up.features.dragdrop && !mailster.util.isTouchDevice) {
				uploaddiv.addClass('drag-drop');
				$('#drag-drop-area').bind('dragover.wp-uploader', function () { // dragenter doesn't fire right :(
					uploaddiv.addClass('drag-over');
				}).bind('dragleave.wp-uploader, drop.wp-uploader', function () {
					uploaddiv.removeClass('drag-over');
				});
			} else {
				uploaddiv.removeClass('drag-drop');
				$('#drag-drop-area').unbind('.wp-uploader');
			}

			if (up.runtime == 'html4')
				$('.upload-flash-bypass').hide();

		});

		uploader.init();

		uploader.bind('FilesAdded', function (up, files) {

			setTimeout(function () {
				up.refresh();
				up.start();
			}, 1);

		});

		uploader.bind('BeforeUpload', function (up, file) {});

		uploader.bind('UploadFile', function (up, file) {});

		uploader.bind('UploadProgress', function (up, file) {
			uploadinfo.html(mailster.util.sprintf(mailster.l10n.templates.uploading, file.percent + '%'));
		});

		uploader.bind('Error', function (up, err) {
			uploadinfo.html(err.message);
			up.refresh();
		});

		uploader.bind('FileUploaded', function (up, file, response) {
			response = $.parseJSON(response.response);
			if (response.success) {
				location.reload();
			} else {
				uploadinfo.html(response.error);
			}
		});

		uploader.bind('UploadComplete', function (up, files) {});
	}

	init();
	mailster.templates = mailster.templates || {};
	mailster.templates.download = downloadTemplate;
	mailster.templates.downloadFromUrl = downloadTemplateFromUrl;
	mailster.templates.delete = deleteTemplate;

	return mailster;

}(mailster || {}, jQuery, window, document));