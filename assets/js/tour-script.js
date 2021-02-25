// tour
mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.tours = {};

	var tourdefaults = {
		debug: true,
		axis: 'x', // use only one axis prevent flickering on iOS devices
		overlayOpacity: 0.8,
		expose: true,
		exposeOffset: 10,
		offset: 10,
		autostart: true,
		startAt: 0,
		strings: {
			'faster': 'faster',
		},
		speed: 1,
		autostart: false,
		autoplay: true,
		pauseOnHover: true,
		keyboardNav: true,
		showProgress: true,
		showControls: false,
		scrollBack: false,
		scrollDuration: 300,
		easing: 'swing',
		onStart: function () {},
		onStop: function () {},
		onPause: function () {},
		onPlay: function () {},
		onChange: function () {},
		onFinish: function () {},

		//these are the defaults for each step which can get overwritten in each step
		position: 'c',
		location: null,
		live: 'auto',
		offset: 0,
		wait: 0,
		expose: false,
		exposeOffset: 0,
		overlayOpacity: 0.4,
		delayIn: 200,
		delayOut: 100,
		animationIn: 'fadeIn',
		animationOut: 'fadeOut',
		buttons: null,
		//speed:0.1,
		onStop: function () {
			//this.skip(10);
			console.log('Tour Stop callback', this, this.id);
			mark_as_seen(this.id);
		},
		onChange: function (current) {},
		onFinish: function (current) {
			console.log('Tour Finished callback');
			mark_as_seen(this.id);
		},
		onStart: function () {
			//console.log('Tour Start callback');
		}

	}

	window.mailsterTour && mailster.events.push('documentReady', function () {
		loadTours().then(function () {
			get_by_index(0).start();
		});
	});

	function loadTour(tour_id) {
		return new Promise(function (resolve, reject) {
			$.getScript(window.mailsterTour.endpoint + tour_id + '.js', function (r) {
				var tourdata = window.jtourdata.tourdata;
				var options = $.extend(tourdefaults, window.jtourdata.options || {});
				options.id = tour_id;
				options.autostart = false;
				delete window.jtourdata;
				mailster.tours[tour_id] = new jTour(tourdata, options);
				resolve();
			})
		})
	}

	async function loadTours() {
		for (var i = 0; i < window.mailsterTour.tours.length; i++) {
			await loadTour(window.mailsterTour.tours[i]);
		}

	}

	function mark_as_seen(tour_id) {

		mailster.util.ajax('tour_mark_as_seen', {
			tour_id: tour_id,
		}, function (response) {
			console.log(response);
		});
	}


	function get_by_index(index) {
		return mailster.tours[Object.keys(mailster.tours)[0]];
	}

	return mailster;

}(mailster || {}, jQuery, window, document));
// end tour