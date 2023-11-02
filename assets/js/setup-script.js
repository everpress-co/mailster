mailster = (function (mailster, $, window, document) {
	'use strict';

	var steps = $('.mailster-setup-step'),
		currentStep,
		currentID,
		status = $('.status'),
		spinner = $('.spinner'),
		startStep = $('#step_start'),
		hash = location.hash.substring(1),
		tinymce = window.tinymce || false;

	if (hash && $('#step_' + hash).length) {
		startStep.hide();
		currentStep = $('#step_' + hash);
	} else {
		currentStep = startStep;
	}

	currentID = currentStep.attr('id').replace(/^step_/, '');

	step(currentID);

	$('form.mailster-setup-step-form').on('submit', function () {
		$('.next-step:visible').hide();
		return false;
	});

	$('#mailster-setup')
		.on('click', '.validation-skip-step', function () {
			return confirm(mailster.l10n.setup.skip_validation);
		})
		.on('click', '.next-step', function () {
			if ($(this).hasClass('disabled')) return false;

			if (tinymce) tinymce.get('post_content').save();

			var form = $(this).parent().parent().find('form'),
				data = form.serialize();
			mailster.util.ajax(
				'wizard_save',
				{
					id: currentID,
					data: data,
				},
				function (response) {}
			);
		})
		.on('click', '.load-language', function () {
			status.html(mailster.l10n.setup.load_language);
			spinner.css('visibility', 'visible');
			mailster.util.ajax('load_language', function (response) {
				spinner.css('visibility', 'hidden');
				status.html(response.data.html);
				if (response.success) {
					location.reload();
				}
			});

			return false;
		})
		.on('click', '.quick-install', function () {
			var _this = $(this);

			quickInstall(
				_this.data('method'),
				_this.data('plugin'),
				'install',
				null,
				function () {
					status.html('');
					spinner.css('visibility', 'hidden');
					deliverynav.find('a.nav-tab-active').trigger('click');
				}
			);
		})
		.on('click', '.edit-slug', function () {
			$(this)
				.parent()
				.parent()
				.find('span')
				.hide()
				.filter('.edit-slug-area')
				.show()
				.find('input')
				.focus()
				.select();
		})
		.on('click', '.mailster-homepage-preview-small', function () {
			var _this = $(this).find('iframe');
			var main = $('.mailster-homepage-preview').eq(0).find('iframe');
			var url = _this.attr('src');
			var mainurl = main.attr('src');
			_this.attr('src', mainurl);
			main.attr('src', url);
		});

	var deliverynav = $('#deliverynav'),
		deliverytabs = $('.deliverytab');

	deliverynav.on('click', 'a.nav-tab', function () {
		deliverynav.find('a').removeClass('nav-tab-active');
		deliverytabs.hide();
		var hash = $(this).addClass('nav-tab-active').attr('href').substr(1);
		$('#deliverymethod').val(hash);
		$('#deliverytab-' + hash).show();

		if ($('#deliverytab-' + hash).find('.quick-install').length) {
			$('.delivery-next-step')
				.addClass('disabled')
				.html(sprintf(mailster.l10n.setup.enable_first, $(this).html()));
		} else {
			$('.delivery-next-step')
				.removeClass('disabled')
				.html(sprintf(mailster.l10n.setup.use_deliverymethod, $(this).html()));
		}
		return false;
	});

	mailster.$.window.on('hashchange', function () {
		var id = location.hash.substr(1) || 'start',
			current = $('.mailster-setup-steps-nav').find("a[href='#" + id + "']");

		if (current.length) {
			step(id);
			current.parent().parent().find('a').removeClass('next prev current');
			current.parent().prevAll().find('a').addClass('prev');
			current.addClass('current');
			if (tinymce && tinymce.activeEditor)
				tinymce.activeEditor.theme.resizeTo('100%', 200);
		}

		switch (id) {
			case 'start':
				break;
			case 'templates':
				query_templates();
				break;
			case 'finish':
				mailster.util.ajax('wizard_save', {
					id: id,
					data: null,
				});
				break;
		}
	});

	mailster.events.push('documentReady', function () {
		mailster.$.window.trigger('hashchange');
	});

	function step(id) {
		var step = $('#step_' + id);

		if (step.length) {
			currentStep.removeClass('active');
			currentStep = step;
			currentStep.addClass('active');
			currentID = id;
		}
	}

	var busy = false;
	function query_templates(cb) {
		busy = true;

		mailster.util.ajax(
			'query_templates',
			{
				search: null,
				type: null,
				browse: 'samples',
				page: 1,
			},
			function (response) {
				console.log(response);

				busy = false;

				if (response.success) {
					$('.templates').html(response.data.html);
				}

				cb && cb();
			},
			function (jqXHR, textStatus, errorThrown) {}
		);
	}

	function quickInstall(id, slug, action, context, cb) {
		status.html(mailster.l10n.setup.install_addon);
		spinner.css('visibility', 'visible');
		var el = $('#deliverytab-' + id);

		mailster.util.ajax(
			'quick_install',
			{
				plugin: slug,
				step: action,
				context: context,
			},
			function (response) {
				if (response.success) {
					if (response.data.next) {
						quickInstall(
							id,
							slug,
							response.data.next,
							['deliverymethod_tab_' + id],
							cb
						);
					} else if (response.data.content) {
						el.html(response.data.content);
						cb && cb(response);
					}
				} else {
				}
			},
			function (jqXHR, textStatus, errorThrown) {}
		);
	}

	return mailster;
})(mailster || {}, jQuery, window, document);
