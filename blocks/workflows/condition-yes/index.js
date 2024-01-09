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

import edit from '../condition/edit';
import save from '../condition/save';
import icon from './Icon';
import json from './block.json';

const { name, ...settings } = json;

registerBlockType(name, {
	...settings,
	icon,
	edit,
	save,
});