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

import '../inspector';
import edit from './edit';
import label from './label';
import save from './save';
import icon from './Icon';
import json from './block.json';

const { name, ...settings } = json;

registerBlockType(name, {
	...settings,
	icon,
	__experimentalLabel: label,
	edit,
	save,
});
