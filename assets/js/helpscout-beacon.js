mailster = (function (mailster, $, window, document) {
	'use strict';

	var params = new URL(document.location).searchParams;
	var name = params.get('page');

	!(function (e, t, n) {
		function a() {
			var e = t.getElementsByTagName('script')[0],
				n = t.createElement('script');
			(n.type = 'text/javascript'),
				(n.async = !0),
				(n.src = 'https://beacon-v2.helpscout.net'),
				e.parentNode.insertBefore(n, e);
		}
		if (
			((e.Beacon = n =
				function (t, n, a) {
					e.Beacon.readyQueue.push({
						method: t,
						options: n,
						data: a,
					});
				}),
			(n.readyQueue = []),
			'complete' === t.readyState)
		)
			return a();
		e.attachEvent
			? e.attachEvent('onload', a)
			: e.addEventListener('load', a, !1);
	})(window, document, window.Beacon || function () {});

	var beacon_config = {
		docsEnabled: true,
		color: mailster.colors.main,
		enableFabAnimation: false,
		enableSounds: false,
		messagingEnabled: mailster.helpscout.verified,
		messaging: {
			chatEnabled: mailster.helpscout.verified,
			_contactForm: {
				customFieldsEnabled: true,
				showName: true,
			},
		},
		display: {
			text: 'Mailster Help',
			style: 'iconAndText',
			style: 'icon',
			_style: 'manual',
			iconImage: 'buoy',
			iconImage: 'question',
			position: 'right',
		},
	};

	var beacon = function () {
		const method = Array.prototype.slice.call(arguments, 0)[0];
		const options = Array.prototype.slice.call(arguments, 1)[0];
		const data = Array.prototype.slice.call(arguments, 2)[0];

		switch (method) {
			case 'init':
				Beacon('reset');
				Beacon('init', 'a32295c1-a002-4dcb-b097-d15532bb73d6');
				Beacon('identify', {
					name: mailster.helpscout.name,
					email: mailster.helpscout.email,
					avatar: mailster.helpscout.avatar,
				});
				Beacon('config', beacon_config);
				Beacon('session-data', {
					'App Version': 'v12.2.0 (Beta)',
					'Last Action': 'Update Profile',
				});
				break;
			case 'suggest':
				beacon('init');
				if (!mailster.helpscout.verified) {
					options.push({
						text: 'Mailster Support',
						url: '#',
					});
				}
				Beacon('suggest', options, data);
				break;
			default:
				Beacon(method, options, data);
		}
	};

	var articles = [];

	mailster.events.push('documentReady', function () {
		$('.mailster-infolink').each(function () {
			if (articles.length >= 9) return;
			var id = $(this).data('article');
			if (!id) return;
			if (articles.includes(id)) return;
			articles.push(id);
		});
		if (articles.length) {
			beacon_config.display.style = 'icon';
			beacon('suggest', articles);
		} else {
			beacon('init');
		}

		beacon('show-message', '9f50516e-ffdc-431a-9899-29e5d4abc385', {
			force: false,
		});
	});

	$('a[href^="https://mailster.co/support"]').on('click', function () {
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

	mailster.$.document.on('click', '.mailster-infolink', function () {
		beacon('article', $(this).data('article'), {
			type: 'sidebar',
		});
		return false;
	});

	mailster.helpscout.beacon = beacon;

	return mailster;
})(mailster || {}, jQuery, window, document);
