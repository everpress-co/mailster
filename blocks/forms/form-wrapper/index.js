/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */

import edit from './edit';
import save from './save';
import json from './block.json';

const { name, attributes, ...settings } = json;

registerBlockType(name, {
	...settings,
	attributes: {
		...attributes,
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
	/**
	 * @see ./edit.js
	 */
	edit,

	/**
	 * @see ./save.js
	 */
	save,
});
