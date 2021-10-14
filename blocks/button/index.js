/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './style.scss';
const { addFilter } = wp.hooks;
const filterBlocks = (settings) => {
	// we need to pass along the settings object
	// even if we haven't modified them!
	return settings;
};
addFilter(
	'blocks.registerBlockType', // hook name, very important!
	'mailster/button', // your name, very arbitrary!
	filterBlocks // function to run
);
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
registerBlockType(name, {
	...settings,
	/**
	 * @see ./edit.js
	 */
	edit,

	/**
	 * @see ./save.js
	 */
	save: () => {
		return null;
	},
});
