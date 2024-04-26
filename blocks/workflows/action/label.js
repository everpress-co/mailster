/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { getAction } from './functions';

/**
 * Internal dependencies
 */

export default function Label(attributes, { context }) {
	return 'a';
	const { action, content } = attributes;

	const actionObj = getAction(action);

	return actionObj?.label || content;
}
