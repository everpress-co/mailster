/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';
import { registerBlockVariation } from '@wordpress/blocks';
import apiFetch from '@wordpress/api-fetch';

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
import json from './block.json';

const { name, ...settings } = json;

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */

apiFetch({ path: '/mailster/v1/fields' }).then((data) => {
	data.map((field) => {
		registerBlockVariation('mailster/input', {
			name: field.id,
			title: field.name,
			example: undefined,
			keywords: ['mailster', field.name, field.id],
			description: field.name + 'Description',
			icon: {
				background: '#ff0',
				src: 'button',
			},
			attributes: {
				id: field.id,
				type: field.type,
				label: field.name,
				requried: field.id == 'email',
				values: field.values || [],
			},
		});
	});
});

registerBlockType(name, {
	...settings,
	/**
	 * @see ./edit.js
	 */
	edit,

	/**
	 * @see ./save.js
	 */
	save,
});
