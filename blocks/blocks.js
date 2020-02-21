( function( wp ) {
	/**
	 * Registers a new block provided a unique name and an object defining its behavior.
	 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/#registering-a-block
	 */
	var registerBlockType = wp.blocks.registerBlockType;
	const { Component } = wp.element;
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

	const ALLOWED_BLOCKS = [ 'mailster/*' ];
	const TEMPLATE = [ [ 'mailster/field-email' ] ];

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
				RadioControl,
				SelectControl,
				TextareaControl,
				ToggleControl,
				RangeControl,
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

			const themeColors = [
				{ name: 'red', color: '#f00' },
				{ name: 'white', color: '#fff' },
				{ name: 'blue', color: '#00f' },
				{ name: 'sblue', color: '#0ef' },
				{ name: 'gblue', color: '#0ff' },
			];

			if(attributes.asterisk)	className += ' has-asterisk';
			if(attributes.align)	className += ' align'+attributes.align;

			const listSelector = [];

			for (var i = 0; i < attributes.available_lists.length; i++) {
				listSelector.push(
					el( CheckboxControl,
						{
							label: attributes.available_lists[i].name,
							checked: attributes.lists.indexOf(parseInt(attributes.available_lists[i].ID, 10)) != -1,
							onChange: ( value ) => {
								console.log(value);
								setAttributes( { userchoice: value } );
							},
						}
					)
				);
			};


			return [
				el('div', {
					className: className,
					key: 'mailster-form',
				},

					// Messages
					(this.state.displaySuccessMessages || this.state.displayErrorMessages) && el( 'div', {
						className: 'wp-block'
					},
						this.state.displaySuccessMessages && el(RichText, {
							value: attributes.doubleoptin ? attributes.confirmMessage : attributes.successMessage,
							allowedFormats: [ 'core/bold', 'core/italic', 'core/link' ],
							style: {color: attributes.successColor, backgroundColor: attributes.successBGColor},
							className: 'mailster-notice mailster-notice-success',
							onChange: ( value ) => {
								if(attributes.doubleoptin){
									setAttributes( { confirmMessage: value } );
								}else{
									setAttributes( { successMessage: value } );
								}
							},
						}),
						this.state.displayErrorMessages && el(RichText, {
							value: attributes.errorMessage,
							allowedFormats: [ 'core/bold', 'core/italic', 'core/link' ],
							style: {color: attributes.errorColor, backgroundColor: attributes.errorBGColor},
							className: 'mailster-notice mailster-notice-error',
							onChange: ( value ) => {
								setAttributes( { errorMessage: value } );
							},
						}),
					),

					// The fields
					el(InnerBlocks,{
						className: 'mailster-form-fields',
						allowedBlocks: ALLOWED_BLOCKS,
						template: TEMPLATE,
					}),

					// GDPR checkbox
					attributes.gdpr && el( 'label', {
						className: 'gdpr-line',
					},
						el( 'input', {
							type: 'checkbox',
							disabled: true,
						}),
						el( RichText, {
							tagName: 'span',
							value: attributes.gdpr_text,
							allowedFormats: [ 'core/bold', 'core/italic', 'core/link' ],
							onChange: ( value ) => {
								setAttributes( { gdpr_text: value } );
							},
						}),
					),

					// Submit button
					el( 'div', {
						className: 'wp-block-button'
					},
						el( RichText, {
							value: attributes.submit,
							allowedFormats: ['core/align'],
							className: 'wp-block-button__link',
							onChange: ( value ) => {
								setAttributes( { submit: value } );
							},
						}),
						el( Fragment, { key:'inspector-form-button' },
							el( InspectorControls, {},

								el( PanelBody, { title: __('Options', 'mailster'), initialOpen: false },

									//el( Fragment, {}, requiredToggle),
									el( Fragment, {},
										el( TextControl,
											{
												label: __('Label', 'mailster'),
												value: attributes.label,
												onChange: ( value ) => {
													setAttributes( { label: value } );
												},
											}
										),
									),

								),

							),
						),
					),
				),
				el( Fragment, {key:'inspector'},
					el( InspectorControls, {},

						el( PanelColorSettings,
							{
								title: __('Success Message', 'mailster'),
								initialOpen: this.state.displaySuccessMessages,
								onToggle: () => {
									this.setState({ displaySuccessMessages: !this.state.displaySuccessMessages });
								},
								colorSettings: [
								{
									label: __('Text Color', 'mailster'),
									value: attributes.successColor,
									onChange: ( value ) => {
										setAttributes( { successColor: value } );
									},
								},
								{
									label: __('Background', 'mailster'),
									value: attributes.successBGColor,
									onChange: ( value ) => {
										setAttributes( { successBGColor: value } );
									},
								},
								],
							}
						),


						el( PanelColorSettings,
							{
								title: __('Error Message', 'mailster'),
								initialOpen: this.state.displayErrorMessages,
								onToggle: () => {
									this.setState({ displayErrorMessages: !this.state.displayErrorMessages });
								},
								colorSettings: [
								{
									label: __('Text Color', 'mailster'),
									value: attributes.errorColor,
									onChange: ( value ) => {
										setAttributes( { errorColor: value } );
									},
								},
								{
									label: __('Background', 'mailster'),
									value: attributes.errorBGColor,
									onChange: ( value ) => {
										setAttributes( { errorBGColor: value } );
									},
								},
								],
							}
						),


						el( PanelBody, { title: __('List Settings', 'mailster'), initialOpen: true },

							el( Fragment, {},
								el( ToggleControl,
									{
										label: __('Users decide which lists they subscribe to.', 'mailster'),
										checked: attributes.userchoice,
										onChange: ( value ) => {
											setAttributes( { userchoice: value } );
										},
									}
								),
								listSelector,
							),

						),

						el( PanelBody, { title: __('Options', 'mailster'), initialOpen: false },

							el( Fragment, {},
								el( ToggleControl,
									{
										label: __('Fill fields with known data if user is logged in.', 'mailster'),
										checked: attributes.prefill,
										onChange: ( value ) => {
											setAttributes( { prefill: value } );
										},
									}
								),
							),
							el( Fragment, {},
								el( ToggleControl,
									{
										label: __('Allow users to update their data with this form.', 'mailster'),
										checked: attributes.overwrite,
										onChange: ( value ) => {
											setAttributes( { overwrite: value } );
										},
									}
								),
							),
							el( Fragment, {},
								el( ToggleControl, {
										label: __('Show Asterisk', 'mailster'),
										checked: attributes.asterisk,
										onChange: function( value ){
											setAttributes( { asterisk: value } );
										},
									}
								),
							),

						),

						el( PanelBody, { title: __('Double Opt in', 'mailster'), initialOpen: false },

							el( Fragment, {},
								el( ToggleControl,
									{
										label: __('Enable Double opt In', 'mailster'),
										checked: attributes.doubleoptin,
										onChange: ( value ) => {
											setAttributes( { doubleoptin: value } );
										},
									}
								),
							),

							attributes.doubleoptin && el( Fragment, {},
								el( Fragment, {},
									el( TextControl,
										{
											label: __('Subject', 'mailster') +  ' - {subject}',
											value: attributes.subject,
											onChange: ( value ) => {
												setAttributes( { subject: value } );
											},
										}
									),
								),

								el( Fragment, {},
									el( TextControl,
										{
											label: __('Headline', 'mailster') + ' - {headline}',
											value: attributes.headline,
											onChange: ( value ) => {
												setAttributes( { headline: value } );
											},
										}
									),
								),

								el( Fragment, {},
									el( TextControl,
										{
											label: __('Linktext', 'mailster') + ' - {link}',
											value: attributes.link,
											onChange: ( value ) => {
												setAttributes( { link: value } );
											},
										}
									),
								),

								el( Fragment, {},
									el( TextareaControl,
										{
											label: __('Text', 'mailster')+ ' - {content}',
											value: attributes.content,
											help: __('The text new subscribers get when Double-Opt-In is selected. Use {link} for the link placeholder. Basic HTML is allowed.', 'mailster'),
											onChange: ( value ) => {
												setAttributes( { content: value } );
											},
										}
									),
								),
							),

						),


						el( PanelBody, { title: __('GDPR', 'mailster'), initialOpen: false },

							el( Fragment, {},
								el( ToggleControl,
									{
										label: __('Add Checkbox', 'mailster'),
										checked: attributes.gdpr,
										onChange: ( value ) => {
											setAttributes( { gdpr: value } );
										},
									}
								),
							),

							attributes.gdpr && el( Fragment, {},
								el( TextareaControl,
									{
										label: __('Text', 'mailster'),
										value: attributes.gdpr_text,
										onChange: ( value ) => {
											setAttributes( { gdpr_text: value } );
										},
									}
								),
							),

						),

					),
				),
			];
		}
	}

	class Input extends Component{

		constructor() {
			super();
			this.type = 'text';
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
			} = wp.components;

			const {
				InnerBlocks,
				RichText,
				InspectorControls,
			} = wp.blockEditor;

			const {
				Fragment
			} = wp.element;

			var requiredToggle = el( ToggleControl, {
					label: __('Required', 'mailster'),
					className: 'mailster-required-toggle',
					checked: attributes.required,
					onChange: function( value ){
						setAttributes( { required: value } );
					},
				}
			);
			if('mailster/field-email' == name){
				requiredToggle = el(Disabled, {}, requiredToggle);
			}

			className = 'wp-block-mailster-field ' + className;
			if(attributes.required)	className += ' is-required';

			return [
				el('div', {
					className: className,
					key: 'input-'+this.type,
				},
					//requiredToggle,
					el(RichText, {
						tagName: 'label',
						className: 'mailster-label',
						value: attributes.label,
						allowedFormats: [ 'core/bold', 'core/italic', 'core/link' ],
						onChange: ( value ) => {
							setAttributes( { label: value } );
						},
					}),
					el('input', {
						type: 'text',
						className: 'mailster-input mailster-input-'+name,
					}),
				),

				el( Fragment, { key:'inspector-input-'+this.type },
					el( InspectorControls, {},

						el( PanelBody, { title: __('Options', 'mailster'), initialOpen: false },

							el( Fragment, {}, requiredToggle),
							el( Fragment, {},
								el( TextControl,
									{
										label: __('Label', 'mailster'),
										value: attributes.label,
										onChange: ( value ) => {
											setAttributes( { label: value } );
										},
									}
								),
							),

						),

					),
				),

			];
		}

	}

	class Email extends Input{

		constructor() {
			super();
			this.type = 'email';
		}

	}


	registerBlockType( 'mailster/form', {
		icon: {
		    foreground: '#000',
		    src: el('svg', { width: 20, height: 20, 'viewBox': "0 0 512 512", 'transform': "scale(0.8)" },
			  el('path', { d: "M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z" } )
			),
		},
		edit: Form,
		save: function( props ) {
		  return el(wp.blockEditor.InnerBlocks.Content);
		  //return el('div', {className: props.attributes.className}, el(wp.blockEditor.InnerBlocks.Content));
		},
	} );

	registerBlockType( 'mailster/field-email', { edit: Email });
	registerBlockType( 'mailster/field-firstname', { edit: Input });
	registerBlockType( 'mailster/field-lastname', { edit: Input });


} )(
	window.wp
);
