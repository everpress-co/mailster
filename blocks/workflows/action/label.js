/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, _n } from '@wordpress/i18n';

import { getAction, getInfo } from './functions';

/**
 * Internal dependencies
 */

export default function Label(attributes, { context }) {
	const { action, content } = attributes;

	const actionObj = getAction(action);
	const info = getInfo(attributes);

	return actionObj?.label || content;
}
