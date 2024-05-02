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
	const { content, email } = attributes;

	const label = sprintf(__('Send Notification to %s', 'mailster'), email || '');

	return label || content;
}
