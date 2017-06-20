jQuery(document).ready(function ($) {

	"use strict"

	$('a.external').on('click', function () {
		window.open(this.href);
		return false;
	});

});