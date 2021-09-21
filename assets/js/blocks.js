(function (wp) {

	var el = wp.element.createElement;
	const {
		__
	} = wp.i18n;
	const {
		registerPlugin
	} = wp.plugins;
	const {
		PluginSidebarMoreMenuItem,
		PluginDocumentSettingPanel
	} = wp.editPost;
	const {
		Component,
		Fragment
	} = wp.element;
	const {
		select,
		withSelect,
		dispatch,
		withDispatch,
	} = wp.data
	const {
		withState,
		compose
	} = wp.compose
	const {
		Disabled,
		Buttons,
		Button,
		Text,
		TextControl,
		CheckboxControl,
		ColorPalette,
		ColorPicker,
		ColorIndicator,
		Draggable,
		RadioControl,
		RangeControl,
		SelectControl,
		TextareaControl,
		ToggleControl,
		Icon,
		PlainText,
		Panel,
		PanelHeader,
		PanelBody,
		PanelRow
	} = wp.components;

	const {
		InnerBlocks,
		RichText,
		InspectorControls,
		PanelColorSettings,
	} = wp.blockEditor;

	class FormSidebar extends Component {

		render() {

			return [
				el(FormOptions),
				el(FormDoubleOptIn)
			]

		}

	}

	class FormPanel extends Component {

		constructor() {
			super();
			this.state = {
				x: 123
			}
		}

	}

	class FormOptions extends FormPanel {

		render() {

			var a = select('core/editor').getEditedPostAttribute('meta');

			return [
				el(PluginDocumentSettingPanel, {
						title: __('Options', 'mailster'),
						name: 'mailster-options',
						className: 'mailster-options',
					},

					el(Fragment, {},
						el(myToggle, {
							metaKey: 'prefill',
							label: __('Fill fields with known data if user is logged in.', 'mailster')
						}),
						el(myToggle, {
							metaKey: 'overwrite',
							label: __('Allow users to update their data with this form.', 'mailster')
						}),
						a.prefill && el(myToggle, {
							metaKey: 'asterisk',
							label: __('Show Asterisk', 'mailster')
						}),
					),
				),
			]

		}

	}

	class FormDoubleOptIn extends FormPanel {

		render() {

			return [
				el(PluginDocumentSettingPanel, {
						title: __('Double Opt in', 'mailster'),
						name: 'mailster-double-opt-in',
						className: 'mailster-double-opt-in',
					},
					el(Fragment, {},
						el(myToggle, {
							metaKey: 'doubleoptin',
							label: __('Send confirmation email on signup.', 'mailster'),
						}),
					),

				),
			]

		}

	}

	var getAttribute = function (metaKey) {
		select('core/editor').getEditedPostAttribute('meta')[metaKey];
	}
	var setAttribute = function (metaKey, value) {
		console.log(metaKey, value);
		dispatch('core/editor').editPost({
			meta: {
				[metaKey]: value
			}
		});
	}

	var myDispatch = function (dispatch, props) {
		return {
			setMetaValue: function (value) {
				dispatch('core/editor').editPost({
					meta: {
						[props.metaKey]: value
					}
				});
			}
		}
	}

	var mySelect = function (select, props) {
		return {
			metaValue: select('core/editor').getEditedPostAttribute('meta')[props.metaKey]
		}
	}

	var myToggle = compose(withDispatch(myDispatch), withSelect(mySelect))(function (props) {
		return el(ToggleControl, {
			label: props.label,
			checked: props.metaValue,
			onChange: (content) => {
				props.setMetaValue(content)
			},
		});
	});


	registerPlugin('mailster-form-sidebar', {
		icon: false,
		render: FormSidebar,
	});

})(
	window.wp
);