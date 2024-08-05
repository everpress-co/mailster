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
	const { step, content, metadata } = attributes;

	if (metadata?.name) return metadata.name;

	const label = sprintf(__('Jump to #%s', 'mailster'), step || '…');

	return label || content;
}
