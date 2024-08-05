/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';
import { getAction } from './functions';

/**
 * Internal dependencies
 */

export default function Label(attributes, { context }) {
	const { action, content, metadata } = attributes;

	if (metadata?.name) return metadata.name;

	if (!action) return content;

	return getAction(action)?.label || content;
}
