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

	mailster.beacon = function () {
		const m = Array.prototype.slice.call(arguments, 0)[0];
		const options = Array.prototype.slice.call(arguments, 1)[0];
		const data = Array.prototype.slice.call(arguments, 2)[0];

		console.warn(m, options, data);
		switch (m) {
			case 'init':
				Beacon('reset');
				Beacon('init', 'a32295c1-a002-4dcb-b097-d15532bb73d6');
				Beacon('prefill', {
					name: mailster_helpscout.name,
					email: mailster_helpscout.email,
				});
				Beacon('config', {
					docsEnabled: true,
					color: mailster.colors.main,
					enableFabAnimation: false,
					enableSounds: false,
					messagingEnabled: true,
					messaging: {
						chatEnabled: true,
						_contactForm: {
							customFieldsEnabled: true,
							showName: true,
						},
					},
					display: {
						text: 'Mailster Help',
						style: 'iconAndText',
						style: 'icon',
						iconImage: 'buoy',
						position: 'right',
					},
				});
				break;
			case 'suggest':
				mailster.beacon('init');
				Beacon('suggest', options, data);
				break;
			default:
				Beacon(m, options, data);
		}
	};

	var articles = [];

	$('.mailster-infolink').each(function (i, e) {
		if (i >= 9) return;
		var id = $(this).data('article');
		if (!id) return;
		if (articles.includes(id)) return;
		$(this).removeClass('external');
		articles.push(id);
	});

	if (articles.length) {
		mailster.beacon('suggest', articles);
	} else {
		mailster.beacon('init');
	}
	mailster.beacon('show-message', '9f50516e-ffdc-431a-9899-29e5d4abc385', {
		force: true,
	});
	$('a[href^="https://mailster.co/support"]').on('click', function () {
		mailster.beacon(
			'show-message',
			'9f50516e-ffdc-431a-9899-29e5d4abc385',
			{ force: true }
		);
		return false;
		mailster.beacon(
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
		mailster.beacon('open');
		mailster.beacon('navigate', '/');
		return false;
	});

	mailster.$.document.on('click', '.mailster-infolink', function () {
		Beacon('article', $(this).data('article'), {
			type: 'modal',
		});
		return false;
	});

	//Beacon('article', '611bb545b55c2b04bf6df0f3', { type: 'modal' });
	//Beacon('search', 'abc');
	// $.getScript('https://beacon-v2.helpscout.net', function (r) {
	// 	console.warn(r);
	// 	Beacon('init', 'a32295c1-a002-4dcb-b097-d15532bb73d6');
	// 	Beacon('open');
	// });

	//Beacon('navigate', '/previous-messages/');

	return mailster;
})(mailster || {}, jQuery, window, document);
