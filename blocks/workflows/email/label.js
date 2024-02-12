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
	const { name, content } = attributes;

	return sprintf(__('Send Email %s', 'mailster'), '"' + name + '"') || content;
}
