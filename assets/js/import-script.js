mailster = (function (mailster, $, window, document) {

	"use strict";

	var uploader,
		uploadinfo = $('.uploadinfo'),
		importidentifier;


	mailster.$.document
		.on('submit', '#import_paste', function () {

			var value = mailster.util.trim($('#paste-import').val());

			if (value) {
				mailster.util.ajax('import_subscribers_upload_handler', {
					data: value
				}, function (response) {

					if (response.success) {
						importidentifier = response.identifier;
						$('#wordpress-users').fadeOut();
						get_import_data();
					} else {
						importstatus.html(response.message);
						progress.addClass('error');
					}
				}, function () {

					importstatus.html('Error');
				});
			}

			return false;

		})
		.on('submit', '#import_wordpress', function () {

			var data = $(this).serialize();
			mailster.util.ajax('import_subscribers_upload_handler', {
				wordpressusers: data
			}, function (response) {

				if (response.success) {
					importidentifier = response.identifier;
					$('#wordpress-users').fadeOut();
					get_import_data();
				} else {
					importstatus.html(response.message);
					progress.addClass('error');
				}
			}, function () {

				importstatus.html('Error');
			});

			return false;
		});

	typeof wpUploaderInit == 'object' && mailster.events.push('documentReady', function () {

		uploader = new plupload.Uploader(wpUploaderInit);

		uploader.bind('Init', function (up) {
			var uploaddiv = $('#plupload-upload-ui');

			if (up.features.dragdrop && !$(document.body).hasClass('mobile')) {
				uploaddiv.addClass('drag-drop');
				$('#drag-drop-area').bind('dragover.wp-uploader', function () { // dragenter doesn't fire right :(
					uploaddiv.addClass('drag-over');
				}).bind('dragleave.wp-uploader, drop.wp-uploader', function () {
					uploaddiv.removeClass('drag-over');
				});
			} else {
				uploaddiv.removeClass('drag-drop');
				$('#drag-drop-area').unbind('.wp-uploader');
			}

			if (up.runtime == 'html4')
				$('.upload-flash-bypass').hide();

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
			uploadinfo.html(mailster.util.sprintf(mailster.l10n.import.uploading, file.percent + '%'));
		});

		uploader.bind('Error', function (up, err) {
			uploadinfo.html(err.message);
			up.refresh();
		});

		uploader.bind('FileUploaded', function (up, file, response) {
			response = JSON.parse(response.response);
			importidentifier = response.identifier;
			if (!response.success) {
				uploadinfo.html(response.message);
				up.refresh();
				uploader.unbind('UploadComplete');
			}
		});

		uploader.bind('UploadComplete', function (up, files) {
			uploadinfo.html(mailster.l10n.import.prepare_data);
			get_import_data();
		});

		uploader.init();
	});


	function get_import_data() {
		mailster.util.ajax('get_import_data', {
			identifier: importidentifier
		}, function (response) {

			console.log(response);

			$('.import-result').eq(0).html(response.html).show();
			$('.import-wrap').hide();

			$('input.datepicker').datepicker({
				dateFormat: 'yy-mm-dd',
				showAnim: 'fadeIn',
				onClose: function () {}
			});

			$.fn.select2 && $('.tags-input').select2({
				placeholder: mailster.l10n.manage.choose_tags,
				tags: true,
				theme: 'mailster'
			});
			return;

			progress.addClass('hidden');

			$('.step1').slideUp();
			$('.step2-body').html(response.html).parent().show();




			importstatus.html('');

			importdata = response.data;
		});
	}

	return mailster;

}(mailster || {}, jQuery, window, document));