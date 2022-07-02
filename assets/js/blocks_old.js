(function (wp) {
	/**
	 * Registers a new block provided a unique name and an object defining its behavior.
	 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/#registering-a-block
	 */
	var registerBlockType = wp.blocks.registerBlockType;
	const {
		Component
	} = wp.element;
	/**
	 * Returns a new element of given type. Element is an abstraction layer atop React.
	 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/packages/packages-element/
	 */
	var el = wp.element.createElement;


	/**
	 * Retrieves the translation of text.
	 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/packages/packages-i18n/
	 */
	var __ = wp.i18n.__;

	const ALLOWED_BLOCKS = ['mailster/*'];
	const TEMPLATE = [
		['mailster/field-email']
	];

	const {
		registerPlugin
	} = wp.plugins;


	class Form extends Component {

		constructor() {
			super(...arguments);
			this.state = {
				displaySuccessMessages: false,
				displayErrorMessages: false,
			}
		}

		render(props) {
			// Setup the attributes
			let {
				attributes,
				setAttributes,
				className
			} = this.props;
			const {
				InnerBlocks,
				RichText,
				InspectorControls,
				PanelColorSettings,
			} = wp.blockEditor;
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
				Fragment
			} = wp.element

			const {
				withState
			} = wp.compose

			if (attributes.asterisk) className += ' has-asterisk';
			if (attributes.align) className += ' align' + attributes.align;

			var listSelector =
				Object.values(attributes.lists_order).map(function (listID, i) {
					return el('div', {
							key: 'list-' + listID,
						},

						attributes.userchoice && el('div', {
								className: 'list-mover'
							},
							el(Icon, {
								icon: 'arrow-up',
								className: (!i ? 'list-mover-is-hidden' : ''),
								title: __('Move list up', 'mailster'),
								label: 'more',
								onClick: () => {
									var copy = Object.assign([], attributes.lists_order);
									copy.splice(i, 0, copy.splice(i - 1, 1)[0]);
									setAttributes({
										lists_order: copy
									});
								}
							}),
							el(Icon, {
								icon: 'arrow-down',
								className: (i == (attributes.lists_order.length - 1) ? 'list-mover-is-hidden' : ''),
								title: __('Move list down', 'mailster'),
								label: 'more',
								onClick: () => {
									var copy = Object.assign([], attributes.lists_order);
									copy.splice(i, 0, copy.splice(i + 1, 1)[0]);
									setAttributes({
										lists_order: copy
									});
								}
							}),
						),
						el(CheckboxControl, {
							label: attributes.lists[listID],
							checked: attributes.lists_selected.indexOf(listID) != -1,
							onChange: (isChecked) => {

								var added = attributes.lists_selected.indexOf(listID) != -1;
								// Don't mutate the original object but make a copy of it and mutate that one
								var copy = Object.assign([], attributes.lists_selected);

								if (!added && isChecked) {
									copy.push(listID);
								} else if (added && !isChecked) {
									copy.splice(copy.indexOf(listID), 1);
								}

								setAttributes({
									lists_selected: copy
								});

							},
						}),

					);
				});

			return [
				el('div', {
						className: className,
						style: {
							color: attributes.formColor,
							backgroundColor: attributes.formBGColor
						},
						key: 'mailster-form',
					},

					// Messages
					(this.state.displaySuccessMessages || this.state.displayErrorMessages) && el('div', {
							className: 'wp-block'
						},
						this.state.displaySuccessMessages && el(RichText, {
							value: attributes.doubleoptin ? attributes.confirmMessage : attributes.successMessage,
							allowedFormats: ['core/bold', 'core/italic', 'core/link'],
							style: {
								color: attributes.successColor,
								backgroundColor: attributes.successBGColor
							},
							className: 'mailster-notice mailster-notice-success',
							onChange: (value) => {
								if (attributes.doubleoptin) {
									setAttributes({
										confirmMessage: value
									});
								} else {
									setAttributes({
										successMessage: value
									});
								}
							},
						}),
						this.state.displayErrorMessages && el(RichText, {
							value: attributes.errorMessage,
							allowedFormats: ['core/bold', 'core/italic', 'core/link'],
							style: {
								color: attributes.errorColor,
								backgroundColor: attributes.errorBGColor
							},
							className: 'mailster-notice mailster-notice-error',
							onChange: (value) => {
								setAttributes({
									errorMessage: value
								});
							},
						}),
					),

					// The fields
					el(InnerBlocks, {
						className: 'mailster-form-fields',
						allowedBlocks: ALLOWED_BLOCKS,
						template: TEMPLATE,
					}),

					attributes.userchoice &&

					Object.values(attributes.lists_order).map(function (listID, i) {

						return attributes.lists_selected.indexOf(listID) != -1 && el('div', {
								key: 'list-block-' + listID,
								className: '',
							},


							el('span', {
									className: 'list-mover'
								},
								el(Icon, {
									icon: 'arrow-up',
									className: (!i ? 'list-mover-is-hidden' : ''),
									title: __('Move list up', 'mailster'),
									label: 'more',
									onClick: () => {
										var copy = Object.assign([], attributes.lists_order);
										copy.splice(i, 0, copy.splice(i - 1, 1)[0]);
										console.log(copy);
										setAttributes({
											lists_order: copy
										});
									}
								}),
								el(Icon, {
									icon: 'arrow-down',
									className: (i == (attributes.lists_order.length - 1) ? 'list-mover-is-hidden' : ''),
									title: __('Move list down', 'mailster'),
									label: 'more',
									onClick: () => {
										var copy = Object.assign([], attributes.lists_order);
										copy.splice(i, 0, copy.splice(i + 1, 1)[0]);
										console.log(copy);
										setAttributes({
											lists_order: copy
										});
									}
								}),
							),

							el('input', {
								type: 'checkbox',
								disabled: true,
								'style': {
									'visibility': attributes.dropdown ? 'hidden' : 'visible',
								}
							}),

							el(RichText, {
								tagName: 'span',
								placeholder: attributes.lists[listID],
								value: attributes.lists[listID],
								allowedFormats: ['core/bold', 'core/italic', 'core/link'],
								onChange: (value) => {
									var copy = Object.assign([], attributes.lists);
									copy[listID] = value;
									//copy.splice(i, 0, copy.splice(i + 1, 1)[0]);
									console.log(copy);
									console.log(i, listID, value);
									setAttributes({
										lists: copy
									});
								},
							}),


						);

						return el('div', {
								key: 'list-block-' + listID,
							},
							el(Disabled, {}, attributes.lists_selected.indexOf(listID) != -1 && el(CheckboxControl, {
								label: attributes.lists[listID],
								onChange: (isChecked) => {},
							}))
						);

					}),

					// GDPR checkbox
					attributes.gdpr && el('label', {
							className: 'gdpr-line',
						},
						el('input', {
							type: 'checkbox',
							disabled: true,
						}),
						el(RichText, {
							tagName: 'span',
							value: attributes.gdpr_text,
							allowedFormats: ['core/bold', 'core/italic', 'core/link'],
							onChange: (value) => {
								setAttributes({
									gdpr_text: value
								});
							},
						}),
					),

					// el(InnerBlocks, {
					// 	className: 'wp-block-button',
					// 	allowedBlocks: ['core/*'],
					// 	//template: [[Button]],
					// }),

					// Submit button
					el('div', {
							className: 'wp-block-button'
						},
						el(RichText, {
							value: attributes.submit,
							placeholder: __('Enter Label', 'mailster'),
							allowedFormats: ['core/align'],
							withoutInteractiveFormatting: true,
							style: {
								color: attributes.buttonColor,
								backgroundColor: attributes.buttonBGColor
							},
							className: 'wp-block-button__link',
							onChange: (value) => {
								setAttributes({
									submit: value
								});
							},
						}),

						el(InspectorControls, {
								key: 'inspector-form-button'
							},

							el(PanelColorSettings, {
								title: __('Form Color', 'mailster'),
								initialOpen: false,
								colorSettings: [{
									label: __('Text Color', 'mailster'),
									value: attributes.formColor,
									onChange: (value) => {
										setAttributes({
											formColor: value
										});
									},
								}, {
									label: __('Background', 'mailster'),
									value: attributes.formBGColor,
									onChange: (value) => {
										setAttributes({
											formBGColor: value
										});
									},
								}, ],
							}),

							el(PanelColorSettings, {
								title: __('Button Color', 'mailster'),
								initialOpen: false,
								colorSettings: [{
									label: __('Text Color', 'mailster'),
									value: attributes.buttonColor,
									onChange: (value) => {
										setAttributes({
											buttonColor: value
										});
									},
								}, {
									label: __('Background', 'mailster'),
									value: attributes.buttonBGColor,
									onChange: (value) => {
										setAttributes({
											buttonBGColor: value
										});
									},
								}, ],
							}),

							el(PanelBody, {
									title: __('Button Option', 'mailster'),
									initialOpen: false
								},
								//el( Fragment, {}, requiredToggle),
								el(Fragment, {},
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

				),
				el(Fragment, {
						key: 'inspector'
					},
					el(InspectorControls, {},

						el(PanelColorSettings, {
							title: __('Success Message', 'mailster'),
							initialOpen: this.state.displaySuccessMessages,
							onToggle: () => {
								this.setState({
									displaySuccessMessages: !this.state.displaySuccessMessages
								});
							},
							colorSettings: [{
								label: __('Text Color', 'mailster'),
								value: attributes.successColor,
								onChange: (value) => {
									setAttributes({
										successColor: value
									});
								},
							}, {
								label: __('Background', 'mailster'),
								value: attributes.successBGColor,
								onChange: (value) => {
									setAttributes({
										successBGColor: value
									});
								},
							}, ],
						}),


						el(PanelColorSettings, {
							title: __('Error Message', 'mailster'),
							initialOpen: this.state.displayErrorMessages,
							onToggle: () => {
								this.setState({
									displayErrorMessages: !this.state.displayErrorMessages
								});
							},
							colorSettings: [{
								label: __('Text Color', 'mailster'),
								value: attributes.errorColor,
								onChange: (value) => {
									setAttributes({
										errorColor: value
									});
								},
							}, {
								label: __('Background', 'mailster'),
								value: attributes.errorBGColor,
								onChange: (value) => {
									setAttributes({
										errorBGColor: value
									});
								},
							}, ],
						}),


						el(PanelBody, {
								title: __('List Settings', 'mailster'),
								className: 'mailster-options-lists',
								initialOpen: true
							},

							el(Fragment, {},
								el(ToggleControl, {
									label: __('Users decide which list they subscribe to', 'mailster'),
									checked: attributes.userchoice,
									onChange: (value) => {
										setAttributes({
											userchoice: value
										});
									},
								}),
								el('h2', {}, attributes.userchoice ? __('Users can subscribe to:', 'mailster') : __('Subscribe new users to:', 'mailster')),
								listSelector,

								attributes.userchoice && el(ToggleControl, {
									label: __('Use dropdown', 'mailster'),
									checked: attributes.dropdown,
									onChange: (value) => {
										setAttributes({
											dropdown: value
										});
									},
								}),
							),

						),

						el(PanelBody, {
								title: __('Options', 'mailster'),
								initialOpen: false
							},

							el(Fragment, {},
								el(ToggleControl, {
									label: __('Fill fields with known data if user is logged in.', 'mailster'),
									checked: attributes.prefill,
									onChange: (value) => {
										setAttributes({
											prefill: value
										});
									},
								}),
							),
							el(Fragment, {},
								el(ToggleControl, {
									label: __('Allow users to update their data with this form.', 'mailster'),
									checked: attributes.overwrite,
									onChange: (value) => {
										setAttributes({
											overwrite: value
										});
									},
								}),
							),
							el(Fragment, {},
								el(ToggleControl, {
									label: __('Show Asterisk', 'mailster'),
									checked: attributes.asterisk,
									onChange: function (value) {
										setAttributes({
											asterisk: value
										});
									},
								}),
							),

						),

						el(PanelBody, {
								title: __('Double Opt in', 'mailster'),
								initialOpen: false
							},

							el(Fragment, {},
								el(ToggleControl, {
									label: __('Enable Double opt In', 'mailster'),
									checked: attributes.doubleoptin,
									onChange: (value) => {
										setAttributes({
											doubleoptin: value
										});
									},
								}),
							),

							attributes.doubleoptin && el(Fragment, {},
								el(Fragment, {},
									el(TextControl, {
										label: __('Subject', 'mailster') + ' - {subject}',
										value: attributes.subject,
										onChange: (value) => {
											setAttributes({
												subject: value
											});
										},
									}),
								),

								el(Fragment, {},
									el(TextControl, {
										label: __('Headline', 'mailster') + ' - {headline}',
										value: attributes.headline,
										onChange: (value) => {
											setAttributes({
												headline: value
											});
										},
									}),
								),

								el(Fragment, {},
									el(TextControl, {
										label: __('Linktext', 'mailster') + ' - {link}',
										value: attributes.link,
										onChange: (value) => {
											setAttributes({
												link: value
											});
										},
									}),
								),

								el(Fragment, {},
									el(TextareaControl, {
										label: __('Text', 'mailster') + ' - {content}',
										value: attributes.content,
										help: __('The text new subscribers get when Double-Opt-In is selected. Use {link} for the link placeholder. Basic HTML is allowed.', 'mailster'),
										onChange: (value) => {
											setAttributes({
												content: value
											});
										},
									}),
								),
							),

						),


						el(PanelBody, {
								title: __('GDPR', 'mailster'),
								initialOpen: false
							},

							el(Fragment, {},
								el(ToggleControl, {
									label: __('Add Checkbox', 'mailster'),
									checked: attributes.gdpr,
									onChange: (value) => {
										setAttributes({
											gdpr: value
										});
									},
								}),
							),

							attributes.gdpr && el(Fragment, {},
								el(TextareaControl, {
									label: __('Text', 'mailster'),
									value: attributes.gdpr_text,
									onChange: (value) => {
										setAttributes({
											gdpr_text: value
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

			const {
				Disabled,
				Panel,
				PanelHeader,
				PanelBody,
				PanelRow,
				TextControl,
				ToggleControl,
				Text,
				RangeControl,
			} = wp.components;

			const {
				InnerBlocks,
				RichText,
				InspectorControls,
			} = wp.blockEditor;

			const {
				Fragment
			} = wp.element;

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

	class Email extends Input {

		constructor() {
			super();
			this.type = 'email';
		}

	}

	registerPlugin('mailster-form', {
		render: function () {

			this.state = {
				displaySuccessMessages: false,
				displayErrorMessages: false,
			}
			const {
				PluginDocumentSettingPanel
			} = wp.editPost;
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
				Fragment
			} = wp.element

			const {
				withState,
				setState
			} = wp.compose
			const {
				select,
				dispatch
			} = wp.data
			const {
				InnerBlocks,
				RichText,
				InspectorControls,
				PanelColorSettings,
			} = wp.blockEditor;

			var attributes = select('core/editor').getEditedPostAttribute('meta');

			console.log(attributes);

			var setAttributes = function (key, value) {
				attributes[key] = value;
				console.log(attributes);
				dispatch('core/editor').editPost({
					meta: attributes
				});
				//dispatch('core/editor').savePost();
			}


			var listSelector =
				Object.values(attributes.lists_order).map(function (listID, i) {
					return el('div', {
							key: 'list-' + listID,
						},

						attributes.userchoice && el('div', {
								className: 'list-mover'
							},
							el(Icon, {
								icon: 'arrow-up',
								className: (!i ? 'list-mover-is-hidden' : ''),
								title: __('Move list up', 'mailster'),
								label: 'more',
								onClick: () => {
									var copy = Object.assign([], attributes.lists_order);
									copy.splice(i, 0, copy.splice(i - 1, 1)[0]);
									setAttributes('lists_order', copy);
								}
							}),
							el(Icon, {
								icon: 'arrow-down',
								className: (i == (attributes.lists_order.length - 1) ? 'list-mover-is-hidden' : ''),
								title: __('Move list down', 'mailster'),
								label: 'more',
								onClick: () => {
									var copy = Object.assign([], attributes.lists_order);
									copy.splice(i, 0, copy.splice(i + 1, 1)[0]);
									setAttributes('lists_order', copy);
								}
							}),
						),
						el(CheckboxControl, {
							label: attributes.lists[listID],
							checked: attributes.lists_selected.indexOf(listID) != -1,
							onChange: (isChecked) => {

								var added = attributes.lists_selected.indexOf(listID) != -1;
								// Don't mutate the original object but make a copy of it and mutate that one
								var copy = Object.assign([], attributes.lists_selected);

								if (!added && isChecked) {
									copy.push(listID);
								} else if (added && !isChecked) {
									copy.splice(copy.indexOf(listID), 1);
								}

								setAttributes('lists_selected', copy);

							},
						}),

					);
				});
			return [

				el(PluginDocumentSettingPanel, {
						title: __('Notifications', 'mailster'),
						name: 'mailster-notifications',
						className: 'mailster-notifications',
					},
					el(Fragment, {},
						el(PanelColorSettings, {
							title: __('Success Message', 'mailster'),
							initialOpen: this.state.displaySuccessMessages,
							onToggle: () => {
								this.setState({
									displaySuccessMessages: !this.state.displaySuccessMessages
								});
							},
							colorSettings: [{
								label: __('Text Color', 'mailster'),
								value: attributes.successColor,
								onChange: (value) => {
									setAttributes({
										successColor: value
									});
								},
							}, {
								label: __('Background', 'mailster'),
								value: attributes.successBGColor,
								onChange: (value) => {
									setAttributes({
										successBGColor: value
									});
								},
							}, ],
						}),


						el(PanelColorSettings, {
							title: __('Error Message', 'mailster'),
							initialOpen: this.state.displayErrorMessages,
							onToggle: () => {
								this.setState({
									displayErrorMessages: !this.state.displayErrorMessages
								});
							},
							colorSettings: [{
								label: __('Text Color', 'mailster'),
								value: attributes.errorColor,
								onChange: (value) => {
									setAttributes({
										errorColor: value
									});
								},
							}, {
								label: __('Background', 'mailster'),
								value: attributes.errorBGColor,
								onChange: (value) => {
									setAttributes({
										errorBGColor: value
									});
								},
							}, ],
						}),

					),

				),



				el(PluginDocumentSettingPanel, {
						title: __('List Settings', 'mailster'),
						name: 'mailster-options-lists',
						className: 'mailster-options-lists',
					},
					el(Fragment, {},
						el(ToggleControl, {
							label: __('Users decide which list they subscribe to', 'mailster'),
							checked: attributes.userchoice,
							onChange: (value) => {
								setAttributes('userchoice', value);
							},
						}),
						el('h2', {}, attributes.userchoice ? __('Users can subscribe to:', 'mailster') : __('Subscribe new users to:', 'mailster')),
						listSelector,

						attributes.userchoice && el(ToggleControl, {
							label: __('Use dropdown', 'mailster'),
							checked: attributes.dropdown,
							onChange: (value) => {
								setAttributes('dropdown', value);
							},
						}),
					),

				),




				el(PluginDocumentSettingPanel, {
						title: __('Options', 'mailster'),
						name: 'mailster-options',
						className: 'mailster-options',
					},

					el(Fragment, {},
						el(ToggleControl, {
							label: __('Fill fields with known data if user is logged in.', 'mailster'),
							checked: attributes.prefill,
							onChange: (value) => {
								setAttributes('prefill', value);
							},
						}),
					),
					el(Fragment, {},
						el(ToggleControl, {
							label: __('Allow users to update their data with this form.', 'mailster'),
							checked: attributes.overwrite,
							onChange: (value) => {
								setAttributes('overwrite', value);
							},
						}),
					),
					el(Fragment, {},
						el(ToggleControl, {
							label: __('Show Asterisk', 'mailster'),
							checked: attributes.asterisk,
							onChange: function (value) {
								setAttributes('asterisk', value);
							},
						}),
					),

				),




				el(PluginDocumentSettingPanel, {
						title: __('Double Opt in', 'mailster'),
						name: 'mailster-double-opt-in',
						className: 'mailster-double-opt-in',
					},

					el(Fragment, {},
						el(ToggleControl, {
							label: __('Send confirmation email on signup.', 'mailster'),
							checked: attributes.doubleoptin,
							onChange: (value) => {
								setAttributes('doubleoptin', value);
							},
						}),
					),

					attributes.doubleoptin && el(Fragment, {},
						el(Fragment, {},
							el(TextControl, {
								label: __('Subject', 'mailster') + ' - {subject}',
								value: attributes.subject,
								onChange: (value) => {
									setAttributes('subject', value);
								},
							}),
						),

						el(Fragment, {},
							el(TextControl, {
								label: __('Headline', 'mailster') + ' - {headline}',
								value: attributes.headline,
								onChange: (value) => {
									setAttributes('headline', value);
								},
							}),
						),

						el(Fragment, {},
							el(TextControl, {
								label: __('Linktext', 'mailster') + ' - {link}',
								value: attributes.link,
								onChange: (value) => {
									setAttributes('link', value);
								},
							}),
						),

						el(Fragment, {},
							el(TextareaControl, {
								label: __('Text', 'mailster') + ' - {content}',
								value: attributes.content,
								help: __('The text new subscribers get when Double-Opt-In is selected. Use {link} for the link placeholder. Basic HTML is allowed.', 'mailster'),
								onChange: (value) => {
									setAttributes('content', value);
								},
							}),
						),
					),

				),


				false && el(PluginDocumentSettingPanel, {
						title: __('GDPR', 'mailster'),
						name: 'mailster-gdpr',
						className: 'mailster-gdpr',
					},

					el(Fragment, {},
						el(ToggleControl, {
							label: __('Add Checkbox', 'mailster'),
							checked: attributes.gdpr,
							onChange: (value) => {
								setAttributes('gdpr', value);
							},
						}),
					),

					attributes.gdpr && el(Fragment, {},
						el(TextareaControl, {
							label: __('Text', 'mailster'),
							value: attributes.gdpr_text,
							onChange: (value) => {
								setAttributes('gdpr_text', value);
							},
						}),
					),

				)
			]

		},
		icon: false // or false if you do not need an icon
	});

	// registerBlockType('mailster/form', {
	// 	icon: {
	// 		foreground: '#000',
	// 		src: el('svg', {
	// 				width: 20,
	// 				height: 20,
	// 				viewBox: "0 0 512 512",
	// 				transform: "scale(0.8)"
	// 			},
	// 			el('path', {
	// 				d: "M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z"
	// 			})
	// 		),
	// 	},
	// 	edit: Form,
	// 	save: function (props) {
	// 		return el(wp.blockEditor.InnerBlocks.Content);
	// 	},
	// });

	// registerBlockType('mailster/field-email', {
	// 	edit: Email,
	// 	icon: {
	// 		foreground: '#000',
	// 		src: el('svg', {
	// 				width: 24,
	// 				height: 24,
	// 				viewBox: "0 0 24 24",
	// 			},
	// 			el('path', {
	// 				d: "M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10h5v-2h-5c-4.34 0-8-3.66-8-8s3.66-8 8-8 8 3.66 8 8v1.43c0 .79-.71 1.57-1.5 1.57s-1.5-.78-1.5-1.57V12c0-2.76-2.24-5-5-5s-5 2.24-5 5 2.24 5 5 5c1.38 0 2.64-.56 3.54-1.47.65.89 1.77 1.47 2.96 1.47 1.97 0 3.5-1.6 3.5-3.57V12c0-5.52-4.48-10-10-10zm0 13c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3z"
	// 			}),
	// 			el('path', {
	// 				fill: 'none',
	// 				d: "M0 0h24v24H0z"
	// 			}),
	// 		),
	// 	},
	// });
	// registerBlockType('mailster/field-firstname', {
	// 	edit: Input
	// });
	// registerBlockType('mailster/field-lastname', {
	// 	edit: Input
	// });


})(
	window.wp
);