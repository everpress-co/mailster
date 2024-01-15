/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, _n } from '@wordpress/i18n';
import { select } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { formatLists, formatTags, formatField } from '../../util/index.js';

export function getAction(id) {
	const allActions = select('mailster/automation').getActions();

	if (!allActions) {
		return null;
	}

	const action = allActions.find((action) => action.id == id);

	return action;
}

export function getInfo(attributes) {
	const { action, field, value, lists, tags } = attributes;

	switch (action) {
		case 'add_list':
		case 'remove_list':
			return formatLists(lists);
		case 'add_tag':
		case 'remove_tag':
			return formatTags(tags);
		case 'update_field':
			return formatField(field, value);

		default:
			break;
	}

	const actionObj = getAction(action);

	return (
		'<i>' + (actionObj?.info || __('Set up an action', 'mailster')) + '</i>'
	);
}
