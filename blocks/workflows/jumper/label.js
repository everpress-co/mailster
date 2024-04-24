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
import { getInfo, getLabel } from './functions';

export default function Label(attributes, { context }) {
	const { content } = attributes;

	return sprintf(__('Jump to %s', 'mailster'), getLabel(attributes)) || content;
}
