mailster = (function (mailster, $, window, document) {
	'use strict';

	var loaded = false;
	var beacon;
	var queue = [];
	var beacondata;
	var articles = [];
	var helpbtn = $('#mailster-admin-help');

	var beacon = function (method, options, data) {
		queue.push({
			method: method,
			options: options,
			data: data,
		});
	};

	mailster.$.document
		.on('click', '#mailster-admin-help', function () {
			if (!requireConsent()) {
				window.open($(this).attr('href'));
				return false;
			}

			beacon('toggle');
			$(this).toggleClass('is-active');
		})
		.on('click', '.mailster-infolink', function (event) {
			if (!requireConsent()) {
				window.open($(this).attr('href'));
				return false;
			}

			beacon('article', $(this).data('article'), {
				type: event.altKey ? 'modal' : 'sidebar',
			});
			return false;
		})
		.on('click', 'a[href^="https://mailster.co/support"]', function (e) {
			e.stopImmediatePropagation();
			if (!requireConsent()) {
				window.open($(this).attr('href'));
				return false;
			}

			beacon('open');
			beacon('navigate', '/');
			return false;
		});

	requireConsent(false);

	beacon('on', 'ready', function () {
		if (beacon('info').status.isOpened) {
			helpbtn.addClass('is-active');
		}
	});
	beacon('on', 'open', function () {
		helpbtn.addClass('is-active');
	});
	beacon('on', 'close', function () {
		helpbtn.removeClass('is-active');
	});

	function loadBeaconData() {
		if (loaded) {
			return;
		}

		helpbtn.addClass('is-loading');

		if (typeof window.Beacon == 'undefined') {
			var script = document.createElement('script');
			script.id = 'beacon';
			script.text = `!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});`;
			script.text = `!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});`;
			document.head.appendChild(script);
		}

		beacondata = mailster.session.get('helpscout');

		//reload at least every hour
		if (beacondata && new Date() / 1000 - beacondata.timestamp < 3600) {
			initBeacon();
			return;
		}

		mailster.util.ajax(
			'get_helpscout_data',
			function (response) {
				beacondata = response.data;
				beacondata.timestamp = Math.round(new Date() / 1000);
				mailster.session.set('helpscout', beacondata);
				!loaded && initBeacon();
			},
			function (jqXHR, textStatus, errorThrown) {}
		);
	}

	function initBeacon() {
		beacon = function (method, options, data) {
			switch (method) {
				case 'init':
					Beacon('reset');
					Beacon('close');
					Beacon('identify', {
						name: beacondata.name,
						email: beacondata.email,
						avatar: beacondata.avatar,
					});
					Beacon('config', {
						docsEnabled: true,
						color: '#f0f0f1',
						messagingEnabled: mailster.verified,
						messaging: {
							chatEnabled: mailster.verified,
						},
						display: {
							style: 'manual',
						},
					});
					return Beacon('init', beacondata.id);
					break;
				case 'suggest':
					if (!mailster.verified) {
						options.push({
							text: 'Mailster Support',
							url: '#',
						});
					}
					return Beacon('suggest', options, data);
					break;
				default:
					return Beacon(method, options, data);
			}
		};

		beacon('init');

		for (var i in queue) {
			beacon(queue[i].method, queue[i].options, queue[i].data);
		}

		$('.mailster-infolink').each(function () {
			if (articles.length >= 9) return;
			var id = $(this).data('article');
			if (!id) return;
			if (articles.includes(id)) return;
			articles.push(id);
		});

		if (articles.length) {
			beacon('suggest', articles);
		}

		beacon('show-message', '9f50516e-ffdc-431a-9899-29e5d4abc385');
		beacon('show-message', '02558a31-163d-4322-ba47-66a142338ff1');

		window.mailster.beacon = beacon;

		loaded = true;
		helpbtn.removeClass('is-loading');
	}

	function requireConsent(ask = true) {
		if (!mailster.user.get('helpscout')) {
			if (!ask || !confirm(mailster.l10n.helpscout.consent)) {
				return false;
			}
			mailster.user.set('helpscout', true);
		}
		loadBeaconData();
		return true;
	}

	return mailster;
})(mailster || {}, jQuery, window, document);
