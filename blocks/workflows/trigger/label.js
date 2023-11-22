/**
 * External dependencies
 */

import { __, sprintf } from '@wordpress/i18n';

/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */

export default function Label(attributes, { context }) {
	const { trigger, content } = attributes;

	return trigger
		? sprintf(__('Trigger %s', 'mailster'), '"' + trigger + '"')
		: content;
}
