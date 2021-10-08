(function (wp) {
	const { registerPlugin } = wp.plugins;

	registerPlugin('mailster-form-sidebar', {
		icon: false,
		render: mailster.components.FormSidebar,
	});
})(window.wp);
