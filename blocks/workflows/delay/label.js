/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */
import { sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { getInfo, getLabel } from './functions';

export default function Label(attributes, { context }) {
	const { content, metadata } = attributes;

	if (metadata?.name) return metadata.name;

	const info = getInfo(attributes);
	const label = getLabel(attributes);

	return sprintf('%s %s', label, info) || content;
}
