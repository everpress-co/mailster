/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType, unregisterBlockType } from '@wordpress/blocks';

import { __experimentalGetCoreBlocks as coreBlocks } from '@wordpress/block-library';
import { useBlockProps, BlockEdit } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './style.scss';

/**
 * Internal dependencies
 */

const { addFilter } = wp.hooks;

addFilter(
	'blocks.registerBlockType',
	'mailster/buttonABC',
	(settings, name) => {
		if ('mailster/button' == name) {
		}
		// we need to pass along the settings object
		// even if we haven't modified them!
		return settings;
	}
);
addFilter(
	'blocks.getSaveElement',
	'mailster/buttonABCed',
	(element, blockType, attributes) => {
		if (blockType.name !== 'mailster/button') {
			return element;
		}

		return element;
	}
);
// add class from core block to get styles working
addFilter(
	'blocks.getBlockDefaultClassName',
	'mailster/set-block-custom-class-name',
	(className, blockName) => {
		return blockName === 'mailster/button' ? 'wp-block-button' : className;
	}
);

import json from './block.json';

const coreButton = coreBlocks().filter((block) => {
	return block.name == 'core/button';
})[0];

const edit = coreButton.settings.edit;
const save = coreButton.settings.save;

const { name, ...settings } = { ...coreButton.metadata, ...json };

settings.attributes = {
	...coreButton.metadata.attributes,
	...settings.attributes,
};

settings.supports = {
	...coreButton.metadata.supports,
	...settings.supports,
};

settings.attributes.text.default = 'HELLO';

console.warn(settings);

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
	edit: (props) => {
		return (
			<div className="wp-block-buttons mailster-wrapper">
				{edit(props)}
			</div>
		);
	},

	save: (props) => {
		return (
			<div className="wp-block-buttons mailster-wrapper">
				{save(props)}
			</div>
		);
	},
});

wp.domReady(function () {
	unregisterBlockType('core/button');
});
