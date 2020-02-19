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
	const TEMPLATE = [ [ 'mailster/input' ] , [ 'mailster/input' ] ];

	class Form extends Component {


		render(props) {
			// Setup the attributes
			const {
				attributes,
				setAttributes,
				className
			} = this.props;
			const {
				InnerBlocks,
				RichText,
				InspectorControls,
			} = wp.blockEditor;
			const {
				TextControl,
				CheckboxControl,
				RadioControl,
				SelectControl,
				TextareaControl,
				ToggleControl,
				RangeControl,
				Panel,
				PanelHeader,
				PanelBody,
				PanelRow
			} = wp.components;

			const {
				Fragment
			} = wp.element

			return [
				el('div', {
					className: className,
					key: 'form',
				},
					el(InnerBlocks,{
						allowedBlocks: ALLOWED_BLOCKS,
						//template: TEMPLATE,
					}),
					attributes.gdpr && el( RichText, {
						tagName: 'p',
						value: attributes.gdpr_text,
						formattingControls: [ 'bold', 'italic', 'link' ],
						onChange: ( value ) => {
							setAttributes( { gdpr_text: value } );
						},
					}),
					el( attributes.submittype, {

					}, 'Subscribe' ),
				),
				el( Fragment, {key:'inspector'},
					el( InspectorControls, {},

						el( PanelBody, { title: __('Options', 'mailster'), initialOpen: true },

							el( PanelRow, {},
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
							el( PanelRow, {},
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

							el( PanelRow, {},
								el( ToggleControl,
									{
										label: __('Use <button> instead of <a>nchor tags', 'mailster'),
										checked: attributes.submittype == 'button',
										onChange: ( value ) => {
											setAttributes( { submittype: value ? 'button' : 'a' } );
										},
									}
								),
							),

						),

						el( PanelBody, { title: __('Double Opt in', 'mailster'), initialOpen: true },

							el( PanelRow, {},
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

							el( PanelRow, {},
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

							el( PanelRow, {},
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

							el( PanelRow, {},
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

							el( PanelRow, {},
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


						el( PanelBody, { title: __('GDPR', 'mailster'), initialOpen: true },

							el( PanelRow, {},
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

							el( PanelRow, {},
								el( TextareaControl,
									{
										label: __('Text', 'mailster'),
										value: attributes.gdpr_text,
										help: __('The text new subscribers get when Double-Opt-In is selected. Use {link} for the link placeholder. Basic HTML is allowed.', 'mailster'),
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
			const {
				attributes,
				setAttributes,
				className,
				name,
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

			return [
				el('div', {
					className: className,
					key: 'input',
				},
					requiredToggle,
					el('label', {
						className: 'mailster-label',
					}, attributes.label),
					el(TextControl, {
						readOnly: true,
						value: '',
					}),
				),
				el( Fragment, {key:'inspectossr'},
					el( InspectorControls, {},

						el( PanelBody, { title: __('Options', 'mailster'), initialOpen: true },

							el( PanelRow, {}, requiredToggle),
							el( PanelRow, {},
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
	registerBlockType( 'mailster/field-input', { edit: Input });


} )(
	window.wp
);
