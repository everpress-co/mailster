/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType, createBlock } from '@wordpress/blocks';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */

window.mailster_fields &&
	window.mailster_fields.map((field) => {
		registerBlockType('mailster/field-' + field.id, {
			apiVersion: 2,
			title: field.name,
			keywords: ['mailster', field.name, field.id],
			category: 'mailster-form-fields',
			description: field.name + ' Description',
			parent: ['mailster/form-wrapper', 'core/column'],
			transforms: {
				to: window.mailster_fields.map((tofields) => {
					return {
						type: 'block',
						blocks: ['mailster/field-' + tofields.id],
						transform: (attributes, innerBlocks) => {
							return createBlock(
								'mailster/field-' + tofields.id,
								{
									id: attributes.id,
									inline: attributes.inline,
									style: attributes.style,
									values: attributes.values,
								},
								innerBlocks
							);
						},
					};
				}),
			},
			supports: {
				html: false,
				multiple: false,
				fontSize: true,
			},
			icon: {
				background: '#ff0',
				src: 'button',
			},
			attributes: {
				label: {
					type: 'string',
					default: field.name,
					source: 'html',
					selector: '.mailster-label',
				},
				name: {
					type: 'string',
					default: field.id,
				},
				id: {
					type: 'string',
					source: 'attribute',
					selector: '.input',
					attribute: 'id',
				},
				pattern: {
					type: 'string',
					source: 'attribute',
					selector: '.input',
					attribute: 'pattern',
				},
				required: {
					type: 'boolean',
					default: field.id == 'email',
					source: 'attribute',
					selector: '.input',
					attribute: 'required',
				},
				inline: {
					type: 'boolean',
					default: false,
				},
				type: {
					type: 'string',
					default: field.type,
				},
				values: {
					type: 'array',
					default: field.values || [],
				},
				selected: {
					type: 'string',
					default: field.default || null,
				},
				native: {
					type: 'boolean',
					default: field.type != 'textfield',
				},
				style: {
					type: 'object',
					default: {
						width: undefined,
						color: undefined,
						backgroundColor: undefined,
						borderColor: undefined,
						labelColor: undefined,
						borderWidth: undefined,
						borderRadius: undefined,
						fontSize: undefined,
					},
				},
			},
			edit,
			save,
		});
	});
