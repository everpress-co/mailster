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

import edit from './edit';
import save from './save';
import json from './block.json';
import icon from './Icon';

const { name, ...settings } = json;

registerBlockType(name, {
	...settings,
	icon,
	/**
	 * @see ./edit.js
	 */
	edit,

	/**
	 * @see ./save.js
	 */
	save,
});
