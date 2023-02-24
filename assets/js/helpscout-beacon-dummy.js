mailster = (function (mailster, $, window, document) {
	//will be loaded if helpscout beacon is disabled

	//open the link in a new window
	mailster.$.document.on('click', '.mailster-infolink', function () {
		window.open(this.href);
		return false;
	});

	return mailster;
})(mailster || {}, jQuery, window, document);
