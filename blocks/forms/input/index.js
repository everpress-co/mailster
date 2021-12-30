/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { registerBlockType, createBlock } from '@wordpress/blocks';

/**
 * Internal dependencies
 */

import edit from './edit';
import save from './save';
import Icons from './Icons';

window.mailster_fields &&
	window.mailster_fields.map((field) => {
		let label = {
			type: 'string',
			default: field.default || field.name,
			source: 'html',
			selector: '.mailster-label',
		};

		switch (field.id) {
			case 'submit':
				label = {
					...label,
					...{
						source: 'attribute',
						selector: 'input',
						attribute: 'value',
					},
				};
				break;
		}

		registerBlockType('mailster/field-' + field.id, {
			apiVersion: 2,
			title: field.name || field.id,
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
				src: Icons[field.id] || Icons.default,
			},
			attributes: {
				label: label,
				hasLabel: {
					type: 'boolean',
					default: !['checkbox', 'submit'].includes(field.type),
				},
				name: {
					type: 'string',
					default: field.id,
				},
				align: {
					type: 'string',
					default: '',
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
						labelColor: undefined,
						borderColor: undefined,
						borderWidth: undefined,
						borderStyle: undefined,
						borderRadius: undefined,
						fontSize: undefined,
					},
				},
			},
			edit,
			save,
		});
	});
