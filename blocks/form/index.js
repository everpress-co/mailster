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

import './style.scss';
import edit from './edit';
import json from './block.json';

import './ButtonExtension';

const { name, ...settings } = json;

registerBlockType(name, {
	...settings,
	edit,
	save: () => {
		return null;
	},
});
