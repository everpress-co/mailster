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
