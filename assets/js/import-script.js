mailster = (function (mailster, $, window, document) {
	'use strict';

	var uploader,
		uploadinfo = $('.uploadinfo'),
		importstatus = $('.status'),
		importstarttime,
		importidentifier;

	mailster.$.document
		.on('submit', '#import_paste', function () {
			var value = mailster.util.trim($('#paste-import').val());

			$(this).prop('readonly', true).css('opacity', 0.8);

			importstatus
				.addClass('progress spinner')
				.html(mailster.l10n.manage.prepare_import);

			if (value) {
				prepare_import({
					type: 'paste',
					data: value,
				});
			}

			return false;
		})
		.on('submit', '#import_wordpress', function () {
			var data = $(this).serialize();

			$(this).prop('readonly', true).css('opacity', 0.8);

			importstatus
				.addClass('progress spinner')
				.html(mailster.l10n.manage.prepare_import);

			if (data) {
				prepare_import({
					type: 'wordpress',
					data: data,
				});
			}

			return false;
		})
		.on('submit', '.importer-form', function () {
			var form = $(this);
			var data = form.serialize();
			var slug = form.data('slug');

			form.prop('readonly', true).addClass('loading');

			mailster.util.ajax(
				'importer_form_submit',
				{
					data: data,
					slug: slug,
				},
				function (response) {
					form.prop('readonly', false).removeClass('loading');
					if (response.success) {
						if (response.data.html) {
							form.replaceWith(response.data.html);
						}
						if (response.data.identifier) {
							importidentifier = response.data.identifier;
							get_import_data();
						}
					}
				}
			);

			return false;
		})
		.on('change', '.column-selector', function () {
			if ('_new' == $(this).val()) {
				tb_show(
					'',
					'#TB_inline?x=1&width=480&height=320&inlineId=create-new-field',
					false
				);
			}
		})
		.on('click', '#addlist', function () {
			var val = $('#new_list_name').val();
			if (!val) {
				return false;
			}

			$(
				'<li><label><input name="lists[]" value="' +
					val +
					'" type="checkbox" checked> ' +
					val +
					' </label></li>'
			).appendTo('#section-lists > ul');
			$('#new_list_name').val('');
			return false;
		})
		.on('change', '#signup', function () {
			$('#signupdate').prop('disabled', !$(this).is(':checked'));
		})
		.on('change', '.list-toggle', function () {
			$(this)
				.parent()
				.parent()
				.parent()
				.find('ul input')
				.prop('checked', $(this).prop('checked'));
		})
		.on('click', '.do-import', function () {
			var data = $('#subscriber-table').serialize();

			if (!/%5D=email/.test(data)) {
				alert(mailster.l10n.manage.select_emailcolumn);
				return false;
			}
			if (!$('input[name="status"]:checked').length) {
				alert(mailster.l10n.manage.select_status);
				return false;
			}

			// if (!confirm(mailster.l10n.manage.confirm_import)) return false;

			var _this = $(this).prop('disabled', true),
				status = $('input[name="status"]:checked').val(),
				existing = $('input[name="existing"]:checked').val(),
				signup = $('#signup').is(':checked'),
				signupdate = $('#signupdate').val(),
				// keepstatus = $('#keepstatus').is(':checked'),
				loader = $('#import-ajax-loading').css({
					display: 'inline-block',
				}),
				identifier = $('#identifier').val(),
				performance = $('#performance').is(':checked');

			importstarttime = new Date();
			importstatus
				.addClass('progress spinner')
				.html(mailster.l10n.manage.prepare_import);

			do_import(0, {
				identifier: identifier,
				data: data,
				status: status,
				//keepstatus: keepstatus,
				//existing: existing,
				//signupdate: signup ? signupdate : null,
				performance: performance,
			});

			$(this).prop('disabled', false);

			return false;
			window.onbeforeunload = function () {
				return mailster.l10n.manage.onbeforeunloadimport;
			};
		})
		.on('click', '.install-addon', function () {
			var slug = $(this).data('slug');
			installAddon(slug);
		});

	typeof wpUploaderInit == 'object' &&
		mailster.events.push('documentReady', function () {
			uploader = new plupload.Uploader(wpUploaderInit);

			uploader.bind('Init', function (up) {
				var uploaddiv = $('#plupload-upload-ui');

				if (
					up.features.dragdrop &&
					!$(document.body).hasClass('mobile')
				) {
					uploaddiv.addClass('drag-drop');
					$('#drag-drop-area')
						.bind('dragover.wp-uploader', function () {
							// dragenter doesn't fire right :(
							uploaddiv.addClass('drag-over');
						})
						.bind(
							'dragleave.wp-uploader, drop.wp-uploader',
							function () {
								uploaddiv.removeClass('drag-over');
							}
						);
				} else {
					uploaddiv.removeClass('drag-drop');
					$('#drag-drop-area').unbind('.wp-uploader');
				}

				if (up.runtime == 'html4') {
					$('.upload-flash-bypass').hide();
				}
			});

			uploader.bind('FilesAdded', function (up, files) {
				$('#media-upload-error').html('');
				$('#wordpress-users').fadeOut();

				setTimeout(function () {
					up.refresh();
					up.start();
				}, 1);
			});

			uploader.bind('BeforeUpload', function (up, file) {
				uploadinfo.html('uploading');
			});

			uploader.bind('UploadFile', function (up, file) {});

			uploader.bind('UploadProgress', function (up, file) {
				uploadinfo.html(
					mailster.util.sprintf(
						mailster.l10n.manage.uploading,
						file.percent + '%'
					)
				);
			});

			uploader.bind('Error', function (up, err) {
				uploadinfo.html(err.message);
				up.refresh();
			});

			uploader.bind('FileUploaded', function (up, file, response) {
				response = JSON.parse(response.response);
				if (response.success) {
					importidentifier = response.data.identifier;
				} else {
					uploadinfo.html(response.data.message);
					up.refresh();
				}
			});

			uploader.bind('UploadComplete', function (up, files) {
				if (importidentifier) {
					uploadinfo.html(mailster.l10n.manage.prepare_data);
					get_import_data();
				}
			});

			uploader.init();
		});

	function prepare_import(data) {
		mailster.util.ajax(
			'import_subscribers_upload_handler',
			data,
			function (response) {
				if (response.success) {
					importidentifier = response.data.identifier;
					get_import_data();
				}
			},
			function () {
				importstatus.html('Error');
				$(this).prop('readonly', false).css('opacity', 1);
			}
		);
	}

	function do_import(id, options) {
		var percentage = 0,
			finished;

		if (!id) {
			id = 0;
		}

		mailster.util.ajax(
			'do_import',
			{
				id: id,
				options: options,
			},
			function (response) {
				if (response.success) {
					percentage =
						Math.min(
							1,
							(response.data.imported + response.data.errors) /
								response.data.total
						) * 100;

					importstatus.html(
						get_stats(
							response.data.f_imported,
							response.data.f_errors,
							response.data.f_total,
							percentage,
							response.data.memoryusage
						)
					);

					finished = percentage >= 100;
					if (finished) {
						window.onbeforeunload = null;
						$('.import-result').html(response.data.html);
						scroll_to_content_top();
					} else {
						do_import(++id, options);
					}
				} else {
					upload_error_handler(response.data.msg);
				}
			},
			function (jqXHR, textStatus, errorThrown) {
				upload_error_handler(textStatus);
			}
		);
	}

	function upload_error_handler(errormsg) {
		importstatus.removeClass('progress spinner').html(errormsg);
	}

	function scroll_to_content_top(pos) {
		window.scroll({
			top: pos || 125,
			left: 0,
			behavior: 'smooth',
		});
	}

	function get_import_data() {
		mailster.util.ajax(
			'get_import_data',
			{
				identifier: importidentifier,
			},
			function (response) {
				if (response.success) {
					scroll_to_content_top();

					$('.import-result').eq(0).html(response.data.html).show();
					$('.import-wrap').hide();

					$('input.datepicker').datepicker({
						dateFormat: 'yy-mm-dd',
						showAnim: 'fadeIn',
						onClose: function () {},
					});

					$.fn.select2 &&
						$('.tags-input').select2({
							placeholder: mailster.l10n.manage.choose_tags,
							tags: true,
							theme: 'mailster',
						});

					importstatus = $('.status');
				}
			}
		);
	}

	function get_stats(imported, errors, total, percentage, memoryusage) {
		var timepast = new Date().getTime() - importstarttime.getTime(),
			timeleft = Math.ceil(
				((100 - percentage) * (timepast / percentage)) / 60000
			);

		return (
			mailster.util.sprintf(
				mailster.l10n.manage.current_stats,
				'<strong>' + imported + '</strong>',
				'<strong>' + total + '</strong>',
				'<strong>' + errors + '</strong>',
				'<strong>' + memoryusage + '</strong>'
			) +
			' ' +
			mailster.util.sprintf(mailster.l10n.manage.estimate_time, timeleft)
		);
	}

	function installAddon(slug) {
		mailster.util.ajax(
			'quick_install',
			{
				plugin: slug,
				step: 'install',
			},
			function (response) {
				console.warn(response);
				if (response.success) {
				} else {
				}
			},
			function (jqXHR, textStatus, errorThrown) {}
		);
	}
	return mailster;
})(mailster || {}, jQuery, window, document);
