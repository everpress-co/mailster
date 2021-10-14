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

const CONTENT = [];

/**
 * Internal dependencies
 */

const data = {
	name: 'my-variation',
	isDefault: true,
	title: 'Variation',
	description: 'Code is poetry!',
	icon: 'WordPressIcon',
	scope: ['inserter'],
	icon: {
		background: '#f00',
		src: 'layout',
	},
	attributes: { providerNameSlug: 'wordpress', align: 'full' },
	innerBlocks: CONTENT,
};

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockVariation('mailster/form-wrapper', data);
