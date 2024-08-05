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
	const { name, content, metadata } = attributes;

	if (metadata?.name) return metadata.name;

	return sprintf(__('Send Email %s', 'mailster'), '"' + name + '"') || content;
}
