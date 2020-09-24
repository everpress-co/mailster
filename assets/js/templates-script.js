mailster = (function (mailster, $, window, document) {

	"use strict";

	var filterbar = $('.wp-filter'),
		templatebrowser = $('.template-browser'),
		filterlinks = filterbar.find('.filter-links a'),
		searchform = filterbar.find('.search-form'),
		searchfield = filterbar.find('.search-field'),
		searchdelay,
		currentfilter,
		lastsearchquery = '',
		currentpage = 1,
		searchquery = searchfield.val(),
		templateInfo,
		templates = [],
		busy = false;

	filterlinks
		.on('click', function () {
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
	.on('click', '.template-screenshot', function(){
		templateInfo.open($(this).closest('.template'));
	});

	mailster.$.window
		.on('popstate', function (event) {
			updateState(event);
		});

	mailster.events.push('documentReady', function () {
		mailster.$.window.on('scroll.mailster', mailster.util.throttle(maybeLoadTemplates, 500))
	});


	templateInfo = {

		overlay: $('.template-overlay'),
		data:{},

		open: function(template){
			data = template.data('item');

			console.log(data);
			overlay.find('.template-name').html(data.name+ '<span class="template-version">'+data.updated+'</span>');
			overlay.find('.template-author-name').html(data.author);
			overlay.find('.template-description').html(data.description);
			overlay.find('.template-tags').html('<span>Tags:</span> '+data.author);
			overlay.find('.template-screenshots img').attr('src', data.image);
			overlay.show();
		}
	};


	function updateState(event) {
		searchfield.val(getQueryStringParameter('search'));
		if (currentfilter = getQueryStringParameter('browse')) {
			setFilter(currentfilter);
		}
		query();
	}

	function maybeLoadTemplates() {

		var bottom = mailster.util.top() + mailster.$.window.height();

		console.log(bottom, Math.round(document.documentElement.scrollHeight * 1));

		if (!busy && bottom > Math.round(document.documentElement.scrollHeight * 0.9)) {
			currentpage++;
			query();
			console.log('load');
		}
	}

	function setFilter(filter) {
		currentfilter = filter;
		filterlinks.removeClass('current');
		$(filterlinks.filter('[data-sort="' + currentfilter + '"]')).addClass('current');
		setQueryStringParameter('browse', currentfilter);
		currentpage = 1;
		templates = [];
	}

	function search() {
		if (searchquery = searchfield.val()) {
			setQueryStringParameter('search', searchquery);
		} else {
			removeQueryStringParameter('search');
		}
		if (lastsearchquery != searchquery) {
			updateState();
		}
		lastsearchquery = searchquery;

	}

	function query() {

		if (currentpage == 1) {
			$('body').addClass('loading-content');
			$('.template-browser').html('');
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
				$('.template-count').html(response.total);
			}
			templates.concat(response.templates);
			console.log(templates);
			console.log(response);
			$('.template-browser').append(response.html);
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

	updateState();

	return mailster;

}(mailster || {}, jQuery, window, document));