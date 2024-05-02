/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, sprintf } from '@wordpress/i18n';
import { getAction } from './functions';

/**
 * Internal dependencies
 */

export default function Label(attributes, { context }) {
	const { action } = attributes;

	return sprintf(__('Action %s', 'mailster'), '"' + action + '"' || '');

	const actionObj = getAction(action);

	if (!actionObj) {
		return content;
	}

	return actionObj.label;
}
