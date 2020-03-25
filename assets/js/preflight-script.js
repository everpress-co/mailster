mailster = (function (mailster, $, window, document) {

	"use strict";

	var api = {},
		id,
		preflight = $('.mailster-preflight'),
		$status = $('.preflight-status'),
		$loader = $('#preflight-ajax-loading'),
		$authentication = $('#preflight-authentication'),
		runbtn = $('.preflight-run'),
		$iframe = $('.mailster-preview-iframe'),
		$iframebody,
		$hx, $hy,
		started = 0,
		images,

		status = function (msg, append) {
			if (append) {
				$status.html($status.html() + msg);
			} else {
				$status.html(msg);
			}
		},

		error = function (msg) {
			var box = $('<div class="error"><p><strong>' + msg + '</strong></p></div>').hide().prependTo($('.score-wrap')).slideDown(200).delay(200).fadeIn().delay(8000).fadeTo(200, 0).delay(1500).slideUp(200, function () {
				box.remove();
			});
			if (console) console.error(msg);
		},

		loader = function (enable) {
			$loader.css('visibility', enable ? 'visible' : 'hidden');
		},

		switchPane = function () {
			var dimensions = $(this).data('dimensions');
			$('.device.desktop').width(dimensions.w).height(dimensions.h);
		},

		initTest = function () {
			clear();
			loader(true);
			status('Sending your campaign.');
			runbtn.prop('disabled', true);
			started = 0;

			mailster.util.ajax('send_test', {
				preflight: true,
				subscriber_id: $('#subscriber_id').val(),
				formdata: $('#post').serialize(),
				to: $('#mailster_testmail').val(),
				content: mailster.$.content.val(),
				head: mailster.$.head.val(),
				plaintext: mailster.$.excerpt.val()

			}, function (response) {

				if (response.success) {
					status('Check for delivery.');
					id = response.id;
					setTimeout(function () {
						checkTest(1);
					}, 3000);
				} else {
					loader(false);
					runbtn.prop('disabled', false);
				}


			}, function (jqXHR, textStatus, errorThrown) {

				loader(false);
				runbtn.prop('disabled', false);

			})

		},

		clear = function () {
			preflight.find('summary').removeAttr('class');
			preflight.find('.preflight-result').empty();
		},


		checkTest = function (tries) {

			if (tries > 10) {
				error('The email wasn\'t sent');
				loader(false);
				runbtn.prop('disabled', false);
				return;
			}

			mailster.util.ajax('preflight', {
				id: id,
			}, function (response) {
				if (response.success) {
					if (!response.ready) {
						setTimeout(function () {
							checkTest(++tries);
						}, 3000);
					} else {
						status('Email delivered, gathering results...');

						$.when.apply($, [
								getResult('blacklist'),
								getResult('spam_report'),
								getResult('authentication'),
								getResult('message'),
								getResult('links', 'tests/links'),
								getResult('images', 'tests/images'),
							])
							.done(function () {
								status('Preflight finished. Please check results.');
								loader(false);
								runbtn.prop('disabled', false);
							});
					}
				}
				if (response.error) {
					error(response.error);
					loader(false);
					runbtn.prop('disabled', false);
				}


			}, function (jqXHR, textStatus, errorThrown) {
				loader(false);
				runbtn.prop('disabled', false);

			})
		},

		getResult = function (part, endpoint) {
			var base = $('#preflight-' + part),
				children = base.find('details'),
				child_part,
				promises = [];

			if (children.length) {
				base.find('summary').eq(0).removeAttr('class').addClass('loading');
				for (var i = 0; i < children.length; i++) {
					child_part = children[i].id.replace('preflight-', '');
					if (child_part) {
						endpoint = 'tests/' + child_part;
						promises.push(getEndpoint(child_part, endpoint));
					}
				}

			} else {
				if (!endpoint) endpoint = part;
				promises.push(getEndpoint(part, endpoint));
			}

			return $.when.apply($, promises)
				.done(function () {
					var status, statuses = {
						'error': 0,
						'warning': 0,
						'notice': 0,
						'success': 0,
					};
					if (typeof arguments[1] != 'string') {
						for (i in arguments) {
							arguments[i] && statuses[arguments[i][0].status]++;
						}
						if (statuses.error) {
							status = 'error';
						} else if (statuses.warning) {
							status = 'warning';
						} else if (statuses.notice) {
							status = 'notice';
						} else {
							status = 'success';
						}
						$('#preflight-' + part).find('summary').eq(0).removeClass('loading').addClass('loaded is-' + status);
					}
				});

		},

		getEndpoint = function (part, endpoint) {

			var base = $('#preflight-' + part),
				summary = base.find('summary').eq(0).removeAttr('class').addClass('loading'),
				body = base.find('.preflight-result');

			return mailster.util.ajax('preflight_result', {
				id: id,
				endpoint: endpoint,
			}, function (response) {

				if (response.success) {
					summary.removeClass('loading').addClass('loaded is-' + response.status);
					if ('success' != response.status) {
						//base.prop('open', true);
					}
					body.html(response.html)
				}

				if (response.error) {
					error(response.error);
					loader(false);
					runbtn.prop('disabled', false);
				}


			}, function (jqXHR, textStatus, errorThrown) {
				loader(false);
				runbtn.prop('disabled', false);

			})

		},

		open = function () {
			mailster.trigger('save');
			mailster.trigger('disable');

			$('.preflight-from').html($('#mailster_from-name').val());
			loadPreview();

		},

		initFrame = function () {
			$iframebody = $iframe.contents().find('html,body');
			$hx = $iframe.contents().find('highlighterx');
			$hy = $iframe.contents().find('highlightery');
		},

		loadPreview = function () {

			var args = {
					id: mailster.campaign_id,
					subscriber_id: $('#subscriber_id').val(),
					content: mailster.editor.getContent(),
					head: mailster.$.head.val(),
					issue: $('#mailster_autoresponder_issue').val(),
					subject: mailster.details.$.subject.val(),
					preheader: mailster.details.$.preheader.val(),
				},
				title = mailster.$.title.val();

			clear();
			mailster.util.ajax('set_preview', args, function (response) {
				$iframe.one('load', initFrame).attr('src', ajaxurl + '?action=mailster_get_preview&hash=' + response.hash + '&_wpnonce=' + response.nonce);
				tb_show((title ? sprintf(mailsterL10n.preflight, '"' + title + '"') : mailsterL10n.preview), '#TB_inline?hash=' +
					response.hash +
					'&_wpnonce=' + response.nonce +
					'&width=' + (Math.min(1440, mailster.$.window.width() - 50)) +
					'&height=' + (mailster.$.window.height() - 100) +
					'&inlineId=mailster_preflight_wrap', null);
				$('.preflight-subject').html(response.subject);
				$('.preflight-subscriber').val(response.to);
				mailster.trigger('enable');
				images = true;

			}, function (jqXHR, textStatus, errorThrown) {
				mailster.trigger('enable');
			});

		},

		toggleImages = function () {
			var body = $iframe.contents().find('body'),
				img = body.find('img');
			if (!images) {
				body.removeClass('preflight-images-hidden');
				$.each(img, function (i, e) {
					$(e).attr('src', $(e).attr('data-src')).removeAttr('data-src');
				});
			} else {
				body.addClass('preflight-images-hidden');
				$.each(img, function (i, e) {
					$(e).attr('data-src', $(e).attr('src')).attr('src', '');
				});
			}
			images = !images;
		},

		highlightElement = function (event) {
			var t = $(this),
				d = t.data(),
				el,
				type = event.type;

			if (!d.el) {
				var url = d.url,
					tag = d.tag,
					attr = d.attr,
					index = d.index,
					el = $iframe.contents().find(tag + '[' + attr + '="' + url + '"]')[index];
				t.data('el', el);
			} else {
				el = d.el;
			}
			if (!el) {
				return;
			}
			if ('mouseleave' == type) {
				$iframebody.removeClass('preflight-highlighter');
				$(el).removeClass('preflight-highlighted');
				return;
			} else {
				$(el).addClass('preflight-highlighted');
				$iframebody.addClass('preflight-highlighter');
			}

			el.scrollIntoView({
				behavior: "smooth",
				block: "center"
			});

			var rect = el.getBoundingClientRect();

			$hx.css({
				'transform': 'translate(' + (rect.x) + 'px, ' + (rect.y + $iframebody.scrollTop()) + 'px)',
				'width': rect.width,
				'height': rect.height,
			})
			$hy.css({
				'transform': 'translate(' + (rect.x) + 'px, ' + (rect.y + $iframebody.scrollTop()) + 'px)',
				'width': rect.width,
				'height': rect.height,
			})

		},

		agreeTerms = function () {

			if (!$('#preflight-agree-checkbox').is(':checked')) {
				alert(mailsterL10n.enter_list_name);
				return false;
			}
			mailster.util.ajax('preflight_agree', function (response) {
				preflight.addClass('preflight-terms-agreed');
			}, function (jqXHR, textStatus, errorThrown) {});

		};

	//preflight box
	$('#mailster_preflight')
		.on('click', '.mailster_preflight', function () {
			open();
			return;
		});

	preflight
		.on('click', '.preflight-switch', switchPane)
		.on('click', '.preflight-run', initTest)
		.on('click', '.preflight-toggle-images', toggleImages)
		.on('mouseenter', '.assets-table tr', highlightElement)
		.on('mouseleave', '.assets-table tr', highlightElement)
		.on('click', '#preflight-agree', agreeTerms);

	$(".preflight-subscriber")
		.on('focus', function () {
			$(this).select();
		})
		.autocomplete({
			source: function (request, response) {
				mailster.util.ajax('search_subscribers', {
					id: mailster.campaign_id,
					term: request.term,
				}, function (data) {
					response(data);
				}, function (jqXHR, textStatus, errorThrown) {});
			},
			classes: {
				"ui-autocomplete": "bbbb",
				"ui-state-focus": "aaaa",
			},
			appendTo: '.prefligth-emailheader',
			minLength: 1,
			select: function (event, ui) {
				$('#subscriber_id').val(ui.item.id);
				loadPreview();
			},
			change: function (event, ui) {
				if (!ui.item) {
					$('#subscriber_id').val(0);
					loadPreview();
				}
			}
		});




	mailster.notices = mailster.notices || {};

	return mailster;

}(mailster || {}, jQuery, window, document));