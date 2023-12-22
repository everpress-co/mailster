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

import edit from './edit';
import save from './save';
import json from './block.json';
import icon from './Icon';
import { TABS } from '../homepage/constants';

const { name, ...settings } = json;

registerBlockType(name, {
	...settings,
	__experimentalLabel: (attributes) => {
		const { content, type } = attributes;
		const currentTab = TABS.find((tab) => tab.id === type);
		return currentTab.name || content;
	},
	icon,
	edit,
	save,
});
