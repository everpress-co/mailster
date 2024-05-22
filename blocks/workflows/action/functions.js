/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';
import { select, useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { formatLists, formatTags, formatField } from '../../util/index.js';
import { ExternalLink } from '@wordpress/components';

export function getAction(action) {
	const allActions = select('mailster/automation').getActions();

	// const allActions = useSelect((select) =>
	// 	select('mailster/automation').getActions()
	// );

	if (!action || !allActions) {
		return null;
	}

	return allActions.find((a) => a.id == action);
}

export function getInfo(props) {
	const { attributes, isSelected } = props;
	const { action, field, value, lists, tags, webhook } = attributes;

	switch (action) {
		case 'add_list':
		case 'remove_list':
			return formatLists(lists);
		case 'add_tag':
		case 'remove_tag':
			return formatTags(tags);
		case 'update_field':
			return formatField(field, value);
		case 'webhook':
			if (!isValidHttpUrl(webhook) || !isSelected) {
				return <>{webhook}</>;
			}
			return <ExternalLink href={webhook}>{webhook}</ExternalLink>;

		default:
			break;
	}

	const actionObj = getAction(action);

	return <i> {actionObj?.info || __('Set up an action', 'mailster')}</i>;
}

function isValidHttpUrl(string) {
	let url;

	try {
		url = new URL(string);
	} catch (_) {
		return false;
	}

	return url.protocol === 'http:' || url.protocol === 'https:';
}
