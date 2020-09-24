mailster = (function (mailster, $, window, document) {

	"use strict";

	var filterbar = $('.wp-filter'),
		templatebrowser = $('.theme-browser'),
		filterlinks = filterbar.find('.filter-links a'),
		searchform = filterbar.find('.search-form'),
		searchfield = filterbar.find('.search-field'),
		searchdelay,
		currentfilter,
		lastsearchquery = '',
		currentpage = 1,
		total = 0,
		currentdisplayed = 0,
		searchquery = searchfield.val(),
		templates = [],
		busy = false;

	filterlinks
		.on('click', function (event) {
			event.preventDefault();
			setFilter($(this).data('sort'));
			return;
			searchfield.val('');
			removeQueryStringParameter('search');
			lastsearchquery = '';
			setQueryStringParameter('browse', $(this).data('sort'));
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

	templatebrowser
		.on('click', '.theme-screenshot', function () {
			console.log('Asdasd');
			overlay.open($(this).closest('.theme'));
		});

	mailster.$.window
		.on('popstate', function (event) {
			//updateState(event);
		});

	mailster.events.push('documentReady', function () {
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
			data = {};

		var open = function (template) {
				currentTemplate = template;
				data = template.data('item');
				overlay.find('.theme-name').html(data.name + '<span class="theme-version">' + data.updated + '</span>');
				overlay.find('.theme-author-name').html(data.author);
				overlay.find('.theme-description').html(data.description);
				overlay.find('.theme-tags').html('<span>Tags:</span> ' + data.tags.join(', '));
				overlay.find('.theme-screenshots img').attr('src', data.image);
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

			init = function () {
				nextbtn.on('click', next);
				prevbtn.on('click', prev);
				closebtn.on('click', close);
			};

		init();

		return {
			open: open,
			close: close,
			next: next,
			prev: prev,
		}

	}();


	function init() {
		searchfield.val(getQueryStringParameter('search'));
		if (currentfilter = getQueryStringParameter('browse')) {} else {
			currentfilter = 'installed';
		}
		setFilter(currentfilter);
	}

	function maybeLoadTemplates() {

		var bottom = mailster.util.top() + mailster.$.window.height();

		if (!busy && bottom > Math.round(document.documentElement.scrollHeight * 0.9) && total > currentdisplayed) {
			currentpage++;
			query();
		}
	}

	function setFilter(filter) {

		currentfilter && $('body').removeClass('browse-' + currentfilter);
		currentfilter = filter;
		filterlinks.removeClass('current');
		$(filterlinks.filter('[data-sort="' + currentfilter + '"]')).addClass('current');
		removeQueryStringParameter('search');
		setQueryStringParameter('browse', currentfilter);
		currentpage = 1;
		templates = [];
		$('body').addClass('browse-' + currentfilter);
		query();
	}

	function search() {
		if (searchquery = searchfield.val()) {
			setQueryStringParameter('search', searchquery);
		} else {
			removeQueryStringParameter('search');
		}
		if (lastsearchquery != searchquery) {
			query();
			lastsearchquery = searchquery;
		}

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
			browse: getQueryStringParameter('browse'),
			author: getQueryStringParameter('author'),
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

	init();

	return mailster;

}(mailster || {}, jQuery, window, document));