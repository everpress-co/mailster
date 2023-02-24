mailster = (function (mailster, $, window, document) {
	'use strict';

	var inited = false;
	var thirdparty = false;
	var beacon;
	var queue = [];
	var beacondata;
	var articles = [];

	var beacon = function (method, options, data) {
		queue.push({
			method: method,
			options: options,
			data: data,
		});
	};

	function requireConsent(ask = true) {
		if (!mailster.user.get('helpscout')) {
			if (!ask || !confirm(mailster.l10n.helpscout.consent)) {
				return false;
			}
			mailster.user.set('helpscout', true);
			//articles.push('611bbc50f886c9486f8d994c');
		}
		initBeacon();
		return true;
	}

	mailster.$.document
		.on('click', '#mailster-admin-help', function () {
			if (!requireConsent()) {
				window.open('https://mailster.co/support');
				return false;
			}

			//var isOpen = Beacon('info').status.isOpened;

			beacon('toggle');
			//console.warn('INFO', isOpen, Beacon('info'));

			$(this).toggleClass('is-active');
		})
		.on('click', '.mailster-infolink', function () {
			if (!requireConsent()) {
				window.open(this.href);
				return false;
			}

			beacon('article', $(this).data('article'), {
				type: 'sidebar',
			});
			return false;
		})
		.on('click', 'a[href^="https://mailster.co/support"]', function (e) {
			e.stopImmediatePropagation();
			if (!requireConsent()) {
				window.open(this.href);
				return false;
			}

			beacon(
				'suggest',
				[
					{
						text: 'Mailster Support',
						url: this.href,
					},
					{
						text: 'Knowledge Base',
						url: 'https://kb.mailster.co/?utm_campaign=plugin&utm_medium=link&utm_source=Mailster%20Plugin&utm_term=mailster_dashboard',
					},
				].concat(articles)
			);
			beacon('open');
			beacon('navigate', '/');
			return false;
		});

	requireConsent(false);

	beacon('on', 'ready', function () {
		if (beacon('info').status.isOpened) {
			$('#mailster-admin-help').addClass('is-active');
		}
	});
	beacon('on', 'open', function () {});

	beacon('on', 'close', function () {});

	function initBeacon(cb) {
		if (inited) {
			return;
		}

		if (typeof window.Beacon == 'undefined') {
			var script = document.createElement('script');
			script.id = 'beacon';
			script.text = `!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});`;
			document.head.appendChild(script);
		}

		if (beacondata === false) {
			return;
		}

		if ((beacondata = mailster.session.get('helpscout'))) {
			run();
			return;
		}

		beacondata = false;

		mailster.util.ajax(
			'get_helpscout_data',
			function (response) {
				beacondata = response.data;
				mailster.session.set('helpscout', beacondata);
				run();
			},
			function (jqXHR, textStatus, errorThrown) {}
		);
	}

	function run() {
		var beacon_config = {
			docsEnabled: true,
			color: mailster.colors.main,
			enableFabAnimation: false,
			enableSounds: false,
			messagingEnabled: mailster.verified,
			messaging: {
				chatEnabled: mailster.verified,
				contactForm: {
					showName: true,
				},
			},
			display: {
				style: 'manual',
				position: 'right',
			},
		};

		beacon = function (method, options, data) {
			switch (method) {
				case 'init':
					//window.Beacon('reset');
					window.Beacon('identify', {
						name: beacondata.name,
						email: beacondata.email,
						avatar: beacondata.avatar,
					});
					window.Beacon('prefill', {
						name: beacondata.name,
						email: beacondata.email,
					});
					// window.Beacon('session-data', {
					// 	'App Version': 'v12.2.0 (Beta)',
					// 	'Last Action': 'Update Profile',
					// });
					window.Beacon('config', beacon_config);
					return window.Beacon(
						'init',
						'a32295c1-a002-4dcb-b097-d15532bb73d6'
					);
					break;
				case 'suggest':
					if (!mailster.verified) {
						options.push({
							text: 'Mailster Support',
							url: '#',
						});
					}
					return window.Beacon('suggest', options, data);
					break;
				default:
					return window.Beacon(method, options, data);
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

		// beacon('show-message', '9f50516e-ffdc-431a-9899-29e5d4abc385', {
		// 	force: true,
		// });

		window.mailster.beacon = beacon;

		inited = true;
	}

	return mailster;
})(mailster || {}, jQuery, window, document);
