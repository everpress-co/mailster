mailster = (function (mailster, $, window, document) {

	"use strict";

	var filterbar = $('.wp-filter'),
		filterlinks = filterbar.find('.filter-links a'),
		searchform = filterbar.find('.search-form'),
		searchfield = filterbar.find('.search-field'),
		searchdelay,
		currentfilter,
		searchquery = searchfield.val();

	filterlinks
		.on('click', function () {
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

	mailster.$.window.on('popstate', function (event) {
		updateState(event);
	});

	function updateState(event) {
		searchfield.val(getQueryStringParameter('search'));
		if (currentfilter = getQueryStringParameter('browse')) {
			setFilter(currentfilter);
		}
		query('updateState');
	}

	function setFilter(filter) {
		currentfilter = filter;
		filterlinks.removeClass('current');
		$(filterlinks.filter('[data-sort="' + currentfilter + '"]')).addClass('current');
		setQueryStringParameter('browse', currentfilter);

	}

	function search() {
		if (searchquery = searchfield.val()) {
			setQueryStringParameter('search', searchquery);
		} else {
			removeQueryStringParameter('search');
		}
		updateState();

	}

	function query(request) {

		$('body').addClass('loading-content');

		mailster.util.ajax('query_templates', {
			search: searchfield.val(),
			browse: getQueryStringParameter('browse')
		}, function (response) {
			$('body').removeClass('loading-content');
			console.log(response);
		}, function (jqXHR, textStatus, errorThrown) {})
		console.log('DO QUERY');
		console.log(request);
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