/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockVariation } from '@wordpress/blocks';

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

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */

const defaultAttributes = {
	name: 'input',
	title: 'Input Field',
	description: 'Code is poetry!',
	keywords: ['mailster', 'input', 'field'],
	scope: ['inserter', 'block', 'transform'],
	icon: {
		background: '#f00',
		src: 'button',
	},
	supports: {
		multiple: false,
	},
	attributes: {
		type: 'text',
		label: 'Input',
		requried: false,
		forceRequired: false,
	},
};

registerBlockVariation('mailster/input', {
	...defaultAttributes,
	...{
		name: 'email',
		title: 'Email Field',
		description: 'Code is poetry!',
		attributes: {
			type: 'email',
			label: 'Email',
			requried: true,
			forceRequired: true,
		},
	},
});

registerBlockVariation('mailster/input', {
	...defaultAttributes,
	...{
		name: 'input',
		isDefault: true,
		title: 'Input Field',
		description: 'Code is poetry!',
		attributes: {
			type: 'text',
			label: 'Input',
			requried: false,
			forceRequired: false,
		},
	},
});

registerBlockVariation('mailster/input', {
	...defaultAttributes,
	...{
		name: 'date',
		title: 'Date Field',
		description: 'Code is poetry!',
		attributes: {
			type: 'text',
			label: 'Date',
			requried: false,
			forceRequired: false,
		},
	},
});
