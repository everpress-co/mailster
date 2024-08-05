/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */
import { sprintf, __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */

export default function Label(attributes, { context }) {
	const { email, content, metadata } = attributes;

	if (metadata?.name) return metadata.name;

	const label = sprintf(__('Send Notification to %s', 'mailster'), email || '');

	return label || content;
}
