/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { registerBlockType } from '@wordpress/blocks';
import { addFilter } from '@wordpress/hooks';
import { useEffect } from '@wordpress/element';
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
				inputColor: undefined,
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

function setParentToBlocks(settings, name) {
	if ('core/column' == name) {
		console.warn(settings);
	}
	if (!/^mailster\//.test(name)) {
		if (!settings['parent']) {
			settings['parent'] = ['mailster/form-wrapper'];
		} else if (!settings['parent'].includes('mailster/form-wrapper')) {
			settings['parent'].push('mailster/form-wrapper');
			settings['parent'] = settings['parent'].filter(
				(item) => item !== 'core/post-content'
			);
		}
	}

	return settings;
}

console.warn('AAA');

wp.hooks.addFilter(
	'blocks.registerBlockType',
	'mailster/forms/set-parent-to-blocks',
	setParentToBlocks
);
