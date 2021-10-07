// block Blocks
mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.blocks = mailster.blocks || {};
	mailster.blocks.go = true;

	var el = wp.element.createElement;

	const {
		__
	} = wp.i18n;
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

	class FormSidebar extends Component {

		render() {

			return [
				el(Fragment, {
						key: 'adasd'
					},
					el(FormOptions),
					el(FormDoubleOptIn)
				)
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
						key: 'mailster-options',
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
						key: 'mailster-double-opt-in',
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


	class Input extends Component {

		constructor() {
			super(...arguments);
			this.type = 'text';
			this.parentBlock;
			this.state = {
				width: 100,
			}
		}

		componentDidMount() {
			this.parentBlock = document.getElementById('block-' + this.props.clientId);
			this.updateBlock();
		}

		updateBlock(value) {

			this.props.clientId && (this.parentBlock.style.width = this.parentBlock.style.maxWidth = (value || this.props.attributes.width) + '%');
		}

		render() {
			// Setup the attributes
			let {
				attributes,
				setAttributes,
				className,
				name,
				clientId,
			} = this.props;

			// const {
			// 	Disabled,
			// 	Panel,
			// 	PanelHeader,
			// 	PanelBody,
			// 	PanelRow,
			// 	TextControl,
			// 	ToggleControl,
			// 	Text,
			// 	RangeControl,
			// } = wp.components;

			// const {
			// 	InnerBlocks,
			// 	RichText,
			// 	InspectorControls,
			// } = wp.blockEditor;

			// const {
			// 	Fragment
			// } = wp.element;

			var requiredToggle = el(ToggleControl, {
				label: __('Required', 'mailster'),
				className: 'mailster-required-toggle',
				checked: attributes.required,
				onChange: function (value) {
					setAttributes({
						required: value
					});
				},
			});
			if ('mailster/field-email' == name) {
				requiredToggle = el(Disabled, {}, requiredToggle);
			}

			className = 'wp-block-mailster-field ' + className;
			if (attributes.required) className += ' is-required';

			return [
				el('div', {
						className: className,
						key: 'input-' + this.type,
						onLoad: () => {
							console.log('load');
						},
						// style: {
						// 	width: 'calc(6 * ('+attributes.width+'vw / 12 ))',
						// 	asd: 'calc(6 * ('+attributes.width+'vw / 12 ))',
						// },
					},
					//requiredToggle,
					el(RichText, {
						tagName: 'label',
						className: 'mailster-label',
						value: attributes.label,
						allowedFormats: ['core/bold', 'core/italic', 'core/link'],
						onChange: (value) => {
							setAttributes({
								label: value
							});
						},
					}),
					el('input', {
						type: 'text',
						className: 'mailster-input mailster-input-' + name,
					}),
				),

				el(Fragment, {
						key: 'inspector-input-' + this.type
					},
					el(InspectorControls, {},

						el(PanelBody, {
								title: __('Options', 'mailster'),
								initialOpen: true
							},

							el(Fragment, {}, requiredToggle),
							// el(Fragment, {}, el(DimensionControl, {
							// 	label: __('Label', 'mailster'),
							// 	icon: 'desktop',
							// 	value: attributes.width,
							// 	onChange: (value) => {
							// 		setAttributes({
							// 			width: value
							// 		});
							// 	},

							// })),
							el(Fragment, {},
								el(RangeControl, {
									label: __('Width', 'mailster'),
									value: attributes.width,
									min: 0,
									max: 100,
									icon: 'dashicons-sticky',
									onChange: (value) => {
										setAttributes({
											width: value
										});
										this.updateBlock(value);
									},
								}),
								// el(RangeControl, {
								// 	label: __('margin', 'mailster'),
								// 	value: attributes.width,
								// 	min: 0,
								// 	max: 100,
								// 	icon: 'dashicons-sticky',
								// 	onChange: (value) => {
								// 		setAttributes({
								// 			width: value
								// 		});
								// 		this.updateBlock(value;
								// 	},
								// }),
								el(TextControl, {
									label: __('Label', 'mailster'),
									value: attributes.label,
									onChange: (value) => {
										setAttributes({
											label: value
										});
									},
								}),
							),

						),

					),
				),

			];
		}

	}
	mailster.components = {
		FormSidebar: FormSidebar,
		FormPanel: FormPanel,
		FormOptions: FormOptions,
		FormDoubleOptIn: FormDoubleOptIn,
		Input: Input,
	}

	return mailster;

}(mailster || {}, jQuery, window, document));
// end Blocks