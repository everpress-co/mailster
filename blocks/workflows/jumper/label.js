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
	const { content, step } = attributes;

	const label = sprintf(__('Jump #%s', 'mailster'), step || '…');

	return label || content;
}
