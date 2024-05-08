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
	const { action, content, metadata } = attributes;

	if (metadata?.name) return metadata.name;

	return sprintf(__('Action %s', 'mailster'), '"' + action + '"' || content);
}
