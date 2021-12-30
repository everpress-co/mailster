/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
//import './style.scss';
//import './../form/style.scss';

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
